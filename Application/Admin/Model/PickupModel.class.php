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
    protected $_link = array(
        'dormitory'=>array(
            'mapping_type'      => self::BELONGS_TO,
            'class_name'        => 'dormitory',
            'foreign_key'       => 'dormitory_id',
            'mapping_name'      => 'dormitory',
            ),
        'school'=>array(
            'mapping_type'      => self::BELONGS_TO,
            'class_name'        => 'school',
            'foreign_key'       => 'school_id',
            'mapping_name'      => 'school',
        ),
        );
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