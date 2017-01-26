<?php
/**
 * Created by PhpStorm.
 * User: ben
 * Date: 25/01/2017
 * Time: 14:57
 */

namespace Home\Common\Service;


use Home\Common\DAO\SchoolDAO;

class AddressService
{
    public function getCities()
    {
        $schoolDAO = new SchoolDAO();
        return $schoolDAO->getCities();
    }

    public function getSchoolsAt($city)
    {
        $schoolDAO = new SchoolDAO();
        return $schoolDAO->getSchoolsAt($city);
    }
}