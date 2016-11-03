<?php

   //获得城市
    function getCitys_local()
    {
        $school = M('school');;
        $schools = $school->field('school_city')->group('school_city')->select();
        $options='';
        
        if(count($schools)==0)
        {
            $options="<option value=''>暂无城市信息</option>";
            $return = $this->getSchools_local('no');
        }
        else
        {
            foreach($schools as $school)
            {
                $city = $school['school_city'];
                $options.="<option value='$city'>$city</option>";
                $return = getSchools_local($schools[0]['school_city']);
            }
        }
        
        $return['city'] = $options;

        return $return;
    }
    
    //获得$city 的 学校
    function getSchools_local($city)
    {
        if($city==false)
        {
            $options="<option value=''>暂无学校信息</option>";
            $dorinfo = getDormitory_local(false);
        }
        else
        {
            $schoolModel = M('school');
            $schools = $schoolModel->field('school_name')->where("school_city='%s'",$city)->select();
            $options = "";
            if(count($schools)==0)
            {
                $options="<option value=''>暂无学校信息</option>";
                $dorinfo = getDormitory_local(false);
            }
            else
            {
                foreach($schools as $school)
                {
                    $name = $school['school_name'];
                    $options.="<option value='$name'>$name</option>";
                }
                $dorinfo = getDormitory_local($schools[0]['school_name']);
            }         
        }

        $return = array
        (
            'school'=>$options,
            'dor'=>$dorinfo
        );
        return $return;
    }
    
    //获得$school的寝室信息
    function getDormitory_local($school)
    {
        if($school==false)
        {
            $options="<option value=''>暂无寝室信息</option>";
        }
        else
        {
            $dorMoel = D('dormitoryView');
            $dors = $dorMoel->where("school_name='%s'",$school)->select();
            if(count($dors)==0)
            {
                $options="<option value=''>暂无寝室信息</option>";
            }
            else
            {
                foreach($dors as $dor)
                {
                    $addr = $dor['dormitory_address'];
                    $options.="<option value='$addr'>$addr</option>";
                }
            }
        }

        return $options;
    }
?>