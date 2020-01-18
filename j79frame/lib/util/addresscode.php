<?php
namespace j79frame\lib\util;

use j79frame\lib\core\db\DBReader;
use j79frame\lib\core\j79obj;
use j79frame\lib\util\Lang;

/**
*  AddressCode
*  @author: jin rong (rong.king@foxmail.com)
*  @attribute   
*		
*  @method


*	   
*         
**/
class AddressCode extends j79obj
{

    const MAX_DEPTH=4;

    public static $WIDTH_SETTING=array(2,2,2,3);


    /**
     * getParentCode
     * @param $code
     * @return bool|int|string
     *          false: code  invalid
     *          null : no parent, this is root node code.
     *          string: parent code.
     */
    public static function getParentCode($code){

        if( !self::verifyCode($code)){
            return false;
        }
        $totalW=array_sum(self::$WIDTH_SETTING);
        $code=trim(''.$code);
        $codeLen=strlen($code);

        $curW=0;
        $parentCodeLen=0;
        for($i=0; $i<count(self::$WIDTH_SETTING); $i++){

            $curW+=self::$WIDTH_SETTING[$i];
            if($curW==$codeLen){
                break;
            }
            $parentCodeLen=$curW;
        }
        if($parentCodeLen>0){
            return substr($code,0,$parentCodeLen);
        }else{
            return NULL;
        }

    }//-/

    /**
     * getParentCodeList
     * get parent code list like: give 222401001 => array( '22','2224','222401')
     * @param $code
     * @return array|bool
     */
    public static function getParentCodeList($code){

        if( !self::verifyCode($code)){
            return false;
        }
        $totalW=array_sum(self::$WIDTH_SETTING);
        $code=trim(''.$code);
        $codeLen=strlen($code);

        $curW=0;

        $parentCodes=array();
        for($i=0; $i<count(self::$WIDTH_SETTING); $i++){

            $curW+=self::$WIDTH_SETTING[$i];
            if($curW==$codeLen){
                break;
            }
            array_push($parentCodes, substr($code,0,$curW));
        }
        return $parentCodes;


    }//-/

    /**
     * getLabelArr
     * get label text array by address code by db reading.
     *
     * @param $code
     * @return array|bool :
     *          false - code invalid
     *          array-empty: not found in db.
     *          array: label names  from root to detail. like :
     *                  array(4) { [0]=> string(9) "吉林省" [1]=> string(24) "延边朝鲜族自治州" [2]=> string(9) "延吉市" [3]=> string(12) "北山街道" }
     */
    public static function getLabelArr($code){

        if( !self::verifyCode($code)){
            return false;
        }

        $parentCodes=self::getParentCodeList($code);
        array_push($parentCodes,$code);

        $codeList='';
        $sep='';
        for($i=0; $i<count($parentCodes);$i++){
            $codeList.=$sep."'".$parentCodes[$i]."'";
            $sep=',';
        }

        $sql="tb_address_data where code IN ($codeList) order by code";
        $dbr=new DBReader();
        $re=$dbr->readQ($sql, '*');

        $result=array();

        if(!empty($re)){

            foreach ($re as $addrItem) {
                array_push($result, isset($addrItem['name']) ?  $addrItem['name']: '');
            }


        }
        return $result;

    }//-/

    /**
     * getLabel
     * @param $code {string/int} : address code.
     * @param $sep {string} : seperator , default '-'.
     * @return string : return address label total text. every label seperated by $sep
     */
    public static function getLabel($code, $sep='-'){

        $labelArr=self::getLabelArr($code);
        if(!empty($labelArr)){
            return implode($sep,$labelArr);
        }else{
            return '';
        }

    }

    /**
     * verifyCode
     * verfiy code format.
     * @param $code
     * @return bool
     */
    public static function verifyCode($code){

        if(!is_string($code) && !is_int($code)){

            return false;
        }

        $code=trim(''.$code);
        $codeLen=strlen($code);

        $totalW=array_sum(self::$WIDTH_SETTING);
        if($codeLen>$totalW){

            return false;
        }

        $flagInvalid=true;
        $curW=0;
        for($i=0; $i<count(self::$WIDTH_SETTING); $i++){

            $curW+=self::$WIDTH_SETTING[$i];
            if($curW==$codeLen){
                $flagInvalid=false;
                break;
            }
        }

        return !$flagInvalid;

    }//-/
		

	
	
		
	
	
	

}//======================/CLASS: AddressCode

