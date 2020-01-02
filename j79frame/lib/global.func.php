<?php
/**
 * Class
 * GF
 * global functions
 */
class GF
{

    /**
     * getKey
     * get value from asoc-array data by key-name.
     * key-name support "data.idx" like multi-level key-name.
     * @param {string} $keyName      : key-name of value to get,support "data.idx" like multi-level key-name.
     * @param {assoc}  $data         : data to read from.
     * @param {mix}    $defaultValue : if invalid key or no-existing key , then return defaultValue, [NULL- default]
     * @return {mix}                 : return value.
     */
    public static function getKey($keyName, $data, $defaultValue=NULL){
        if(!is_string($keyName) || trim($keyName)=='' ||  !is_array($data) || count($data)<=0 || !self::isAssoc($data) ){
            return $defaultValue;
        }
        $keyNameArr=explode('.', $keyName);
        $curData=$data;
        foreach ($keyNameArr as $keyN) {
            if(isset( $curData[$keyN])){
                $curData=$curData[$keyN];
            }else{
                return $defaultValue;
            }
        }
        return $curData;

    }//-/

    /**
     *  fillKey
     *  [static]
     *  read dataArray, find keyName item, if exist set the value to targetVar.
     *  if not exist  or dataArray is not valid:
     *                if default!=NULL and default!='NULL' ,
     *                   then set default value to targetVar;
     *                if default=='NULL',
     *                   then set NULL to targetVar;
     *                else
     *                   do nothing to targetVar.
     *  otherwise: do nothing.
     *
     *  @param {mix}    targetVar : target var. Be noticed, it pass by reference.
     *  @param {string} keyName   : array key name
     *  @param {array}  dataArray : key-array which contain main data.
     *  @param {mix}    default   : default value. if NULL, then not set; if 'NULL' string then, set NULL when default.
     *
     */
    public static function fillKey(&$targetVar, $keyName, $dataArray, $default=NULL){

        //read value and set to targetVar when value not NULL.
        $readValue=self::getKey($keyName,$dataArray,NULL);
        if(!is_null($readValue)){
            $targetVar=$readValue;
            return;
        }

        //set default value if default!=NULL.
        if(!is_null($default)){
            $targetVar=strcasecmp($default,'NULL')!=0? $default: NULL;
        }

    }//-/



    /**
     * isOK
     *  check if result is success or not.
     *  @param  {key-array} result : result data in key-array format.
     *  @return {boolean}          : if result['result']==1 then return true, else return false.
     */
    public static function isOK($result){
        if( is_array($result) && array_key_exists('result', $result) && intval($result['result'])==1){
            return true;
        }else{
            return false;
        }
    }//-/
	
	/**
	*  parseInt
	*  parse given strings to integer ( regardless of length of number).
	*  accept thousand-seperator.
	*  e.g.: 12,000 => 12000
	*        -123   => -123
	*        1.3400 => 0
	*        12aab  => 0
	*        ,1100  => 0
	*        00100  => 100
	*
	*  @param  {string} numString : given number string to parse  
	*  @return {string}           : intenger number in string.
	*/
	public static function parseInt($numString){
	       $numString=trim((string)$numString);	   
		   $numString=str_replace(' ','',$numString); 
		   $preChar=substr($numString,0,1);
		   
		   if($preChar==',' || ($preChar=='-' && substr($numString,1,1)==',')){
			  return '0';   
		   }	  
		   
		   $numString=str_replace(',','',$numString);   
		   
		   if($preChar=='-'){
			  $numString=trim(substr($numString,1)); 
		   }else{
			  $preChar='';   
		   }
		    
		   $arrMatch=array();
		   if(preg_match('/^[0-9]+$/',$numString,$arrMatch)>0){			   
			   
			  $re= $arrMatch[0];			  
			  $re=preg_replace('/^0+/','',$re);
			  $re=$re==''? '0':$re;			  		  	  
			  return $preChar.$re;
			  
		   }else{
			  return '0'; 
		   }
		 

	}//-/parseInt
	
	
	/**
	*  parseIdx
	*  read idx param, and try to parse it into idx or idx list.
	*  return int or array or false when failed.
	*
	*  @param {mix}    idx  :  idx to parse, 
	*                          -can be int single number
	*                          -can be array
	*                          -can be string contains numbers seperate by ","
	*  @param {string} sep  :  seperator of idx list string,default=','  
	*  @return {int/array/bool} : false-- failed in parsing , idx is not valid.
	*                             int  -- single idx number
	*                             array-- array of idx, length >0                 
	*/
	public static function parseIdx($idx, $sep=','){
		
		
		if(is_array($idx)){//if idx is array:
		    $result=array();			
			for($i=0;$i<count($idx);$i++){				
				if(intval($idx[$i])>0){
					array_push($result,intval($idx[$i]));
				}
			}
			if(count($result)>1){
				return $result;	
			}else if(count($result)==1){
			    return $result[0];	
			}
			
		}else if(is_numeric($idx) && intval($idx)>=0){//if idx is single number		
			return intval($idx);
			
		}else if(is_string($idx)){
			$arrIdx=explode($sep,(string)$idx);
			if($arrIdx!==false){//if idx contains number seperate by ",": like '1,2,3,4'
			    $result=array();			
				for($i=0;$i<count($arrIdx);$i++){				
					if(is_numeric($arrIdx[$i]) && intval($arrIdx[$i])>=0){
						array_push($result,intval($arrIdx[$i]));
					}
				}
				if(count($result)>1){
					return $result;	
				}else if(count($result)==1){
					return $result[0];	
				}			
			}
		}
		return false;
		
		
	}//-/

