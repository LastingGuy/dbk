<?php
/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 2017/1/22
 * Time: 19:42
 */

namespace Home\Common\DAO;


class SchoolDAO extends Models
{
    /**通过schoolid查询
     * @param $id
     * @return mixed
     */
    public function getSchoolInfoById($id)
    {
        $m_school = self::M_school();
        $data = $m_school->where("school_id='%s'",$id)->find();

        return $data;
    }

    /**通过学校名称查找
     * @param $name
     * @return mixed
     */
    public function getSchoolInfoByName($name)
    {
        $m_school = self::M_school();
        $data = $m_school->where("school_name='%s'",$name)->find();

        return $data;
    }

    public function getCities()
    {
        $m_school = self::M_school();
        $cities = $m_school->field('school_city as city')->where("display=1")->group('school_city')->order("count(*) desc")->select();
        return $cities;
    }

    public function getSchoolsAt($city=false)
    {
        if(!$city)
            return array();
        $m_school = self::M_school();
        $schools = $m_school->field('school_name as school')->where("school_city='%s' and display=1",$city)->select();
        return $schools;
    }

}