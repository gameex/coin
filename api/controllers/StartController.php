<?php

namespace api\controllers;

use Yii;
use common\models\StartPage;
use common\models\Article;
use common\models\ArticleCate;
use yii\data\Pagination;
use jianyan\basics\common\models\sys\ArticleSingle;


class StartController extends ApibaseController
{
    public $modelClass = '';

    public function init(){
        parent::init();
    }
    
    public function actionStartPage(){
        $request = Yii::$app->request;
        $host = Yii::$app->request->hostInfo;
        $type = $request->post('type');
        $this->check_empty($type,'类型不能为空');
        $data = StartPage::find()->select('title,img,url')->where(['status'=>1,'type'=>$type])->asArray()->all();
        if ($data&&$type=1) {
            foreach ($data as $k =>$v) {
                $data[$k]['img'] = $host.$v['img'] ;
            }
            $this->success_message($data,'_Success_');

        }elseif($data&&$type=2){
            foreach ($data as $k =>$v) {
                $data[$k]['img'] = $host.$v['img'] ;
            }
            $this->success_message($data,'_Success_');
        }else{
            $this->error_message('_No_Data_Query_');
        }
    }

    public function actionCate(){
        $request = Yii::$app->request;
        $host = Yii::$app->request->hostInfo;
        $id = $request->post('id');
        if (empty($id)) {
            $id = $request->get('id');
        }
        
        $this->check_empty($id,'分类ID不能为空');
        $models = Article::find()->select('id,title,append,cover,link')->where(['cate_id'=>$id,'status'=>1])->orderBy('append DESC');
        $data = $this->actionCheckPage($models);
        $count = $models->count();
        $os = strtolower($request->post('os'));
        $host = Yii::$app->request->hostInfo;
        foreach ($data as $key =>$v) {
           // $data[$key]['url'] = $os == 'web' ? '/notice/'.$v['id'] : ($host . '/notice/'.$v['id']);
            $data[$key]['article_url'] = $host.'/article/index?id='.$v['id'] ;
            $data[$key]['url'] = $v['link'] ;
            $data[$key]['cover'] = $host.$v['cover'] ;
        }
        if ($data) {
            $ret = ['code' => 200, 'count' => $count,'data' => $data, 'message' => 'success'];
            $this->do_aes(json_encode($ret));
        }else{
            $this->error_message('_No_Data_Query_');
        }
    }

    public function actionWiki(){
        $request = Yii::$app->request;
        $id = $request->post('id');
        $this->check_empty($id,'分类ID不能为空');
        $models = Article::find()->select('id,title,append,cover')->where(['cate_id'=>$id,'status'=>1])->orderBy('id asc');
        $data = $this->actionCheckPage($models);
        $count = $models->count();
        foreach ($data as $key =>$v) {
            $data[$key]['url'] = '/wiki/'.$v['id'] ;
            $data[$key]['cover'] = ''.$v['cover'] ;
        }
        if ($data) {
            $ret = ['code' => 200, 'count' => $count,'data' => $data, 'message' => 'success'];
            $this->do_aes(json_encode($ret));
        }else{
            $this->error_message('_No_Data_Query_');
        }
    }

    // 分页代码
    private function actionCheckPage($models){
        $request = Yii::$app->request;
        $count = $models->count();
        $limit_begin = $request->post('limit_begin');
        $limit_num = $request->post('limit_num');
        $limit_begin = empty($limit_begin)?0:$limit_begin;
        $limit_num = empty($limit_num)?intval($count):intval($limit_num);
        $pages = new Pagination(['totalCount'=>$count,'pageSize'=>$limit_num]);
        // $pages->setPage($limit_begin-1);

        $data = $models->offset($limit_begin)->limit($pages->limit)->asArray()->all();
        return $data;
    }
}