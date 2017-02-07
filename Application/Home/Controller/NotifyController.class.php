<?php
/**
 * Created by PhpStorm.
 * User: ben
 * Date: 06/02/2017
 * Time: 17:26
 */

namespace Home\Controller;


use Home\Common\Util\PickupPayNotify;
use Think\Controller;

class NotifyController extends Controller
{
    public function pickupNotify()
    {
        $notify = new PickupPayNotify();
        $notify->Handle();
    }
}