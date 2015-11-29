<?php
namespace Home\Model;
use Think\Model;

/**
 * mn_favshare
 */
class FavshareModel extends BaseModel{

    /**
     * 获取收藏的分享总数
     * 针对本人
     * @param integer $ownerId 拥有者id
     * @return integer 成功返回总数;失败返回false
     */
    public function getSelfFavshares_count($ownerId){
        return $this->table($this->getSelfFavshares_sql($ownerId).' tmp')->count();
    }

    /**
     * 获取收藏的分享
     * 针对本人
     * @param integer $ownerId 拥有者id
     * @return string sql语句
     */
    public function getSelfFavshares_sql($ownerId) {
        // // 验证ownerId有效，账号启用
        // if (!$this->checkUserStatus($ownerId)){
        //     return false;
        // }
        
        // // ownerId是否用户本身，不同的过滤条件
        // $where = '(fs.owner_id=' . $ownerId . ' AND sh.isPublic=1 AND ur.`status`=1)';/*自己收藏的->公开的分享，且账号状态为启用*/
        // if ($self){
        //     $where .= ' OR (fs.owner_id=sh.user_id AND sh.isPublic=0 AND sh.user_id='.$ownerId.')';/*自己收藏的->自己私密的分享*/
        //     $where = '( '.$where.' )';
        // }
        
        $sql = $this->alias('fs')
                ->join('LEFT JOIN mn_share sh ON fs.s_id=sh.s_id')
                ->join('LEFT JOIN mn_user ur ON sh.user_id=ur.user_id')
                ->field('FROM_UNIXTIME(fs.cTime,"%Y-%m-%d %H:%i:%s") AS collectTime,
                    fs.s_id,
                    sh.user_id,
                    sh.text,
                    sh.imgs,
                    FROM_UNIXTIME(sh.cTime,"%Y-%m-%d %H:%i:%s") AS cTime,
                    sh.isPublic,
                    sh.cmt_count,
                    sh.tb_count,
                    1 AS collected')/*必已收藏标志*/
                ->where('( (fs.owner_id=' . $ownerId . ' AND sh.isPublic=1 AND ur.`status`=1)'
                    .' OR (fs.owner_id=sh.user_id AND sh.isPublic=0 AND sh.user_id='.$ownerId.') )')
                ->order('fs.cTime DESC')/*按收藏时间逆序*/
                ->buildsql();
        return $sql;
    }

    /**
     * 插入收藏分享记录
     * @param integer $ownerId 拥有者id
     * @param integer $shareId 被收藏的分享的id
     * @return boolean 另: 模型的error可获取错误提示信息
     */
    public function insertFavshare($ownerId, $shareId){
        // 检验shareId存在
        if (!M('share')->where("s_id = %d",$shareId)->count()){
            $err['errcode'] = 404;
            $err['errmsg'] = "sid invalid";
            $this->error = $err;
            return false;
        }
        // 验证shareId所属的用户账号状态是否为启用
        if (!$this->checkUserStatus_byShareId($shareId)){
            return false;
        }
        // 检验ownerId和shareId记录未存在
        if ($this->where("owner_id = %d AND s_id = %d", array($ownerId, $shareId))->count()) {
            $err['errcode'] = 414;
            $err['errmsg'] = "duplicated";
            $this->error = $err;
            return false;
        }

        $result = $this->add([
            'owner_id' => $ownerId,
            's_id'     => $shareId,
            'cTime'    => NOW_TIME,
        ]);

        if ($result === false) {
            $err['errcode'] = 400;
            $err['errmsg'] = "collect favshare failed";
            $this->error = $err;
            return false;
        }

        $err['errcode'] = 0;
        $err['errmsg'] = "ok";
        $this->error = $err;
        return true;
    }

    /**
     * 删除收藏分享记录
     * @param integer $ownerId 拥有者id
     * @param integer $shareId 被收藏的分享的id
     * @return boolean 另: 模型的error可获取错误提示信息
     */
    public function delFavshare($ownerId, $shareId){
        // 验证shareId所属的用户账号状态是否为启用
        if (!$this->checkUserStatus_byShareId($shareId)) {
            return false;
        }
        // 验证$ownerId和$userId记录存在
        $result = $this->where("owner_id = %d AND s_id = %d", array($ownerId, $shareId))->delete();
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
     * [按条件]返回收藏分享的数目
     * @param myx $map 条件
     * @return integer 收藏分享数
     */
    public function countFavshare($map){
        return $this->where($map)->count();
    }
}