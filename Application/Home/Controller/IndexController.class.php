<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller{
    public function index()
    {
        $this->display();
    }

    public function order()
    {
        $this->display();
    }

    #create the order of receiving mail
    public function newRecvOrder()
	{
        if(IS_POST)
        {
            $data = I('post.');
            $school = $data['school'];
            $city = $data['city'];
            $address = $data['address'];

            ///获得寝室id
            $DOR = D('DormitoryView'); //实例化寝室模型
            $dor = $DOR->field('dormitory_id')->where("school_name='$school' and school_city='$city' and dormitory_address = '$address'")->select();
            if(count($dor)>0)
            {
                $dor = $dor[0]['dormitory_id'];
                $data['dor'] = $dor;
            }
            else
            {
                $this->ajaxReturn('请填写正确的收货人地址');
            }

            //写入数据库
            $data['receiver_name'] = $data['rename'];
            $data['receiver_phone'] = $data['tel'];
            $data['dormitory_id'] = $data['dor'];
            $data['express_company'] = $data['express'];
            $data['express_code'] = $data['fetch_code'];
            $data['time'] = date('Y-m-d H:i:s');

            $pickup = D('pickup');
            if($pickup->create($data))
            {
                if($pickup->add($data))
                {
                    $this->ajaxReturn('提交成功'); 
                }
                else
                {
                    $this->ajaxReturn('提交失败');
                }
            
            }
            else
            {
                 
                //  exit($pickup->getError());
                 $this->ajaxReturn($pickup->getError());
                // echo 'wrong';
            }

        }
        else
        {
            $this->ajaxReturn('提交失败');
        }
    }

    public function newSendOrder()
    {
        if(IS_POST)
        {
            $send = D('send');
            $data = I('post.');

            $school = $data['school'];
            $city = $data['city'];
            $address = $data['address'];
            // $data['rename'] = '123';
            // $data['tel'] = 123;
            // $data['dor'] = 1;
            // $data['delivery'] = 'gum';
            // $data['remark'] = 'no';

            ///获得寝室id
            $DOR = D('DormitoryView'); //实例化寝室模型
            $dor = $DOR->field('dormitory_id')->where("school_name='$school' and school_city='$city' and dormitory_address = '$address'")->select();
            if(count($dor)>0)
            {
                $dor = $dor[0]['dormitory_id'];
                $data['dor'] = $dor;
            }
            else
            {
                $this->ajaxReturn('请填写正确的收货人地址');
            }

            ///写入数据库
            $data['sender_name'] = $data['rename'];
            $data['sender_phone'] = $data['tel'];
            $data['dormitory_id'] = $data['dor'];
            $data['sender_goods'] = $data['delivery'];
            $data['time'] = date('Y-m-d H:i:s');
            // var_dump($data);
            if($send->create($data))
            {
                if($send->add($data))
                {
                    print('提交成功');
                }
                else
                {
                    print('提交失败');
                }
            }
            else
            {
                print($send->getError());
            }
        }
        else
        {
           print('提交失败！');
        }
    }

}
?>