<?php
/**
 * Created by PhpStorm.
 * User: landehua
 * Date: 2018/5/29 0029
 * Time: 12:23
 */

namespace api\controllers;

use api\models\MemberVerified;
use common\helpers\FileHelper;
use common\helpers\IdCardHelper;
use common\helpers\StringHelper;
use common\models\base\AccessToken;
use Yii;
use yii\web\UploadedFile;

class CertificationController extends ApibaseController{
    public $modelClass = '';
    public function actionGetInfo(){
        $uid = Yii::$app->user->identity->user_id;
        $data = MemberVerified::find()->where(['uid' => $uid])->andWhere(['>', 'status', '0'])->asArray()->one();
        if(empty($data)){
            $this->error_message('_No_Certification_Certified_In_Time_');
        }
        $request = Yii::$app->request;
        $language = $request->post('language');
        $language =  $language == 'en_us' ? 'en_us' : 'zh_cn';

        $data['id_card_img'] = $this->get_user_avatar_url($data['id_card_img']);
        $data['id_card_img2'] = $this->get_user_avatar_url($data['id_card_img2']);

        $status_msg = [
                        Yii::t($language,'_Deleted_'),
                        Yii::t($language,'_Waiting_For_Audit_'),
                        Yii::t($language,'_Audit_Has_Passed_'),
                        Yii::t($language,'_Audit_Failed_Upload_Real_Info_'),
                    ];
        $data['status_msg'] = $status_msg[$data['status']];

        $this->success_message($data);
    }

    public function actionEliminate(){
        $uid = Yii::$app->user->identity->user_id;
        $data = MemberVerified::find()->where(['uid' => $uid,'status' => 3])->one();
        if(empty($data)){
            $this->error_message('不能重新认证');
        }
        $data['status'] = 0;
        if($data->save() > 0){
            $this->success_message();
        }else{
            $this->error_message('重新认证失败');
        }
    }

    // 实名认证
    public function actionSetReal(){
        $request = Yii::$app->request;
        $uid = Yii::$app->user->identity->user_id;
        $this->check_submit($uid);
        $real_name = $request->post('real_name');
        $id_number = $request->post('id_number');
        $id_card_img = $request->post('id_card_img');
        $id_card_img2 = $request->post('id_card_img2');

        $this->check_empty($real_name, '_Name_NOT_Empty_');
        $this->check_empty($id_number, '_IdCard_Not_Empty_');
        $this->check_empty($id_card_img, '_Upload_IdCard_Front_');
        $this->check_empty($id_card_img2, '_Upload_IdCard_Back_');

        $this->check_img($id_card_img);
        $this->check_img($id_card_img2);

        $IDCard = new IdCardHelper();
        if(!$IDCard->validation_filter_id_card($id_number)){
            $this->error_message('_IdCard_Format_Wrong_');
        }
        if($this->save_info($uid,$real_name,$id_number,$id_card_img,$id_card_img2)){
            $this->success_message();
        }else{
            $this->error_message('_Save_Failure_Try_Again_');
        }
    }

    /**
     * 检查是否能提交
     */
    private function check_submit($uid){
        $exist = MemberVerified::find()->where(['uid' => $uid])->asArray()->one();
        if(empty($exist)){
            return true;
        }else{
            if(in_array($exist['status'],[1,2])){
                $this->error_message('_Have_Submit_Auth_Info_');
            }else{
                return true;
            }
        }
    }

    /**
     * 检查图片是否存在
     */
    private function check_img($url){
        $file = Yii::getAlias("@rootPath/web") . $url;
        if(!file_exists($file)){
            $this->error_message('_Picture_Not_Exist_Reupload_');
        }
    }

    /**
     * @param $uid
     * @param $real_name
     * @param $id_number
     * @param $url1
     * @param $url2
     * @return bool
     */
    private function save_info($uid,$real_name,$id_number,$url1,$url2){
        $model = MemberVerified::findOne(['uid' => $uid]);
        if(empty($model)) {
            $model = new MemberVerified();
            $model->uid = $uid;
        }
        $model->real_name = $real_name;
        $model->id_number = $id_number;
        $model->id_card_img  = $url1;
        $model->id_card_img2 = $url2;
        $model->status = 1;
        $model->ctime = date('Y-m-d H:i:s');
        return $model->save();
    }

    /**
     * 上传图片
     */
    public function actionUploadImage(){
        $uid = Yii::$app->user->identity->user_id;
        $this->check_submit($uid);
        $file = $_FILES['image'];
        $data = $this->upload($file, 'image');
        $this->success_message($data);
    }

    /**
     * @param $file
     * @param $name
     * @return array
     */
    private function upload($file,$name){
        $type = 'imagesUpload';
        $uploadConfig = Yii::$app->params[$type];
        $stateMap = Yii::$app->params['uploadState'];
        $file_size = $file['size'];
        $file_name = $file['name'];
        $file_exc = StringHelper::clipping($file_name);

        if ($file_size > $uploadConfig['maxSize']){
            $message = $stateMap['ERROR_SIZE_EXCEED'];
            $this->error_message($message);
        } else if (!$this->checkType($file_exc, $type)){
            $message = $stateMap['ERROR_TYPE_NOT_ALLOWED'];
            $this->error_message($message);
        } else {
            if (!($path = $this->getPath($type))) {
                $message = '_Folder_Creation_Failed__IsOpen_Attachment_Write_Permission_';
                $this->error_message($message);
            }
            $filePath = $path . $uploadConfig['prefix'] . StringHelper::random(10) . $file_exc;
            $uploadFile = UploadedFile::getInstanceByName($name);

            if ($uploadFile->saveAs(Yii::getAlias("@attachment/") . $filePath)) {
                $data = [
                    'urlPath' => Yii::getAlias("@attachurl/") . $filePath,
                ];
                return $data;
            } else {
                $message = '_File_Move_Error_';
                $this->error_message($message);
            }
        }
    }

    /**
     * 文件类型检测
     *
     * @param $ext
     * @param $type
     * @return bool
     */
    private function checkType($ext, $type)
    {
        if(empty(Yii::$app->params[$type]['maxExc']))
        {
            return true;
        }

        return in_array($ext, Yii::$app->params[$type]['maxExc']);
    }
    /**
     * 获取文件路径
     *
     * @param $type
     * @return string
     */
    public function getPath($type)
    {
        // 文件路径
        $file_path = Yii::$app->params[$type]['path'];
        // 子路径
        $sub_name = Yii::$app->params[$type]['subName'];
        $path = $file_path . date($sub_name,time()) . "/";
        $add_path = Yii::getAlias("@attachment/") . $path;
        // 创建路径
        FileHelper::mkdirs($add_path);
        return $path;
    }
}