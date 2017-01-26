<?php
/**
 * Author: ben
 * Date: 19/01/2017
 * Time: 14:19
 * Description:
 *  dbk_school 操作
 */

namespace Admin\Common\DAO;

class SchoolDAO
{
    private $M_School;
    private $school;
    private $noErr;

    //errors
    const E_FAIL = 0;
    const E_OK = 1;
    const E_NotFound = 2;
    const E_NoChange = 3;

    public function __construct()
    {
        $this->M_School = M('school');
    }

    public function updateOnline($school_id, $service, $open)
    {
        $school['school_id'] = $school_id;
        if($service=="send")
            $school['send_online'] = $open;
        else if($service=="pickup")
            $school['pickup_online'] = $open;
        $this->M_School->save($school);
    }

    public function getSchool($school_id){
        $school = $this->M_School->find($school_id);
        return $school;
    }


}































