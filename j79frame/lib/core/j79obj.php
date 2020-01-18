<?php
/**
 * Created by PhpStorm.
 * User: jinrong
 * Date: 2020/1/18
 * Time: 21:06
 */

namespace j79frame\lib\core;

/**
 * Class j79obj
 * root object class for all j79frame-class.
 * @package j79frame\lib\core
 */
class j79obj
{
    const ERROR_INVALID_DATA        = 1;    // error code for invalid data format
    const ERROR_INVALID_PARAM       = 1000; // error code for invalid params.
    const ERROR_FAILED_OPERATION    = 2000; // error code for failing normal operation
    const ERROR_FAILED_DB_OPERATION = 3000; // error code for failing when db operation
    const ERROR_FAILED_FILE_OPERATION=4000; // error code for failing file operation.

    const WARNING_NO_OPERATION =10000; // warning for no operation.


    protected $_error=array();  //error info pushed in stack




    /**
     * Errpush
     * push error info and code into err-list
     * @param int    $errCode : error code
     * @param string $errMsg  : error text info.
     */
    public function errPush($errCode,$errMsg){
        array_push($this->_error,array('code' =>$errCode, 'msg' =>$errMsg));
    }//-/


    /**
     * errPop
     * get error info of latest.
     *
     * @param int $lineAmount : 0[default] or negative integer- get latest single error info.
     *                          other larger than 0 integer - get [$listAmount] error info and return array.
     * @return array|mixed
     */
    public function errPop($lineAmount=0){
        if(count($this->_error)<=0){
            return NULL;
        }
        if($lineAmount>0){
            return array_slice($this->_error, 0, $lineAmount);
        }else{
            return $this->_error[0];
        }
    }//-/

    /**
     * errClear
     * clear err-list
     */
    public function errClear(){
        $this->_error=array();
    }//-/
}