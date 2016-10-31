<?php
/**
*   Author:Wang Jinglu
*   Date:2016/10/31
*   Description:
*       opeations of order
*/
namespace Home\Common;

interface IOrderDAO
{
    public function deletePickupOrder($id);
    public function deleteSendOrder($id);
    public function getAllPickupOrders();
    public function getAllSendOrders();
    public function getAllOrders();
    public function getDeleteOrders();
}
?>