<?php
/**
 * Created by PhpStorm.
 * User: op
 * Date: 2018-05-29
 * Time: 19:26
 */

namespace api\controllers;

use api\models\Member;
use api\models\Merchants;
use common\helpers\FileHelper;
use common\helpers\StringHelper;
use common\jinglan\Common;
use common\jinglan\Jinglan;
use common\models\base\AccessToken;
use api\models\Message;
use yii\db\Expression;
use yii\web\UploadedFile;
use Yii;
use yii\data\Pagination;
use jinglan\bitcoin\Balance;
use yii\db\Query;
use Denpa\Bitcoin\Client as BitcoinClient;
use common\jinglan\CreateWallet;


class MerchantsController extends ApibaseController
{
    public $modelClass = '';

    public  $uploadConfig = [
        // 视频上传配置
        'videosUpload' => [
            'maxSize'    => 1024 * 1024 * 10,// 最大上传大小,默认10M
            'maxExc'     => ['.mp4'],// 可上传文件后缀不填写即为不限
            'path'       => 'videos/',// 创建路径
            'subName'    => 'Y/m/d',// 上传子目录规则
            'prefix'     => 'video_',// 名称前缀
        ],
        // 上传状态映射表
        'uploadState' => [
            "ERROR_TMP_FILE"           => "临时文件错误",
            "ERROR_TMP_FILE_NOT_FOUND" => "找不到临时文件",
            "ERROR_SIZE_EXCEED"        => "文件大小超出网站限制",
            "ERROR_TYPE_NOT_ALLOWED"   => "文件类型不允许",
            "ERROR_CREATE_DIR"         => "目录创建失败",
            "ERROR_DIR_NOT_WRITEABLE"  => "目录没有写权限",
            "ERROR_FILE_MOVE"          => "文件保存时出错",
            "ERROR_FILE_NOT_FOUND"     => "找不到上传文件",
            "ERROR_WRITE_CONTENT"      => "写入文件内容错误",
            "ERROR_UNKNOWN"            => "未知错误",
            "ERROR_DEAD_LINK"          => "链接不可用",
            "ERROR_HTTP_LINK"          => "链接不是http链接",
            "ERROR_HTTP_CONTENTTYPE"   => "链接contentType不正确"
        ],
    ];

    public function init(){
        parent::init();
    }

    //商家认证信息
    public function actionInfo(){
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        $os = strtolower($request->post('os'));
        if (!empty($access_token)){
            if (in_array($os, ['ios','android'])){
                $uinfo = $this->checkToken($access_token);
            }else{
                $uinfo = $this->memberToken($access_token);
            }
            $uid = $uinfo['id'];
        }else{
            $uid = 0;
        }
        $res = Member::find()->where(['id'=>$uid, 'verified_status'=>1])->one();
        if(empty($res)){
            $this->error_message('请先去实名认证');
        }
        $data = Merchants::find()->where(['uid' => $uid])->andWhere(['>', 'status', '0'])->asArray()->one();
        if(empty($data)){
            $this->error_message('未认证!');
        }
        $request = Yii::$app->request;
        $language = $request->post('language');
        $language =  $language == 'en_us' ? 'en_us' : 'zh_cn';
        $data['image'] = $this->get_user_avatar_url($data['image']);
        $data['video'] = $this->get_user_avatar_url($data['video']);
        $status_msg = [
                        Yii::t($language,'_Deleted_'),
                        Yii::t($language,'_Waiting_For_Audit_'),
                        Yii::t($language,'_Audit_Has_Passed_'),
                        Yii::t($language,'_Audit_Failed_Upload_Real_Info_'),
                    ];
        $data['status_msg'] = $status_msg[$data['status']];
        $this->success_message($data);
    }

