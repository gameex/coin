<?php
namespace api\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use common\controllers\ActiveController;

/**
 * 基类控制器
 *
 * Class AController
 * @package api\controllers
 */
class AController extends ActiveController
{
     public function behaviors()
     {
          return [
              'verbs' => [
                  'class' => \yii\filters\VerbFilter::className(),
                  'actions' => [
                      'index'  => ['GET', 'POST'],
                      'view'   => ['GET', 'POST'],
                      'create' => ['GET', 'POST'],
                      'update' => ['GET', 'PUT', 'POST'],
                      'delete' => ['POST', 'DELETE', 'POST'],
                  ],
              ],
          ];
     }

    public function actions()
    {p('actions');
        $actions = parent::actions();
        // 注销系统自带的实现方法
        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);
        // 自定义数据indexDataProvider覆盖IndexAction中的prepareDataProvider()方法
        // $actions['index']['prepareDataProvider'] = [$this, 'indexDataProvider'];
        //p(Yii::$app->request->getMethod());die();
        return $actions;
    }

    /**
     * 首页
     *
     * @return ActiveDataProvider
     */
    public function actionIndex()
    {p($this->modelClass);p('actionIndex');//exit();
        $modelClass = $this->modelClass;
        $query = $modelClass::find();

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
     * 创建
     *
     * @return bool
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

        if ($model = $this->modelClass::findOne($id))
        {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t($language,'_Failure_Requested_Data_'));
    }
}
