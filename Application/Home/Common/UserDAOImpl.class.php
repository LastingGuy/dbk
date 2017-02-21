<?php
/**
 * Created by PhpStorm.
 * User: Zheng chengyang
 * Date: 2016/10/24
 * Time: 21:35
 * Discription:
 *  用户DAO
 * LOG：
 *    2017/1/22：数据库变更，
 */
namespace Home\Common;

use Think\Model;

class UserDAOImpl implements IUserDAO{

    //用户登录验证
    public function login()
    {

        $weixin_code = I("get.code");
        $weixin_token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?".
        "appid=wx9b82294bfe34589e&".
        "secret=20af5faa385f8bce23e8920dfbbb545b&".
        "code=$weixin_code&".
        "grant_type=authorization_code";
        $content = file_get_contents($weixin_token_url);

        $weixin_user = json_decode($content,true);
        if($weixin_user != null)
        {
            $openid = $weixin_user['openid'];
            session('access_token',$weixin_user['access_token']);
            $model = D("user");

            if($model->where("openid = '$openid'")->find()){
                session("userid",'');
            }
            else{
                $data['openid'] = $openid;
                $data['register_time'] = date("Y-m-d H:i:s");
                $data['userid'] = self::getUUID();
                if($model->create($data)->add()){
                    session("userid",$data['userid']);
                }
            }
            return true;
        }
        else
        {
            return false;
        }
    }

    /**获取微信用户个人信息
     * @return bool
     */
    public function getUserInfo()
    {
        if(!session('?userid'))
        {
            return false;
        }
        else
        {
            $userid = session('userid');
            $access_token = session('access_token');

            $url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token.
            "&openid=".$userid.
            "&lang=zh_CN";
            $content = file_get_contents($url);
            if($info = json_decode($content,true))
            {
                session('user_name',$info['nickname']);
                session('headimgurl',$info['headimgurl']);
                return true;
            }
            else
            {
                return false;
            }
        }
    }


    /**获得默认收获地址
     * @param $id 用户id
     * @return ResponseGenerator
     */
    public function getUserDefaultInfo($id)
    {
        $response = new ResponseGenerator('getUserDefaultInfo');
        $userid = $id;
        $model = M('defaultinfo');
        $data = $model->where("userid='$userid'")->find();
        if($data)
        {
            $body = array(
                'city'=>$data['default_city'],
                'school'=>$data['default_school'],
                'dor'=>$data['default_dormitory'],
                'phone'=>$data['default_phone'],
                'name'=>$data['default_name']
            );

            $response->setSuccess(true)->setBody($body)->setCode(1)->setMsg('获得默认地址成功');

            //检查学校是否隐藏
            $r = isSchoolDisplay($body['school']);
            if($r!==true)
            {
                $response->setCode(22)->setMsg($r);
                return $response;
            }

            //检查寝室是否已弃用
            $r = isDorOnline($body['dor']);
            if($r!==true)
            {
                $response->setCode(23)->setMsg($r);
                return $response;
            }

            return $response;
        }
        else
        {
            $response->setCode(21)->setMsg("用户无默认地址");
            return $response;
        }
    }

    /**新建UUID
     * @return mixed
     */
    private static function getUUID()
    {
        $model = new Model();
        $data = $model->query("select uuid()");
        return $data[0]['uuid()'];
    }

}