<?php
namespace api\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * 直播间
 *
 * Class RoomController
 * @package api\controllers
 */
class RoomController extends AController
{
    public $modelClass = 'common\models\live\Room';

    /**
     * 首页
     *
     * @return ActiveDataProvider
     */
    public function actionIndex()
    {
        $modelClass = $this->modelClass;
        $query = $modelClass::find();

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
     * @return bool|void
     */
    public function actionCreate()
    {
        $model = new $this->modelClass();
        $model->member_id = Yii::$app->user->identity->user_id;
        $model->attributes = Yii::$app->request->post();

        if (!$model->save())
        {
            // 返回数据验证失败
            return $this->setResponse($this->analysisError($model->getFirstErrors()));
        }

        return $model;
    }

    /**
     * 更新
     *
     * @param $id
     * @return mixed|void
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->attributes = Yii::$app->request->post();
        if (!$model->save())
        {
            // 返回数据验证失败
            return $this->setResponse($this->analysisError($model->getFirstErrors()));
        }

        return $model;
    }

    /**
     * 删除
     *
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        return $this->findModel($id)->delete();
    }

    /**
     * 详情
     *
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->findModel($id);
    }

    /**
     * 返回模型
     *
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $request = Yii::$app->request;
        $language = $request->post('language');
        $language =  $language == 'en_us' ? 'en_us' : 'zh_cn';

        if (empty($id))
        {
            throw new NotFoundHttpException(Yii::t($language,'_Failure_Requested_Data_'));
        }

        $modelClass = $this->modelClass;
        if ($model = $modelClass::findOne($id))
        {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t($language,'_Failure_Requested_Data_'));
    }
}
