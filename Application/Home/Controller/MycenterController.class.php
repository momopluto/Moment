<?php
namespace Home\Controller;
use Think\Controller;

/**
 * Home个人中心控制器
 */
class MycenterController extends BaseController
{

    /**
     * [废弃]设置屏蔽/开启自己的分享
     * @return [type] [description]
     */
    public function sharetoggle()
    {
        // AJAX POST
        // 接受参数{无}
        // 成功返回true
        // TODO，失败返回错误信息数组[格式待定]
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
        if(IS_POST){
        }
        $this->ajaxReturn($ret);
    }

    /**
     * 查看xx发布的分享
     * 在xx的分享页面
     * @param integer id [GET]用户id
     * @return html页面/[AJAX] JSON
     */
    public function selfshare() {
        // GET请求
        // 查看自己的分享，路由为 myct/share
        // 查看别人的分享，路由为 other/share

        $userId = I('param.id', self::$user_id);
// TODO, 测试测试测试
        // $userId = 541;

        $model = D('Content');
        $sql = $model->getOnesShare_sql($userId, $userId == self::$user_id);
        if (!$sql){
            $this->redirect('myct/share', '', 3, $model->getError());
            return;
        }

        $count      = $model->getOnesShare_count($userId, $userId == self::$user_id);// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
        // p($Page);
        $show       = $Page->show();// 分页显示输出
        // p($show);
        $sql .= ' limit '.$Page->firstRow.','.$Page->listRows;// 拼装分页语句
        $list       = $model->query($sql);

        if (IS_AJAX){
            // AJAX请求时，则只返回分享内容的数组
            $rData = json_decode($list);
            $this->ajaxReturn($rData);
            return;
        }

        // 获取home主页公共的数据
        $this->get_home_public_data($userId);
        
        $this->assign('list',json_encode($list));// 赋值数据集
        $totalPages = ceil($Page->totalRows / $Page->listRows);// 计算页数
        $this->assign('totalPages',$totalPages);
        $this->assign('page',$show);// 赋值分页输出，可考虑同上json返回
        // 分享内容json格式
        // p(json_encode($list));
        // echo '分页内容<br/>';
        // // p($show);
        // echo $totalPages.'<br/>';
        // die;
        $this->display('home'); // 输出模板
    }
    
    /**
     * 查看user关注的人
     * @param integer id [GET]用户id
     * @return html页面???
     */
    public function selffocus(){
        // GET请求
        // 查看自己关注的人，路由为 myct/focus
        // 查看别人关注的人，路由为 other/focus
        
        $userId = I('param.id', self::$user_id);
// TODO, 测试测试测试
//         $userId = 17765;
//         $userId = 7839;

        $model = D('Favuser');
        $sql = $model->getFavusers_sql($userId, $userId == self::$user_id);
        if (!$sql){
            $this->redirect('myct/focus', '', 3, $model->getError());
            return;
        }

        $count      = $model->getFavusers_count($userId, $userId == self::$user_id);// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
        // p($Page);
        $show       = $Page->show();// 分页显示输出
        // p($show);
        $sql .= ' limit '.$Page->firstRow.','.$Page->listRows;// 拼装分页语句
        $list       = $model->query($sql);
//        p($list);die;
        
        if (IS_AJAX){
            // AJAX请求时，则只返回分享内容的数组
            $rData = json_decode($list);
            $this->ajaxReturn($rData);
            return;
        }
        
        // 获取home主页公共的数据
        $this->get_home_public_data($userId);
        
        
//        $this->assign('data',$list);// 赋值数据集Array
        
        $this->assign('list',json_encode($list));// 赋值数据集String
        $totalPages = ceil($Page->totalRows / $Page->listRows);// 计算页数
        $this->assign('totalPages',$totalPages);
        
//        echo '$lists = <br/>';
//        p(json_encode($list));
//        echo '$totalPages = <br/>';
//        echo $totalPages;
//        die;
        
        $this->assign('page',$show);// 赋值分页输出，可考虑同上json返回
        $this->display('follow'); // 输出模板
    }
    
