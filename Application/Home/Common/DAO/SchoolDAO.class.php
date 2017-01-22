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
        $data = $m_school->where("school_id='?'",$id)->find();

        return $data;
    }

    /**通过学校名称查找
     * @param $name
     * @return mixed
     */
    public function getSchoolInfoByName($name)
    {
        $m_school = self::M_school();
        $data = $m_school->where("school_name='?'",$name)->find();

        return $data;
    }

}