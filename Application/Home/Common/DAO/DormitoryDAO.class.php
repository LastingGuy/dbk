<?php
/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 2017/1/22
 * Time: 20:02
 */

namespace Home\Common\DAO;


class DormitoryDAO extends Models
{
    /**通过id查询寝室
     * @param $id
     * @return mixed
     */
    public function getDormitoryInfoById($id)
    {
        $model = self::M_dormitory();
        $data = $model->where("dormitory_id = '%s'",$id)->find();
        return $data;
    }

    /**通过地址查询寝室
     * @param $schoolid
     * @param $address
     * @return mixed
     */
    public function getDormitoryInfoByAddress($schoolid,$address)
    {
        $model = self::M_dormitory();
        $data = $model->where("school_id = '%s' and dormitory_address = '%s'",$schoolid,$address)->find();
        return $data;
    }


}