<?php 
/*
* name: xiaocai
* date: 2018-8-27 12:00
*/
namespace api\controllers;

use Yii;
use api\models\Coin;
use jinglan\ethereum\EthereumRPC;
use api\models\MemberWallet;
use api\models\Transaction;
use jinglan\bitcoin\Unspent;
use jinglan\bitcoin\Balance;
use common\models\TransactionBtc;
use Denpa\Bitcoin\Client as BitcoinClient;
use Denpa\Bitcoin\Omnicore as OmnicoreClient;
use yii\data\Pagination;
use common\jinglan\WithdrawCash;

class BankoutController extends ApibaseController
{
    public $modelClass = '';

    // 银行充值最小金额限制
    protected $limit_amount = 0.0001;// ETH
    protected $limit_amount_btc = 0.0001;// BTC

    // 银行转出最低额度
    protected $limit_trun_out = 0.0001;

    public function init(){
        parent::init();
    }

    
    // 转出（提现）申请(银行=>KK钱包)
    public function actionTurnCard()
    {
        $request      = Yii::$app->request;
        $access_token = $request->post('access_token');
        $uinfo        = $this->checkToken($access_token);

        WithdrawCash::turnCard($uinfo['id']);
    }

}