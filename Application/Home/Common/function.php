<?php

   //获得城市
    function getCitys_local()
    {
        $school = M('school');;
        $schools = $school->field('school_city as city')->where("school_id!=0")->group('school_city')->select();
        // $options='';
        
        // if(count($schools)==0)
        // {
        //     $options="<option value=''>暂无城市信息</option>";
        //     $return = $this->getSchools_local('no');
        // }
        // else
        // {
        //     foreach($schools as $school)
        //     {
        //         $city = $school['school_city'];
        //         $options.="<option value='$city'>$city</option>";
        //     }
        //     $return = getSchools_local($schools[0]['school_city']);
        // }

        $return = getSchools_local($schools[0]['city']);
        $return['citys'] = $schools;

        return $return;
    }
    
    //获得$city 的 学校
    function getSchools_local($city)
    {
        if($city==false)
        {
            // $options="<option value=''>暂无学校信息</option>";
            $schools = array();
            $dorinfo = getDormitory_local(false);
        }
        else
        {
            $schoolModel = M('school');
            $schools = $schoolModel->field('school_name as school')->where("school_city='%s'",$city)->select();
            // $options = "";
            // if(count($schools)==0)
            // {
            //     $options="<option value=''>暂无学校信息</option>";
            //     $dorinfo = getDormitory_local(false);
            // }
            // else
            // {
            //     foreach($schools as $school)
            //     {
            //         $name = $school['school_name'];
            //         $options.="<option value='$name'>$name</option>";
            //     }
            //     $dorinfo = getDormitory_local($schools[0]['school_name']);
            // }
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
            // $options="<option value=''>暂无寝室信息</option>";
            $dors = array();
        }
        else
        {
            $dorMoel = D('dormitoryView');
            $dors = $dorMoel->field('dormitory_address as dor')->where("school_name='%s'",$school)->select();
            // if(count($dors)==0)
            // {
            //     $options="<option value=''>暂无寝室信息</option>";
            // }
            // else
            // {
            //     foreach($dors as $dor)
            //     {
            //         $addr = $dor['dormitory_address'];
            //         $options.="<option value='$addr'>$addr</option>";
            //     }
            // }
        }

        // return $options;
        return $dors;
    }


    //获得快递点信息
    function getExpress_local($school)
    {
         if($school==false)
        {
            // $options="<option value=''>暂无寝室信息</option>";
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