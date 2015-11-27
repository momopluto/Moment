<?php
/**
 * Home模块公共函数
 */

/**
 * 获取(targetId的) 关注.粉丝.分享 总数
 * @param integer $targetId 目标id
 * @param boolean $self     是否用户本人
 * @return array 结果数组
 */
function getUser_FocusFanShare_Count($targetId, $self = false)
{
    $data['focus'] = D('Favuser')->getFavusers_count($targetId, $self);
    $data['fans'] = D('Favuser')->getFans_count($targetId, $self);
    $data['share'] = D('Content')->getOnesShare_count($targetId, $self);

    return $data;
}

/**
 * 创建目录
 * @param $path
 * @return bool
 */
function checkPathOrCreate($path)
{
    if(file_exists($path)){
        return;
    }else{
        mkdir($path, 0777, true);
    }
}

?>