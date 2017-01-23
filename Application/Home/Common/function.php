<?php

/**登录检测
 * @return bool
 */
function notSign()
{
    return !session('?userid');
}

/**判断联系号码是否合法
 * @param $mobile
 * @return bool
 */
function isMobile($mobile)
{
    $is_tel = preg_match("/^([0-9]{3,4}-)?[0-9]{7,8}$/",$mobile)?true:false;
    if($is_tel)
    {
        return true;
    }

    if (!is_numeric($mobile))
    {
        return false;
    }
    $is_mobile =preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
    return $is_mobile;
}



   //获得城市
    function getCitys_local()
    {
        $school = M('school');;
        $schools = $school->field('school_city as city')->where("school_id!=0 and display=1")->group('school_city')->select();

        $return = getSchools_local($schools[0]['city']);
        $return['citys'] = $schools;

        return $return;
    }
    
    //获得$city 的 学校
    function getSchools_local($city)
    {
        if($city==false)
        {
            $schools = array();
            $dorinfo = getDormitory_local(false);
            $express = getExpress_local(false);
        }
        else
        {
            $schoolModel = M('school');
            $schools = $schoolModel->field('school_name as school')->where("school_city='%s' and display=1",$city)->select();
            $dorinfo = getDormitory_local($schools[0]['school']);   
            $express = getExpress_local($schools[0]['school']);
            $typesOfExpress = getExpressSize_local($schools[0]['school']);
        }

        $return = array
        (
            'schools'=>$schools,
            'dors'=>$dorinfo,
            'express'=>$express,
            'typesOfExpress'=>$typesOfExpress
        );
        return $return;
    }
    
    //获得$school的寝室信息
    function getDormitory_local($school)
    {
        if($school==false)
        {
            $dors = array();
        }
        else
        {
            $dorMoel = D('dormitoryView');
            $dors = $dorMoel->field('dormitory_address as dor')->where("school_name='%s'and dormitory.online=1 and school.display=1",$school)->select();

        }
        return $dors;
    }


    //获得快递点信息
    function getExpress_local($school)
    {
         if($school==false)
        {
            $express = array();
        }
        else
        {
            $model = D('express');
            $school = $model->relation(true)->where("school_name='%s' and display=1",$school)->select();
            $express = array();
            if(count($school)>0)
            {
                $i = 0;
                foreach($school[0]['express'] as $e)
                {
                    if($e['online']==1)
                    {
                        $express[$i] = $e;
                        $i++;
                    }
                }
            }
        }


        return $express;
    }

    //获得快递大小信息
    function getExpressSize_local($school)
    {
        if($school==false)
        {
            $types = array();
        }
        else
        {
            $model = D('ExpresspriceView');
            $data = $model->where("school_name='%s' and  fee.online=1",$school)->select();
            $types = array();
            foreach($data as $k => $size)
            {
                $types[$k] = array
                (
                    'size'=>$size['size'],
                    'description'=>$size['description']
                );
            }
        }
        return $types;
    }

    //获得价格
    // 参数：
    //      $school:学校名称
    //      $size:快递类型 
    //返回 
    //      -100 无效订单 
    function getPrice($school,$size,$all = false)
    {
        $model = D('ExpresspriceView');
        // var_dump($model->select());
        $data = $model->where("school_name='%s' and size='%s' and fee.online=1",$school,$size)->find();
        // var_dump($data);
        if($data==false)
        {
            return -100;
        }
        else
        {
            if(!$all)
            {
                return $data['price'];
            }
            else
            {
                return $data;
            }
        }

    }

    /**检查学校是否下线
     * @param $schoolName
     * @return bool|string
     */
    function isSchoolOnline($schoolName)
    {
        $model = M('school');
        $data = $model->where("school_name='%s'",$schoolName)->find();
        if($data)
        {
            if($data['online']==1)
            {
                return true;
            }
            else
            {
                return $data['offline_msg'];
            }
        }
        else
        {
            return '此学校已经下线';
        }

    }



    function isSchoolDisplay($schoolName)
    {
        $model = M('school');
        $data = $model->where("school_name='%s'",$schoolName)->find();
        if($data)
        {
            if($data['display']==1)
            {
                return true;
            }
            else
            {
                return $data['offline_msg'];
            }
        }
        else
        {
            return '此学校已经下线';
        }

    }

    /**检查寝室是否已弃用
     * @param $dor
     * @return bool|string
     */
    function isDorOnline($dor)
    {
        $model = M('dormitory');
        $data = $model->where("dormitory_address='%s'",$dor)->find();
        if($data)
        {
            if($data['online']==1)
            {
                return true;
            }
            else
            {
                return $data['offline_msg'];
            }
        }
        else
        {
            return '此学校已经下线';
        }
    }
?>