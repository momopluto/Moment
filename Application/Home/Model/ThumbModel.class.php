<?php
namespace Home\Model;
use Think\Model;

/**
 * mn_thumb表模型
 */
class ThumbModel extends BaseModel{

    /**
     * 获取点赞榜
     * @param string $theday_timestr UNIX时间戳
     * @param integer $lmt 显示条数，默认10条
     * @return mix 分享内容信息数组
     */
    public function get_thumbuplist($theday_timestr,$lmt=10){
        
        // 全部
        $all = D('Share')->cache('all_thumblist',1800)->alias('sh')
                ->join('LEFT JOIN mn_user ur ON sh.user_id=ur.user_id')
                ->field('sh.s_id,
                    sh.user_id,
                    sh.text,
                    FROM_UNIXTIME(sh.cTime,"%Y-%m-%d %H:%i:%s") AS cTime,
                    sh.tb_count')
                ->where('sh.tb_count>0 AND sh.isPublic=1 AND ur.`status`=1')/*限制公开的分享，且'启用的用户' 才能参与排行榜*/
                ->order('sh.tb_count DESC')
                ->limit($lmt)
                ->select();
        // 上周一至今
        $monday   = strtotime('-1 week Monday', $theday_timestr);// 过去的周一
        $today_st = strtotime(date('Y-m-d 00:00:00',$theday_timestr));
        $today_ed = strtotime(date('Y-m-d 23:59:59',$theday_timestr));
        $week = $this->alias('tb')
                ->join('LEFT JOIN mn_share sh ON tb.s_id=sh.s_id')
                ->join('LEFT JOIN mn_user ur ON sh.user_id=ur.user_id')
                ->field('tb.s_id,
                    COUNT(*) AS tb_count,
                    sh.user_id,
                    sh.text,
                    FROM_UNIXTIME(sh.cTime,"%Y-%m-%d %H:%i:%s") AS cTime')
                ->where('((tb.cTime BETWEEN '.$monday.' AND '.$today_ed.')'/*时间段查询导致ALL全表扫描，待改进*/
                        . ' AND  (ur.`status`=1 AND sh.isPublic=1))')/*限制公开的分享，且'启用的用户' 才能参与排行榜*/
                ->group('tb.s_id')
                ->order('tb_count DESC')
                ->limit($lmt)
                ->select();
        // 今天
        $today = $this->alias('tb')
                ->join('LEFT JOIN mn_share sh ON tb.s_id=sh.s_id')
                ->join('LEFT JOIN mn_user ur ON sh.user_id=ur.user_id')
                ->field('tb.s_id,
                    COUNT(*) AS tb_count,
                    sh.user_id,
                    sh.text,
                    FROM_UNIXTIME(sh.cTime,"%Y-%m-%d %H:%i:%s") AS cTime')
                ->where('((tb.cTime BETWEEN '.$today_st.' AND '.$today_ed.')'/*时间段查询导致ALL全表扫描，待改进*/
                        . ' AND  (ur.`status`=1 AND sh.isPublic=1))')/*限制公开的分享，且'启用的用户' 才能参与排行榜*/
                ->group('tb.s_id')
                ->order('tb_count DESC')
                ->limit($lmt)
                ->select();
        
        $data['all'] = $all;
        $data['week'] = $week;
        $data['today'] = $today;
        
//        $data['all'] = $sql_all;
//        $data['week'] = $sql_week;
//        $data['today'] = $sql_today;
        
//        $data['all'] = $this->query($sql_all);
//        $data['week'] = $this->query($sql_week);
//        $data['today'] = $this->query($sql_today);

        return $data;
    }

    /**
     * 插入点赞记录
     * @param integer $shareId 分享内容的id
     * @param integer $userId 用户id
     * @return boolean 另: 模型的error可获取错误提示信息
     */
    public function insertThumb($shareId, $userId){
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
        
        // 检验shareId和userId记录未存在
        if ($this->where("s_id = %d AND user_id = %d",array($shareId,$userId))->count()){
            $err['errcode'] = 414;
            $err['errmsg'] = "duplicated";
            $this->error = $err;
            return false;
        }
        
        // $userId必存在且合法，Controller调用时保证
        $result = $this->add([
            's_id'    => $shareId,
            'user_id' => $userId,
            'cTime'   => NOW_TIME,
        ]);

        if($result === false){
            $err['errcode'] = 400;
            $err['errmsg'] = "thumb failed";
            $this->error = $err;
            return false;
        }
        
//        $this->update_share_tb_count($shareId);// 更新tb_count
        $err['errcode'] = 0;
        $err['errmsg'] = "ok";
        $this->error = $err;
        return true;
    }

    /**
     * 删除点赞记录
     * @param integer $shareId 分享内容的id
     * @param integer $userId 用户id
     * @return boolean 另: 模型的error可获取错误提示信息
     */
    public function cclThumb($shareId, $userId){
        // 验证shareId所属的用户账号状态是否为启用
        if (!$this->checkUserStatus_byShareId($shareId)){
            return false;
        }
        
        // 验证$shareId和$userId记录存在
        $result = $this->where("s_id = %d AND user_id = %d",array($shareId,$userId))->delete();
        if($result === false){
            $err['errcode'] = 404;
            $err['errmsg'] = "no match record";
            $this->error = $err;
            return false;
        }

//        $this->update_share_tb_count($shareId);// 更新tb_count
        $err['errcode'] = 0;
        $err['errmsg'] = "ok";
        $this->error = $err;
        return true;
    }
    
    /**
     * [按条件]返回点赞的数目
     * @param mix $map 条件
     * @return integer 点赞数目
     */
    public function countThumb($map){
        return $this->where($map)->count();
    }

    /**
     * 获取(userId的)点赞总数
     * @param integer $userId 用户id
     * @param boolean $self 是否用户本人
     * @return integer 成功返回总数;失败返回false
     */
    public function getThumbSend_count($userId, $self=false){
        return $this->table($this->getThumbSendShare_sql($userId, $self).' tmp')->count();
    }

    /**
     * 获取(userId)点赞过的分享
     * @param integer $userId 用户id
     * @param boolean $self 是否用户本人
     * @return string 成功返回sql语句;失败返回false
     */
    public function getThumbSendShare_sql($userId, $self=false){
        
        // 验证userId有效，账号启用
        if (!$this->checkUserStatus($userId)){
            return false;
        }
        
        // userId是否用户本身，不同的过滤条件
        $where = '(sh.isPublic=1 AND tb.user_id='.$userId.' AND ur.`status`=1)';/*自己点赞的->公开的分享，且用户账号为启用*/
        if ($self){
            $where .= ' OR (tb.user_id=sh.user_id AND sh.isPublic=0 AND sh.user_id='.$userId.')';/*自己点赞的->自己的私密分享*/
            $where = '( '.$where.' )';
        }
        $sql = $this->alias('tb')
                ->join('LEFT JOIN mn_share sh ON tb.s_id=sh.s_id')
                ->join('LEFT JOIN mn_user ur ON sh.user_id=ur.user_id')
                ->field('FROM_UNIXTIME(tb.cTime,"%Y-%m-%d %H:%i:%s") AS thumbTime,
                    sh.s_id,
                    sh.user_id,
                    sh.text,
                    sh.imgs,
                    FROM_UNIXTIME(sh.cTime,"%Y-%m-%d %H:%i:%s") AS cTime,
                    sh.isPublic,
                    sh.cmt_count,
                    sh.tb_count')
                ->where($where)
                ->order('tb.cTime DESC')/*按点赞时间逆序*/
                ->buildsql();
        return $sql;
    }

    /**
     * 获取自己收到的点赞总数
     * 针对自己
     * @param integer $userId 用户id
     * @return integer 总数
     */
    public function getThumbReceive_count($userId){
        return $this->table($this->getThumbReceiveShare_sql($userId).' tmp')->count();
    }

    /**
     * 获取自己被点赞过的分享(同一分享可能因为多人点赞而出现多次)
     * 针对自己
     * @param integer $userId 用户id
     * @return string sql语句
     */
    public function getThumbReceiveShare_sql($userId){
        // userId必合法，只能是自己查看 自己被点赞过的分享

        // $sql = 'SELECT tb.user_id,
        //             FROM_UNIXTIME(tb.cTime,"%Y-%m-%d %H:%i:%s") AS thumbTime,
        //             sh.s_id,
        //             sh.text,
        //             sh.imgs,
        //             FROM_UNIXTIME(sh.cTime,"%Y-%m-%d %H:%i:%s") AS cTime,
        //             sh.isPublic,
        //             sh.cmt_count,
        //             sh.tb_count 
        //         FROM mn_share sh LEFT JOIN mn_thumb tb ON sh.s_id=tb.s_id LEFT JOIN mn_user ur ON tb.user_id=ur.user_id 
        //         WHERE ( sh.user_id='.$userId.' AND ur.`status`=1 ) 
        //         ORDER BY tb.cTime DESC';

        $sql = M('share')->alias('sh')
                ->join('LEFT JOIN mn_thumb tb ON sh.s_id=tb.s_id')
                ->join('LEFT JOIN mn_user ur ON tb.user_id=ur.user_id')
                ->field('tb.user_id,
                    FROM_UNIXTIME(tb.cTime,"%Y-%m-%d %H:%i:%s") AS thumbTime,
                    sh.s_id,
                    sh.text,
                    sh.imgs,
                    sh.user_id,
                    FROM_UNIXTIME(sh.cTime,"%Y-%m-%d %H:%i:%s") AS cTime,
                    sh.isPublic,
                    sh.cmt_count,
                    sh.tb_count')
                ->where('( sh.user_id='.$userId.' AND ur.`status`=1 )')
                ->order('tb.cTime DESC')
                ->buildsql();

        return $sql;
    }
}