<?php
$requestDataType=isset($_REQUEST['format'])? intval($_REQUEST['format']) : 0 ;
if($requestDataType==5){ //xml
    header("Content-type:text/xml");
}else{//other html
    header("Content-Type: text/html; charset=utf-8");
}

//include config files.
require_once   dirname(__FILE__).DIRECTORY_SEPARATOR."global.config.php";


//do dispatching:
use j79frame\lib\controller\Dispatcher;
$dp=new Dispatcher();
$dp->dispatch();

?>