<?php
namespace Home\Controller;

use Think\Controller;

/**
 * 通用控制器
 */
class ComController extends BaseController
{

    /**
     * 最热的[若干条]分享
     * ps: 限制游客可浏览的分享数目
     *       注册用户浏览需要分页
     * @return [type] [description]
     */
    public function hotshare()
    {
        // 展示给游客最热的几条分享吧
        // 不然最新的分享相对来说变化较快
        // 而且最热的几条分享也能诱导用户注册

        // 独立1个页面展示  ->游客
        // GET请求
    }

    /**
     * 查找分享
     * @return [type] [description]
     */
    public function searchshare()
    {
        // 独立1个页面展示搜索结果
        // GET请求

        // 限制搜索条件
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
        // 嵌入在主页中
        // GET请求 [AJAX]

        // TODO，这应该写成1个model方法，用于获取数据即可
    }

    /**
     * 个人主页
     */
    public function index()
    {
        // 某人的 id
        $userId = I('param.id', '');
    }

    /**
     * 个人相册
     */
    public function album()
    {
        $userId = I('param.id', '');
    }

    /**
     * 粉丝
     */
    public function fans()
    {
        $userId = I('param.id', '');
    }

    /**
     * 关注人
     */
    public function follow()
    {
        $userId = I('param.id', '');
    }

    /**
     * 收藏列表
     */
    public function collection()
    {
        $userId = self::$user_id;
    }

    /**
     * 收到的评论
     */
    public function commentReceive()
    {
        $userId = self::$user_id;
    }

    /**
     * 发出的评论
     */
    public function commentSend()
    {
        $userId = self::$user_id;
    }

    /**
     * 收到的点赞
     */
    public function thumbReceive()
    {
        $userId = self::$user_id;
    }

    /**
     * 发出的点赞
     */
    public function thumbSend()
    {
        $userId = self::$user_id;
    }

}