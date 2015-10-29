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
        $sql_all = 'SELECT tb.s_id, COUNT(*) AS total, sh.user_id, sh.text, sh.imgs, FROM_UNIXTIME(sh.cTime,"%Y-%m-%d %H:%i:%s") AS cTime'
                . ' FROM mn_thumb tb LEFT JOIN mn_share sh ON tb.s_id=sh.s_id'
                . ' LEFT JOIN mn_user ur ON sh.user_id=ur.user_id'/*验证 分享所属的用户 是否为启用状态*/
                . ' WHERE (ur.`status`=1 AND sh.isPublic=1)'/*限制'启用的用户'且分享为公开 才能参与排行榜*/
                . ' GROUP BY tb.s_id'
                . ' ORDER BY total DESC'
                . ' LIMIT '.$lmt;
        // 上周一至今
        $monday   = strtotime('-1 week Monday', $theday_timestr);// 过去的周一
        $today_st = strtotime(date('Y-m-d 00:00:00',$theday_timestr));
        $today_ed = strtotime(date('Y-m-d 23:59:59',$theday_timestr));
        $sql_week = 'SELECT tb.s_id, COUNT(*) AS total, sh.user_id, sh.text, sh.imgs, FROM_UNIXTIME(sh.cTime,"%Y-%m-%d %H:%i:%s") AS cTime'
                . ' FROM mn_thumb tb LEFT JOIN mn_share sh ON tb.s_id=sh.s_id'
                . ' LEFT JOIN mn_user ur ON sh.user_id=ur.user_id'/*验证 分享所属的用户 是否为启用状态*/
                . ' WHERE ((tb.cTime BETWEEN '.$monday.' AND '.$today_ed.')'/*时间段查询导致ALL全表扫描，待改进*/
                . ' AND (ur.`status`=1 AND sh.isPublic=1))'/*限制'启用的用户'且分享为公开 才能参与排行榜*/
                . ' GROUP BY tb.s_id'
                . ' ORDER BY total DESC'
                . ' LIMIT '.$lmt;
        // 今天
        $sql_today = 'SELECT tb.s_id, COUNT(*) AS total, sh.user_id, sh.text, sh.imgs, FROM_UNIXTIME(sh.cTime,"%Y-%m-%d %H:%i:%s") AS cTime'
                . ' FROM mn_thumb tb LEFT JOIN mn_share sh ON tb.s_id=sh.s_id'
                . ' LEFT JOIN mn_user ur ON sh.user_id=ur.user_id'/*验证 分享所属的用户 是否为启用状态*/
                . ' WHERE ((tb.cTime BETWEEN '.$today_st.' AND '.$today_ed.')'/*时间段查询导致ALL全表扫描，待改进*/
                . ' AND (ur.`status`=1 AND sh.isPublic=1))'/*限制'启用的用户'且分享为公开 才能参与排行榜*/
                . ' GROUP BY tb.s_id'
                . ' ORDER BY total DESC'
                . ' LIMIT '.$lmt;
        
//        $data['all'] = $sql_all;
//        $data['week'] = $sql_week;
//        $data['today'] = $sql_today;
        
        $data['all'] = $this->query($sql_all);
        $data['week'] = $this->query($sql_week);
        $data['today'] = $this->query($sql_today);

        return $data;
    }

    /**
     * 插入点赞记录
     * @param integer $shareId 分享内容的id
     * @param integer $userId 用户id
     * @return boolean 另: 模型的error可获取错误提示信息
     */
    public function insertThumb($shareId, $userId){
        // 检验shareId有效
        if (!M('share')->where("s_id = %d",$shareId)->count()){
            $err['errcode'] = 404;
            $err['errmsg'] = "sid invalid";
            $this->error = $err;
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
        // 验证$shareId和$userId记录存在
        $result = $this->where("s_id = %d AND user_id = %d",array($shareId,$userId))->delete();
        if($result === false){
            $err['errcode'] = 412;
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
     * [按条件]返回点赞的数目
     * @param mix $map 条件
     * @return integer 点赞数目
     */
    public function countThumb($map){
        return $this->where($map)->count();
    }

    /**
     * 获取自己点赞过的分享
     * @param integer $userId 用户id
     * @return string sql语句
     */
    public function getSelfThumbShare_sql($userId){
        $sql = 'SELECT FROM_UNIXTIME(tb.cTime,"%Y-%m-%d %H:%i:%s") AS tb_cTime, sh.s_id, sh.user_id, sh.text, sh.imgs, FROM_UNIXTIME(sh.cTime,"%Y-%m-%d %H:%i:%s") AS sh_cTime, sh.isPublic'
                . ' FROM mn_thumb tb LEFT JOIN mn_share sh ON tb.s_id=sh.s_id'
                . ' WHERE (sh.isPublic=1 AND tb.user_id='.$userId.' OR (tb.user_id=sh.user_id AND sh.isPublic=0 AND sh.user_id='.$userId.'))'
                /*OR条件为选择 自己点赞->自己的私密发布分享*/
                . ' ORDER BY tb.cTime DESC';/*按点赞时间逆序*/

        return $sql;
    }
}