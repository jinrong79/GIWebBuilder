<?php
namespace j79frame\lib\util;
use j79frame\lib\core\j79obj;

/**
 * HttpRequest
 *
 *@author: jin rong (rong.king@foxmail.com)
 *@method:
 *
 **/
class HttpRequest extends j79obj
{
	
	const VALUE_TYPE_INT=0;
	const VALUE_TYPE_TEXT=1;
	const VALUE_TYPE_FLOAT=3;
	
	/**
	*  getURLParams
	*  get url params in single string excluding indicated key.
	*  it returns 'key1=value2&key2=value2' like string format.
	*
	*  @param {string/array}  excludeKey : exclude key name string or list.
	*  @return {string}                  : return http request value in key=value&key=value format.
	*/
	public static function getURLParams($excludeKey=''){
		$re='';
		$sep='';
		
		if(!is_array($excludeKey)){
			$keyList=array($excludeKey);
		}else{
		    $keyList=$excludeKey;	
		}
		
		foreach( $_GET as $key => $value){
			$flag=false;
			foreach($keyList as $exKey){				
				if(strcasecmp($exKey, $key)==0){
					$flag=true;
					break;	
				}			
			}
			if($flag==false){
				    $re.= $sep. $key.'='.$value;
				    $sep='&';
			}	
			
		}
		return $re;
	}//-/
	
	
	/**
	*  getURLValue
	*  get url value by keyname. if not exist, return default value.
	*
	*  @param string  $keyName : key name
	*  @param int     $type    : HttpRequest::VALUE_TYPE_INT[default] | VALUE_TYPE_TEXT | VALUE_TYPE_FLOAT
	*  @param mixed   $default : when not exist key, return default value.
    *  @return mixed           : value get from url
	* 
	*/
	public static function getURLValue($keyName, $type=0, $default=''){
		
		if(trim($keyName)!=''){
			switch(intval($type)){
				case static::VALUE_TYPE_INT:					
					return array_key_exists($keyName, $_GET)? intval($_GET[$keyName]): $default;
					break;
				case static::VALUE_TYPE_TEXT:					
					return array_key_exists($keyName, $_GET)? addslashes($_GET[$keyName]): $default;
					break;
				case static::VALUE_TYPE_FLOAT:					
					return array_key_exists($keyName, $_GET)? floatval($_GET[$keyName]): $default;
					break;
				
			}
			
		}
			
	}//-/
	

}//============/CLASS:HttpRequest
