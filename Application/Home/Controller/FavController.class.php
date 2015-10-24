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
        $userId = I('post.user_id', '', 'strip_tags');
        $ownerId = $this->getUserId();
        $dao = D('favuser');
        $count = $dao->checkExist($ownerId, $userId);

        if(intval($count) === 0){
            $result = $dao->insertFavuser($ownerId, $userId);
            if($result === false){
                $ret = [
                    'success' => false,
                    'message' => '收藏失败',
                ];
            }else{
                $ret = [
                    'success' => true,
                    'message' => '收藏成功',
                ];
            }
        }else{
            $ret = [
                'success' => false,
                'message' => '收藏失败',
            ];
        }
        $this->ajaxReturn($ret);
    }

    /**
     * 取消收藏用户
     * @return [type] [description]
     */
    public function ccluser()
    {
        $userId = I('post.user_id', '', 'strip_tags');
        $ownerId = $this->getUserId();

        $dao = D('favuser');
        $count = $dao->checkExist($ownerId, $userId);
        if(intval($count) === 0){
            $ret = [
                'success' => false,
                'message' => '取消收藏失败',
            ];
        }else{
            $result = $dao->DelFavuser($ownerId, $userId);
            if($result === false){
                $ret = [
                    'success' => false,
                    'message' => '取消收藏失败',
                ];
            }else{
                $ret = [
                    'success' => true,
                    'message' => '取消收藏成功',
                ];
            }
        }
        $this->ajaxReturn($ret);
    }

    /**
     * 收藏分享
     * @return [type] [description]
     */
    public function content()
    {
        $ownerId = $this->getUserId();
        $shareId = I('post.s_id', '', 'strip_tags');

        $dao = D('favshare');
        $count = $dao->checkExist($ownerId, $shareId);
        if(intval($count) === 0){
            $result = $dao->insertFavshare($ownerId, $shareId);
            if($result === false){
                $ret = [
                    'success' => false,
                    'message' => '收藏失败',
                ];
            }else{
                $ret = [
                    'success' => true,
                    'message' => '收藏成功',
                ];
            }
        }else{
            $ret = [
                'success' => false,
                'message' => '收藏失败',
            ];
        }

        $this->ajaxReturn($ret);
    }

    /**
     * 取消收藏分享
     * @return [type] [description]
     */
    public function cclcontent()
    {
        $ownerId = $this->getUserId();

        $shareId = I('post.s_id', '', 'strip_tags');
        $dao = D('favshare');
        $count = $dao->checkExist($ownerId, $shareId);
        if(intval($count) === 0){
            $ret = [
                "success" => false,
                "message" => '取消收藏失败',
            ];
        }else{
            $result = $dao->delFavshare($ownerId, $shareId);
            if($result === false){
                $ret = [
                    'success' => false,
                    'message' => '取消收藏失败',
                ];
            }else{
                $ret = [
                    'success' => true,
                    'message' => '取消收藏成功',
                ];
            }
        }

        $this->ajaxReturn($ret);
    }
}