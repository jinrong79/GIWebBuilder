<?php
namespace j79frame\lib\controller;
use j79frame\lib\core\j79obj;

/**
*  DataSource
*  主要用于向网页传递全局结果数据
*/
class DataSource extends j79obj
{
	public static $RESULT=array();	 //静态变量，用于存model产生的结果数据，并传递给template view。
	
	public static $LANG=NULL;        // Lang Object saver.
	
	public static $OPERATOR=NULL;    // current operator.
	
	
	/**
	*  isSuccess
	*  check result to find success or not.
	*  @return {bool} : true - success('result'=>1);  false - failed ('result'=>0)
	*/
	public static function isSuccess(){
	    if(is_array(static::$RESULT) &&
		   count(static::$RESULT)>0 &&
		   array_key_exists('result', static::$RESULT)){
		
		   $result=intval(static::$RESULT['result']);
		   return $result==1? true :false;			   
		
		}else{
			return false;
		}		
	}//-/
	
	
	
}//=/
