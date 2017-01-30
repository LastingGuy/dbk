<?php
/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 2017/1/22
 * Time: 18:47
 */

namespace Home\Common\DAO;


class Models
{
    private static $_M_pickup = false;
    public static function M_pickup()
    {
        if(self::$_M_pickup===false)
            self::$_M_pickup = M('pickup');

        return self::$_M_pickup;
    }

    private static $_M_school = false;
    public static function M_school()
    {
        if(self::$_M_school===false)
            self::$_M_school = M('school');

        return self::$_M_school;
    }

    private static $_M_dormitory = false;
    public static function M_dormitory()
    {
        if(self::$_M_dormitory===false)
            self::$_M_dormitory = M('dormitory');

        return self::$_M_dormitory;
    }

    private static $_M_fee = false;
    public static function M_fee()
    {
        if(self::$_M_fee===false)
            self::$_M_fee = M('fee');

        return self::$_M_fee;
    }

    private static $_M_express_company = false;
    public static function M_express_company()
    {
        if(self::$_M_express_company===false)
            self::$_M_express_company = M('express_company');

        return self::$_M_express_company;
    }

    private static $_M_user =false;
    public static function M_user()
    {
        if(self::$_M_user===false)
            self::$_M_user= M('user');
        return self::$_M_user;
    }

    private static $_M_pickup_pay =false;
    public static function M_pickup_pay()
    {
        if(self::$_M_pickup_pay===false)
            self::$_M_pickup_pay= M('pickup_pay');
        return self::$_M_pickup_pay;
    }

    private static $_M_pickup_view =false;
    public static function M_pickup_view()
    {
        if(self::$_M_pickup_view===false)
            self::$_M_pickup_view= M('pickup_view');
        return self::$_M_pickup_view;
    }
}