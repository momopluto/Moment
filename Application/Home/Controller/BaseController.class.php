<?php
namespace Home\Controller;

use Common\Controller\CommonController;

class BaseController extends CommonController
{
    /**
     * Home模块总控制器，直接继承CommonController
     */

    protected static $user_id = '';// 用户id
    protected static $nickname = '';// 用户昵称
    protected static $userdata = '';// 用户数据
    protected static $navigation = array();// 导航选中配置

    /**
     * Home模块初始化方法
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
            'LOGIN_FLAG'    => true,
            'USERDATA'      => mix,
            'LAST_OP_TIME'=> 1445151324
        );
        */

        // 判断是否存在session
        if(!(session('?LOGIN_FLAG') && session('LOGIN_FLAG'))){
            // 未登录
            // 跳转至hotshare热门分享，该页面右上角有"登录/注册"入口
            $this->redirect('usr/signin', '', 3, '未登录！');
 
            return;
        }
 
        // 判断session是否过期
        if(NOW_TIME - session('LAST_OP_TIME') > self::SESSION_EXPIRE){
            // 用户2次操作时间间隔已经超过session过期时间间隔
            session('LOGIN_FLAG', null);
            session('USERDATA', null);
            session('LAST_OP_TIME', null);
 
            $this->redirect('usr/signin', '', 3, '登录过期！请重新登录！');
 
            return;
        }
        session('LAST_OP_TIME', NOW_TIME);// 未过期，更新最后操作时间

        // 通过检验，已登录
        // 进行全局静态变量赋值
        $userdata = session('USERDATA');
        self::$user_id = $userdata['user_id'];
        self::$nickname = '';
        self::$userdata = $userdata;
    }

    /**
     * 获取home页面公共的数据
     * 1、关注.粉丝.分享 总数
     * 2、相册的前几张图片
     * @param integer $userId 用户id
     */
    protected function get_home_public_data($userId)
    {
        // 获取(userId的) 关注.粉丝.分享 总数
        $countData = getUser_FocusFanShare_Count($userId, $userId == self::$user_id);
        $this->assign('count', $countData);
        //        echo '$count = <br/>';
        //        p($countData);
        // 获取相册的前几张图片
        $pics = [];
        $result = D('Content')->getPic($userId, $userId == self::$user_id);
        if($result){
            $picPath = PATH_IMG . "/";
            $this->assign('picPath', $picPath);// 访问url，后面直接拼图片名即可访问

            $pics = $result;
        }
        $this->assign('userPath', md5($userId));
        $this->assign('pics', $pics);
       // echo '$picPath = <br/>';
       // echo $picPath;
       // echo '$pics = <br/>';
       // p($pics);die;
    }

    /**
     * 获取index页面公共的数据
     * 1、关注.粉丝.分享 总数
     * 2、点赞榜
     * @param integer $userId
     */
    protected function get_index_public_data($userId)
    {
        // 获取(userId的) 关注.粉丝.分享 总数
        $countData = getUser_FocusFanShare_Count($userId, $userId == self::$user_id);
        $this->assign('count', $countData);
        //        echo '$count = <br/>';
        //        p($countData);

        // 获取点赞榜
        $thumblist = D('Thumb')->get_thumbuplist(NOW_TIME, 5);
        $this->assign('thumblist', $thumblist);
        //        echo '$thumblist = <br/>';
        //        p($thumblist);

        $picPath = PATH_IMG . "/";
        $this->assign('picPath', $picPath);// 访问url，后面md5($userId)为文件夹名后，再拼图片名即可访问
        //        echo '$picPath = <br/>';
        //        echo $picPath;
    }

    protected function getUserId()
    {
        if(self::$user_id){
            return self::$user_id;
        }else{
            $this->redirect('usr/signin', '', 3, '未登录！');

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