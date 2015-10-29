<?php
namespace Home\Model;
use Think\Model;

/**
 * mn_user表模型
 */
class UserModel extends BaseModel {

	/**
	 * 自动验证
	 * @var array
	 */
	protected $_validate = array(
		 array('username','','此账号已经存在！',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT), // 在新增的时候验证name字段是否唯一
		 array('user_pwd','_checkPwd','密码格式不正确！',self::EXISTS_VALIDATE,'callback'), // 自定义函数验证密码格式
	);
        
        /**
         * 校验密码格式
         * 暂未确定密码规则，直接返回true
         * @access protected
         * @param string $password 密码
         * @return boolean
         */
        protected function _checkPwd($password){
            /*$len = strlen($password);
            if ($len < 6 || $len > 18){
                return false;
            }*/
            return true;
        }
        
        /**
         * 自动完成
         * @var type 
         */
        protected $_auto = array(
            array('user_pwd', 'md5', self::MODEL_BOTH, 'function'), // 对password字段在新增和编辑的时候使用md5函数处理。
            // 传过来的password已经由前端md5加密过1次，此处再进行1次加密。双重md5加密
            
            array('reg_time', 'datetime', self::MODEL_INSERT, 'function'), // 新增的时候把reg_time字段设置为当前时间
            array('last_login_ip', 'get_client_ip', self::MODEL_INSERT, 'function'), // 新增的时候把last_login_ip字段设置为注册ip
            array('last_login_time', 'datetime', self::MODEL_INSERT, 'function'), // 新增的时候把last_login_time字段设置为当前时间
            array('status', '1'), // 新增的时候把status字段设置为1
        );

        /**
	 * 验证账号密码
         * @access public
         * @param string $username 用户名
         * @param string $password 密码，已经由前端md5加密1次，需要加密1次与数据库密码比较
	 * @return mix/boolean 成功返回用户数据/失败返回false
	 */
	public function checkAuth($username,$password){
            $map['username'] = $username;
            if (($rst = $this->where($map)->find()) != false){
                if ($rst['user_pwd'] === md5($password)){
                    // 通过验证
                    if ($rst['status'] == 0){
                        $this->error = '账号被封停！请联系管理员！';
                        return false;
                    }
                    unset($rst['user_pwd']);// 删除密码
                    return $rst;// 返回用户账号数据
                }  else {
                    $this->error = '密码错误';
                    return false;
                }
            }  else {
                $this->error = '用户名不存在';
                return false;
            }
	}
        
        /**
         * 更新用户的最后一次登录ip和时间
         * @access public
         * @param integer $user_id 用户id
         */
        public function updateLoginData($user_id){
            $map['user_id']          = $user_id;
            $map['last_login_ip']    = get_client_ip();
            $map['last_login_time']  = datetime();
            $this->save($map);
        }

        /**
	 * 修改账号密码
         * @param integer $user_id 用户id
         * @param string $old_pwd 原密码
         * @param string $new_pwd 新密码
	 * @return boolean 成功返回true;失败返回false，可获取错误信息
	 */
	public function chpwd($user_id,$old_pwd,$new_pwd){
            $old = $this->field('user_id,username,user_pwd')->find($user_id);
            if ($old['user_pwd'] === md5($old_pwd)){
                $data['user_id'] = $user_id;
                $data['user_pwd'] = $new_pwd;
                
                if ($this->save($data)){
                    return true;
                }  else {
                    $this->error = '写数据库失败';
                    return false;
                }
            }  else {
                $this->error = '原密码不正确';
                return false;
            }
	}

}