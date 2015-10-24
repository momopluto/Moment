<?php
namespace Home\Model;

use Think\Model;

/**
 * mn_favshare
 */
class FavshareModel extends BaseModel
{
    public function delShareFavshare($shareId)
    {
        return $this->where(['s_id' => $shareId])->delete();
    }

    public function checkExist($ownerId, $shareId)
    {
        return $this->where([
            'owner_id' => $ownerId,
            's_id'     => $shareId,
        ])->count();
    }

    public function insertFavshare($ownerId, $shareId)
    {
        return $this->add([
            'owner_id' => $ownerId,
            's_id'     => $shareId,
            'cTime'    => time(),
        ]);
    }

    public function delFavshare($ownerId, $shareId)
    {
        return $this->where([
            'owner_id' => $ownerId,
            's_id'     => $shareId,
        ])->delete();
    }

}