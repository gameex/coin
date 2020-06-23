<?php
namespace backend\modules\member\controllers;

use Yii;
use yii\data\Pagination;
use yii\web\Session;
use yii\web\NotFoundHttpException;
use yii\helpers\Url;
use api\models\MemberWallet;
use common\models\member\Member;
use common\models\BalanceLog;
use common\models\MemberWealthOrder;
use common\models\MemberWealthPackage;
use common\models\MemberVerified;
use common\jinglan\Trade;
use common\jinglan\CreateWallet;
use jinglan\ves\VesRPC;


/**
 * 用户控制器
 *
 * Class MemberController
 * @package backend\modules\member\controllers
 */
class WealthController extends UController{
    public $VERIFIED_STATUS = [
        0 => '未认证',
        1 => '已认证'
    ];
    public $STATUS_COLOR = [
        0 => '#aaa',
        1 => '#1ab394',
    ];

    // 交易类型
    protected $transaction_type = [
        0  => '<i class="fa fa-warning" style="color:#FFC157" title="未定义"></i>',//未定义
        1  => '<span style="color:#28A745"><i class="fa fa-plus-circle fa-fw"></i>充值</span>',
        10 => '<span style="color:#DC3545"><i class="fa fa-minus-circle fa-fw"></i>转出</span>',
    ];
    // 交易描述
    protected $detial_type = [
        'default'      => '<i class="fa fa-warning" style="color:#FFC157" title="未定义"></i>',//未定义
        'exchange'     => '交易所',
        'chain'        => '链上',
        'system'       => '系统',
        'withdraw_fee' => '提现手续费',
        'otc'          => '法币交易',
        'withdraw'     => '转出申请',
    ];
    /**
     * 首页
     */
    public function actionIndex()
    {
        $data = (new \yii\db\Query())->from('jl_member_wealth_package')->where(['status' => 1])->all();
        return $this->render('index',[
            'data'  => $data,
        ]);
    }

    public function actionWealthlock()
    {
        $data = (new \yii\db\Query())->from('jl_balance_log')->where(['type' => [2,3]])->all();
        return $this->render('index',[
            'data'  => $data,
        ]);
    }

