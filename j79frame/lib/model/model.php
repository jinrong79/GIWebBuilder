<?php
namespace j79frame\lib\model;

use j79frame\lib\controller\DataFormat;

class Model
{

    const ERROR_INVALID_DATA        = 1;    // error code for invalid data format
    const ERROR_INVALID_PARAM       = 1000; // error code for invalid params.
    const ERROR_FAILED_OPERATION    = 2000; // error code for failing normal operation
    const ERROR_FAILED_DB_OPERATION = 3000; // error code for failing when db operation
    const ERROR_FAILED_FILE_OPERATION=4000; // error code for failing file operation.

    const WARNING_NO_OPERATION =10000; // warning for no operation.





	protected $_error=array();  //error info pushed in stack

    protected static $_logMaxSize=307200; //max log size. 300kb
	
	/**
	*   resultFormatter
	*   格式化返回值的格式，返回给controller
	*   一切Model返回给controller的返回值，都统一使用resultFormatter来格式化。
	*/
	public static function resultFormatter($result, $errCode=0, $errMsg='', $resultData=NULL){
		
		/**
			每个子类可以重载此方法，详细编写特殊结果格式化过程。
			通用的格式化，通过DataFormat::ModelResultFormatter来完成。
		*/
		
		//if result is null, set default error result.
		if($result===true){//result===true, means success:

            //如果第一个参数为true，那么,第四个参数$resultData作为返回数据，再添加上result，error_code，和msg字段。
            $result=!empty($resultData) && is_array($resultData) ? $resultData : array();
            $result['result']=1;
            $result['error_code']=$errCode;
            $result['msg']=$errMsg;

        }else if($result===false || is_null($result)){//result===false /NULL : set failed.
            $result=array();
            $result['result']=0;
            $result['error_code']=$errCode;
            $result['msg']=$errMsg;

        }
        //other: just return $result.
		return DataFormat::ModelResultFormatter($result); //统一格式化

	}//-/
	
	
	/**
	*  dataKeyRenamer
	*  rename data key and return data.
	*  It will keep the data whose key is not specified by settings.
	*  @param {key-array} data       : target data to rename key.
	*  @param {key-array} settings   : rename setting.
	*                                  =array(
	*                                           'original-key-name'=>'new-key-name' 
	*                                            //if empty new-key-name, then use original-key-name.
	*                                            //if original-key-name not exist in data, 
	*                                            //then do not return when flagExclude==true.
	*                                        );
	*  @param {bool}      flagExclude : whether ignore data which key is not exists in settings. default[false]
	*  @return {key-array} return data by indicated keyname.
	*                      e.g.:  original data = ('aaa'=>1, 'bbb'=>2, 'ccc'=>3)
	*                                  settings = ('aaa'=>'AAA', 'bbb'=>'','ddd'=>'DDD');
	*                                    result = ('AAA'=>1,'bbb'=>2);
	*/
	public static function dataKeyRenamer($data, $settings, $flagExclude=false){
		
		$result=array();

		foreach ($data as $key => $value) {
			$finalkey=$key;
			if(isset($settings[$key])) {// key exists in settings.
            	$finalkey=trim($settings[$key])!='' ? trim($settings[$key]) : $finalkey;
            	$result[$finalkey]=$value;

			}else{//not exists in settings.
				if($flagExclude==false){
					$result[$finalkey]=$value;
				}
			}
			
		}	
		
		return $result;
		
	}//-/
	
	/**
	*  dataArrKeyRenamer
	*  rename key of every line of the array  and return array result.
	*  It will keep the data whose key is not specified by settings.
	*  @param {array}     data        : target array of data to rename key.
	*  @param {key-array} settings    : rename setting.
	*                                   refer to FUNCTION:dataKeyRenamer
	*  @param {bool}      flagExclude : whether ignore data which key is not exists in settings. default[false]
	*  @return {key-array} return array of key-renamed data. 	
	*/
	public static function dataArrKeyRenamer($data, $settings,$flagExclude=false){
		
		$result=array();
		
		foreach($data as $key=>$lineData){
			$renamedLine=static::dataKeyRenamer($lineData, $settings,$flagExclude);
			array_push($result, $renamedLine);
		}
		
		return $result;
		
	}//-/
	
	/**
	*  getParam
	*  get key value from a key-array data.
	*  if not setted, then return $default(NULL as default setting).
	*  if setted, then return value.
	*/
	public static function getParam($keyName, $data, $default=NULL){

        $result=\GF::getValue($keyName,$data);

        if(is_null($result)){
            return $default;
        }
        return $result;

	}//-/
	
