<?php
namespace Home\Controller;
use Think\Controller;

/**
 * Home用户身份控制器
 */
class UserController extends Controller {

    /**
         * 登录
         * @access public
         * @return void
         */
    public function login() {
        // 判断是否存在session
        if (session('?LOGIN_FLAG') && session('LOGIN_FLAG')) {
            // 已登录
            $this->redirect('Index/index', '', 3, '您已登录！跳转至主页...');
            return;
        }
        
        if (IS_POST){
            $login_ask = 'usr/signin';
            
            $verify = new \Think\Verify();
            if ($verify->check(I('post.verify'))){
                $this->redirect($login_ask, '', 3, '验证码错误！');
                return;
            }
//            $username = 'testuser_1';
//            $password = md5('testuser_1');
            $username = I('post.username');
            $password = I('post.password');
            if ($username == '' || $password == ''){
                $this->redirect($login_ask, '', 3, '用户名和密码不能为空！');
                return;
            }
            
            $model = D('User');
            $rst = $model->checkAuth($username,$password);// 验证账号密码
            
            if ($rst){
                // 写session
                session('LOGIN_FLAG', true);
                session('USERDATA', $rst);
                session('LAST_OP_TIME', NOW_TIME);
                
                // 更新last_login_ip和last_login_time
                $model->updateLoginData($rst['user_id']);
                
                $this->redirect('Index/index', '', 1, '登录成功！正在跳转...');
                return;
            }  else {
                session('LOGIN_FLAG', null);
                session('USERDATA', null);
                session('LAST_OP_TIME', null);
                
                $this->redirect($login_ask, '', 3, $model->getError());
                return;
            }
        }else{
//            p(get_defined_constants(true));
//            die;
            $this->display();
        }
    }

    /**
         * 注册
         * @return [type] [description]
         */
    public function signup() {
        if (IS_POST){
            $login_ask = 'usr/signin';
            $signup_ask = 'usr/signup';
            
            $verify = new \Think\Verify();
            if ($verify->check(I('post.verify'))) {
                $this->redirect($signup_ask, '', 3, '验证码错误！');
                return;
            }
            $username = I('post.username');
            $password = I('post.password');
            $repassword = I('post.repassword');
            if ($username == '' || $password == '') {
                $this->redirect($signup_ask, '', 3, '用户名和密码不能为空！');
                return;
            }
            if ($password !== $repassword){
                $this->redirect($signup_ask, '', 3, '两次输入的密码不一致！');
                return;
            }

            $data['username'] = $username;
            $data['user_pwd'] = $password;
            $model = D('User');
            if ($model->create($data) && $model->add()) {
                $this->redirect($login_ask, '', 1, '注册成功！跳转至登录页面...');
                return;
            } else {
                $this->redirect($signup_ask, '', 3, $model->getError());
                return;
            }
            
        }  else {
            $this->display();
        }
    }
    
    /**
         * 获取验证码
         * @access public
         */
    public function verify() {

        $config = array(
            'fontSize'      => 15,      // 验证码字体大小
            'useNoise'      => false,   // 关闭验证码杂点
            'codeSet'       => '2345678abcdef', //验证码字符
            'length'        => 4,       // 验证码位数
        );
        $Verify = new \Think\Verify($config);
        $Verify->entry();
    }

}
