<?php
/**
 * Created by PhpStorm.
 * User: ben
 * Date: 25/01/2017
 * Time: 13:22
 */

namespace Home\Controller;


use Home\Common\ResponseGenerator;
use Home\Common\Service\AccountService;
use Think\Controller;

class SignController extends Controller
{
    public function wechatlogin()
    {
        //错误码
        $code = array(
            'success'=>11,
            'fail'=>10
        );

        $response = new ResponseGenerator("login",false,$code['fail'],'FailToLogin');

        $weixin_code = I("get.code");
        if(!$weixin_code)
        {
            $this->ajaxReturn($response->generate());
        }

        $weixin_token_url =
            "https://api.weixin.qq.com/sns/oauth2/access_token?".
            "appid=wx9b82294bfe34589e&".
            "secret=20af5faa385f8bce23e8920dfbbb545b&".
            "code=$weixin_code&".
            "grant_type=authorization_code";

        $content = file_get_contents($weixin_token_url);

        $weixin_user = json_decode($content,true);
        $openid = $weixin_user['openid'];


        $accountSerivice = new AccountService();
        if($accountSerivice->WechatLogin($openid))
        {
            $response->setSuccess(true)->setCode($code['success'])->setMsg("SuccessToLogin");
            $this->ajaxReturn($response->generate());
        }
        else
            $this->ajaxReturn($response->generate());

    }























}