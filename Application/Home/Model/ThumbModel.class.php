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

        $result = $this->add([
            's_id'    => $shareId,
            'user_id' => $userId,
            'cTime'   => time(),
        ]);

        if($result === false){
            $this->error = '点赞失败';

            return false;
        }

        return true;
    }

    public function delShareThumb($shareId)
    {

        $result = $this->where(['s_id' => $shareId])->delete();
        if($result === false){
            $this->error('删除点赞记录失败');

            return false;
        }

        return true;
    }

    public function cclThumb($shareId, $userId)
    {

        $result = $this->where([
            's_id'    => $shareId,
            'user_id' => $userId,
        ])->delete();

        if($result === false){
            $this->error = '取消点赞失败';

            return false;
        }

        return true;
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