    /**
     * 编辑/新增
     *
     * @return string|yii\web\Response
     * @throws yii\base\Exception
     */
    public function actionEdit()
    {
        $request  = Yii::$app->request;
        $id       = $request->get('id');
        $model    = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()))
        {
            // 提交创建
            if($model->save())
            {
                return $this->redirect(['index']);
            }
        }

        return $this->render('edit', [
            'model' => $model,
        ]);
    }

    /**
     * 删除
     *
     * @param $id
     * @return mixed
     * @throws \Exception
     * @throws \Throwable
     * @throws yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        if($model = $this->findModel($id))
        {
            $model->status = 0;
            $model->save();
            return $this->message("删除成功",$this->redirect(['index']));
        }
        else
        {
            return $this->message("删除失败",$this->redirect(['index']),'error');
        }
    }

    /**
     * 修改个人资料
     *
     * @return string|yii\web\Response
     */
    public function actionPersonal()
    {
        $request  = Yii::$app->request;
        $id       = $request->get('id');
        $model    = $this->findModel($id);

        // 提交表单
        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['index']);
        }

        return $this->render('personal', [
            'model' => $model,
        ]);
    }

    /**
     * 返回模型
     *
     * @param $id
     * @return Member|null|static
     */
    protected function findModel($id)
    {
        if (empty($id))
        {
            return new MemberWealthPackage;
        }

        if (empty($model = MemberWealthPackage::findOne($id)))
        {
            return new MemberWealthPackage;
        }

        return $model;
    }
    public function actionQuery($id)
    {    
        return json_encode(['code'=>200,'data'=>$balance_all]);
    }

    // 用户信息详情页
    public function actionUserDetail()
    {
        $request  = Yii::$app->request;
        $id       = $request->get('id');

        // 1用户基本信息
        $user = Member::find()->where(['id' => $id])->one();


        // 2用户资产信息
        $_POST['chain_network'] = 'main_network';
        // $_POST['chain_network'] = 'testnet';
        $_POST['return_way'] = 'array';
        $balance_all = Trade::balance_v2($id) ?: [];// 成功返回数据，失败返回false

        // 交易记录信息
        // 判断是否有搜索条件
        $key_type   = $request->get('key_type');
        $keyword    = $request->get('keyword');
        $query      = BalanceLog::find()->where(['member_id' => $id])->andWhere(['network' => 0])->orderBy('id DESC');
        // $query      = BalanceLog::find()->where(['member_id' => $id])->andWhere(['network' => 1]);
        if (!empty($key_type) && !empty($keyword)) {
            $query->andWhere([$key_type => $keyword]);
        }
        $count      = $query->count();
        $pagination = new Pagination(['totalCount' => $count]);
        $pagination->setPageSize(10);
        $balance_logs = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('user-detail', [
            'user'             => $user,
            // 'wallets'          => $wallets,
            'balance_logs'     => $balance_logs,
            'balance_all'      => $balance_all[0],
            'transaction_type' => $this->transaction_type,
            'detial_type'      => $this->detial_type,
            'pages'            => $pagination,
        ]);
    }

    // 用户信息详情页
    public function actionAguserDetail()
    {
        $request  = Yii::$app->request;
        $id       = $request->get('id');

        // 1用户基本信息
        $user = Member::find()->where(['id' => $id])->one();


        // 2用户资产信息
        $_POST['chain_network'] = 'main_network';
        // $_POST['chain_network'] = 'testnet';
        $_POST['return_way'] = 'array';
        $balance_all = Trade::balance_v2($id) ?: [];// 成功返回数据，失败返回false

        // 交易记录信息
        // 判断是否有搜索条件
        $key_type   = $request->get('key_type');
        $keyword    = $request->get('keyword');
        $query      = BalanceLog::find()->where(['member_id' => $id])->andWhere(['network' => 0])->orderBy('id DESC');
        // $query      = BalanceLog::find()->where(['member_id' => $id])->andWhere(['network' => 1]);
        if (!empty($key_type) && !empty($keyword)) {
            $query->andWhere([$key_type => $keyword]);
        }
        $count      = $query->count();
        $pagination = new Pagination(['totalCount' => $count]);
        $pagination->setPageSize(10);
        $balance_logs = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('aguser-detail', [
            'user'             => $user,
            // 'wallets'          => $wallets,
            'balance_logs'     => $balance_logs,
            'balance_all'      => $balance_all[0],
            'transaction_type' => $this->transaction_type,
            'detial_type'      => $this->detial_type,
            'pages'            => $pagination,
        ]);
    }




    // 后台手动更新账户币种资产
    public function actionUpdateBalance()
    {
        $request  = Yii::$app->request;

        // 获取参数信息
        $change_type = $request->post('change_type');
        $member_id   = $request->post('member_id');
        $coin        = $request->post('coin_name');
        $addr        = $request->post('addr');
        $value_dec   = $request->post('value');
      
        if (empty($addr)){
            //return $this->message("请先生成地址！",$this->redirect(['user-detail', 'id'=>$member_id]),'error');
        }


        if (empty($change_type) || empty($member_id) || empty($coin) || empty($value_dec)) {
            return $this->message("参数不完整！",$this->redirect(['user-detail', 'id'=>$member_id]),'error');
        }

        // 充值金额合法性判断
        //$message_text = intval($change_type) == 1 ? '充币' : '扣币';
        if (intval($value_dec) <= 0) {
            return $this->message("The quantity is illegal！",$this->redirect(['user-detail', 'id'=>$member_id]),'error');
        }

        // 读取balance_log数据表获取用户银行当前资产
        $balance = BalanceLog::find()
            ->select(['balance', 'network'])
            ->where(['member_id' => intval($member_id)])
            ->andWhere(['coin_symbol' => $coin])
            ->orderBy('ctime DESC')
            ->one();
        if ($balance) {
            // 用户该地址在balance_log表中有记录
            $user_balance = $balance->balance;
            $network      = $balance->network;
        }else{
            // 用户该地址无资金变动记录，查询member_wallet表获取该地址所属网络
            $member_wallet = MemberWallet::find()
                ->select(['network'])
                ->where(['uid' => intval($member_id)])
                ->andWhere(['coin_symbol' => '_'.$coin.'_'])
                ->one();
            if (!$member_wallet) {
                //return $this->message("查询不到该用户地址信息！",$this->redirect(['user-detail', 'id'=>$member_id]),'error');
                $user_balance = 0;
                $network      = 0;
            }else{
                $user_balance = 0;
                $network      = $member_wallet->network;
            }
        }
        if($change_type < 20){//加币  扣币
             $change = intval($change_type)==1 ? (float)$value_dec : -(float)$value_dec;
            // 数据库存储数据【balance_log表】
            $balance_log              = new BalanceLog();
            $balance_log->type        = intval($change_type);
            $balance_log->member_id   = intval($member_id);
            $balance_log->coin_symbol = $coin;
            $balance_log->addr        = $addr;
            $balance_log->change      = $change;
            $balance_log->balance     = (float)$user_balance + $change;
            $balance_log->fee         = 0;
            $balance_log->detial      = $member_id.'-'.time().'-'.$addr;
            $balance_log->detial_type = 'system';
            $balance_log->ctime       = time();
            $balance_log->network     = $network;

            if ($balance_log->save()) {
                return $this->message("更新用户资产成功！",$this->redirect(['user-detail', 'id'=>$member_id]),'success');
            }else{
                return $this->message("更新用户资产失败！",$this->redirect(['user-detail', 'id'=>$member_id]),'error');
            }           
        }else{//冻结 解冻
            $change = intval($change_type)==21 ? (float)$value_dec : -(float)$value_dec;

            $tablePrefix = Yii::$app->db->tablePrefix;

             Yii::$app->db->createCommand()->insert("{$tablePrefix}otc_merchants_freeze", [ 
            'uid' => intval($member_id),
            'amount' => $change ,
            'coin_symbol' => $coin,   
            'update_time' => time(),                                                
            ])->execute();
            $id = Yii::$app->db->getLastInsertID();   
            if ($id) {
                return $this->message("更新用户资产成功！",$this->redirect(['user-detail', 'id'=>$member_id]),'success');
            }else{
                return $this->message("更新用户资产失败！",$this->redirect(['user-detail', 'id'=>$member_id]),'error');
            }           
        }

    }

    // 生成地址
    public function actionGenerateAddress()
    {
        $request  = Yii::$app->request;
        $_POST['chain_network'] = 'main_network';
        // $_POST['chain_network'] = 'testnet';
        $_POST['coin_symbol'] = $request->post('coin_symbol');
        CreateWallet::create_v2($request->post('user_id'));
    }

    // 启用禁用用户
    public function actionChangeStatus()
    {
        // 获取参数
        $request = Yii::$app->request;
        $uid     = $request->get('uid')??'';
        $status  = $request->get('status')??'';
        if ($uid=='' || $status=='') {
            return $this->message("参数不完整！",$this->redirect(['index']),'error');
        }

        // 更新status字段
        $member = Member::find()
            ->where(['id'=>intval($uid)])
            ->andWhere(['status'=>intval($status)])
            ->one();
        if (empty($member)) {
            return $this->message("查询不到该用户信息！",$this->redirect(['index']),'error');
        }
        $member->status = intval($status) == 0 ? 10 : 0;
        if ($member->save()) {
            return $this->message("修改成功！",$this->redirect(['index']),'success');
        }else{
            return $this->message("修改失败！",$this->redirect(['index']),'error');
        }
    }

    public function actionUserFriend(){
        $request  = Yii::$app->request;
        $id       = $request->get('id');

        
        // 1用户基本信息
        $data = Member::find();
        // $data   = Member::find()->with(['verified']);

        $where[]='and';
        if (!empty($starttime)) {
            $where[]=array('>','jl_member.created_at',$starttime);
        }
        if (!empty($endtime)) {
            $where[]=array('<','jl_member.created_at',$endtime);
        }
        $where[]=array('=','jl_member.last_member',$id);
        $data->where($where);
        $count = $data->count();
        $pagination = new Pagination(['totalCount' => $count]);
        $models = $data->offset($pagination->offset)
           ->orderBy('type desc,created_at desc')
           ->limit($pagination->limit)
           ->all();
        return $this->render('user-friend',[
           'models'  => $models,
           'pagination'   => $pagination,
           'status'  => $this->VERIFIED_STATUS,
           'status_color' => $this->STATUS_COLOR,
        ]);
    }

    // 更新汇率字段
    public function actionChangeRate()
    {
        $request = Yii::$app->request;
        $id      = $request->post('id');
        $rate    = $request->post('rate');
        $type    = $request->post('type');

        if (empty($rate)) {
            $rate = 0;
        }

        $result = [
            'code'    => 500,
            'message' => '',
        ];

        $member = Member::find()->where(['id' => $id])->one();
        if ($type == 'proportion') {
            $member->proportion = floatval($rate);
        }else{
            $member->proportion = floatval($rate);
        }
        if ($member->save()) {
            $result['code'] = 200;
        }else{
            $result['message'] = $member->getErrors();
        }

        return json_encode($result);
    }

    // 推荐架构
    public function actionRecommend()
    {
        $request = Yii::$app->request;

        // 输入用户信息，查找该用户的下级树
        $pid      = $request->post('keyword');

        $user  =   Member::find();
        if (empty($pid)) {
            $pid = 1000463;
        }
        $k_where['id'] = $pid;
        $query=$user->where($k_where)->one();
        if (empty($query)){
            return $this->render('recommend',[
               'tree'  => '查询的用户不存在',
               'keyword'  => $pid,
            ]);
        }
        if($pid!='0')
        {
            $k_where['id'] = $pid;
            $k_where2['mobile_phone'] = $pid;
            $query=$user->where($k_where)->one()->toArray();

            $pid=$query['id'];
            $pid2=$query['last_member'];
        }
        $tree     =   $this->getTree($pid2,$pid,1);
        return $this->render('recommend',[
           'tree'  => $tree,
           'keyword'  => $pid,
        ]);
    }

    // 推荐架构
    public function actionMyrecommend()
    {
        // 输入用户信息，查找该用户的下级树
        $request = Yii::$app->request;
        $id      = $request->get('id');
        $user  =   Member::find();
        if (empty($pid)) {
            $pid = 1000463;
        }

        if($pid!='0')
        {
            $k_where['id'] = $pid;
            $k_where2['mobile_phone'] = $pid;
            $query=$user->where($k_where)->one()->toArray();
            $pid=$query['id'];
            $pid2=$query['last_member'];
        }
        $tree     =   $this->getTree($pid2,$pid,1);
        return $this->render('myrecommend',[
           'tree'  => $tree,
           'keyword'  => $pid,
        ]);
    }


    public  function getTree($pid='0',$uid=0,$ceng=0)
    {
        $t=Member::find();
        if ($ceng == 1) {
            $wherea['id']=$uid;
        }
        $wherea['last_member']=$pid;
        //$list=$t->where(array('pid'=>$pid,'sex'==0))->order('userid asc')->select();

        $list=$t->where($wherea)->orderBy('id asc')->asArray()->all();

        if(is_array($list)){
                $html = '';
                foreach($list as $k => $v)
                {
                    $map['last_member']=$v['id'];
                    $count=$t->where($map)->count(1);
                    $class=$count==0 ? 'fa-user':'fa-sitemap';
                   if($v['last_member'] == $pid)
                   {  
                        if (empty($v['mobile_phone'])) {
                            $v['mobile_phone'] = '手机号未绑定';
                        }
                        if (empty($v['email'])) {
                            $v['email'] = '邮箱未绑定';
                        }
                        //父亲找到儿子了
                        $html .= '<li style="display:none" >';
                        $html .= '<span><i class="icon-sitemap '.$class.' blue "></i>';
                        $html .= $v['mobile_phone'];
                        $html .= '</span>';
                        $html .= $v['email'];
                        $html .= ' <a href="'.Url::to(['aguser-detail','id'=>$v['id']]).'" class="blue">查看持仓及交易记录</a>';
                        $html .= $this->getTree($v['id']);
                        $html = $html."</li>";
                   }
                }
            return $html ? '<ul>'.$html.'</ul>' : $html ;
        }
    }


    public function share(){
        // 输入用户信息，查找该用户的下级树
        $pid   =   input('keyword', '0', 'string');
        $user  =   Db::name('user');
        if (empty($pid)) {
            $pid = 8000142;
        }
        $this->assign('keyword',$pid);
        if($pid!='0')
        {
            $k_where['id|mobile'] = $pid;
            $query=$user->where($k_where)->find();
            $pid=$query['id'];
            $pid2=$query['last_member'];

            //推荐人数
            $zhitui_count = $user->where(['last_member' => $pid])->count();
            $all_count = $user->where('parent_tree','like','%'.$pid.'%')->count();

            //推荐业绩
            $zhitui_user = $user->where(['last_member' => $pid])->field('id')->select()->toarray();
            $zhitui_num = 0;
            foreach($zhitui_user as $k => $v){
                $Temp_yeji = Db::name('wealth_order')->where(['user_id' => $v['id']])->sum('amount');
                $zhitui_num = $zhitui_num + $Temp_yeji;
            }
            $all_user = $user->where('parent_tree','like','%'.$pid.'%')->field('id')->select()->toarray();
            $all_num = 0;
            foreach($all_user as $k => $v){
                $Temp_yeji = Db::name('wealth_order')->where(['user_id' => $v['id']])->sum('amount');
                $all_num = $all_num + $Temp_yeji;
            }
            
            $this->assign('zhitui_count',$zhitui_count);
            $this->assign('all_count',$all_count);
            $this->assign('zhitui_num',$zhitui_num);
            $this->assign('all_num',$all_num);
        }
        $tree     =   $this->getTree($pid2,$pid,1);
        $this->assign('tree',$tree);
        return $this->fetch();
    }

















}