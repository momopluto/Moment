<?php
namespace Admin\Model;

use Think\Model;

class ManagerModel extends BaseModel
{
    /**
     * 验证账号密码
     * @param $username
     * @param $pwd
     * @return bool|mixed
     */
    public function checkAuth($username, $pwd)
    {
        $manager = $this->where(['mngname' => $username])->find();
        if(!$manager){
            $err['errcode'] = '404';
            $err['err'] = '用户名不存在';
            $this->error = $err;

            return false;
        }
        if($manager['mng_pwd'] === $pwd){
            return $manager;
        }else{
            $err['errcode'] = '400';
            $err['err'] = '密码错误';
            $this->error = $err;

            return false;
        }
    }

    /**
     * 修改账号密码
     * @param $username
     * @param $oldPassword
     * @param $newPassword
     */
    public function changePassword($username, $oldPassword, $newPassword)
    {
        $manager = $this->where(['mngname' => $username])->find();
        if(!$manager){
            $err['errcode'] = '404';
            $err['err'] = '用户名不存在';
            $this->error = $err;

            return false;
        }else{
            if($manager['mng_pwd'] === $oldPassword){
                $result = $this->where(['mngname' => $manager['mngname']])->data(['mng_pwd' => $newPassword])->save();
                if($result === false){
                    $err['errcode'] = '400';
                    $err['err'] = '数据库操作失败';
                    $this->error = $err;

                    return false;
                }else{
                    $err['errcode'] = 0;
                    $err['err'] = 'ok';
                    $this->error = $err;
                }
            }else{
                $err['errcode'] = '400';
                $err['err'] = '密码错误';
                $this->error = $err;

                return false;
            }
        }
    }

    public function updateLoginData($id)
    {
        $this->where(['mng_id' => $id])->save([
            'last_login_ip'   => get_client_ip(),
            'last_login_time' => datetime(),
        ]);
    }
}