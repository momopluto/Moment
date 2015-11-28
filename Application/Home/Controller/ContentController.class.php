<?php
namespace Home\Controller;

use Think\Controller;
use Think\Upload;

/**
 * Home分享内容控制器
 */
class ContentController extends BaseController
{
    public function getShareIndex()
    {
        $model = D('content');
        $data = $model->getShareIndex(self::$user_id);
    }

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

        // 有上传图片（前端限制能够上传的图片后缀）
        //      1、判断$_FILES中文件数目是否 等于 imgcount。 不等，则直接返回错误信息'upload $imgcount files failed'
        //      2、以上确保图片正确上传后，调用模型方法insertShare得到返回的s_id
        // 得到s_id，组装imgs字段
        //        if ($imgcount) {// 如果有图片
        //            $imgArr = array();
        //            for ($i = 0; $i < $imgcount; $i++) {
        //                $upload->saveName = md5($s_id . '_$i');// 设置上传文件名
        //                $info = $upload->uploadOne($oneFile);// 单个文件上传
        //                if ($info){
        //                    array_push($imgArr, $upload->saveName);
        //                }
        //            }
        //            $imgs = implode(',', $imgArr);// 得到所有上传图片的saveName字符串，以,号分隔
        //        }
        //      3、使用tp的Upload类，每次设置好saveName后，用uploadOne()上传，确保每个上传的文件都按照我们的意愿命名
        //          这里如果有个别文件没uploadOne成功，咋办？
        // 没上传图片
        //      1、调用模型方法insertShare得到返回的s_id
        // 
        // 不管有没图片，只要上传成功后，都返回s_id

        if(IS_POST){
            $text = I('post.text', '', 'strip_tags');
            $isPublic = I('post.is_public', '', 'strip_tags');
            $fileCount = I('post.file_count');
            if(intval($fileCount) !== count($_FILES)){
                $this->ajaxReturn([
                    'errcode' => '400',
                    'errmsg'  => 'upload failed',
                ]);
            }

            $userId = self::$user_id;
            $model = D('content');
            $id = $model->insertShare($userId, $text, $isPublic);
            //            $dealFile = $this->dealFiles($_FILES);
            $imgs = [];
            $upload = new Upload();
            $upload->maxSize = 3145728;// 设置附件上传大小
            $upload->exts = array(
                'jpg',
                'gif',
                'png',
                'jpeg',
            );// 设置附件上传类型

            $upload->rootPath = AS_PATH_IMG . '/';  // 设置附件上传根目录
            $upload->savePath = md5($userId) . '/'; // 设置附件上传（子）目录
            $upload->subName = '';
            checkPathOrCreate($upload->rootPath);
            $i = 0;
            foreach($_FILES as $key => $value){
                $upload->saveName = md5($id . '_' . $i++);
                // 上传文件
                $result = $upload->uploadOne($value);
                if($result){
                    $imgs[] = $result;
                }
            }

            if(count($imgs) !== count($_FILES)){
                $this->ajaxReturn([
                    'errcode' => '400',
                    'errmsg'  => 'upload failed',
                ]);
            }

            $imgs = array_column($imgs, 'savename');
            $imgs = implode($imgs, ',');
            $result = true;
            if($imgs){
                $result = $model->saveShare(['s_id' => $id], ['imgs' => $imgs]);
            }
            if(!$result){
                $this->ajaxReturn($model->getError());

                return;
            }

            $this->ajaxReturn($model->getShareById($id));
        }else{
            $this->ajaxReturn([
                'errcode' => '404',
                'errmsg'  => 'request failed',
            ]);
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
        $shareId = I('post.sid', '', 'strip_tags');

        $result = $model->delShare($shareId, self::$user_id);

        $this->ajaxReturn($model->getError());
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

        $shareId = I('post.sid', '', 'strip_tags');
        $pid = I('post.pid', '', 'strip_tags');
        $userId = self::$user_id;
        $content = I('post.content', '', 'strip_tags');

        $model = D('comment');
        $result = $model->insertComment($shareId, $userId, $content, $pid);

        $this->ajaxReturn($model->getError());
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
        $commentId = I('post.cid', '', 'strip_tags');

        $model = D('comment');
        $result = $model->delComment($commentId, self::$user_id);

        $this->ajaxReturn($model->getError());
    }
}