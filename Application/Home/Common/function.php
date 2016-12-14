<?php

   //获得城市
    function getCitys_local()
    {
        $school = M('school');;
        $schools = $school->field('school_city as city')->where("school_id!=0")->group('school_city')->select();

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
        }
        else
        {
            $schoolModel = M('school');
            $schools = $schoolModel->field('school_name as school')->where("school_city='%s'",$city)->select();
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
            $dors = $dorMoel->field('dormitory_address as dor')->where("school_name='%s'",$school)->select();

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
            $school = $model->relation(true)->where("school_name='%s'",$school)->select();
            $express = $school[0]['express'];
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
            $data = $model->where("school_name='%s'",$school)->select();
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
        $data = $model->where("school_name='%s' and size='%s'",$school,$size)->find();
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
?>