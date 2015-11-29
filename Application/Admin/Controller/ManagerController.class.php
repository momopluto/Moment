<?php
namespace Admin\Controller;

use Think\Controller;

/**
 * 管理员身份控制器
 */
class ManagerController extends Controller
{

    /**
     * 登录
     * @return [type] [description]
     */
    public function login()
    {
        if(session('?MANAGER_LOGIN_FLAG') && session('MANAGER_LOGIN_FLAG')){
            // 已登录
            $this->redirect('/Admin/User/lists', '', 3, '您已登录！跳转至主页...');

            return;
        }

        if(IS_POST){
            $login = '/Admin/mng/signin';
            $verify = new \Think\Verify();
            if(!$verify->check(I('post.verify'))){
                $this->redirect($login, '', 3, '验证码错误！');

                return;
            }

            $username = I('post.username');
            $password = I('post.password');
            if($username == '' || $password == ''){
                $this->redirect($login, '', 3, '用户名和密码不能为空！');

                return;
            }

            $model = D('Manager');

            $result = $model->checkAuth($username, md5($password));
            if(!$result){
                session('MANAGER_LOGIN_FLAG', false);
                session('MANAGERDATA', null);
                session('LAST_OP_TIME', null);
                $this->redirect($login, '', 3, $model->getError()['err']);

                return;
            }else{
                session('MANAGER_LOGIN_FLAG', true);
                session('MANAGERDATA', $result);
                session('LAST_OP_TIME', NOW_TIME);

                // 更新last_login_ip和last_login_time
                $model->updateLoginData($result['mng_id']);

                $this->redirect('/Admin/usr/lists', '', 1, '登录成功！正在跳转...');

                return;
            }
        }else{
            $this->display();
        }
    }

    /**
     * 登出
     * @return [type] [description]
     */
    public function logout()
    {
        session('MANAGER_LOGIN_FLAG', false);
        session('MANAGERDATA', null);
        session('LAST_OP_TIME', null);
        $login = '/Admin/mng/signin';
        $this->redirect($login, '', 1, '退出成功！正在跳转...');
    }

    /**
     * 修改密码
     * @return [type] [description]
     */
    public function chpwd()
    {
        if(IS_POST){
            $username = I('post.username', '', 'strip_tags');
            $password = I('post.password', '', 'strip_tags');
            $newPassword = I('post.new_passowrd', '', 'strip_tags');
            $login = '/Admin/mng/signin';
            if($username == '' || $password == ''){
                $this->redirect($login, '', 3, '用户名和密码不能为空！');

                return;
            }
            $model = D('Manager');
            $result = $model->changePassword($username, md5($password), md5($newPassword));

            $this->ajaxReturn($model->getError());
        }else{
            $this->display();
        }
    }
}