<?php
namespace j79frame\lib\core\db;
use  j79frame\lib\util\Log;

use mysqli;

/**
*  DBConnector
*  数据库连接类。
*  数据库连接针对一个应用只保持一个，存在全局设置的\GSetting::$DB_CONNECT 当中。	
*
*  @attribute:
*				dbConnect: mysqli数据库连接。					
*  					
*
**/

class DBConnector
{
	
	public static $_dbConnect=false; //mysqli db connect
	
	
	/**
	*  __get
	*  读取属性dbConnect
	*/
	public function __get($name){
	   
	   if(strcasecmp($name,'dbConnect')==0){		  
		  return self::connect();		     
	   }
	   
	   	
	}//-/
	
	/**
	*  __set
	*  设置dbConnect名，设置内部变量_dbConnect
	*/
	public function __set($name, $value){
		 
		 if(strcasecmp($name,'dbConnect')==0 && !is_null($value) && $value instanceof mysqli ){	
		    $this->_dbConnect= $value;
		 }
		
	}//-/
	
	/**
	* connect
	* 
	* start connecting db and return db-connection.
	* 如果静态变量dbConnect不为空，而且是mysqli类对象，直接返回dbConnect，
	* 如果不是，则执行全局设置\GSetting中的GET_DB_CONNECT来获取数据库连接。
	* 数据库连接针对一个应用只保持一个，存在全局设置的\GSetting::$DB_CONNECT 当中。
	*
	* @return {mix}:
	*              null: 出错
	*              mysqli：成功则返回数据库连接。
	*/
	public static function connect($dbConnectSetting=NULL){
		

        //get db connection setting:
        // if empty, get from global config.
        $dbConnectSetting=empty($dbConnectSetting)? \CONFIG::$APP['dbConnectSetting']:$dbConnectSetting;
		if(empty($dbConnectSetting)){
            Log::add('DB connection setting is empty!');
            return null;
        }

		//if current connection is empty, then create connection.
		if( empty(self::$_dbConnect)){

            $db = new mysqli($dbConnectSetting['host'], $dbConnectSetting['user'], $dbConnectSetting['pwd'], $dbConnectSetting['dbname']);
            if (mysqli_connect_errno()) {
                Log::add('Error: can not open DB, code-' . mysqli_connect_errno());
                return null;
            } else {
                $db->select_db($dbConnectSetting['dbname']);
                $db->set_charset('utf8');
                self::$_dbConnect= $db;
            }
		}

        //return connection instance if valid
        return self::$_dbConnect;



				
	}//-/

	
	
	
	
	
}//=/DBConnector

