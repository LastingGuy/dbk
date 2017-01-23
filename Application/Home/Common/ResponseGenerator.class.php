<?php
/*
    Author: Wang Jinglu
    Date:2016/11/21
    Descrition:
        return the response like:
        {
            "action":<action>,
            "success":<isSuccess>,
            "code":<errorCode>,
            "msg":<msg>,
            "body":<body>
        }
        action : the name of the action,for example: if you want to login,the action is "login";
        success : whether the request is be executed successfully;
        code : the error code;
        msg : message;
        body : addition
*/

namespace Home\Common;

class ResponseGenerator
{
    private $action;
    private $success;
    private $code;
    private $msg;
    private $body;

    public function __construct($action="",$success=false,$code=-1,$msg="",$body=array())
    {
        $this->action = $action;
        $this->success = $success;
        $this->code = $code;
        $this->msg = $msg;
        $this->body = $body;
    }

    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    public function setSuccess($success)
    {
        $this->success = $success;
        return $this;
    }

    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function setMsg($msg)
    {
        $this->msg = $msg;
        return $this;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getSuccess()
    {
        return $this->success;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getMsg()
    {
        return $this->msg;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function generate()
    {
        $data = array
        (
            'action'=>$this->action,
            'success'=>$this->success,
            'code'=>$this->code,
            'msg'=>$this->msg,
            'body'=>$this->body
        );

        return $data;
    }


    public static function NOTSIGN($action)
    {
        $r = new ResponseGenerator($action,false,2,'NOTSIGN');
        return $r;
    }
}
?>