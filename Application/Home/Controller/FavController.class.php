<?php
namespace Home\Controller;

use Think\Controller;

/**
 * Home收藏控制器
 */
class FavController extends BaseController
{

    /**
     * 收藏用户
     * @return [type] [description]
     */
    public function user()
    {
        // AJAX POST
        // 接受参数{"uid":"被收藏用户的id"}
        // 成功返回true
        // TODO，失败返回错误信息数组[格式待定]
        $userId = I('post.user_id', '', 'strip_tags');
        $ownerId = self::$user_id;
        $model = D('favuser');

        $result = $model->insertFavuser($ownerId, $userId);
        $this->ajaxReturn($model->getError());
    }

    /**
     * 取消收藏用户
     * @return [type] [description]
     */
    public function ccluser()
    {
        // AJAX POST
        // 接受参数{"uid":"被收藏用户的id"}
        // 成功返回true
        // TODO，失败返回错误信息数组[格式待定]
        $userId = I('post.user_id', '', 'strip_tags');
        $ownerId = self::$user_id;

        $model = D('favuser');
        $result = $model->delFavuser($ownerId, $userId);

        $this->ajaxReturn($model->getError());
    }

    /**
     * 收藏分享
     * @return [type] [description]
     */
    public function content()
    {
        // AJAX POST
        // 接受参数{"sid":"被收藏评论的id"}
        // 成功返回true
        // TODO，失败返回错误信息数组[格式待定]
        $ownerId = self::$user_id;
        $shareId = I('post.sid', '', 'strip_tags');

        $model = D('favshare');
        $result = $model->insertFavshare($ownerId, $shareId);

        $this->ajaxReturn($model->getError());
    }

    /**
     * 取消收藏分享
     * @return [type] [description]
     */
    public function cclcontent()
    {
        // AJAX POST
        // 接受参数{"sid":"被收藏评论的id"}
        // 成功返回true
        // TODO，失败返回错误信息数组[格式待定]

        $ownerId = self::$user_id;
        $shareId = I('post.sid', '', 'strip_tags');

        $model = D('favshare');
        $result = $model->delFavshare($ownerId, $shareId);

        $this->ajaxReturn($model->getError());
    }
}