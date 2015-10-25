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
        $count = intval($this->checkExist($ownerId, $userId));
        if($count === 0){
            $result = $this->add([
                'owner_id' => $ownerId,
                'user_id'  => $userId,
                'cTime'    => time(),
            ]);

            if($result === false){
                $this->error = '收藏用户成功';

                return false;
            }

            return true;
        }else{
            $this->error = '已收藏';

            return false;
        }
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
        $result = $this->where([
            'owner_id' => $ownerId,
            'user_id'  => $userId,
        ])->delete();

        if($result === false){
            $this->error = '取消收藏用户失败';

            return false;
        }

        return true;
    }
}