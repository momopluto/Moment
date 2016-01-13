<?php
namespace Admin\Controller;

use Think\Controller;

/**
 * 注册用户管理控制器
 */
class UserController extends BaseController
{

    /**
     * 所有注册用户列表
     * @return [type] [description]
     */
    public function lists()
    {
        $model = D('user');
        $sql = $model->getUser_sql();

        $count = $model->getUser_count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
        $show = $Page->show();// 分页显示输出
        $sql .= ' limit ' . $Page->firstRow . ',' . $Page->listRows;// 拼装分页语句
        $list = $model->query($sql);

        if(IS_AJAX){
            // AJAX请求时，则只返回分享内容的数组
            $this->ajaxReturn($rData);

            return;
        }

        $this->assign('list', $list);// 赋值数据集
        $this->assign('page', $show);// 赋值分页输出，可考虑同上json返回
        $this->display(); // 输出模板
    }

    /**
     * 禁止/开启用户的分享行为
     * @return [type] [description]
     */
    public function shareset()
    {
        if(IS_POST){
            $id = I('post.id');
            $model = D('user');
            $result = $model->changStatus($id);

            if($model->getError()['errcode']){
                $this->ajaxReturn($model->getError());
            }else{
                $ret = $model->getError();
                $ret['status'] = $result;
                $this->ajaxReturn($ret);
            }
        }else{
            $this->ajaxReturn([
                'errcode' => '400',
                'err'     => 'request invaild',
            ]);
        }
    }
}