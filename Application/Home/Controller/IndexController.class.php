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

    public function newOrder()
	{
        if(IS_POST)
        {
            
            $pickup = D('pickup');
            if($pickup->create())
            {
                if($pickup->add())
                {
                    $this->ajaxReturn('提交成功!'); 
                }
                else
                {
                    // echo $pickup->getError();
                    // $this->ajaxReturn('提交失败');
                    exit($pickup->getError());
                }
            }
            else
            {
                 // echo $pickup->getError();
                 exit($pickup->getError());
                 $this->ajaxReturn('提交失败');
            }

        }
        else
        {
            $this->ajaxReturn('提交失败');
        }
    }
}
?>