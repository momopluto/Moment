<?php
namespace Home\Model;

use Think\Model;

/**
 * mn_thumb表模型
 */
class ThumbModel extends BaseModel
{

    /**
     * 获取点赞榜
     * @return [type] [description]
     */
    public function get_thumbuplist()
    {
    }

    public function insertThumb($shareId, $userId)
    {
        return $this->add([
            's_id'    => $shareId,
            'user_id' => $userId,
        ]);
    }

    public function checkThumb($shareId, $userId)
    {
        return $this->where([
            's_id'    => $shareId,
            'user_id' => $userId,
        ])->count();
    }

    public function delShareThumb($shareId)
    {
        return $this->where(['s_id' => $shareId])->delete();
    }

    public function cclThumb($shareId, $userId)
    {
        return $this->where([
            's_id'    => $shareId,
            'user_id' => $userId,
        ])->delete();
    }

    public function countThumb($where)
    {
        return $this->where($where)->count();
    }

    public function getSelfThumb($userId, $page, $limit)
    {
        $start = ($page - 1) * $limit;
        $sql = "select t.s_id, t.user_id, t.cTime, u.username, s.text, s.imgs, s.isPublic from mn_thumb t left join mn_user u on t.user_id=u.user_id left join mn_share s on t.s_id=s.s_id where t.user_id=$userId order by t.cTime desc limit $start, $limit";

        return M()->query($sql);
    }
}