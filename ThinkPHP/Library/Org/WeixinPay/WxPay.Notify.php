<?php
/**
 * 
 * 回调基础类
 * @author widyhu
 *
 */
class WxPayNotify extends WxPayNotifyReply
{
	/**
	 * 
	 * 回调入口
	 * @param bool $needSign  是否需要签名输出
	 */
	final public function Handle($needSign = true)
	{
		$msg = "OK";
        \Think\Log::write('handle','WARN');
		//当返回false的时候，表示notify中调用NotifyCallBack回调失败获取签名校验失败，此时直接回复失败
		$result = WxpayApi::notify(array($this, 'NotifyCallBack'), $msg);
		if($result == false){
            \Think\Log::write('fail','WARN');
			$this->SetReturn_code("FAIL");
			$this->SetReturn_msg($msg);
			$this->ReplyNotify(false);
			return;
		} else {
            \Think\Log::write('success','WARN');
			//该分支在成功回调到NotifyCallBack方法，处理完成之后流程
			$this->SetReturn_code("SUCCESS");
			$this->SetReturn_msg("OK");
		}
		$this->ReplyNotify($needSign);
	}
	
	/**
	 * 
	 * 回调方法入口，子类可重写该方法
	 * 注意：
	 * 1、微信回调超时时间为2s，建议用户使用异步处理流程，确认成功之后立刻回复微信服务器
	 * 2、微信服务器在调用失败或者接到回包为非确认包的时候，会发起重试，需确保你的回调是可以重入
	 * @param array $data 回调解释出的参数
	 * @param string $msg 如果回调处理失败，可以将错误信息输出到该方法
	 * @return true回调出来完成不需要继续回调，false回调处理未完成需要继续回调
	 */
	public function NotifyProcess($data, &$msg)
	{
        \Think\Log::write('通知','WARN');
        //TODO 用户基础该类之后需要重写该方法，成功的时候返回true，失败返回false
        if(!array_key_exists("transaction_id", $data)){
            $msg = "输入参数不正确";
            \Think\Log::write('测试日志信息，输入参数不正确','WARN');
            return false;
        }
        //查询订单，判断订单真实性
        if(!$this->Queryorder($data["transaction_id"])){
            $msg = "订单查询失败";
            \Think\Log::write('测试日志信息，订单查询失败','WARN');
            return false;
        }

        //查看微信支付订单是否在表中，不在直接返回false
        $model = M("weixin_pay");
        $find['trade_no'] = $data['out_trade_no'];
        \Think\Log::write($find['trade_no'],'WARN');
        if($row = $model->where("trade_no='%s'",$find['trade_no'])->find()){
            \Think\Log::write('测试日志信息，找到支付订单','WARN');
            //找到之后查看是否已经验证过
            if($row['pay_status'] == 0){
                \Think\Log::write('测试日志信息，支付订单未验证','WARN');
                $row['pay_status'] = 1;
                $row['transaction_id'] = $data['transaction_id'];
                $row['time_end'] = $data['time_end'];
                $model->save($row);

                if($row['pay_type']==1){
                    //代拿订单更新， 变成已支付
                    \Think\Log::write('代拿订单更新， 变成已支付','WARN');
                    $pickup['pickup_id'] = $row['order_id'];
                    $pickup['express_status'] = 2;
                    $pickup['pay_time'] = date('Y-m-d H:i:s');
                    $model = M("pickup");
                    $model->save($pickup);
                }
                else if($row['pay_type']==2){
                    //代寄订单更新， 变成已支付
                    \Think\Log::write('代拿订单更新， 变成已支付','WARN');
                    $send['send_id'] = $row['order_id'];
                    $send['sender_status'] = 2;
                    $model = M("send");
                    $model->save($send);
                }

            }
            return true;
        }
        else{
            \Think\Log::write('测试日志信息，未找到支付订单','WARN');
            return false;
        }
		return true;
	}
	
	/**
	 * 
	 * notify回调方法，该方法中需要赋值需要输出的参数,不可重写
	 * @param array $data
	 * @return true回调出来完成不需要继续回调，false回调处理未完成需要继续回调
	 */
	final public function NotifyCallBack($data)
	{
		$msg = "OK";
		$result = $this->NotifyProcess($data, $msg);
		
		if($result == true){
			$this->SetReturn_code("SUCCESS");
			$this->SetReturn_msg("OK");
		} else {
			$this->SetReturn_code("FAIL");
			$this->SetReturn_msg($msg);
		}
		return $result;
	}
	
	/**
	 * 
	 * 回复通知
	 * @param bool $needSign 是否需要签名输出
	 */
	final private function ReplyNotify($needSign = true)
	{
		//如果需要签名
		if($needSign == true && 
			$this->GetReturn_code($return_code) == "SUCCESS")
		{
			$this->SetSign();
		}
		WxpayApi::replyNotify($this->ToXml());
	}
}