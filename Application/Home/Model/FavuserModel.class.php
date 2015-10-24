<?php
namespace Home\Model;

use Think\Model;

/**
 * mn_favuser表模型
 */
class FavuserModel extends BaseModel
{

    /**
     * 获取所有收藏的用户
     * @return [type] [description]
     */
    public function get_allfavusers()
    {
    }

    public function insertFavuser($ownerId, $userId)
    {
        return $this->add([
            'owner_id' => $ownerId,
            'user_id'  => $userId,
            'cTime'    => time(),
        ]);
    }

    public function checkExist($ownerId, $userId)
    {
        return $this->where([
            'owner_id' => $ownerId,
            'user_id'  => $userId,
        ])->count();
    }

    public function DelFavuser($ownerId, $userId)
    {
        return $this->where([
            'owner_id' => $ownerId,
            'user_id'  => $userId,
        ])->delete();
    }
}