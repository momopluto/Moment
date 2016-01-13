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