    /**
     * parseIdxArray
     * parseIdx array or string, and return in array
     * @param $idx
     * @param string $sep
     * @return array: if error then return empty array. Still return array when single idx.
     */
    public static function parseIdxArray($idx, $sep=','){
        $re=static::parseIdx($idx, $sep);

        if($re===false){
            return array();
        }
        if(!is_array($re)){
            return array($re);
        }
        return $re;
    }//-/

	
	/**
	*  strCount
	*  count charactors in str. including utf-8.
	*  return {int} : amount of charactors in a string.
	*/
	public static function strCount($str)
    {
        preg_match_all("/./us", $str, $matches);
        return count(current($matches));
    }//-/
	
	
	/**
	*  isInt
	*  check if value is int like value inspite of value type.	
	*  following will return true:
	*  -string of integer with blank at the front or at the back of it. like ' 29 '
    *  -int 
	*
	*  @param {mix} value : testing value.
	*  @return {bool} : true- is integer; false-not
	*/
	public static function isInt($value){

		if(!is_numeric($value) && !is_string($value)){
			return false;
		}
		
		$value=trim(''.$value);
        $valueParsed=self::parseInt($value);

        if(strcasecmp($value,$valueParsed)==0){
            return true;
        }
		return false;
		
	}//-/
	
	/**
	*  isAssoc
	*  判断数组是否为关联数字
	*/
	public static function isAssoc($arr) {  
        return is_array($arr) && array_keys($arr) !== range(0, count($arr) - 1);
    }//-/  
	
	
	/**
	*  isExpired
	*  判断当前是否在timeStart和timeEnd之间， 是，返回true。
	*/
	public static function isExpired($timeStart, $timeEnd){
		
		$timeStart= is_null($timeStart)? strtotime('2000-01-01 00:00:00'):strtotime($timeStart);		
		$timeEnd= is_null($timeEnd)? strtotime('2999-01-01 00:00:00'):strtotime($timeEnd);
		
		if( $timeStart< time() && time()< $timeEnd){
		   return true;	
		}		
		return false;		
		
	}//-/
	
	/**
	*  getIP
	*/
	public static function getIP(){
		$ip='Unkown IP';
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
			return static::isIP($_SERVER['HTTP_CLIENT_IP'])?$_SERVER['HTTP_CLIENT_IP']:$ip;
		}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			return static::isIP($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$ip;
		}else{
			return static::isIP($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:$ip;
		}
	}//-/
	
	/**
	*  isIP
	*/
	public static function isIP($str){
		$ip=explode('.',$str);
		for($i=0;$i<count($ip);$i++){  
			if($ip[$i]>255){  
				return false;  
			}  
		}  
		return preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/',$str);  
	}//-/
	


    /**
     * getJSON
     * get json format data and transfer into Assoc-array( key-array).
     * if string, then json_decode
     * if array, then return data directly
     * if obj, then return  get_object_vars(data).
     * else return null;
     * @param $data
     * @return mixed|null
     */
    public static function getJSON($data){
        if(is_array($data)){
            return $data;
        }else if(is_object($data)){
            return get_object_vars($data);
        }else if(is_string($data) && trim($data)!=''){
            return json_decode($data,true);
        }else{
            return null;
        }
    }//-/


    /**
     * formatPath
     * return standard path string. dir is sperated by DIRECTORY_SEPARATOR constant
     * @param  string pathString : path or url string.
     * @return string          : return path string.
     */
    public static function formatPath($pathString)
    {
        return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $pathString);

    }//-/




	
	
	
	
	
	
	
	
}//=/

