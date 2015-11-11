<?php
namespace Home\Model;
use Common\Model\CommonModel;

class BaseModel extends CommonModel{
    /**
     * Home模块总模型，直接继承CommonModel
     */

// 说明：isPublic只能在发布share时设置。私密的分享，只允许自己点赞、评论、收藏。
//      在操作share时，要确保该share为公开的 OR 该share属于自己
    
    /**
     * 查询userId账号状态
     * @param integer $userId 用户id
     * @return boolean
     */
    protected function checkUserStatus($userId){
        if (M('user')/*->cache('check_user_status',1800)*/->where('`status`=1 AND user_id=%d',$userId)->count() == 0){
            $err['errcode'] = 412;
            $err['errmsg'] = "target user was disabled or not found";// userId账号状态为禁用，或者无此账号
            $this->error = $err;
            return false;
        }

        return true;
    }

    /**
     * 查询shareId所属的用户账号状态
     * @param integer $shareId 分享内容的id
     * @return boolean
     */
    protected function checkUserStatus_byShareId($shareId){
        if (M('share')/*->cache('check_shares_user_status',1800)*/->alias('sh')
                ->join('LEFT JOIN mn_user ur ON sh.user_id=ur.user_id')
                ->where('ur.`status`=1 AND sh.s_id=%d',$shareId)
                ->count() == 0){

            $err['errcode'] = 412;
            $err['errmsg'] = "target share's user was disabled";// shareId所属的用户账号状态为禁用
            $this->error = $err;
            return false;
        }

        return true;
    }
    
    /**
     * 更新mn_share表的cmt_count字段
     * 代替数据库的触发器
     * @param integer $shareId
     * @return boolean
     */
    protected function update_share_cmt_count($shareId){
        $sql = 'UPDATE mn_share
            SET cmt_count=(
                SELECT COUNT(*) AS cmt_total
                FROM mn_comment
                WHERE s_id='.$shareId.'
                GROUP BY s_id
            )
            WHERE mn_share.s_id='.$shareId;
        return $this->execute($sql);
    }
    
    /**
     * 更新mn_share表的tb_count字段
     * 代替数据库的触发器
     * @param integer $shareId
     * @return boolean
     */
    protected function update_share_tb_count($shareId){
        $sql = 'UPDATE mn_share
            SET tb_count=(
                SELECT COUNT(*) AS tb_total
                FROM mn_thumb
                WHERE s_id='.$shareId.'
                GROUP BY s_id
            )
            WHERE mn_share.s_id='.$shareId;
        return $this->execute($sql);
    }
}