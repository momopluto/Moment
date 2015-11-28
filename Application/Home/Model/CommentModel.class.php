<?php
namespace Home\Model;

use Think\Model;

/**
 * mn_comment
 */
class CommentModel extends BaseModel
{

    /**
     * 插入评论记录
     * @param integer $shareId 分享内容的id
     * @param integer $userId  用户id
     * @param string  $content 评论内容
     * @param integer $pid     父级评论的id（如果是回复，则是'所回复的评论'的id值；如果直接评论分享内容(即为一级评论)，则是0）
     * @return boolean 成功返回插入得到的c_id;失败返回false
     */
    public function insertComment($shareId, $userId, $content, $pid = 0)
    {
        // 验证shareId存在
        if(!M('share')->where("s_id = %d", $shareId)->count()){
            $err['errcode'] = 404;
            $err['errmsg'] = "sid invalid";
            $this->error = $err;

            return false;
        }
        // 验证shareId所属的用户账号状态为启用
        if(!$this->checkUserStatus_byShareId($shareId)){
            return false;
        }

        // userId必合法，由Controller调用时保证
        // 验证content最多为100个中文字符mb_strlen
        $TEXT_LEN = 100;
        if(mb_strlen($content, 'UTF-8') > $TEXT_LEN){
            $err['errcode'] = 411;
            $err['errmsg'] = "comment too long. must <= $TEXT_LEN";
            $this->error = $err;

            return false;
        }
        // 没有指定pid，则pid=0
        // 若pid不为0，验证c_id为此pid的评论存在
        if($pid && !$this->where('c_id = %d', $pid)->count()){
            $err['errcode'] = 404;
            $err['errmsg'] = "pid invalid";
            $this->error = $err;

            return false;
        }

        // 插入数据库
        $result = $this->add([
            's_id'    => $shareId,
            'pid'     => $pid,
            'user_id' => $userId,
            'content' => $content,
            'cTime'   => NOW_TIME,
        ]);

        // 成功，返回插入得到的c_id
        // 失败，返回false + error原因
        if($result === false){
            $err['errcode'] = 400;
            $err['errmsg'] = "comment failed";
            $this->error = $err;

            return false;
        }

        //        $this->update_share_cmt_count($shareId);// 更新cmt_count
        $err['errcode'] = 0;
        $err['errmsg'] = "ok";
        $err['cid'] = $result;// 插入后得到的主键c_id评论id
        $this->error = $err;

        return $result;
    }

    /**
     * 递归寻找以$pid为根的子回复
     * @access private
     * @param integer $shareId 分享内容的id
     * @param integer $pid     评论的父级id
     * @param array   $sub     子回复结果数组
     */
    private function rec_findsubcmt($shareId, $pid, &$sub)
    {
        $map['s_id'] = $shareId;
        $map['pid'] = $pid;
        if(($rst = $this->where($map)->select())){
            foreach($rst as $v){
                //                echo '<br/>---* '.$v['c_id'].' ||';
                array_push($sub, $v['c_id']);
                $this->rec_findsubcmt($shareId, $v['c_id'], $sub);
            }
        }
        //        echo "pid=$pid ，完！****<br/>";
    }

    /**
     * 删除评论(及其子回复)
     * @param integer $commentId 评论id
     * @param integer $userId    用户id
     * @return boolean 另: 模型的error可获取错误提示信息
     */
    public function delComment($commentId, $userId)
    {
        // 判断commentId和userId对应的评论存在，得到s_id
        if(!($cmt = $this->where('c_id = %d AND user_id = %d', array(
            $commentId,
            $userId,
        ))->find())
        ){
            $err['errcode'] = 404;
            $err['errmsg'] = "no match record";
            $this->error = $err;

            return false;
        }
        // 判断是否有以commentId为pid的其它评论，有则记录
        // 递归，找到commentId下的所有回复，最后一并删除
        $sub = array();
        $this->rec_findsubcmt($cmt['s_id'], $commentId, $sub);
        array_push($sub, $commentId);// 加上要删除的根评论

        $subIds = implode(',', $sub);
        // 一次性删除所有涉及到的评论
        $result = $this->where('s_id = %d', $cmt['s_id'])->delete($subIds);
        if($result === false){
            $err['errcode'] = 400;
            $err['errmsg'] = "delete comment failed";
            $this->error = $err;

            return false;
        }

        //        $this->update_share_cmt_count($cmt['s_id']);// 更新cmt_count
        $err['errcode'] = 0;
        $err['errmsg'] = "ok";
        $this->error = $err;

        return true;
    }

