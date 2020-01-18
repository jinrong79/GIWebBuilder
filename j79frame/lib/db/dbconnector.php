<?php
namespace j79frame\lib\db;
use j79frame\lib\core\j79obj;
use  j79frame\lib\util\Log;

use mysqli;

/**
 * Class DBConnector
 *
 * 数据库连接类。
 * 数据库连接，建立后存入全局变量\CONFIG::$APP['db'][dbLinkKey], 其中dbLinkKey是指此连接的全局调用名称，默认值是'default'。
 * 也就是说\CONFIG::$APP['db'][’default']是全局共享的数据库连接对象。
 * 如果想使用新的连接，建立连接是传递不同的dbLinkKey值就可以。
 *
 *
 *  @attribute:
 *				dbConnect: mysqli数据库连接。
 *
 * @package j79frame\lib\db
 */
class DBConnector extends j79obj
{
	
	public static $_dbConnect=false; //mysqli db connect

    public static $errorMsg='';
	
	
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
		    self::$_dbConnect= $value;
		 }
		
	}//-/
	
	/**
	 * connect
	 * try connecting db and return db-connection when success.
     *
	 * -首先获取数据库连接设定$dbConnectSetting,如果参数为空，就从全局设定\CONFIG::$APP['dbConnectSetting']获取。
     * -然后，根据里面连接标识$dbLinkKey，检查全局\CONFIG::$APP['db']里面是否存在此连接标识为键值的连接对象
     *  如果存在，就不新建连接，直接返回此连接对象；
     *  如果不存在，就新建数据库连接，存入\CONFIG::$APP['db'][$dbLinkKey]里面，同时返回此新的连接对象。
     *
     * 使用说明：
     *        1. 一般情况下，空参数调用，即可返回default的数据库连接对象，此对象全局共享。
     *        2. 如果需要使用回滚等操作，需要独立连接时，$dbLinkKey设定为单独名称，即可获取新的数据库连接，只要保持$dbLinkKey的全局唯一，就不会发生冲突。
     *
	 * @param $dbConnectSetting : db connect setting, [default]=null, then get from \CONFIG::$APP['dbConnectSetting'].
     * @param $dbLinkKey        : db link keyname in \CONFIG::$APP['db'] array. [default]='default', global-shared db link.
	 * @return mixed            : null-出错; mysqli：成功则返回数据库连接。
	 */
	public static function connect($dbConnectSetting=NULL,$dbLinkKey='default'){

        self::$errorMsg='';

        //get db connection setting:
        //if empty, get from global config.
        $dbConnectSetting=empty($dbConnectSetting)? \CONFIG::$APP['dbConnectSetting']:$dbConnectSetting;
		if(empty($dbConnectSetting)){

            self::$errorMsg='DB connection setting is empty!';
            Log::add(self::$errorMsg);
            return null;
        }


		//if connection with current keyname in global APP is empty, then create connection.
		if( empty(\CONFIG::$APP['db'][$dbLinkKey])){

            $db = new mysqli($dbConnectSetting['host'], $dbConnectSetting['user'], $dbConnectSetting['pwd'], $dbConnectSetting['dbname']);
            if (mysqli_connect_errno()) {

                self::$errorMsg='Error: can not open DB, code-' . mysqli_connect_errno();
                Log::add(self::$errorMsg);
                return null;
            } else {
                $db->select_db($dbConnectSetting['dbname']);
                $db->set_charset('utf8');
                self::$_dbConnect= $db;

                //save to global APP data.
                \CONFIG::$APP['db'][$dbLinkKey]=$db;
            }
		}

        //return connection instance if valid
        return self::$_dbConnect;

				
	}//-/

	
	
	
	
	
}//=/DBConnector

