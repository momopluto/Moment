<?php
namespace Home\Model;
use Think\Model;

/**
 * mn_favuser表模型
 */
class FavuserModel extends BaseModel{

    /**
     * 获取(ownerId)收藏[关注]的用户总数
     * @param integer $ownerId 拥有者id
     * @param boolean $self 是否用户本人
     * @return integer 成功返回总数;失败返回false
     */
    public function getFavusers_count($ownerId, $self=false){
        return $this->table($this->getFavusers_sql($ownerId, $self).' tmp')/*->cache('count_focus',1800)*/->count();
    }

    /**
     * 获取(ownerId)收藏[关注]的用户
     * @param integer $ownerId 拥有者id
     * @param boolean $self 是否用户本人
     * @return string 成功返回sql语句;失败返回false
     */
    public function getFavusers_sql($ownerId, $self=false){
        // 验证ownerId有效，账号启用
        if (!$this->checkUserStatus($ownerId)){
            return false;
        }
        
        // 是否用户本人对查询结果无影响
        
        $sql = $this->alias('fu')
                ->join('LEFT JOIN mn_user ur ON fu.user_id=ur.user_id')
                ->field('FROM_UNIXTIME(fu.cTime,"%Y-%m-%d %H:%i:%s") AS focusTime,
                    fu.user_id,
                    ur.username,
                    ur.reg_time,
                    ur.last_login_time,
                    ur.`status`')
                ->where('(fu.owner_id='.$ownerId.' AND ur.`status`=1)')/*验证用户账号启用*/
                ->order('fu.cTime DESC')/*按收藏时间逆序*/
                ->buildsql();
        return $sql;
    }

    /**
     * 获取(userId的)粉丝总数
     * @param integer $userId 被关注者id
     * @param boolean $self 是否用户本人
     * @return integer 成功返回总数;失败返回false
     */
    public function getFans_count($userId, $self=false){
        return $this->table($this->getFans_sql($userId, $self).' tmp')/*->cache('count_fans',1800)*/->count();
    }

    /**
     * 获取(userId的)粉丝
     * @param integer $userId 被关注者id
     * @param boolean $self 是否用户本人
     * @return string 成功返回sql语句;失败返回false
     */
    public function getFans_sql($userId, $self=false){
        // xx的粉丝，即其它人owner_id收藏[关注]xx的user_id

        // 验证userId有效，账号启用
        if (!$this->checkUserStatus($userId)){
            return false;
        }
        
        // 是否用户本人对查询结果无影响

        $sql = $this->alias('fu')
                ->join('LEFT JOIN mn_user ur ON fu.owner_id=ur.user_id')
                ->join('LEFT JOIN mn_favuser fu2 ON (fu2.user_id=fu.owner_id AND fu2.owner_id=fu.user_id)')/*判断是否已经与粉丝互相关注*/
                ->field('FROM_UNIXTIME(fu.cTime,"%Y-%m-%d %H:%i:%s") AS followTime,
                    fu.owner_id,
                    ur.username,
                    ur.reg_time,
                    ur.last_login_time,
                    ur.`status`,
                    IF(fu2.cTime,1,0) AS correlated')/*correlated为1代表已互相关注，0为未关注粉丝*/
                ->where('( fu.user_id='.$userId.' AND ur.`status`=1 )')
                ->order('fu.cTime DESC')
                ->buildsql();

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
        // 验证userId有效，账号是否启用
        if (!$this->checkUserStatus($userId)) {
            return false;
        }
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