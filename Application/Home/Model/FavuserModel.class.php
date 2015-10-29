<?php
namespace Home\Model;
use Think\Model;

/**
 * mn_favuser表模型
 */
class FavuserModel extends BaseModel{

    /**
     * 获取所有收藏的用户
     * @param integer $ownerId 拥有者id
     * @return string sql语句
     */
    public function getAllFavusers_sql($ownerId){
        // 不管被收藏用户的账号是否启用，一律返回
        // 允许取消收藏'被禁用的用户'
        $sql = 'SELECT FROM_UNIXTIME(fu.cTime,"%Y-%m-%d %H:%i:%s") AS cTime, fu.user_id, ur.reg_time, ur.last_login_time, ur.`status`'
                . ' FROM mn_favuser fu LEFT JOIN mn_user ur ON fu.user_id=ur.user_id'
                . ' WHERE (fu.owner_id='.$ownerId.')'
                . ' ORDER BY fu.cTime DESC';
        return $sql;
    }

    /**
     * 插入收藏用户记录
     * @param integer $ownerId 拥有者id
     * @param integer $userId 被收藏者id
     * @return boolean 另: 模型的error可获取错误提示信息
     */
    public function insertFavuser($ownerId, $userId){
        // 不能收藏自己，ownerId != userId
        if ($ownerId == $userId){
            $err['errcode'] = 409;
            $err['errmsg'] = "can't collect self";
            $this->error = $err;
            return false;
        }
        // 验证userId有效，账号启用
        if (!$this->checkUserStatus($userId)){
            $err['errcode'] = 412;
            $err['errmsg'] = "target user was disabled";// userId账号状态为禁用
            $this->error = $err;
            return false;
        }
        // 验证ownerId和userId记录未存在
        if ($this->where("owner_id = %d AND user_id = %d", array($ownerId, $userId))->count()) {
            $err['errcode'] = 414;
            $err['errmsg'] = "duplicated";
            $this->error = $err;
            return false;
        }
        
        // $ownerId必合法，由Controller保证
        $result = $this->add([
            'owner_id' => $ownerId,
            'user_id'  => $userId,
            'cTime'    => NOW_TIME,
        ]);

        if($result === false){
            $err['errcode'] = 400;
            $err['errmsg'] = "collect favuser failed";
            $this->error = $err;
            return false;
        }
        
        $err['errcode'] = 0;
        $err['errmsg'] = "ok";
        $this->error = $err;
        return true;
    }

    /**
     * 删除收藏用户记录
     * @param integer $ownerId 拥有者id
     * @param integer $userId 被收藏者id
     * @return boolean 另: 模型的error可获取错误提示信息
     */
    public function delFavuser($ownerId, $userId){
//        // 验证userId有效，账号启用
//        // 允许取消收藏'被禁用的用户'
//        if (!$this->checkUserStatus($userId)) {
//            $err['errcode'] = 412;
//            $err['errmsg'] = "target user was disabled"; // userId账号状态为禁用
//            $this->error = $err;
//            return false;
//        }
        // 验证$ownerId和$userId记录存在
        $result = $this->where("owner_id = %d AND user_id = %d", array($ownerId, $userId))->delete();
        if ($result === false) {
            $err['errcode'] = 404;
            $err['errmsg'] = "no match record";
            $this->error = $err;
            return false;
        }

        $err['errcode'] = 0;
        $err['errmsg'] = "ok";
        $this->error = $err;
        return true;
    }

    /**
     * [按条件]返回收藏用户的数目
     * @param myx $map 条件
     * @return integer 收藏用户数
     */
    public function countFavuser($map){
        return $this->where($map)->count();
    }
}