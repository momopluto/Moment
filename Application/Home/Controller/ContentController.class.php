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
        // AJAX POST
        // 接受参数{"content":"文字内容","isPublic":"是否公开","imgcount":"图片张数"}
        // 如果有上传文件，在此处理
        // 成功返回true，前端再接着展示新增的分享
        // TODO，失败返回错误信息数组[格式待定]
        if(IS_AJAX && IS_POST){
            $text = I('post.text', '', 'strip_tags');
            $imgs = I('post.imgs', '', 'strip_tags');
            $isPublic = I('post.is_public', '', 'strip_tags');
            $userId = self::$user_id;

            $model = D('content');
            $result = $model->insertShare($userId, $text, $imgs, $isPublic);
            if($result === false){
                $this->dataReturn('100', $model->getError());
            }else{
                $this->dataReturn();
            }
        }else{
            $this->dataReturn('100', '非法请求');
        }
    }

    /**
     * 删除分享
     * ps: 开启事务，同时删除点赞和评论
     * @return [type] [description]
     */
    public function delshare()
    {
        // AJAX POST
        // 同时删除该分享下的评论和赞，前端要给出警告
        // 接受参数{"sid":"分享内容id"}
        // 成功返回true
        // TODO，失败返回错误信息数组[格式待定]
        $model = D('content');
        $shareId = I('post.s_id', '', 'strip_tags');

        $result = $model->delShare($shareId, self::$user_id);
        if($result === true){
            $this->dataReturn();
        }else{
            $this->dataReturn('100', $model->getError());
        }
    }

    /**
     * 评论/回复
     * @return [type] [description]
     */
    public function comment()
    {
        // AJAX POST
        // 接受参数{"sid":"分享内容id","content":"评论内容","pid":"如果是回复，则是所回复的评论的id值;如果是一级评论，则是0"}
        // 成功返回true
        // TODO，失败返回错误信息数组[格式待定]

        $shareId = I('post.s_id', '', 'strip_tags');
        $pid = I('post.pid', '', 'strip_tags');
        $userId = self::$user_id;
        $content = I('post.content', '', 'strip_tags');

        $model = D('comment');
        $result = $model->insertComment($shareId, $pid, $userId, $content);
        if($result === false){
            $this->dataReturn('100', $model->getError());
        }

        $this->dataReturn();
    }

    /**
     * 删除评论/回复
     * ps: 如果该评论有回复，同时删除其下的回复
     * @return [type] [description]
     */
    public function delcomment()
    {
        // AJAX POST
        // 删除该评论下的回复
        // 接受参数{"cid":"评论id"}
        // 成功返回true
        // TODO，失败返回错误信息数组[格式待定]
        $commentId = I('post.c_id', '', 'strip_tags');

        $model = D('comment');
        $result = $model->delComment($commentId, self::$user_id);

        if($result === false){
            $this->dataReturn('100', $model->getError());
        }

        $this->dataReturn();
    }

    /**
     * 点赞
     * @return [type] [description]
     */
    public function thumb()
    {
        // AJAX POST
        // 接受参数{"sid":"分享内容id"}
        // 成功返回true
        // TODO，失败返回错误信息数组[格式待定]
        $shareId = I('post.s_id', '', 'strip_tags');
        $userId = self::$user_id;
        $model = D('thumb');

        $result = $model->insertThumb($shareId, $userId);

        if($result === false){
            $this->dataReturn('100', $model->getError());
        }
        $this->dataReturn();
    }

    /**
     * 取消点赞
     * @return [type] [description]
     */
    public function cclthumb()
    {
        // AJAX POST
        // 接受参数{"sid":"分享内容id"}
        // 成功返回true
        // TODO，失败返回错误信息数组[格式待定]
        $shareId = I('post.s_id', '', 'strip_tags');
        $userId = self::$user_id;

        $model = D('thumb');
        $result = D('thumb')->delThumb($shareId, $userId);
        if($result === false){
            $this->dataReturn('100', $model->getError());
        }
        $this->dataReturn();
    }

}