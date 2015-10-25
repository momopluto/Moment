<?php
namespace Home\Model;

use Think\Model;

/**
 * mn_comment
 */
class CommentModel extends BaseModel
{

    public function insertComment($shareId, $pid, $userId, $content)
    {

        $result = $this->add([
            's_id'    => $shareId,
            'pid'     => $pid,
            'user_id' => $userId,
            'content' => $content,
            'cTime'   => time(),
        ]);

        if($result === false){
            $this->error = '评论插入失败';

            return false;
        }

        return true;
    }

    public function delComment($commentId, $userId)
    {
        $result = $this->where([
            'c_id'    => $commentId,
            'user_id' => $userId,
        ])->delete();

        if($result === false){
            $this->error = '删除评论失败';

            return false;
        }

        return true;
    }

    public function delShareComment($shareId)
    {
        return $this->where(['s_id' => $shareId])->delete();
    }

    public function delParentComment($commentId)
    {
        return $this->where(['pid' => $commentId])->delete();
    }

    public function countComment($where = [])
    {
        return $this->where($where)->count();
    }

    public function getSelfComment($userId, $page, $limit)
    {
        $start = ($page - 1) * $limit;
        $sql = "select c.c_id, c.s_id, c.pid, c.user_id, c.content, c.cTime, u.username, s.text, s.imgs, s.cTime as share_cTime from mn_comment c left join mn_user u on c.user_id=u.user_id left join mn_share s on c.s_id=s.s_id where c.user_id=$userId order by c.cTime limit $start, $limit";

        return M()->query($sql);
    }
}