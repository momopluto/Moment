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

        $model = D('Comment');
        $sql = $model->getCommentReceiveShare_sql($userId);

        $count = $model->getCommentReceive_count($userId);// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
        $show = $Page->show();// 分页显示输出
        $sql .= ' limit ' . $Page->firstRow . ',' . $Page->listRows;// 拼装分页语句
        $list = $model->query($sql);

        if(IS_AJAX){
            // AJAX请求时，则只返回分享内容的数组
            $rData = json_decode($list);
            $this->ajaxReturn($rData);

            return;
        }

        // 获取(userId的) 关注.粉丝.分享 总数
        $countData = getUser_FocusFanShare_Count($userId, $userId == self::$user_id);
        // 点赞榜
        $thumbList = D('Thumb')->get_thumbuplist(time());

        $this->assign('count', $countData);
        $this->assign('thumb_list', $thumbList);

        // 获取相册的前几张图片
        $this->assign('list', json_encode($list));// 赋值数据集
        $this->assign('page', $show);// 赋值分页输出，可考虑同上json返回
        $this->display('comment_receive'); // 输出模板
    }

    /**
     * 发出的评论
     */
    public function commentSend()
    {
        $userId = self::$user_id;
        $model = D('Comment');
        $sql = $model->getCommentSendShare_sql($userId, $userId == self::$user_id);

        $count = $model->getCommentSend_count($userId, $userId == self::$user_id);// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
        $show = $Page->show();// 分页显示输出
        $sql .= ' limit ' . $Page->firstRow . ',' . $Page->listRows;// 拼装分页语句
        $list = $model->query($sql);

        if(IS_AJAX){
            // AJAX请求时，则只返回分享内容的数组
            $rData = json_decode($list);
            $this->ajaxReturn($rData);

            return;
        }

        // 获取(userId的) 关注.粉丝.分享 总数
        $countData = getUser_FocusFanShare_Count($userId, $userId == self::$user_id);
        // 点赞榜
        $thumbList = D('Thumb')->get_thumbuplist(time());

        $this->assign('count', $countData);
        $this->assign('thumb_list', $thumbList);
        // 获取相册的前几张图片
        $this->assign('list', json_encode($list));// 赋值数据集
        $this->assign('page', $show);// 赋值分页输出，可考虑同上json返回
        $this->display('comment_send'); // 输出模板
    }

    /**
     * 收到的点赞
     */
    public function thumbReceive()
    {
        $userId = self::$user_id;
        $model = D('Thumb');
        $sql = $model->getThumbReceive_count($userId);

        $count = $model->getThumbReceiveShare_sql($userId);// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
        $show = $Page->show();// 分页显示输出
        $sql .= ' limit ' . $Page->firstRow . ',' . $Page->listRows;// 拼装分页语句
        $list = $model->query($sql);

        if(IS_AJAX){
            // AJAX请求时，则只返回分享内容的数组
            $rData = json_decode($list);
            $this->ajaxReturn($rData);

            return;
        }

        // 获取(userId的) 关注.粉丝.分享 总数
        $countData = getUser_FocusFanShare_Count($userId, $userId == self::$user_id);
        // 点赞榜
        $thumbList = D('Thumb')->get_thumbuplist(time());
        $this->assign('count', $countData);
        $this->assign('thumb_list', $thumbList);
        // 获取相册的前几张图片
        $this->assign('list', json_encode($list));// 赋值数据集
        $this->assign('page', $show);// 赋值分页输出，可考虑同上json返回
        $this->display('thumb_receive'); // 输出模板
    }

    /**
     * 发出的点赞
     */
    public function thumbSend()
    {
        $userId = self::$user_id;
        $model = D('Thumb');
        $sql = $model->getThumbSendShare_sql($userId);

        $count = $model->getThumbSend_count($userId);// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
        $show = $Page->show();// 分页显示输出
        $sql .= ' limit ' . $Page->firstRow . ',' . $Page->listRows;// 拼装分页语句
        $list = $model->query($sql);

        if(IS_AJAX){
            // AJAX请求时，则只返回分享内容的数组
            $rData = json_decode($list);
            $this->ajaxReturn($rData);

            return;
        }

        // 获取(userId的) 关注.粉丝.分享 总数
        $countData = getUser_FocusFanShare_Count($userId, $userId == self::$user_id);
        // 点赞榜
        $thumbList = D('Thumb')->get_thumbuplist(time());
        $this->assign('count', $countData);
        $this->assign('thumb_list', $thumbList);
        // 获取相册的前几张图片
        $this->assign('list', json_encode($list));// 赋值数据集
        $this->assign('page', $show);// 赋值分页输出，可考虑同上json返回
        $this->display('thumb_send'); // 输出模板
    }

}