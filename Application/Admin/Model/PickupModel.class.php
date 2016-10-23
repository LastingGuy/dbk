<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/10/23
 * Time: 11:52
 */
namespace Admin\Model;
use Think\Model;
class PickupModel extends Model
{
    protected $_map = array(
        "pickup" => 'pickup_id',
        "user" => 'user_id',
        'name' => 'receiver_name',
        'tel' => 'receiver_phone',
        'dor' => 'dormitory_id',
        'express' => 'express_company',
        'sms' => 'express_sms',
        'type' => 'express_type',
        'fetch_code' => 'express_code',
        'remarks' => 'remarks',
        'price' => 'price',
        'time' => 'time',
        'status' => 'express_status'
    );
}