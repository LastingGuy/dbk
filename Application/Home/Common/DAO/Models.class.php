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
}