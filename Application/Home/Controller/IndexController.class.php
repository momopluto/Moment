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
     * 个人信息统计数据，关注，分享，粉丝
     * 点赞表
     * 分享列表
	 * @return [type] [description]
	 */
    public function index(){
    	
    	echo "Home/Index/index";
    }
}