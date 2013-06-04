<?php
/*
 *$scriptlang数组中存储脚本文件的语言包，
 $templatelang 数组中存储模版文件的语言包，
 $installlang 数组中存储安装、升级、卸载脚本用的语言包。
 */
$scriptlang['myrepeats'] = array(
	'login_strike' => "密码错误次数过多，请重新设置马甲账号信息并在 15 分钟后再尝试切换。",

	/* 含有变量值的语言包一般用在脚本文件中调用, 其中变量可以在showmessage(), lang()等函数中某个参数以数组	键值对的形式指定替换值。*/
	//例如：showmessage('myrepeats:adduser_succeed', 'home.php?mod=spacecp&ac=plugin&id=myrepeats:	memcp', array('usernamenew' => stripslashes($usernamenew))); */
	'adduser_succeed' => "马甲账号 {usernamenew} 已成功添加。",
);


$templatelang['myrepeats'] = array(
	'myrepeats' => "我的马甲",
	'adduser' => "添加马甲账号",
);

$installlang['myrepeats'] = array(
	
);

?>