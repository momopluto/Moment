<?php
namespace Home\Controller;

use Think\Controller;

/**
 * 通用控制器
 */
class ComController extends BaseController
{

    /**
     * 最新发布的[若干条]分享
     * ps: 限制游客可浏览的分享数目
     *       注册用户浏览需要分页
     * @return [type] [description]
     */
    public function newestshare()
    {
    }

    /**
     * 查找分享
     * @return [type] [description]
     */
    public function searchshare()
    {
        $q = I("param.wd", '', 'strip_tags');
        $page = intval(I('post.page', 1, 'strip_tags'));
        $page = $page ? $page : 1;
        $limit = intval(I('post.limit', 25, 'strip_tags'));
        $limit = $limit ? $limit : 25;
        $q = "%" . $q . "%";
        $data['data'] = D('content')->searchShare($q, $page, $limit);
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 点赞榜
     * @return [type] [description]
     */
    public function thumbuplist()
    {
        $page = intval(I('post.page', 1, 'strip_tags'));
        $page = $page ? $page : 1;
        $limit = intval(I('post.limit', 25, 'strip_tags'));
        $limit = $limit ? $limit : 25;


    }

}