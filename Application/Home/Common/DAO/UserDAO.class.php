<?php
/**
 * Created by PhpStorm.
 * User: ben
 * Date: 25/01/2017
 * Time: 14:04
 */

namespace Home\Common\DAO;


use Think\Exception;
use Think\Model;

class UserDAO extends Models
{
    /**微信注册
     * @param $openid
     * @return bool
     */
    public function addUser($openid)
    {
        $model = self::M_user();
        $data['userid'] = self::getUUID();
        $data['openid'] = $openid;
        $data['register_time'] = date("Y-m-d H:i:s");
       try
        {
            $model->data($data)->add();
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }

    /**判断用户是否已存在
     * @param $openid
     * @return bool
     */
    public function isUserExist($openid)
    {
        $model = self::M_user();
        if($model->where("openid = '$openid'")->find()){
            return true;
        }
        else
            return false;
    }

    /**生成一个uuid
     * @return mixed
     */
    private static function getUUID()
    {
        $model = new Model();
        $data = $model->query("select uuid()");
        return $data[0]['uuid()'];
    }

    /**查询openid
     * @return mixed
     */
    public function getOpenid(){
        return session("weixin_user");
    }
}