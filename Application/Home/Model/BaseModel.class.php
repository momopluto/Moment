<?php
namespace Home\Model;
use Common\Model\CommonModel;

class BaseModel extends CommonModel{
    /**
     * Home模块总模型，直接继承CommonModel
     */
    
    /**
     * 查询userId账号状态
     * @param integer $userId 用户id
     * $return integer 1为账号启用，0为账号被禁
     */
    protected function checkUserStatus($userId){
        return M('user')->where('ur.`status`=1 AND user_id=%d',$userId)->count();
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
}