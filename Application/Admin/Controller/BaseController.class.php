<?php
namespace Admin\Controller;

use Common\Controller\CommonController;

class BaseController extends CommonController
{
    /**
     * Admin模块总控制器，直接继承CommonController
     */

    protected static $mng_id = '';// 用户id
    protected static $nickname = '';// 用户昵称
    protected static $mngdata = '';// 用户数据
    protected static $navigation = array();// 导航选中配置

    /**
     * Admin模块初始化方法
     * @return [type] [description]
     */
    protected function _initialize()
    {
        parent::_initialize();
        // p($_SERVER);
        // die;
        /*
        session格式
        array(
            'MANAGER_LOGIN_FLAG'    => true,
            'MANAGERDATA'      => mix,
            'MANAGER_LAST_OP_TIME'=> 1445151324
        );
        */

        // 判断是否存在session
        if(!(session('?MANAGER_LOGIN_FLAG') && session('MANAGER_LOGIN_FLAG'))){
            // 未登录
            $this->redirect('Admin/mng/signin', '', 3, '未登录！');

            return;
        }

        // 判断session是否过期
        if(NOW_TIME - session('MANAGER_LAST_OP_TIME') > self::SESSION_EXPIRE){
            // 用户2次操作时间间隔已经超过session过期时间间隔
            session('MANAGER_LOGIN_FLAG', null);
            session('MANAGERDATA', null);
            session('MANAGER_LAST_OP_TIME', null);

            $this->redirect('Admin/mng/signin', '', 3, '登录过期！请重新登录！');

            return;
        }
        session('MANAGER_LAST_OP_TIME', NOW_TIME);// 未过期，更新最后操作时间

        // 通过检验，已登录
        // 进行全局静态变量赋值
        $mngData = session('MANAGERDATA');
        self::$mng_id = $mngData['mng_id'];
        self::$nickname = '';
        self::$mngdata = $mngData;
    }
}