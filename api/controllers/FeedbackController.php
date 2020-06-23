<?php
namespace api\controllers;

use Yii;
use api\models\Feedback;
use yii\web\UploadedFile;
use common\helpers\FileHelper;
use common\helpers\StringHelper;

class FeedbackController extends ApibaseController{
    public $modelClass = '';

	public function init(){
		parent::init();
	}

	//提交意见反馈
	public function actionAdd(){
		$request = Yii::$app->request;

		$access_token = $request->post('access_token');
		$uinfo = $this->checkToken($access_token);
        $language = $request->post('language');
        $language =  $language == 'en_us' ? 'en_us' : 'zh_cn';

		$content = $request->post('content');
		$this->check_empty($content,'_Feedback_Content_Not_Empty_');

		$feedback_model = new Feedback;
        $feedback_model->type = 1;
		$feedback_model->member_id = $uinfo['id'];
		$feedback_model->content = $content;

        if(!empty($_FILES['thumb']['tmp_name'])){
            /**
             * 图片配置名称
             */
            $type = 'imagesUpload';
            $stateMap = Yii::$app->params['uploadState'];

            // 图片上传配置
            $uploadConfig = Yii::$app->params[$type];
            $file = $_FILES['thumb'];
            $file_size = $file['size'];// 大小
            $file_name = $file['name'];// 原名称

            $file_exc = StringHelper::clipping($file_name);// 后缀

            if($file_size > $uploadConfig['maxSize'])// 判定大小是否超出限制
            {
                return $this->setResponse($stateMap['ERROR_SIZE_EXCEED']);
            }
            else if(!$this->checkType($file_exc, $type))// 检测类型
            {
                return $this->setResponse($stateMap['ERROR_TYPE_NOT_ALLOWED']);
            }
            else
            {
                // 相对路径
                if(!($path = $this->getPath($type)))
                {
                    return $this->setResponse(Yii::t($language,'_Folder_Creation_Failed__IsOpen_Attachment_Write_Permission_'));
                }

                $filePath = $path . $uploadConfig['prefix'] . StringHelper::random(10) . $file_exc;
                // 利用yii2自带的上传
                $uploadFile = UploadedFile::getInstanceByName('thumb');

                if($uploadFile->saveAs(Yii::getAlias("@attachment/") . $filePath))
                {
                    $temp = [
                        'path' => $filePath,
                        'urlPath' => Yii::getAlias("@attachurl/") . $filePath,
                    ];
                    $feedback_model->thumb = $temp['urlPath'];
                }else{
                    return $this->setResponse(Yii::t($language,'_File_Move_Error_'));
                }
            }
        }

        if($feedback_model->save() > 0){
            $this->success_message();
        }else{
            $this->error_message('_Submission_Failure_');
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
