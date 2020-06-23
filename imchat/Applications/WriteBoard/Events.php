<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

use \GatewayWorker\Lib\Gateway;
use \Workerman\Connection\MysqlConnection;
/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{
    // 当客户端连接上来时，设置连接的onWebSocketConnect，即在websocket握手时的回调
    public static function onWebSocketConnect($client_id, $data)
    {
        var_dump('Event---onWebSocketConnect---');
        //var_dump($client_id);
        //var_export($data);
        // 可以在这里判断连接来源是否合法，不合法就关掉连接
        // $_SERVER['HTTP_ORIGIN']标识来自哪个站点的页面发起的websocket链接
        if($data['server']['HTTP_ORIGIN'] != 'http://kedou.workerman.net')
        {
            //Gateway::closeClient($client_id);
        }
        // onWebSocketConnect 里面$_GET $_SERVER是可用的
        //var_dump($_GET, $_SERVER);

        if (!isset($data['get']['app']) || !isset($data['get']['token'])) {
            Gateway::sendToClient($client_id, json_encode(['type'=>'error','code'=>501,'message'=>'need app and token']));
            Gateway::closeClient($client_id);
        }
        //处理socket验证，appkey token
        $app_key = strval($data['get']['app']);
        $user_token = strval($data['get']['token']);
        $user_info = [
          'app_key' => strval($data['get']['app']),
          'user_token' => strval($data['get']['token']),
          'merchant_uid' => intval($data['get']['merchant_uid']),
          'user_id' => intval($data['get']['uid']),
          'user_name' => strval($data['get']['nickname']),
          'user_portrait' => urldecode(strval($data['get']['portrait'])),
        ];
        //查询是否重复登录
        if(Gateway::isUidOnline($user_info['app_key'].'_'.$user_info['user_id'])){
            $online_client_id = Gateway::getClientIdByUid($user_info['app_key'].'_'.$user_info['user_id']);
            Gateway::sendToClient($online_client_id[0], json_encode(['type'=>'error','code'=>500,'message'=>'token connect from somewhere else']));
            Gateway::closeClient($online_client_id[0]);
        }
        $user_info['client_id'] = $client_id;
        var_export($user_info);
        Gateway::setSession($client_id, $user_info);
        $arr = array(
            'type' => 'connect',
            'code' => 200,
            'message' => 'success',
        );
        Gateway::sendToClient($client_id, json_encode($arr));
        //Bind
        Gateway::bindUid($client_id, $user_info['app_key'].'_'.$user_info['user_id']);
        //end
    }
    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     * 
     * @param int $client_id 连接id
     * 客户端连接方式：ws://127.0.0.1:58282?app=app123&token=kcgudZDVTzQA2FevpDCqG29glRfiVssYtMsWff0bWzA40nKs_0
     */
    public static function onConnect($client_id)
    {
        var_dump('Event---onConnect---');
        //var_export('Event---onConnect---getAllClientIdCount::'.Gateway::getAllClientIdCount());
        //var_dump($client_id);
    }
    
   /**
    * 当客户端发来消息时触发
    * @param int $client_id 连接id
    * @param mixed $message 具体消息
    */
   public static function onMessage($client_id, $message)
   {
       // 客户端传递的是json数据
       $message_data = json_decode($message, true);
       if(!$message_data)
       {
           Gateway::sendToClient($client_id, json_encode(['type'=>'error','code'=>500,'message'=>'message need to be json']));
           return ;
       }

       // 根据类型执行不同的业务
       switch($message_data['type']) {
           // 客户端回应服务端的心跳
           case 'ping':
               return;
           // 客户端登录
           case 'join':
               // 判断是否有房间号
               if (!isset($message_data['groupId']) || empty($message_data['groupId'])) {
                   Gateway::sendToClient($client_id, json_encode(['type'=>'error','code'=>500,'message'=>'groupId is required']));
                   return;
               }
               $uinfo = Gateway::getSession($client_id);
               // 把房间号放到session中
               $groupId = $uinfo['app_key'].'_'.$message_data['groupId'];
               $_SESSION['groupId'] = $groupId;
               $_SESSION['platform'] = $message_data['platform'];
               $uinfo['groupId'] = $groupId;

               // 获取房间内所有用户列表
               $clients_list = Gateway::getClientSessionsByGroup($groupId);
               $clients_list2 = array();
               if(!empty($clients_list)){
                   foreach ($clients_list as $key => $x){
                       array_push($clients_list2, ['from'=>$x['user_id'],'name'=>$x['user_name'],'portrait'=>$x['user_portrait']]);
                   }
               }

               $clients_list2[$client_id] = [ 'from'=>$uinfo['user_id'], 'name'=>$uinfo['user_name'], 'portrait'=>$uinfo['user_portrait']];
               // 转播给当前房间的所有客户端，xx进入聊天室
               $new_message = array('type'=>$message_data['type'],'platform'=>$message_data['platform'], 'from'=>$uinfo['user_id'], 'name'=>$uinfo['user_name'], 'portrait'=>$uinfo['user_portrait'],'message'=>'success','extras'=>$message_data['extras'], 'time'=>date('Y-m-d H:i:s'));
               Gateway::sendToGroup($groupId, json_encode($new_message));
               Gateway::joinGroup($client_id, $groupId);

               // 给当前用户发送用户列表
               $new_message['code'] = 200;
               $new_message['client_list'] = array_values($clients_list2);
               Gateway::sendToCurrentClient(json_encode($new_message));

               //写入数据库
               $new_message['platform'] = $message_data['platform'];
               $new_message['to'] = 'all';
               self::db($client_id, $new_message, $uinfo);
               return;
           case 'TxtMsg':
               //客户端群聊 message格式: {"type":"TxtMsg","platform":"ios/android/web","groupId":10000,"to":"all","message":"自定义消息内容","extras":"扩展消息"} ，添加到客户端，广播给聊天室所有客户端
               //客户端单聊 message格式: {"type":"TxtMsg","platform":"ios/android/web","groupId":10000,"to":"[123, 789]","message":"自定义消息内容","extras":"扩展消息"} ，添加到客户端，广播给聊天室所有客户端
               // 非法请求
               if(!isset($_SESSION['groupId']))
               {
                   Gateway::sendToClient($client_id, json_encode(['type'=>'error','code'=>500,'message'=>'need to join group first']));
                   return;
               }
               $uinfo = Gateway::getSession($client_id);var_export($uinfo);
               $groupId = $uinfo['groupId'];
               $from = $uinfo['user_id'];
               $name = $uinfo['user_name'];
               $portrait = $uinfo['user_portrait'];

               // 私聊
               if($message_data['to'] != 'all')
               {
                   if(!is_array($message_data['to'])){
                       Gateway::sendToClient($client_id, json_encode(['type'=>'error','code'=>500,'message'=>'receiver [to] need to be an array']));
                       return;
                   }
                   if(empty($message_data['to'])){
                       Gateway::sendToClient($client_id, json_encode(['type'=>'error','code'=>500,'message'=>'receiver [to] need to have value']));
                       return;
                   }
                   $to = array();
                   foreach ($message_data['to'] as $x){
                       array_push($to,$uinfo['app_key'].'_'.$x);
                   }
                   $new_message = array(
                       'type'=>$message_data['type'],
                       'platform'=>$message_data['platform'],
                       'from'=>$from,
                       'name'=>$name,
                       'portrait'=>$portrait,
                       'to'=>$message_data['to'],
                       'message'=>$message_data['message'],
                       'extras' => $message_data['extras'],
                       'time'=>date('Y-m-d H:i:s'),
                   );
                   Gateway::sendToUid($to, json_encode($new_message));
                   Gateway::sendToClient($client_id, json_encode(['type'=>$message_data['type'],'code'=>200,'message'=>'success']));
                   self::db($client_id, $new_message, $uinfo);
                   return;
               }

               $new_message = array(
                   'type'=>$message_data['type'],
                   'platform'=>$message_data['platform'],
                   'from'=>$from,
                   'name'=>$name,
                   'portrait'=>$portrait,
                   'to'=>'all',
                   'message'=>$message_data['message'],
                   'extras' => $message_data['extras'],
                   'time'=>date('Y-m-d H:i:s'),
               );
               Gateway::sendToGroup($groupId ,json_encode($new_message));
               Gateway::sendToClient($client_id, json_encode(['type'=>$message_data['type'],'code'=>200,'message'=>'success']));
               self::db($client_id, $new_message, $uinfo);
               return;
           case 'exit':
               if(!isset($_SESSION['groupId']))
               {
                   Gateway::sendToClient($client_id, json_encode(['type'=>'error','code'=>500,'message'=>'need to join group first']));
                   return;
               }
               $uinfo = Gateway::getSession($client_id);
               $groupId = $uinfo['groupId'];
               Gateway::leaveGroup($client_id, $groupId);

               $new_message = array('type'=>$message_data['type'],'platform'=>$message_data['platform'], 'from'=>$uinfo['user_id'], 'name'=>$uinfo['user_name'], 'portrait'=>$uinfo['user_portrait'],'message'=>'success','extras'=>$message_data['extras'], 'time'=>date('Y-m-d H:i:s'));
               Gateway::sendToGroup($groupId, json_encode($new_message));

               Gateway::sendToCurrentClient(json_encode(['type'=>$message_data['type'],'code'=>200,'message'=>'success']));

               //写入数据库
               $new_message['platform'] = $message_data['platform'];
               $new_message['to'] = 'all';
               self::db($client_id, $new_message, $uinfo);
               return;
       }
   }
   
   /**
    * 当用户断开连接时触发
    * @param int $client_id 连接id
    */
   public static function onClose($client_id)
   {
       $uinfo = $_SESSION;
       if(!isset($_SESSION['groupId']))
       {
           return;
       }
       $groupId = $uinfo['groupId'];

       $new_message = array('type'=>'close','platform'=>$uinfo['platform'], 'from'=>$uinfo['user_id'], 'name'=>$uinfo['user_name'], 'portrait'=>$uinfo['user_portrait'],'message'=>'success','extras'=>'', 'time'=>date('Y-m-d H:i:s'));
       Gateway::sendToGroup($groupId, json_encode($new_message));
   }

   /*
    * 消息写入数据库
    */
   public static function db($client_id, $data, $uinfo){
       list($t1, $t2) = explode(' ', microtime());
	   try{
		   $m = new MongoDB\Driver\Manager("mongodb://im_dba:im_dba@127.0.0.1:27017/im");
		   // 插入数据
		   $bulk = new MongoDB\Driver\BulkWrite;
		   $bulk->insert([
				'timestamp' => (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000),
			   'client_id' => $client_id,
			   'type' => $data['type'],
			   'merchant_uid' => $uinfo['merchant_uid'],
			   'app_key' => $uinfo['app_key'],
			   'platform' => isset($data['platform'])?$data['platform']:'',
			   'from' => $uinfo['user_id'],
			   'name' => $uinfo['user_name'],
			   'portrait' => $uinfo['user_portrait'],
			   'groupId' => str_replace($uinfo['app_key'].'_', '', $uinfo['groupId']),
			   'to' => isset($data['to'])?$data['to']:'',
			   'message' => isset($data['message'])?$data['message']:'',
			   'extras' => isset($data['extras'])?$data['extras']:'',
		   ]);
		   return $m->executeBulkWrite('im.message', $bulk);
	   }catch (Exception $e){
           var_dump($e);
       }
   }
}
