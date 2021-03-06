<?php
use j79frame\lib\util\Log;

//set time zone
date_default_timezone_set('PRC');
error_reporting(E_ALL);  //turn error off when system online.


/* include global.func.php  */
// path can be changed.
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'j79frame' . DIRECTORY_SEPARATOR .'lib' . DIRECTORY_SEPARATOR. 'global.func.php';


/* global config*/
/**
 * Class
 * CONFIG
 * global setting data.
 * this setting is accessible anywhere in application.
 */
class CONFIG
{



    /* path def. */

    public static $PATH_ROOT = '';               //current system root dir, automatically acquired according to current global.config.php file path.



    public static $URL_SETTINGS = '/j79frame/app/settings';  //url of setting files
    public static $URL_WEBPAGE = '/pages';                   //pc端页面所在相对路径
    public static $URL_MOBILE_WEBPAGE='/mpages';             //手机端页面所在相对路径


    /* operator: current operator instance */
    public static $OPERATOR=NULL;




    //application data: all application setting data.
    public static $APP=array(

        'timeStart'=>NULL,  //current script start time.
        'lang'=>1,          //current language idx. detail info ,pls refer to j79frame\lib\util\Lang,
        'operator'=>NULL,   //current operator,


        /*url setting of current application*/
        'urlHome'=>'/index.html',
        'urlAdmin'=>'/admin/index.html',

        /* site name */
        'siteName'=>'GI WebBuilder Application',//website name viewed.


        //db connection setting for currently running.
        'dbConnectSetting'=>NULL,

        'siteDomain'=>'', //site domain, like 'www.giwebbuilder.com'

        //db connection list:
        'db'=>array(
            'default'=>false,  // default db connection, use it most case.
            'temp'=>false,     // temporal db connection, newly created for current page.
        ),

        //following data set in app.config.php:

        /*'siteName'=>'GI WebBuilder',//website name viewed.

        //url setting of current application
        'urlHome'=>'/index.html',
        'urlAdmin'=>'/admin/index.html',

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
        ),*/

    );


    /**
     *  GET_SPENT_TIME
     *  get spent time from the start of this php script
     */
    public static function GET_SPENT_TIME()
    {
        $curTime = microtime(true);
        $timeSpent = round(($curTime - static::$APP['timeStart']), 4);
        return $timeSpent;
    }//-/


}//=/


/* set script start time */
CONFIG::$APP['timeStart'] = microtime(true);

/* acquire current path to PATH_ROOT */
CONFIG::$PATH_ROOT = dirname(__FILE__);

/* include app.config.php  */
require_once CONFIG::$PATH_ROOT . DIRECTORY_SEPARATOR . 'app.config.php';


/* set current db connection setting */
if (stripos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    CONFIG::$APP['dbConnectSetting'] = CONFIG::$APP['dbConnectSettingLocal'];
}else{
    CONFIG::$APP['dbConnectSetting'] = CONFIG::$APP['dbConnectSettingSite'];
}


/* set include path */
set_include_path(
    CONFIG::$PATH_ROOT . "/j79frame/app" . PATH_SEPARATOR .
    CONFIG::$PATH_ROOT . "/j79frame/app/util" . PATH_SEPARATOR .
    CONFIG::$PATH_ROOT . "/j79frame/app/3rd" . PATH_SEPARATOR .
    get_include_path()
);


/* set the class auto loading */
function j79_autoloader($class)
{
    if (stripos($class, 'CONFIG',0) === false && stripos($class, 'GF',0) === false) {
        require_once(strtolower($class) . ".php");
    }
}
spl_autoload_register('j79_autoloader');





