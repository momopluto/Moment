<?php
namespace Home\Model;

use Think\Model;

/**
 * mn_content表模型
 */
class ContentModel extends BaseModel
{
    protected $tableName = 'share';
    /**
     * 自动验证
     * @var array
     */
    protected $_validate = array(
        // array('verify','require','验证码必须！'), //默认情况下用正则进行验证
        // array('name','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
        // array('value',array(1,2,3),'值的范围不正确！',2,'in'), // 当值不为空的时候判断是否在一个范围内
        // array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
        // array('password','checkPwd','密码格式不正确',0,'function'), // 自定义函数验证密码格式
    );

    /*

    以下需要实现的Model方法

    获取所有分享的评论和点赞、
    获取特定分享的评论和点赞、
    #获取所有收藏的分享、
    获取特定收藏的分享的评论和点赞、
    获取自己特定的分享的评论和点赞、
    获取自己所有的分享、
    获取特定收藏用户的分享、
    获取最新发布的分享

    */

    // 统计userId共发布了xx条分享、收藏[关注]xx人、被xx人收藏[关注]
    // 收藏xx分享
    // 评论了xx条，被评论了xx条，点赞了xx个，收获了xx个点赞
    // xxxxx

/*
    public function getUserData($userId)
    {
        $where = ['user_id' => $userId];
        $favuserCount = D('favuser')->countFavuser($where);
        $shareCount = $this->countShare($where);
        $favshareCount = D('favshare')->countFavshare($where);

        return [
            'fav_user_count'  => $favuserCount,
            'fav_share_count' => $favshareCount,
            'share_count'     => $shareCount,
        ];
    }
    
    // ?? 内部分好页？
    public function getShareIndex($userId)
    {
        $userdata = $this->getUserData($userId);
        $p = I('param.p', 1);
        $count = $this->join('LEFT JOIN mn_user ON mn_share.user_id = mn_user.user_id')->where([
            'mn_share.isPublic' => 1,
            'mn_user.status'    => 1,
        ])->count();
        $oPage = new \Think\Page($count, 25);
        $show = $oPage->show();


        $list = $this->join('LEFT JOIN mn_user ON mn_share.user_id = mn_user.user_id')->where([
            'mn_share.isPublic' => 1,
            'mn_user.status'    => 1,
        ])->field([
            'mn_user.username',
            'mn_share.s_id',
            'mn_share.user_id',
            'mn_share.text',
            'mn_share.imgs',
            'mn_share.cTime',
            'mn_share.isPublic',
        ])->order('mn_share.cTime desc')->page($p, 25)->select();

        return [
            'userdata' => $userdata,
            'show'     => $show,
            'list'     => $list,
        ];
    }
*/
    /**
     * 获取最热的分享总数
     * @param integer $userId 用户id
     * @return integer 总数
     */
    public function getHotShare_count($userId){
        return $this->table($this->getHotShare_sql($userId) . ' tmp')->cache('count_hotShare', 1800)->count();// 缓存30分钟
    }

