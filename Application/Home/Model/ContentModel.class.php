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
    获取所有收藏的分享、
    获取特定收藏的分享的评论和点赞、
    获取自己特定的分享的评论和点赞、
    获取自己所有的分享、
    获取特定收藏用户的分享、
    获取最新发布的分享

    */


    public function getShare($where, $field = [], $page = 1, $limit = 25, $isLock = false)
    {
        if(!$where){
            return false;
        }

        return $this->where($where)->field($field)->page($page, $limit)->lock($isLock)->select();
    }

    public function insertShare($userId, $text, $imgs, $isPublic)
    {

        $result = $this->add([
            'user_id'  => $userId,
            'text'     => $text,
            'imgs'     => $imgs,
            'isPublic' => $isPublic,
            'cTime'    => time(),
        ]);

        if($result === false){
            $this->error = '保存失败';

            return false;
        }

        return true;
    }

    public function delShare($shareId, $userId)
    {
        $this->startTrans();
        $where = [
            's_id'    => $shareId,
            'user_id' => $userId,
        ];

        $count = intval($this->countShare($where));
        if($count === 0){
            $this->rollback();
            $this->error = '权限验证失败';

            return false;
        }else{
            $result = $this->where(['s_id' => $shareId])->delete();
            // 删除评论
            $result1 = D('comment')->delShareComment($shareId);
            // 删除点赞
            $result2 = D('thumb')->delShareThumb($shareId);
            // 删除收藏
            $result3 = D('favshare')->delShareFavshare($shareId);

            if($result && $result1 && $result2 && $result3){
                $this->commit();

                return true;
            }else{
                $this->rollback();
                $this->error = "删除失败";

                return false;
            }
        }
    }

    public function editShare($where, $updateData)
    {
        if(!$where || !$updateData){
            return false;
        }

        return $this->where($where)->save($updateData);
    }

    public function countShare($where = [])
    {
        return $this->where($where)->count();
    }

    public function searchShare($q, $page, $limit)
    {
        $field = 's.s_id, s.user_id, s.text, s.imgs, s.cTime, u.username';
        $where = [
            's.text'     => [
                'like',
                $q,
            ],
            's.isPublic' => 1,
        ];
        $ret = [];
        $ret['allcount'] = $this->alias('s')
            ->join('left join mn_user u on s.user_id=u.user_id')
            ->$where($where)
            ->count();
        $ret['data'] = $this->alias('s')
            ->join('left join mn_user u on s.user_id=u.user_id')
            ->$where($where)
            ->field($field)
            ->page($page, $limit)
            ->select();

        return $ret;
    }
}