    /**
     * [按条件]返回评论的数目
     * @param mix $map 条件
     * @return integer 评论数目
     */
    public function countComment($map)
    {
        return $this->where($map)->count();
    }

    /**
     * 获取(userId的)评论总数
     * @param integer $userId 用户id
     * @param boolean $self   是否用户本人
     * @return integer 成功返回总数;失败返回false
     */
    public function getCommentSend_count($userId, $self = false)
    {
        return $this->table($this->getCommentSendShare_sql($userId, $self) . ' tmp')->count();
    }

    /**
     * 获取(userId)评论过的分享(同一分享可能重复出现)
     * @param integer $userId 用户id
     * @param boolean $self   是否用户本人
     * @return string 成功返回sql语句;失败返回false
     */
    public function getCommentSendShare_sql($userId, $self = false)
    {
        // 验证userId有效，账号启用
        if(!$this->checkUserStatus($userId)){
            return false;
        }

        // userId是否用户本身，不同的过滤条件
        $where = '(sh.isPublic=1 AND cmt.user_id=' . $userId . ' AND ur.`status`=1)';/*自己评论的->公开的分享，且用户账号为启用*/
        if($self){
            $where .= ' OR (cmt.user_id=sh.user_id AND sh.isPublic=0 AND sh.user_id=' . $userId . ')';/*自己评论的->自己的私密分享*/
            $where = '( ' . $where . ' )';
        }

        // 对同一条分享，可以有多条评论
        $sql = $this->alias('cmt')
            ->join('LEFT JOIN mn_share sh ON cmt.s_id=sh.s_id')
            ->join('LEFT JOIN mn_user ur ON sh.user_id=ur.user_id')
            ->field('FROM_UNIXTIME(cmt.cTime,"%Y-%m-%d %H:%i:%s") AS commentTime,
                    cmt.content,
                    sh.s_id,
                    sh.user_id,
                    sh.text,
                    sh.imgs,
                    FROM_UNIXTIME(sh.cTime,"%Y-%m-%d %H:%i:%s") AS cTime,
                    sh.isPublic,
                    sh.cmt_count,
                    sh.tb_count')
            ->where($where)
            ->order('cmt.cTime DESC')/*按评论时间逆序*/
            ->buildsql();

        return $sql;
    }

    /**
     * 获取自己收到的评论总数
     * 针对自己
     * @param integer $userId 用户id
     * @return integer 总数
     */
    public function getCommentReceive_count($userId)
    {
        return $this->table($this->getCommentReceiveShare_sql($userId) . ' tmp')->count();
    }

    /**
     * 获取自己被评论过的分享(同一分享可能因为多人评论而出现多次)
     * 针对自己
     * @param integer $userId 用户id
     * @return string sql语句
     */
    public function getCommentReceiveShare_sql($userId)
    {

        // $sql = 'SELECT cmt.user_id,
        //             FROM_UNIXTIME(cmt.cTime,"%Y-%m-%d %H:%i:%s") AS commentTime,
        //             sh.s_id,
        //             sh.text,
        //             sh.imgs,
        //             FROM_UNIXTIME(sh.cTime,"%Y-%m-%d %H:%i:%s") AS cTime,
        //             sh.isPublic,
        //             sh.cmt_count,
        //             sh.tb_count 
        //             FROM mn_share sh LEFT JOIN mn_comment cmt ON sh.s_id=cmt.s_id LEFT JOIN mn_user ur ON cmt.user_id=ur.user_id 
        //             WHERE ( sh.user_id='.$userId.' AND ur.`status`=1 ) 
        //             ORDER BY cmt.cTime DESC';

        $sql = M('share')
            ->alias('sh')
            ->join('LEFT JOIN mn_comment cmt ON sh.s_id=cmt.s_id')
            ->join('LEFT JOIN mn_user ur ON cmt.user_id=ur.user_id')
            ->field('cmt.user_id,
                    cmt.content,
                    FROM_UNIXTIME(cmt.cTime,"%Y-%m-%d %H:%i:%s") AS commentTime,
                    sh.s_id,
                    sh.text,
                    sh.imgs,
                    FROM_UNIXTIME(sh.cTime,"%Y-%m-%d %H:%i:%s") AS cTime,
                    sh.isPublic,
                    sh.cmt_count,
                    sh.tb_count')
            ->where('( sh.user_id=' . $userId . ' AND ur.`status`=1 )')
            ->order('cmt.cTime DESC')
            ->buildsql();

        return $sql;
    }

    /**
     * 获取单条分享的评论
     * @param $sId int 评论id
     * @return mixed
     */
    public function getCommentById($sId)
    {
        return $comments = $this->where(['s_id' => $sId])->select();
    }
}