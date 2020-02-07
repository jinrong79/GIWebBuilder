<?php
header("Content-Type:text/html;charset=utf-8");
error_reporting(E_ERROR | E_WARNING);
date_default_timezone_set("Asia/chongqing");
include "Uploader.class.php";
//上传配置
$config = array(
    "savePath" => "upload/",             //存储文件夹
    "maxSize" => 1000,                   //允许的文件最大尺寸，单位KB
    "allowFiles" => array(".gif", ".png", ".jpg", ".jpeg", ".bmp")  //允许的文件格式
);
$FILE_MAX=50*1024;   //IMG FILE MAX SIZE , in KB. currently 50MB

//上传文件目录
$rootPath = $_SERVER['DOCUMENT_ROOT'];
$rootPath = str_replace('\\', '/', $rootPath);
$Path = isset($_REQUEST['save_path']) ? $rootPath . $_REQUEST['save_path'] : 'upload/';
$Path = str_replace('\\', '/', $Path);

//上传文件大小‘
$sizeLimit = isset($_REQUEST['max_size']) && intval($_REQUEST['max_size'])>0 ? intval($_REQUEST['max_size']) : 1000;
$sizeLimit= $sizeLimit> $FILE_MAX ? $FILE_MAX :$sizeLimit;
$config['maxSize']=$sizeLimit;

//背景保存在临时目录中
$config["savePath"] = $Path;

//$content='======='.var_export($config, TRUE).'====/====';
//$result=file_put_contents('c:\\web-ps\\data\\t1.txt', $content, FILE_APPEND);


$up = new Uploader("upfile", $config);
$type = $_REQUEST['type'];
$callback = $_GET['callback'];


$info = $up->getFileInfo();
if (isset($info['url'])) {
    $info['url'] = str_replace($rootPath, '', $info['url']);
}
/**
 * 返回数据
 */
/*$content='======='.var_export($info, TRUE).'====/====';
$result=file_put_contents('c:\\web-ps\\data\\t1.txt', $content, FILE_APPEND);*/
if ($callback) {
    echo '<script>' . $callback . '(' . json_encode($info) . ')</script>';
} else {
    echo json_encode($info);
}
