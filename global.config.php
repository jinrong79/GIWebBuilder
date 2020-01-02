<?php
//set time zone
date_default_timezone_set('PRC');
error_reporting(E_ALL);  //turn error off when system online.


/* include global.func.php  */
// path can be changed.
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR.'j79frame'.DIRECTORY_SEPARATOR . 'global.func.php';


/* global config*/
/**
 * Class
 * CONFIG
 * global setting data.
 * this setting is accessible anywhere in application.
 */
class CONFIG
{


    public static $SITE_NAME = 'GI Web Builder';     //site name


    /* path def. */

    public static $PATH_ROOT = '';           //current system root dir, automatically acquired according to current global.config.php file path.

    public static $SITE_DOMAIN='';                           //site domain, like 'www.giwebbuilder.com'
    public static $URL_SETTINGS = '/j79frame/app/settings';  //url of setting files
    public static $URL_WEBPAGE = '/pages';                   //pc 网页端页面所在相对路径
    public static $URL_MOBILE_WEBPAGE='/mpages';             //手机网页端所在相对路径



    //site root links
    public static $URL_HOME = '/index.html';
    public static $URL_HOME_ADMIN = '/admin/index.html';


    //application data: all application setting data.
    public static $APP=array(

        'timeStart'=>NULL, //current script start time.

        //url setting of current application
        'urlHome'=>'/index.html',
        'urlAdmin'=>'/admin/index.html',

        'lang'=>1, //current language idx. detail info ,pls refer to j79frame\lib\util\Lang,

        'operator'=>NULL, //current operator,



        //db connection list:
        'db'=>array(
            'default'=>false,  // default db connection, use it most case.
            'temp'=>false,     // temporal db connection, newly created for current page.
        ),

        //db connection setting for currently running.
        'dbConnectSetting'=>NULL,

        //db connection setting for local testing:
        'dbConnectSettingLocal'=> array(
            'driver'=>'mysqli',
            'host' => 'p:localhost:3306',
            'user' => 'root',
            'pwd' => 'sheepyang',
            'dbname' => 'db_eyb'
        ),

        //db connection setting for remote site:
        'dbConnectSettingSite'=> array(
            'driver'=>'mysqli',
            'host' => 'p:localhost:3306',
            'user' => 'root',
            'pwd' => 'oArpCnd4',
            'dbname' => 'db_eyb'
        ),



    );




    /**
     *  GET_DB_CONNECT
     *
     *  connect db by settings and save it in global value \CONFIG::$DB
     *
     * @return {bool/mysqli} :
     *                          false -> error
     *                          mysqli handler -> success.
     *
     */
    public static function GET_DB_CONNECT()
    {
        //Log::add('db connect start:'.\GSetting::GET_SPENT_TIME());
        if (is_null(self::$DB_CONNECT) || !(self::$DB_CONNECT instanceof mysqli)) {//if dbConnect is null or  is not instance of mysqli

            //判断本地/远程, 加载不同数据库连接设定：
            self::$DB_CONNECT_SETTINGS = self::$DB_CONNECT_SETTINGS_REMOTE;
            if (stripos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
                self::$DB_CONNECT_SETTINGS = self::$DB_CONNECT_SETTINGS_LOCAL;
            }

            //verify dbSettings.
            if (!is_array(self::$DB_CONNECT_SETTINGS) || count(self::$DB_CONNECT_SETTINGS) <= 0) {
                return false;
            }

            $db = new mysqli(self::$DB_CONNECT_SETTINGS['host'], self::$DB_CONNECT_SETTINGS['user'], self::$DB_CONNECT_SETTINGS['pwd'], self::$DB_CONNECT_SETTINGS['dbname']);

            if (mysqli_connect_errno()) {
                $err_msg = 'Error: can not open DB, code-' . mysqli_connect_errno();
                Log::add($err_msg);
                //echo '<p>Fatal Error: Error occur when connect DB !</p>';
                return false;

            } else {
                $db->select_db(self::$DB_CONNECT_SETTINGS['dbname']);
                $db->set_charset('utf8');
                self::$DB_CONNECT = $db;
                //Log::add('db connect end[instatic]:'.\GSetting::GET_SPENT_TIME());
                return $db;
            }
        } else {//if dbConnect already exist and is instance of mysqli , just return it.
            return self::$DB_CONNECT;
        }
    }//-/

    /**
     *  GET_SPENT_TIME
     *  get spent time from the start of this php
     */
    public static function GET_SPENT_TIME()
    {
        $curTime = microtime(true);
        $timeSpent = round(($curTime - static::$TIME_START), 4);
        return $timeSpent;
    }//-/


    /**
     *  FORMAT_PATH
     *  return standard path string. dir is sperated by DIRECTORY_SEPARATOR constant
     *
     * @param  {string} pathString : path or url string.
     *
     * @return {string}           : return path string.
     */
    public static function FORMAT_PATH($pathString)
    {
        return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $pathString);

    }//-/


}//=/


/* set script start time */
CONFIG::$APP['timeStart'] = microtime(true);

/* acquire current path to PATH_ROOT */
CONFIG::$PATH_ROOT = dirname(__FILE__);


/* include app.config.php  */
require_once CONFIG::$PATH_ROOT . DIRECTORY_SEPARATOR . 'app.config.php';


/* set the class auto loading */
function j79_autoloader($class)
{
    if (stripos($class, 'CONFIG') === false && stripos($class, 'GFunc') === false) {
        //&& file_exists(strtolower($class) . ".php")

        require_once(strtolower($class) . ".php");


    }
}

spl_autoload_register('j79_autoloader');


//set include path
set_include_path(
    CONFIG::$PATH_ROOT . "/j79frame/app" . PATH_SEPARATOR .
    CONFIG::$PATH_ROOT . "/j79frame/app/util" . PATH_SEPARATOR .
    CONFIG::$PATH_ROOT . "/j79frame/app/3rd" . PATH_SEPARATOR .
    get_include_path()
);


