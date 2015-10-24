<?php
namespace Home\Controller;

use Think\Controller;

/**
 * Home分享内容控制器
 */
class ContentController extends BaseController
{

    /**
     * 发布分享内容
     * @return [type] [description]
     */
    public function share()
    {
        $text = I('post.text', '', 'strip_tags');
        $imgs = I('post.imgs', '', 'strip_tags');
        $isPublic = I('post.is_public', '', 'strip_tags');
        $userId = self::$user_id;

        $result = D('content')->insertShare($userId, $text, $imgs, $isPublic);
        if(!$result){
            $ret = [
                'success' => false,
                'message' => '保存失败',
            ];
        }else{
            $ret = [
                'success' => true,
                'message' => '保存成功',
            ];
        }
        $this->ajaxReturn($ret);
    }

    /**
     * 删除分享
     * ps: 开启事务，同时删除点赞和评论
     * @return [type] [description]
     */
    public function delshare()
    {
        $dao = D('content');
        $modelName = 'content';
        $shareId = I('post.s_id', '', 'strip_tags');
        $authority = $this->checkAuthority($modelName, $shareId);
        if($authority){
            $dao->startTrans();
            // 删除分享
            $result = D('content')->delShare($shareId);
            // 删除评论
            $result1 = D('comment')->delShareComment($shareId);
            // 删除点赞
            $result2 = D('thumb')->delShareThumb($shareId);
            // 删除收藏
            $result3 = D('favshare')->delShareFavshare($shareId);

            if($result === false || $result1 === false || $result2 === false || $result3 === false){
                $dao->rollback();
                $ret = [
                    'success' => false,
                    'message' => "删除失败",
                ];
            }else{
                $dao->commit();
                $ret = [
                    'success' => true,
                    'message' => '删除成功',
                ];
            }
        }else{
            $ret = [
                'success' => false,
                'message' => "删除失败",
            ];
        }

        $this->ajaxReturn($ret);
    }

    /**
     * 评论/回复
     * @return [type] [description]
     */
    public function comment()
    {
        $shareId = I('post.s_id', '', 'strip_tags');
        $pid = I('post.pid', '', 'strip_tags');
        $userId = $this->getUserId();
        $content = I('post.content', '', 'strip_tags');

        $result = D('comment')->insertComment($shareId, $pid, $userId, $content);
        if($result === false){
            $ret = [
                'success' => false,
                'message' => '保存失败',
            ];
        }else{
            $ret = [
                'success' => true,
                'message' => '保存成功',
            ];
        }

        return $this->ajaxReturn($ret);
    }

    /**
     * 删除评论/回复
     * ps: 如果该评论有回复，同时删除其下的回复
     * @return [type] [description]
     * TODO 递归删除，效率奇低
     */
    public function delcomment()
    {
        $commentId = I('post.c_id', '', 'strip_tags');
        $modelName = 'comment';
        $authority = $this->checkAuthority($modelName, $commentId);

        if($authority){
            $dao = D('comment');
            $dao->startTrans();
            $result = $dao->delComment($commentId);
            $result1 = $dao->delParentComment($commentId);
            if($result === false || $result1 === false){
                $dao->rollback();
                $ret = [
                    'success' => false,
                    'message' => "删除失败",
                ];
            }else{

                $dao->commit();
                $ret = [
                    'success' => true,
                    'message' => '删除成功',
                ];
            }
        }else{
            $ret = [
                'success' => false,
                'message' => "删除失败",
            ];
        }
        $this->ajaxReturn($ret);
    }

    /**
     * 点赞
     * @return [type] [description]
     */
    public function thumb()
    {
        $shareId = I('post.s_id', '', 'strip_tags');
        $userId = $this->getUserId();
        $dao = D('thumb');
        $count = $dao->checkThumb($shareId, $userId);

        if(intval($count) === 0){
            $result = $dao->insertThumb($shareId, $userId);
            if($result === false){
                $ret = [
                    'success' => false,
                    'message' => '点赞失败',
                ];
            }else{
                $ret = [
                    'success' => true,
                    'message' => '点赞成功',
                ];
            }
        }else{
            $ret = [
                'success' => false,
                'message' => '已点赞',
            ];
        }

        $this->ajaxReturn($ret);
    }

    /**
     * 取消点赞
     * @return [type] [description]
     */
    public function cclthumb()
    {
        $shareId = I('post.s_id', '', 'strip_tags');
        $userId = I('post.user_id', '', 'strip_tags');

        $dao = D('thumb');
        $count = $dao->checkThumb($shareId, $userId);

        if(intval($count) === 0){
            $ret = [
                'success' => false,
                'message' => '权限验证不通过',
            ];
        }else{
            $result = D('thumb')->delThumb($shareId, $userId);
            if($result === false){
                $ret = [
                    'success' => false,
                    'message' => '删除失败',
                ];
            }else{
                $ret = [
                    'success' => true,
                    'message' => '删除成功',
                ];
            }
        }
        $this->ajaxReturn($ret);
    }
}