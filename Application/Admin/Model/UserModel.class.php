<?php
namespace Admin\Model;

use Think\Model;

class UserModel extends BaseModel
{

    /**
     * 验证账号密码
     * @return [type] [description]
     */
    public function checkAuth()
    {
    }

    /**
     * 修改账号密码
     * ps: 不太确定是否要放在Model里
     * @return [type] [description]
     */
    public function chpwd()
    {
    }

    public function getUser_count()
    {
        return $this->table($this->getUser_sql() . ' tmp')->count();
    }

    public function getUser_sql()
    {
        $sql = $this->order('user_id DESC')->buildsql();

        return $sql;
    }

    public function changStatus($id)
    {
        $user = $this->where(['user_id' => $id])->find();
        $newStatus = $user['status'] == 0 ? 1 : 0;
        $result = $this->where(['user_id' => $id])->save(['status' => $newStatus]);
        if($result === false){
            $err['errcode'] = '400';
            $err['err'] = 'save failed';
        }else{
            $err['errcode'] = 0;
            $err['err'] = 'ok';
        }
        $this->error = $err;

        return $newStatus;
    }
}