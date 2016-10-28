<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/10/24
 * Time: 21:35
 */
namespace Home\Common;

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
        print_r($content);

        $weixin_user = json_decode($content,true);
        if($weixin_user != null)
        {
            $openid = $weixin_user['openid'];
            session('access_token',$weixin_user['access_token']);
            $model = D("weixin_user");
            if($model->where("openid = '$openid'")->find()){
                session("weixin_user",$openid);
            }
            else{
                $data['openid'] = $openid;
                $data['register_time'] = date("Y-m-d H:i:s");
                if($model->data($data)->add()){
                    session("weixin_user",$openid);
                }
            }
            return true;
        }
        else
        {
            return false;
        }
    }

    //获取微信用户个人信息
    public function getUserInfo()
    {
        if(!session('?weixin_user'))
        {
            return false;
        }
        else
        {
            $openid = session('weixin_user');
            $access_token = session('access_token');

            $url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token.
            "&openid=".$openid.
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

}