    //重新申请商家认证
    public function actionEliminate(){
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        $os = strtolower($request->post('os'));
        if (!empty($access_token)){
            if (in_array($os, ['ios','android'])){
                $uinfo = $this->checkToken($access_token);
            }else{
                $uinfo = $this->memberToken($access_token);
            }
            $uid = $uinfo['id'];
        }else{
            $uid = 0;
        }
        $res = Member::find()->where(['id'=>$uid, 'verified_status'=>1])->one();
        if(empty($res)){
            $this->error_message('请先去实名认证');
        }
        $data = Merchants::find()->where(['uid' => $uid,'status' => 3])->one();
        if(empty($data)){
            $this->error_message('不能重新认证!');
        }
        $data['status'] = 0;
        if($data->save() > 0){
            $this->success_message();
        }else{
            $this->error_message('重新认证失败');
        }
    }

    // 商家认证
    public function actionMerchants(){
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        $os = strtolower($request->post('os'));
        if (!empty($access_token)){
            if (in_array($os, ['ios','android'])){
                $uinfo = $this->checkToken($access_token);
            }else{
                $uinfo = $this->memberToken($access_token);
            }
            $uid = $uinfo['id'];
        }else{
            $uid = 0;
        }
        $res = Member::find()->where(['id'=>$uid, 'verified_status'=>1])->asArray()->one();
        if(empty($res)){
            $this->error_message('请先去实名认证');
        }
        $this->check_submit($uid);
        $image = $request->post('image');
        $describe = $request->post('describe');
        $video = $request->post('video');
        $this->check_empty($image, '图片不能为空!');
        $this->check_empty($describe, '描述不能为空!');
        $this->check_empty($video, '视频不能为空!');
        $this->check_img($image);
        $this->check_vdo($video);
        if($this->save_info($uid,$image,$describe,$video)){
            $this->success_message();
        }else{
            $this->error_message('_Save_Failure_Try_Again_');
        }
    }

