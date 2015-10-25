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
        $result = $this->where(['s_id' => $shareId])->delete();
        if($result === false){
            $this->error = '取消收藏分享失败';

            return false;
        }

        return true;
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

        $count = intval($this->checkExist($ownerId, $shareId));

        if($count === 0){
            $result = $this->add([
                'owner_id' => $ownerId,
                's_id'     => $shareId,
                'cTime'    => time(),
            ]);

            if($result === false){
                $this->error = '收藏分享失败';

                return false;
            }

            return true;
        }else{
            $this->error = '已收藏';

            return false;
        }
    }

    public function delFavshare($ownerId, $shareId)
    {
        $result = $this->where([
            'owner_id' => $ownerId,
            's_id'     => $shareId,
        ])->delete();

        if($result === false){
            $this->error = '取消收藏分享失败';

            return false;
        }

        return true;
    }

}