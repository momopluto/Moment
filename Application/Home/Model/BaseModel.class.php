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
     * $return integer 1为账号启用，0为账号被禁
     */
    protected function checkUserStatus($userId){
        return M('user')->where('`status`=1 AND user_id=%d',$userId)->count();
    }

    /**
     * 查询shareId所属的用户账号状态
     * @param integer $shareId 分享内容的id
     * @return integer 1为账号启用，0为账号被禁
     */
    protected function checkUserStatus_byShareId($shareId){
        return M('share')->alias('sh')
                ->join('LEFT JOIN mn_user ur ON sh.user_id=ur.user_id')
                ->where('ur.`status`=1 AND sh.s_id=%d',$shareId)
                ->count();
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