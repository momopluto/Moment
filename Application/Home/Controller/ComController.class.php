<?php
namespace Home\Controller;

use Think\Controller;

/**
 * 通用控制器
 */
class ComController extends BaseController
{

    /**
     * 最热的[随机1页的10条]分享
     * 30分钟内最热的[随机1页的10条]分享内容一致
     * @return JSON
     */
    public function hotshare()
    {
        // 展示给游客最热的几条分享吧
        // 不然最新的分享相对来说变化较快
        // 而且最热的几条分享也能诱导用户注册

        $userId = session('?LOGIN_FLAG') ? self::$user_id : -100;// -100为游客标识

        // 独立1个页面展示  ->游客
        // GET请求
        $model = D('Content');
        $sql = $model->getHotShare_sql($userId);
        // echo $sql;die;
        $page_listRows = 10;// 默认可见10条
        $allCount = $model->getHotShare_count($userId);
        // echo $allCount;die;
        $totalPages = ceil($allCount / $page_listRows);

        if (S('return_hotShare')) {
            // echo "************";
            $list = S('return_hotShare');
        } else {
            $cur_page = mt_rand(1, $totalPages);// 随机展示1页
            $page_begin = ($cur_page - 1) * $page_listRows;
            $sql .= " limit {$page_begin},{$page_listRows}";
            // echo $sql;die;
            $list = $model->query($sql);
            S('return_hotShare', $list, 1800);// 缓存30分钟
        }

        $this->ajaxReturn($list);
    }

    /**
     * [AJAX]搜索分享
     * 在主页面展示
     * @param string key [GET]关键字
     * @return JSON
     */
    public function searchshare()
    {
        // 独立1个页面展示搜索结果
        // get请求
        // P($_SERVER);die;
        if (IS_AJAX) {
            $key = I('param.key');
            if ($key == '') {
                $this->redirect('Index/index', '', 3, '搜索关键字不能为空');
                return;
            }

            $model = D('Content');
            $sql = $model->getSearchShare_sql(self::$user_id, $key);
            // echo $sql;die;
            if (!$sql){
                $this->redirect('Index/index', '', 3, $model->getError());
                return;
            }

            $count      = $model->getSearchShare_count(self::$user_id, $key);// 查询满足要求的总记录数
            $Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
            // p($Page);
            $show       = $Page->show();// 分页显示输出
            // p($show);
            $sql .= ' limit '.$Page->firstRow.','.$Page->listRows;// 拼装分页语句
            $list       = $model->query($sql);

            $rData['result'] = $list;
            $totalPages = ceil($Page->totalRows / $Page->listRows);// 计算页数
            $rData['totalPages'] = $totalPages;
            $this->ajaxReturn($rData);
        }
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
        $sql = $model->getThumbReceiveShare_sql($userId);

        $count = $model->getThumbReceive_count($userId);// 查询满足要求的总记录数
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

    /**
     * 获取单条分享的评论。
     */
    public function getComment()
    {
        $sId = I('post.sid', '', 'strip_tags');
        $model = D('comment');
        $comments = $model->getCommentById($sId);

        $this->ajaxReturn($comments);
    }

}