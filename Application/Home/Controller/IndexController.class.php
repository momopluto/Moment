<?php
namespace Home\Controller;
use Think\Controller;

/**
 * Home默认的主页控制器
 */
class IndexController extends BaseController {

	/**
	 * 首页
	 * 进行游客/注册用户判断后，再跳转
	 * @return [type] [description]
	 */
    public function index(){
    	
    	echo "Home/Index/index";
    }
}