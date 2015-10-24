<?php
namespace Home\Controller;

use Think\Controller;

/**
 * Home个人中心控制器
 */
class MycenterController extends BaseController
{

    /**
     * 设置屏蔽/开启自己的分享
     * @return [type] [description]
     */
    public function sharetoggle()
    {
        $shareId = I('post.share_id', '', 'strip_tags');
        $userId = $this->getUserId();
        $dao = D('content');
        $dao->startTrans();

        $where = array(
            's_id'    => $shareId,
            'user_id' => $userId,
        );
        $share = $dao->getShare($where, [], 1, 1, true);
        if(!$share){
            $dao->rollback();
            $ret = [
                'success' => false,
                'message' => '修改失败',
            ];
        }else{
            $share = $share[0];
            $newStatus = $share['isPublic'] ? 0 : 1;
            $updateData = [
                'isPublic' => $newStatus,
            ];
            $result = $dao->editShare($where, $updateData);
            if($result === false){
                $dao->rollback();
                $ret = [
                    'success' => false,
                    'message' => '修改失败',
                ];
            }else{
                $dao->commit();
                $ret = [
                    'success' => true,
                    'message' => '修改成功',
                ];
            }
        }
        $this->ajaxReturn($ret);
    }

    /**
     * 自己分布的所有分享
     * @return [type] [description]
     */
    public function selfshare()
    {
        $page = I('param.page', 1, 'strip_tags');
        $page = intval($page) ? intval($page) : 1;
        $limit = I('param.limit', 25, 'strip_tags');
        $limit = intval($limit) ? intval($limit) : 25;
        $dao = D('content');
        $userId = $this->getUserId();
        $where = [
            'user_id' => $userId,
        ];

        $allcount = intval($dao->countShare($where));
        $data["data"] = $dao->getShare($where, [], $page, $limit, false);
        $data['allcount'] = $allcount;
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 自己点赞/评论过的分享(self Thumbed aNd Commented)
     * @return [type] [description]
     */
    public function selfThumb()
    {
        $page = intval(I('param.page', 1, 'strip_tags'));
        $page = $page ? $page : 1;
        $limit = intval(I('param.limit', 25, 'strip_tags'));
        $limit = $limit ? $limit : 25;

        $userId = $this->getUserId();
        $dao = D('thumb');

        $where = [
            'user_id' => $userId,
        ];
        $data['allcount'] = $dao->countThumb($where);
        $data['data'] = $dao->getSelfThumb($userId, $page, $limit);

        $this->display();
    }

    public function selfComment()
    {
        $page = intval(I('param.page', 1, 'strip_tags'));
        $page = $page ? $page : 1;
        $limit = intval(I('param.limit', 25, 'strip_tags'));
        $limit = $limit ? $limit : 25;

        $userId = $this->getUserId();
        $dao = D('comment');

        $where = [
            'user_id' => $userId,
        ];
        $data['allcount'] = $dao->countComment($where);
        $data['data'] = $dao->getSelfComment($userId, $page, $limit);

        $this->display();
    }

    /**
     * 登出
     * @access public
     * @return void
     */
    public function logout()
    {
        session('LOGIN_FLAG', null);
        session('USERDATA', null);
        session('SESSION_EXPIRE', null);

        $this->redirect('Home/User/login', '', 3, '安全退出！跳转至登录页面...');
    }

    /**
     * 修改密码
     * @access public
     * @return void
     */
    public function chpwd()
    {
        if(IS_POST){
            $cur_ask = 'Home/Mycenter/chpwd';
            $verify = new \Think\Verify();
            if($verify->check(I('post.verify'))){
                $this->redirect($cur_ask, '', 3, '验证码错误！');

                return;
            }

            $old_pwd = I('post.oldpassword');
            if($old_pwd == ''){
                $this->redirect($cur_ask, '', 3, '原密码不能为空！');

                return;
            }
            $new_pwd = I('post.password');
            $renew_pwd = I('post.repassword');
            if($new_pwd !== $renew_pwd){
                $this->redirect($cur_ask, '', 3, '两次输入的密码不一致！');

                return;
            }

            $model = D('User');
            if($model->chpwd(self::$user_id, $old_pwd, $new_pwd)){
                $this->redirect($cur_ask, '', 3, '修改密码成功');

                return;
            }else{
                $this->redirect($cur_ask, '', 3, $model->getError());

                return;
            }
        }else{
            $this->display();
        }
    }

}
