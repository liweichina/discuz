<?php
/* 所有与插件有关的程序，包括全部的前后台程序，因全部使用外壳调用, 请务必在第一行加入以下三行代码, 以免其被 URL 直接请求调用，产生安全问题。 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

/* 全局嵌入点类（必须存在）*/
class plugin_myrepeats {
	var $value = array(); //初始化返回值变量。

	/* 嵌入点对象初始化函数, 属于php面向对象机制特性。这里的函数名和类名是一致的, 在初始化类的时候以便执行这	个函数,对$value进行赋值,以便下面的global_usernav_extra1()函数调用。*/
	function plugin_myrepeats() { 
		global $_G;
		if(!$_G['uid']) {
			return;
		}

		/* 读取可以使用马甲的用户组 usergroups 变量值。需要注意参数的读取方式,详情见插件手册-参数读取		。 */
		$myrepeatsusergroups = (array)dunserialize($_G['cache']['plugin']['myrepeats']['use		rgroups']);
		if(in_array('', $myrepeatsusergroups)) {
			$myrepeatsusergroups = array();
		}
		$userlist = array();

		/* 对当前登录用户进行马甲验证, 即当前用户组不再权限许可范围内, 但其他帐号所在用户组有权限, 则当		前用户也有使用权限。*/
		if(!in_array($_G['groupid'], $myrepeatsusergroups)) {
			if(!isset($_G['cookie']['myrepeat_rr'])) {

				/* 这里需要注意一下你所建的数据表对象的构建, 即 source/plugin/myrepeats/t				able/下的 table_新建表名.php */
				$users = count(C::t('#myrepeats#myrepeats')->fetch_all_by_username(				$_G['username']));
				dsetcookie('myrepeat_rr', 'R'.$users, 86400);
			} else {
				$users = substr($_G['cookie']['myrepeat_rr'], 1);
			}
			if(!$users) {
				return '';
			}
		}

		/* 前台显示代码 */
		$this->value['global_usernav_extra1'] = '<script>'.
		'function showmyrepeats() {if(!$(\'myrepeats_menu\')) {'.
		'menu=document.createElement(\'div\');menu.id=\'myrepeats_menu\';menu.style			.display=\'none\';menu.className=\'p_pop\';'.
		'$(\'append_parent\').appendChild(menu);'.
		'ajaxget(\'plugin.php?id=myrepeats:switch&list=yes\',\'myrepeats_menu\',\'a			jaxwaitid\');}'.
		'showMenu({\'ctrlid\':\'myrepeats\',\'duration\':2});}'.
		'</script>'.
		/* 此处是对个人前台设置管理马甲程序模块的连接,需要注意下格式是固定的。 */
		'<span class="pipe">|</span><a id="myrepeats" href="home.php?mod=spacecp&ac=plugin&		id=myrepeats:memcp" class="showmenu cur1" onmouseover="delayShow(this, showmyrepeat		s)">'.lang('plugin/myrepeats', 'switch').'</a>'."\n";
	}
	/* 这里使用了嵌入点函数 global_usernav_extra1() 返回到它对应输的显示位置, 所有嵌入点函数及对应位置见	手册。 */
	function global_usernav_extra1() {
		return $this->value['global_usernav_extra1'];
	}

}
?>