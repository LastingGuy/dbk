<?php
/**
 * Created by PhpStorm.
 * User: nelson
 * Date: 2017/1/31
 * Time: 上午10:53
 */

namespace Home\Common;


class InvitationDAOImpl implements IInvitationDAO
{
    //获取用户邀请码
    public function getCode()
    {
        $openid = session('weixin_user');
        $model = M('invitation');
        $condition['openid'] = $openid;
        $code = $model->where($condition)->getField('invitation_code');
        if($code==null){
            do{
                $code = $this->buildCode();
            }while($model->where('invitation_code="'.$code.'"')->find()=='NULL');

            $data[invitation_code] = $code;
            $data[openid] = $openid;
            $model->add($data);
        }
        return $code;
    }

    //随机生成6位带字母数字的邀请码
    private function buildCode()
    {
        $arr = array_merge(range(0, 9), range('a', 'z'));

        $str = '';
        $arr_len = count($arr);
        for ($i = 0; $i < 6; $i++)
        {
            $rand = mt_rand(0, $arr_len-1);
            $str.=$arr[$rand];
        }

        return $str;
    }

    //验证邀请码
    public function checkInvitation($code)
    {
        $model = M('invitation');
        $condition['invitation_code'] = $code;
        $inviter = $model->where($condition)->getField('openid');
        if ($inviter != 'NULL'){
            $this->sentTimesCoupon(session('weixin_user'),1,$inviter);
            $this->sentTimesCoupon($inviter,0,null);
        } else {
            return '无效邀请码';
        }
    }

    //分发抵次优惠券
    private function sentTimesCoupon($openid,$status,$inviter){
        $data['openid'] = $openid;
        $date = date('ymd');
        $stamp = time() % 100000;
        $random = rand(100,999);
        $model = M('coupon');
        $data['coupon_code'] = $date.$stamp.$random;
        $data['type'] = 1;
        $data['inviter'] = $inviter;
        $data['status'] = $status;
        $model->add($data);
    }

    //验证优惠券,目前只认证type=1的抵次优惠券，验证成功返回1，反之返回0
    public function checkCoupon()
    {
        $openid = session('weixin_user');
        $model = M('coupon');
        $condition['openid'] = $openid;
        $condition['type'] = 1;
        $condition['status'] = 1;
        $res = $model->where($condition)->getField('coupon_code');
        if ($res!=null){
            return $res;
        }else{
            return 0;
        }
    }

    //启用优惠券
    public function enableCoupon($couponCode)
    {
        $model = M('coupon');
        $data['status'] = 1;
        $condition['coupon_code'] = $couponCode;
        $model->where($condition)->save($data);
    }

    //使用优惠券
    public function useCoupon($couponCode)
    {
        $model = M('coupon');
        $data['status'] = 2;
        $condition['coupon_code'] = $couponCode;
        $model->where($condition)->save($data);
    }
}