    /**
     * 检查是否能提交
     */
    private function check_submit($uid){
        $exist = Merchants::find()->where(['uid' => $uid])->asArray()->one();
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
     * 检查视频是否存在
     */
    private function check_vdo($path){
        $file = Yii::getAlias("@rootPath/web") . $path;
        if(!file_exists($file)){
            $this->error_message('视频不存在!');
        }
    }

    /**
     * @param $uid
     * @param $url
     * @param $describe
     * @param $url2
     * @return bool
     */
    private function save_info($uid,$url1,$describe,$url2){
        $model = Merchants::findOne(['uid' => $uid]);
        if(empty($model)) {
            $model = new Merchants();
            $model->uid = $uid;
        }
        $model->image = $url1;
        $model->describe = $describe;
        $model->video  = $url2;
        $model->status = 1;
        $model->created_at = date('Y-m-d H:i:s');
        return $model->save();
    }

    /**
     * 上传图片
     */
    public function actionImage(){
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        $os = strtolower($request->post('os'));
        if (!empty($access_token)){
            if (in_array($os, ['ios','android'])){
                $uinfo = $this->checkToken($access_token);
            }else{
                $uinfo = $this->memberToken($access_token);
            }
            $uid = $uinfo['id'];
        }else{
            $uid = 0;
        }
        $this->check_submit($uid);
        $res = Member::find()->where(['id'=>$uid, 'verified_status'=>1])->asArray()->one();
        if(empty($res)){
            $this->error_message('请先去实名认证');
        }
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


    /**
     * 上传视频
     */
    public function actionVideo(){
        $request = Yii::$app->request;
        $access_token = $request->post('access_token');
        $os = strtolower($request->post('os'));
        if (!empty($access_token)){
            if (in_array($os, ['ios','android'])){
                $uinfo = $this->checkToken($access_token);
            }else{
                $uinfo = $this->memberToken($access_token);
            }
            $uid = $uinfo['id'];
        }else{
            $uid = 0;
        }
        $this->check_submit($uid);
        // $res = Member::find()->where(['id'=>$uid, 'verified_status'=>1])->asArray()->one();
        // if(empty($res)){
        //     $this->error_message('请先去实名认证');
        // }
        $file = $_FILES['video'];
        $data = $this->move($file, 'video');
        $this->success_message($data);
    }

    /**
     * 文件上传
     */
    private function move($file,$name){
        $type = 'videosUpload';
        $file = $_FILES['video'];
        $file_size = $file['size']; // 视频大小
        $file_name = $file['name']; // 文件名称
        $file_exc = strtolower(strrchr($file_name, '.'));// 后缀
        if($file_size > $this->uploadConfig['videosUpload']['maxSize']){// 判定大小是否超出限制
            $message =  $this->uploadConfig['uploadState']['ERROR_SIZE_EXCEED'];
            $this->error_message($message);
            // return $this->uploadConfig['uploadState']['ERROR_SIZE_EXCEED'];
        }else if(!$this->actionCheckType($this->uploadConfig,$file_exc, $type)){// 检测类型
            $message = $this->uploadConfig['uploadState']['ERROR_TYPE_NOT_ALLOWED'];
            $this->error_message($message);
            // return $this->uploadConfig['uploadState']['ERROR_TYPE_NOT_ALLOWED'];
        } else {
            // 相对路径
            if(!($path = $this->actionGetPath($this->uploadConfig,$type)))
            {
                $this->error_message('文件夹创建失败,请确认是否开启attachment文件夹写入权限');
                // return '文件夹创建失败,请确认是否开启attachment文件夹写入权限';
            }
            $filePath = $path . $this->uploadConfig['videosUpload']['prefix'] . StringHelper::random(10) . $file_exc;
            // 利用yii2自带的上传
            $uploadFile = \yii\web\UploadedFile::getInstanceByName('video');
            if($uploadFile->saveAs(Yii::getAlias("@attachment/") . $filePath))
            {
                $temp = [
                    'path' => $filePath,
                    'urlPath' => Yii::getAlias("@attachurl/")  . $filePath,
                ];
                return $temp['urlPath'];
            }else{
                // return '文件移动错误';
                $this->error_message('文件移动错误');

            }
        }
    }

    /**
     * 文件路径
     * @param $alias
     * @return bool|string
     */
    public  function actionGetAlias($alias)
    {
        if (is_string($alias)) {
            return $_SERVER['DOCUMENT_ROOT'].$alias;
        }
        return false;
    }
    /**
     * 返回带协议的域名
     */
    protected function actionGetHost(){
        $host=$_SERVER["HTTP_HOST"];
        $protocol=$this->is_ssl()?"https://":"http://";
        return $protocol.$host;
    }
    /**
     * 判断是否SSL协议
     * @return boolean
     */
    function is_ssl() {
        if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))){
            return true;
        }elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] )) {
            return true;
        }
        return false;
    }
    /**
     * 获取随机字符串
     *
     * @param $length
     * @param bool $numeric
     * @return string
     */
    public static function actionRandom($length, $numeric = false)
    {
        $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
        if ($numeric)
        {
            $hash = '';
        }
        else
        {
            $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
            $length--;
        }
        $max = strlen($seed) - 1;
        for ($i = 0; $i < $length; $i++)
        {
            $hash .= $seed{mt_rand(0, $max)};
        }
        return $hash;
    }
    
    /**
     * 获取文件路径
     *
     * @param $type
     * @return string
     */
    public function actionGetPath($uploadConfig,$type)
    {
        // 文件路径
        $file_path = $uploadConfig[$type]['path'];
        // 子路径
        $sub_name = $uploadConfig[$type]['subName'];
        $path = $file_path . date($sub_name,time()) . "/";
        $add_path = $this->actionGetAlias("/attachment/") . $path;
        FileHelper::mkdirs($add_path);
        return $path;
    }
    /**
     * 文件类型检测
     *
     * @param $ext
     * @param $type
     * @return bool
     */
    public function actionCheckType($uploadConfig,$ext, $type)
    {
        if(empty($uploadConfig[$type]['maxExc']))
        {
            return true;
        }
        return in_array($ext, $uploadConfig[$type]['maxExc']);
    }
}