<?php
namespace Home\Controller;
use Common\Controller\CommonController;
class BaseController extends CommonController {
/**
 * Home模块总控制器，直接继承CommonController
 */

    protected static $user_id		=	'';// 用户id
    protected static $nickname		=	'';// 用户昵称
    protected static $userdata		=	'';// 用户数据
    protected static $navigation        =	array();// 导航选中配置

    /**
     * Home模块初始化方法
     * @return [type] [description]
     */
    protected function _initialize(){
        parent::_initialize();
        
    	echo "Home/BaseController";
//        p($_SERVER);
//        die;
        /*
        session格式
        array(
            'LOGIN_FLAG'    => true,
            'USERDATA'      => mix,
            'LAST_OP_TIME'=> 1445151324
        );
        */

        // 判断是否存在session
        if (!(session('?LOGIN_FLAG') && session('LOGIN_FLAG'))) {
            // 未登录
            $this->redirect('Home/User/login', '', 3, '未登录！');
            return;
        }
        
        // 判断session是否过期
        if (NOW_TIME - session('LAST_OP_TIME') > self::SESSION_EXPIRE) {
            // 用户2次操作时间间隔已经超过session过期时间间隔
            session('LOGIN_FLAG', null);
            session('USERDATA', null);
            session('LAST_OP_TIME', null);

            $this->redirect('Home/User/login', '', 3, '登录过期！请重新登录！');

            return;
        }
        session('LAST_OP_TIME',NOW_TIME);// 未过期，更新最后操作时间
        
        // 通过检验，已登录
        // 进行全局静态变量赋值
        $userdata = session('USERDATA');
        self::$user_id = $userdata['user_id'];
        self::$nickname = '';
        self::$userdata = $userdata;
    }

    protected function getUserId()
    {
        if(self::$user_id){
            return self::$user_id;
        }else{
            $this->redirect('Home/User/login', '', 3, '未登录！');

            return;
        }
    }

    protected function checkAuthority($modelName, $id)
    {
        $userId = $this->getUserId();

        $result = M($modelName)->where([
            's_id'    => $id,
            'user_id' => $userId,
        ])->count();
        
        if(intval($result) === 0){
            return false;
        }else{
            return true;
        }
    }
}