	/**
	*  val
	*  get not empty value from a key-array data.
	*  if not setted or value is empty, or when string, check if it is only filled with blank char, then return $default(NULL as default setting).
	*  otherwise, return value.
     * @return mixed
	*/
	public static function val($keyName, $data, $default=NULL){

        $result=\GF::getValue($keyName,$data);

        if(is_null($result)){
            return $default;
        }
        if(is_string($result) &&  strlen(trim($result))<=0){
            return $default;
        }

        return $result;

	}//-/


    /**
     * getParamNE
     * val的别名
     * @param $keyName
     * @param $data
     * @param null $default
     * @return mixed
     */
    public static function getParamNE($keyName, $data, $default=NULL){
        return self::val($keyName, $data, $default);
    }//-/
	
    
    /**
    *  getVParams
    *  verify params for vital keys.
    *  if all exists, then return those key data.
    *  else return false, and filled noErrorOrEmptyKeys with empty key name array.
    *  @param {key-array}    data               : param data.
    *  @param {array/string} keyNameList        : keyname list string or array.
    *  @param {mix}         &noErrorOrEmptyKeys : will be filled with true if all exists.
    *                                             else will be filled with empty key name array.
    *  @return {false/array}                    : if all exists, then return value of key data in array. else false;
    *
    *  @usage: 
    *          list($idx,$uid,$sid)=self::getVParams($params,'idx,user_idx,shop_idx',$noErr);        
    *          if($noErr!==true){
    *          	  return self::emptyParam($noErr);
    *          }
    */
	public static function getVParams($data, $keyNameList, &$noErrorOrEmptyKeys,$flagNE=true){


        $keyNameList=is_string($keyNameList) && strlen($keyNameList)>0 ? explode(',',$keyNameList) : $keyNameList;
        if(!is_array($keyNameList)){//error
            return false;
        }

        $result=array();
        $emptyList=array();
        foreach($keyNameList as $keyName){

            if(trim($keyName)!=''){
                $curV= $flagNE ? self::val($keyName, $data) : self::val($keyName, $data);
                array_push($result, $curV);
                if(is_null($curV)){
                    array_push($emptyList, $keyName);
                }
            }
        }

        $noErrorOrEmptyKeys=true;
		if(count($emptyList)>0){
            $noErrorOrEmptyKeys=$emptyList;
		}

        return $result;
		

	}//-/



	/**
	*  isOK
	*  check if result is success or not.
	*  @param {key-array} result : result data in key-array format.
	*  @return {boolean}         : if result['result']==1 then return true, else return false.  
	*/
	public static function isOK($result){
		
		if( is_array($result) && isset($result['result']) && intval($result['result'])==1){			
			return true;
		}
		return false;
		
	}//-/
	
	



    /**
     * Errpush
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
     * clear error array.
     */
    public function errClear(){
        $this->_error=array();
    }//-/


    /**
     * getIP
     * get current ip
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
     * isIP
     * check whether is IP or not.
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
     * log
     * [static]
     * add log.
     * @param $title   : log title
     * @param $content : log main content
     * @return bool
     */
    public static function log($title,$content){

        $log_dir=$_SERVER['DOCUMENT_ROOT']."/data/log";
        if($content==''){//param is empty: return false;
            return false;
        }

        //content label:
        $contentLabel='<span class="time">['.date("Y-m-d H:i:s").']</span><span class="ip">{'.static::getIP().'}</span>';
        if(!empty($title)){
            $contentLabel.='<div class="log-value-name">'.$title.'</div>';
        }


        //content main:
        if (is_array($content) ||  is_object($content)) {
            $content='<div class="log-value">'.var_export($content, TRUE).'</div>';

        }else{
            if(is_bool($content)){
                $content=$content==true ? 'true' : 'false';
            }
            $content = '<div class="log-value">' . $content . '</div>';
        }


        //total content:
        $content=$contentLabel.$content.PHP_EOL;


        //if log dir not exist, then make dir.
        if( ! is_dir($log_dir)){//check for existence of dir, if not exist then create dir.
            $re_md=mkdir($log_dir,0777,true);
            if ( !$re_md){
                return false;
            }

        }
        //append log content to the log file
        $logFileName=$log_dir."/log.log";
        if(file_exists($logFileName) && filesize($logFileName)> static::$_logMaxSize){
            unlink($logFileName);
        }

        $errLvl=error_reporting();
        error_reporting(0);

        $result=file_put_contents($logFileName, $content, FILE_APPEND);
        error_reporting($errLvl);
        return (bool)$result;

    }//-/


	
	
	
	
}