<?php
/**
* Author: Wang Jinglu
* Date: 2016/11/3
* Description:
*   express_company school 表连接查询
*/

namespace Home\Model;
use Think\Model\RelationModel;

class ExpressModel extends RelationModel
{
    protected $tableName = 'school';
    protected $_link = array(
        'express'=>array(
            'mapping_type'=>self::HAS_MANY,
            'class_name'=>'express_company',
            'foreign_key'=>'school_id',
        )
    );
}
?>