    /**
     * 获取最热的分享的
     * @param integer $userId 用户id
     * @return string sql语句
     */
    public function getHotShare_sql($userId){
        $sql = $this->alias('sh')
            ->join('LEFT JOIN mn_favshare fs ON sh.s_id=fs.s_id AND fs.owner_id=' . $userId)/*判断userId是否有收藏此分享*/
            ->join('LEFT JOIN mn_thumb th ON sh.s_id=th.s_id AND th.user_id=' . $userId)/*判断userId是否有点赞此分享*/
            ->field('sh.s_id,
                md5(sh.user_id) AS imgPath,
                sh.`text`,
                sh.imgs,
                FROM_UNIXTIME(sh.cTime,"%Y-%m-%d %H:%i:%s") AS cTime,
                sh.isPublic,
                sh.cmt_count,
                sh.tb_count,
                IF(fs.cTime,1,0) AS collected,
                IF(th.cTime,1,0) AS thumbed')/*collected为1代表已收藏，0为未收藏; thumbed为1代表点赞了,0为未点赞*/
            ->where('sh.cmt_count + sh.tb_count >= 500')
            ->order('sh.cTime DESC')
            ->buildsql();
        return $sql;
    }

    /**
     * 获取(userId)用户可查看的分享总数
     * 限制分享的发布时间为[一周内]
     * TODO ，耗时长，需缓存
     * @param integer $userId 用户id
     * @return integer 成功返回总数;失败返回false
     */
    public function getAllCanSeeShare_count($userId)
    {
        return $this->table($this->getAllCanSeeShare_sql($userId) . ' tmp')->count();
    }

    /**
     * 获取(userId)用户可查看的分享
     * 限制分享的发布时间为[一周内]
     * TODO ，耗时长，需缓存
     * @param integer $userId 用户id
     * @return boolean 成功返回sql语句;失败返回false
     */
    public function getAllCanSeeShare_sql($userId)
    {
        // 验证userId有效，账号启用
        if(!$this->checkUserStatus($userId)){
            return false;
        }

        $theday = strtotime('-0 day', NOW_TIME);// 方便测试
        $monday = strtotime('-1 week Monday', $theday);// 过去的周一
        //        $today_st = strtotime(date('Y-m-d 00:00:00',$theday));
        $today_ed = strtotime(date('Y-m-d 23:59:59', $theday));
        $sql = $this->alias('sh')
            ->join('LEFT JOIN mn_user ur ON sh.user_id=ur.user_id')
            ->join('LEFT JOIN mn_favshare fs ON sh.s_id=fs.s_id AND fs.owner_id=' . $userId)/*判断userId是否有收藏此分享*/
            ->join('LEFT JOIN mn_thumb th ON sh.s_id=th.s_id AND th.user_id=' . $userId)/*判断userId是否有点赞此分享*/
            ->field('sh.s_id,
                    sh.user_id,
                    md5(sh.user_id) AS imgPath,
                    sh.`text`,
                    sh.imgs,
                    FROM_UNIXTIME(sh.cTime,"%Y-%m-%d %H:%i:%s") AS cTime,
                    sh.isPublic,
                    sh.cmt_count,
                    sh.tb_count,
                    IF(fs.cTime,1,0) AS collected,
                    IF(th.cTime,1,0) AS thumbed')/*collected为1代表已收藏，0为未收藏; thumbed为1代表点赞了,0为未点赞*/
            // ->where('('
            //         . ' (sh.cTime BETWEEN ' . $monday . ' AND ' . $today_ed . ')'/*限制时间段*/
            //         . ' AND ('
            //             . ' (sh.isPublic=1 AND ur.`status`=1)'/*公开的分享，且分享所属用户状态为启用*/
            //             . ' OR (sh.isPublic=0 AND sh.user_id=' . $userId . ')'/*自己发布的私密分享*/
            //         . ' )'
            //     . ' )')
            ->where('( (ur.`status`=1'/*分享所属用户状态为启用*/
                    . ' AND (sh.cTime BETWEEN ' . $monday . ' AND ' . $today_ed . '))'/*限制时间段*/
                    . ' AND ('
                        . ' (sh.isPublic=1 AND sh.user_id<>' . $userId . ')'/*其它用户发布的公开的分享*/
                        . ' OR (sh.user_id=' . $userId . ')'/*自己发布的所有分享*/
                    . ' )'
                . ' )')
            ->order('sh.cTime DESC')
            ->buildSql();

        // 还要判断userId是否有收藏此分享

        return $sql;
    }

    /**
     * 获取(userId)用户的分享总数
     * @param integer $userId 用户id
     * @param boolean $self   是否为用户本人
     * @return integer 成功返回总数;失败返回false
     */
    public function getOnesShare_count($userId, $self = false)
    {
        return $this->table($this->getOnesShare_sql($userId, $self) . ' tmp')/*->cache('count_share',1800)*/
        ->count();
    }

    /**
     * 获取(userId)用户的分享
     * @param integer $userId 用户id
     * @param boolean $self   是否为用户本人
     * @return boolean 成功返回sql语句;失败返回false
     */
    public function getOnesShare_sql($userId, $self = false)
    {
        // 验证userId有效，账号启用
        if(!$this->checkUserStatus($userId)){
            return false;
        }

        // userId是否用户本身，不同的过滤条件
        $where = '(sh.user_id=' . $userId . ')';
        if(!$self){// 非用户本身
            $where .= ' AND (sh.isPublic=1)';// 只允许查看公开的
            $where = '( ' . $where . ' )';
        }

        $sql = $this->alias('sh')
            ->join('LEFT JOIN mn_favshare fs ON sh.s_id=fs.s_id AND fs.owner_id=' . $userId)/*判断userId是否有收藏此分享*/
            ->field('sh.s_id,
                    sh.user_id,
                    sh.`text`,
                    sh.imgs,
                    FROM_UNIXTIME(sh.cTime,"%Y-%m-%d %H:%i:%s") AS cTime,
                    sh.isPublic,
                    sh.cmt_count,
                    sh.tb_count,
                    IF(fs.cTime,1,0) AS collected')/*collected为1代表已收藏，0为未收藏*/
            ->where($where)
            ->order('sh.cTime DESC')
            ->buildSql();

        return $sql;
    }

    /**
     * 插入分享记录
     * [imgs字段为空]
     * @param integer $userId   用户id
     * @param string  $text     分享的文字内容
     * @param integer $isPublic 是否公开
     * @return boolean 成功返回s_id(分享内容的id);失败返回false
     */
    public function insertShare($userId, $text, $isPublic = 1)
    {
        // userId必合法，由Controller调用时保证
        // 验证text字数少于300中文字符
        $TEXT_LEN = 300;
        if(mb_strlen($text, 'UTF-8') > $TEXT_LEN){
            $err['errcode'] = 411;
            $err['errmsg'] = "text too long. must <= $TEXT_LEN";
            $this->error = $err;

            return false;
        }
        // imgs字段在此默认为空，需要插入获取s_id后再组装
        $imgs = '';
        // isPublic默认为1，公开

        // 插入数据库
        $result = $this->add([
            'user_id'  => $userId,
            'text'     => $text,
            'imgs'     => $imgs,
            'cTime'    => NOW_TIME,
            'isPublic' => $isPublic,
        ]);

        // 成功，返回插入得到的s_id
        // 失败，返回false + error原因
        if($result === false){
            $err['errcode'] = 400;
            $err['errmsg'] = "share failed";
            $this->error = $err;

            return false;
        }

        $err['errcode'] = 0;
        $err['errmsg'] = "ok";
        $err['sid'] = $result;// 插入后得到的主键s_id(分享内容的id)
        $this->error = $err;

        return $result;
    }


    // 删除share
    // 及该share下的评论和点赞
    /**
     * 删除分享记录
     * 一并删除comment、thumb、favshare
     * @param integer $shareId 分享内容的id
     * @param integer $userId  用户id
     * @return boolean
     */
    public function delShare($shareId, $userId)
    {
        // 验证shareId和userId对应的记录存在，即userId对shareId具备拥有权
        if(!M('share')->where('s_id = %d AND user_id = %d', array(
            $shareId,
            $userId,
        ))->count()
        ){
            $err['errcode'] = 404;
            $err['errmsg'] = "no match record";
            $this->error = $err;

            return false;
        }

        // 获取将被删除的share的记录
        $old_row = $this->where('s_id = %d AND user_id = %d', array(
            $shareId,
            $userId,
        ))->find();
        // 删除share
        $result = $this->where('s_id = %d AND user_id = %d', array(
            $shareId,
            $userId,
        ))->delete();
        //      (再删除share下的所有comment、所有thumb、所有收藏的share)!!!!按理不能删啊，直接让他们的comment或thumb找不到share然后提示已删除就好了喂！
        //      !!!!但是是是是是，虽然都是LEFT JOIN mn_share sh，但有where sh.isPublic = 1，这也无济于事啊，被删掉的share肯定找不着
        //      考虑都来就复杂了，目前暂且采用"全关联删除"吧
        if($result === false){
            $err['errcode'] = 400;
            $err['errmsg'] = "delete share failed";
            $this->error = $err;

            return false;
        }

        // 删除涉及到的图片
        if($old_row['imgs'] != ''){
            $imgsArr = explode(',', $old_row['imgs']);
            $dirname = md5($userId);
            foreach($imgsArr as $imgName){
                $imgPath = AS_PATH_IMG . "/$dirname/" . $imgName;
                unlink($imgPath);// 删除图片，不检查是否删除成功
            }
        }

        while(true){// 确保删除share的所有comment
            if(D('comment')->where('s_id = %d', $shareId)->delete() !== false){
                break;
            }
            echo '擦咧，comment没删除成功<br/>';
        }
        while(true){// 确保删除share的所有thumb
            if(D('thumb')->where('s_id = %d', $shareId)->delete() !== false){
                break;
            }
            echo '擦咧，thumb没删除成功<br/>';
        }
        while(true){// 确保删除share的所有favshare收藏
            if(D('favshare')->where('s_id = %d', $shareId)->delete() !== false){
                break;
            }
            echo '擦咧，favshare没删除成功<br/>';
        }

        // 以上都删除，则成功，返回true
        //      ..不允许失败
        $err['errcode'] = 0;
        $err['errmsg'] = "ok";
        $this->error = $err;

        return true;
    }

    /**
     * [按条件]返回分享的数目
     * @param mix $map 条件
     * @return integer 分享数目
     */
    public function countShare($map)
    {
        return intval($this->where($map)->count());
    }

    // 还没有搜索用户的功能

    /**
     * 获取搜索结果总数
     * @param integer $userId 发起搜索的用户的id
     * @param string $key 搜索关键字
     * @return integer 成功返回总数;失败返回false
     */
    public function getSearchShare_count($userId, $key)
    {
        return $this->table($this->getSearchShare_sql($userId, $key) . ' tmp')->count();
    }

    // 搜索share
    // 对于自己的分享，都可以查找
    // 对于他人的分享，只能查找公开的
    /**
     * 获取搜索结果
     * @param integer $userId 发起搜索的用户的id
     * @param string $key 搜索关键字
     * @return string sql语句
     */
    public function getSearchShare_sql($userId, $key)
    {
        // 验证userId有效，账号启用
        if(!$this->checkUserStatus($userId)){
            return false;
        }

        $sql = $this->alias('sh')
            ->join('LEFT JOIN mn_user ur ON sh.user_id=ur.user_id')
            ->join('LEFT JOIN mn_favshare fs ON sh.s_id=fs.s_id AND fs.owner_id=' . $userId)/*判断userId是否有收藏此分享*/
            ->join('LEFT JOIN mn_thumb th ON sh.s_id=th.s_id AND th.user_id=' . $userId)/*判断userId是否有点赞此分享*/
            ->field('sh.s_id,
                    sh.user_id,
                    md5(sh.user_id) AS imgPath,
                    sh.`text`,
                    sh.imgs,
                    FROM_UNIXTIME(sh.cTime,"%Y-%m-%d %H:%i:%s") AS cTime,
                    sh.isPublic,
                    sh.cmt_count,
                    sh.tb_count,
                    IF(fs.cTime,1,0) AS collected,
                    IF(th.cTime,1,0) AS thumbed')/*collected为1代表已收藏，0为未收藏; thumbed为1代表点赞了,0为未点赞*/
            ->where("(ur.`status`=1"/*分享所属用户状态为启用*/
                ." AND sh.`text` like '%{$key}%')"/*查询关键字*/
                 ." AND ("
                    ." ( sh.user_id={$userId} )"/*用户自己发布的所有分享*/
                    ." OR ( sh.user_id<>{$userId} AND sh.isPublic=1 )"/*其它用户发布的公开的分享*/
                 ." )")
            ->order('sh.cTime DESC')
            ->buildSql();

        return $sql;
    }


    // 删除分享下的所有点赞、所有评论，这些动作都只能发生在share被删除后的连带作用下。
    // 故，以上2个方法要和delShare放在一起，作为private内方法
    // 然而，实际上如果要删除所有的点赞和评论，则需要一定成功，返回error也无实际用途，不如直接调用
    /**
     * [废弃]删除(自己的)分享下的所有评论
     * @param integer $shareId 分享内容的id
     * @return boolean 成功返回被删的条数;失败返回false
     */
    private function delShareComment($shareId)
    {
        // userId必合法，由Controller调用时保证
        $result = M('comment')->where('s_id = %d', $shareId)->delete();
        if($result === false){
            $err['errcode'] = 400;
            $err['errmsg'] = "delete share's comment failed";
            $this->error = $err;

            return false;
        }

        //        $this->update_share_cmt_count($shareId);// 更新cmt_count
        $err['errcode'] = 0;
        $err['errmsg'] = "ok";
        $this->error = $err;

        return $result;// 返回被删的记录条数
    }

    /**
     * [废弃]删除(自己的)分享下的所有点赞
     * @param integer $shareId 分享内容的id
     * @return boolean 成功返回被删的条数;失败返回false
     */
    private function delShareThumb($shareId)
    {
        // userId必合法，由Controller调用时保证
        $result = M('thumb')->where('s_id = %d', $shareId)->delete();
        if($result === false){
            $err['errcode'] = 400;
            $err['errmsg'] = "delete share's thumb failed";
            $this->error = $err;

            return false;
        }

        //        $this->update_share_tb_count($shareId);// 更新tb_count
        $err['errcode'] = 0;
        $err['errmsg'] = "ok";
        $this->error = $err;

        return $result;// 返回被删的记录条数
    }

    /**
     * 获取最新一条分享的图片
     * @param            $userId
     * @param bool|false $self
     * @return bool
     */
    public function getPic($userId, $self = false)
    {
        if($self){
            $where = [
                'user_id'  => $userId,
                'isPublic' => 1,
                'imgs'     => [
                    'neq',
                    '',
                ],
            ];
        }else{
            $where = [
                'user_id' => $userId,
                'imgs'    => [
                    'neq',
                    '',
                ],
            ];
        }

        $result = $this->field('imgs')->where($where)->limit(1)->order('cTime desc')->select();

        if($result){
            $picArray = explode(',', $result[0]['imgs']);
            if(!end($picArray)){
                array_pop($picArray);
            }
        }else{
            $picArray = [];
        }


        $err['errcode'] = 0;
        $err['errmsg'] = 'ok';
        $this->error = $err;

        return $picArray;
    }

    /**
     * 获取所有图片的链接
     * @param            $userId
     * @param bool|false $self
     * @return bool
     */
    public function getAlbum($userId, $self = false)
    {
        // 判断
        if($self){
            $where = [
                'user_id' => $userId,
            ];
        }else{
            $where = [
                'user_id'  => $userId,
                'isPublic' => 1,
            ];
        }

        $result = $this->where($where)->field('imgs')->order('cTime desc')->select();

        $tmp = array_column($result, 'imgs');
        $picArray = [];
        foreach($tmp as $value){
            if($value){
                $temp = explode(',', $value);
                if(end($temp) == ''){
                    array_pop($temp);
                }
                $picArray = array_merge($picArray, $temp);
            }
        }

        $err['errcode'] = 0;
        $err['errmsg'] = 'ok';
        $this->error = $err;

        return $picArray;
    }

    /**
     * 更新share数据（目前仅用作更新imgs字段）
     * @param string $where    条件
     * @param array  $saveData 更新的数据
     * @return boolean
     */
    public function saveShare($where, $saveData = [])
    {
        if(!$where || !$saveData){
            $err['errcode'] = 404;
            $err['errmsg'] = 'not found';
            $this->error = $err;

            return false;
        }
        $result = $this->where($where)->data($saveData)->save();
        if($result !== false){
            $err['errcode'] = 0;
            $err['err'] = 'ok';
            $this->error = $err;

            return true;
        }else{
            $err['errcode'] = '400';
            $err['err'] = 'save failed';
            $this->error = $err;

            return false;
        }
    }

    /**
     * 通过分享的s_id得到该条分享数据
     * @param integer $id 分享内容的id
     * @return array 分享数据
     */
    public function getShareById($id)
    {
        return $this->field('s_id,user_id,md5(user_id) AS imgPath,text,imgs,cTime,isPublic,cmt_count,th_count')
            ->where(['s_id' => $id])
            ->select();
    }
}