    /**
     * 查看user的粉丝
     * @param integer id [GET]用户id
     * @return html页面???
     */
    public function selffans(){
        // GET请求
        // 查看自己关注的人，路由为 myct/fans
        // 查看别人关注的人，路由为 other/fans
        
        $userId = I('param.id', self::$user_id);
// TODO, 测试测试测试
//         $userId = 17765;
//         $userId = 541;

        $model = D('Favuser');
        $sql = $model->getFans_sql($userId, $userId == self::$user_id);
        if (!$sql){
            $this->redirect('myct/fans', '', 3, $model->getError());
            return;
        }

        $count      = $model->getFans_count($userId, $userId == self::$user_id);// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
        // p($Page);
        $show       = $Page->show();// 分页显示输出
        // p($show);
        $sql .= ' limit '.$Page->firstRow.','.$Page->listRows;// 拼装分页语句
        $list       = $model->query($sql);
//        p($list);die;
        
        if (IS_AJAX){
            // AJAX请求时，则只返回分享内容的数组
            $rData = json_decode($list);
            $this->ajaxReturn($rData);
            return;
        }
        
        // 获取home主页公共的数据
        $this->get_home_public_data($userId);
        
        
        $this->assign('data',$list);// 赋值数据集Array
        
        $this->assign('list',json_encode($list));// 赋值数据集String
        $totalPages = ceil($Page->totalRows / $Page->listRows);// 计算页数
        $this->assign('totalPages',$totalPages);
        
//        echo '$lists = <br/>';
//        p(json_encode($list));
//        echo '$totalPages = <br/>';
//        echo $totalPages;
//        die;
        
        $this->assign('page',$show);// 赋值分页输出，可考虑同上json返回
        $this->display('fans'); // 输出模板
    }
    
    /**
     * 查看自己收藏的分享
     * 登录用户本人
     * @return html页面/[AJAX] JSON
     */
    public function selfcollect(){
        // GET请求

        $userId = self::$user_id;
// TODO, 测试测试测试
//        $userId = 50001;

        $model = D('Favshare');
        $sql = $model->getSelfFavshares_sql($userId);

        $count = $model->getSelfFavshares_count($userId); // 查询满足要求的总记录数
        $this->assign('collectCount',$count);
        $Page = new \Think\Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数(10)
        // p($Page);
        $show = $Page->show(); // 分页显示输出
        // p($show);
        $sql .= ' limit ' . $Page->firstRow . ',' . $Page->listRows; // 拼装分页语句
        $list = $model->query($sql);
//        p($list);die;
        if (IS_AJAX) {
            // AJAX请求时，则只返回分享内容的数组
            $rData = json_decode($list);
            $this->ajaxReturn($rData);
            return;
        }
        
        // 获取index页面公共的数据
        $this->get_index_public_data($userId);
 
        $this->assign('list', json_encode($list)); // 赋值数据集
        $totalPages = ceil($Page->totalRows / $Page->listRows); // 计算页数
        $this->assign('totalPages', $totalPages);
        
//        echo '$lists = <br/>';
//        p(json_encode($list));
//        echo '$totalPages = <br/>';
//        echo $totalPages;
//        die;
        
        $this->assign('page', $show); // 赋值分页输出，可考虑同上json返回
        $this->display('collect');
    }

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

        $this->redirect('User/login', '', 3, '安全退出！跳转至登录页面...');
    }

    /**
     * 修改密码
     * @access public
     * @return void
     */
    public function chpwd()
    {
        if(IS_POST){
            $cur_ask = 'Mycenter/chpwd';
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

    public function album()
    {
        if(IS_GET){
            $userId = I('param.id', '');
            $dao = D('Content');
            $result = $dao->getAlbum($userId, $userId == self::$user_id);
            if($result){
                $this->assign('pics', $dao->getError()['data']);
                $this->display();
            }
        }
    }

    public function pic()
    {
        if(IS_GET){
            $userId = I('param.id', '');
            $dao = D('Content');
            $result = $dao->getPic($userId, $userId == self::$user_id);
            if($result){
                $this->assign('pics', $dao->getError()['data']);
                $this->display();
            }
        }
    }

    public function  test()
    {
        echo '<br/>test -------------<br/>';
        p(get_defined_constants(true));
        // p(getUser_FocusFanShare_Count(541));

       // $model = D('Thumb');
//        $model->insertThumb(4170006, self::$user_id);
       // echo $model->getThumbReceiveShare_sql(self::$user_id);
    }
}
