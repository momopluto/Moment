<?php
namespace Home\Controller;
use Think\Controller;

/**
 * Home默认的主页控制器
 */
class IndexController extends BaseController {

    /**
     * 查看所有能查看到的分享
     * 登录用户本人
     * @return html页面/[AJAX] JSON
     */
    public function index(){
        // GET请求
        // 注册用户，当前页面Index/index
        // 游客，跳转到common/hotshare
        
// TODO，判断是游客还是注册用户

        $userId = self::$user_id;
// TODO, 测试测试测试
//         $userId = 541;

        $model = D('Content');
        $sql = $model->getAllCanSeeShare_sql($userId);

        $count = $model->getAllCanSeeShare_count($userId); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数(10)
        // p($Page);
        $show = $Page->show(); // 分页显示输出
        // p($show);
        $sql .= ' limit ' . $Page->firstRow . ',' . $Page->listRows; // 拼装分页语句
        $list = $model->query($sql);

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
        $this->assign('page', $show); // 赋值分页输出，可考虑同上json返回
        $this->display();
    }
}