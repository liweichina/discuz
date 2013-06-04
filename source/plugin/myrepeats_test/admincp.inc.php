<?php

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

/* 语言包文件已经引入, 这里直接读取语言包,赋值给变量 $Plang。 */
$Plang = $scriptlang['myrepeats'];

/* 锁定、删除处理流程 */
if($_GET['op'] == 'lock') {
	/* 插件数据库表对象方法的调用和使用形式。 */
	$myrepeat = C::t('#myrepeats#myrepeats')->fetch_all_by_uid_username($_GET['uid'], $_GET['us	ername']);
	$lock = $myrepeat['lock'];
	$locknew = $lock ? 0 : 1;
	C::t('#myrepeats#myrepeats')->update_locked_by_uid_username($_GET['uid'], $_GET['username']	, $locknew);
	ajaxshowheader();
	echo $lock ? $Plang['normal'] : $Plang['lock'];
	ajaxshowfooter();
} elseif($_GET['op'] == 'delete') {
	C::t('#myrepeats#myrepeats')->delete_by_uid_usernames($_GET['uid'], $_GET['username']);
	ajaxshowheader();
	echo $Plang['deleted'];
	ajaxshowfooter();
}

$ppp = 100;
$resultempty = FALSE;
$srchadd = $searchtext = $extra = $srchuid = '';
$page = max(1, intval($_GET['page']));
if(!empty($_GET['srchuid'])) {
	$srchuid = intval($_GET['srchuid']);
	$srchadd = "AND uid='$srchuid'";
} elseif(!empty($_GET['srchusername'])) {
	$srchuid = C::t('common_member')->fetch_uid_by_username($_GET['srchusername']);
	if($srchuid) {
		$srchadd = "AND uid='$srchuid'";
	} else {
		$resultempty = TRUE;
	}
} elseif(!empty($_GET['srchrepeat'])) {
	$extra = '&srchrepeat='.rawurlencode($_GET['srchrepeat']);
	$srchadd = "AND username='".addslashes($_GET['srchrepeat'])."'";
	$searchtext = $Plang['search'].' "'.$_GET['srchrepeat'].'" '.$Plang['repeats'].' ';
}

if($srchuid) {
	$extra = '&srchuid='.$srchuid;
	$member = getuserbyuid($srchuid);
	$searchtext = $Plang['search'].' "'.$member['username'].'" '.$Plang['repeatusers'].' ';
}

$statary = array(-1 => $Plang['status'], 0 => $Plang['normal'], 1 => $Plang['lock']);
$status = isset($_GET['status']) ? intval($_GET['status']) : -1;

if(isset($status) && $status >= 0) {
	$srchadd .= " AND locked='$status'";
	$searchtext .= $Plang['search'].$statary[$status].$Plang['statuss'];
}

if($searchtext) {
	$searchtext = '<a href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&id	entifier=myrepeats&pmod=admincp">'.$Plang['viewall'].'</a> '.$searchtext;
}

/* 加载用户组缓存信息。 */
loadcache('usergroups');

/* 这里输出表格头部和表单 html 到当前位置。Discuz! 后台输出 html 界面函数, 可在后台函数库文件source/function/function_admincp.php 中查看具体输出内容。*/
showtableheader();

/* 本页面的地址连接,其中 do = $pluginid 为当前插件标识id, 此id为自动生成的id, 在书写本页面地址时需要注意此参数。*/
showformheader('plugins&operation=config&do='.$pluginid.'&identifier=myrepeats&pmod=admincp', 'repeatsubmit');
showsubmit('repeatsubmit', $Plang['search'], $lang['username'].': <input name="srchusername" value="'.htmlspecialchars($_GET['srchusername']).'" class="txt" />  '.$Plang['repeat'].': <input name="srchrepeat" value="'.htmlspecialchars($_GET['srchrepeat']).'" class="txt" />', $searchtext);
showformfooter();

$statselect = '<select onchange="location.href=\''.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=myrepeats&pmod=admincp'.$extra.'&status=\' + this.value">';
foreach($statary as $k => $v) {
	$statselect .= '<option value="'.$k.'"'.($k == $status ? ' selected' : '').'>'.$v.'</option>';
}
$statselect .= '</select>';

/* 界面具体内容显示输出。*/
echo '<tr class="header"><th>'.$Plang['username'].'</th><th>'.$lang['usergroup'].'</th><th>'.$Plang['repeat'].'</th><th>'.$Plang['lastswitch'].'</th><th>'.$statselect.'</th><th></th></tr>';

if(!$resultempty) {
	$count = C::t('#myrepeats#myrepeats')->count_by_search($srchadd);
	$myrepeats = C::t('#myrepeats#myrepeats')->fetch_all_by_search($srchadd, ($page - 1) * $ppp	, $ppp);
	$uids = array();
	foreach($myrepeats as $myrepeat) {
		$uids[] = $myrepeat['uid'];
	}
	$users = C::t('common_member')->fetch_all($uids);
	$i = 0;
	foreach($myrepeats as $myrepeat) {
		$myrepeat['lastswitch'] = $myrepeat['lastswitch'] ? dgmdate($myrepeat['lastswitch']		) : '';
		$myrepeat['usernameenc'] = rawurlencode($myrepeat['username']);
		$opstr = !$myrepeat['locked'] ? $Plang['normal'] : $Plang['lock'];
		$i++;
		echo '<tr><td><a href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$plugin		id.'&identifier=myrepeats&pmod=admincp&srchuid='.$myrepeat['uid'].'">'.$users[$myre		peat['uid']]['username'].'</a></td>'.'<td>'.$_G['cache']['usergroups'][$users[$myre		peat['uid']]['groupid']]['grouptitle'].'</td>'.'<td><a href="'.ADMINSCRIPT.'?action		=plugins&operation=config&do='.$pluginid.'&identifier=myrepeats&pmod=admincp&srchre		peat='.rawurlencode($myrepeat['username']).'" title="'.htmlspecialchars($myrepeat['		comment']).'">'.$myrepeat['username'].'</a>'.'</td>'.'<td>'.($myrepeat['lastswitch'		] ? $myrepeat['lastswitch'] : '').'</td>'.'<td><a id="d'.$i.'" onclick="ajaxget(thi		s.href, this.id, \'\');return false" href="'.ADMINSCRIPT.'?action=plugins&operation		=config&do='.$pluginid.'&identifier=myrepeats&pmod=admincp&uid='.$myrepeat['uid'].'		&username='.$myrepeat['usernameenc'].'&op=lock">'.$opstr.'</a></td>'.'<td><a id="p'		.$i.'" onclick="ajaxget(this.href, this.id, \'\');return false" href="'.ADMINSCRIPT		.'?action=plugins&operation=config&do='.$pluginid.'&identifier=myrepeats&pmod=admin		cp&uid='.$myrepeat['uid'].'&username='.$myrepeat['usernameenc'].'&op=delete">['.$la		ng['delete'].']</a></td></tr>';
	}
}
showtablefooter();

/* 分页输出 */
echo multi($count, $ppp, $page, ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=myrepeats&pmod=admincp$extra");

?>