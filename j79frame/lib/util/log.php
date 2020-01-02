<?php
namespace j79frame\lib\util;
/**
 * LOG
 * log类
 * log文件名，add操作时，临时设置，没有指定，就采用默认名：'log'。
 * log文件的扩展名，由静态变量LOG_FILE_EXT给定。
 * log文件的目录，不可由外部制定，默认定位/data/log目录
 *
 * @author: jin rong (rong.king@foxmail.com)
 * @method:
 *  		add      : 添加log指定内容,
 *       addV     : 添加log一个变量名和值
 *       addPlain : 加纯粹内容，不做任何改动。
 *
 **/
class LOG
{

	protected static  $_log_dir='data/log';//相对于网站根目录的log目录地址,开始和结尾都不带目录分隔符好。
	const LOG_FILE_EXT='log'; //log文件的扩展名	
	const MAX_SIZE=1024000; //max log size. 1mb

	public function __construct(){		
			
	}//-/__construct


    /**
     * addV
     * add var current value into log.
     * @param $valueName : var name
     * @param $value     : current value
     */
	public static function addV($valueName, $value=''){
		
	
        //add time and ip prefix.
		static::addPlain('<span class="time">['.date("Y-m-d H:i:s").']</span><span class="ip">{'.static::getIP().'}</span>');
		
		//add value name
		static::addPlain('<div class="log-value-name">','',false);
		static::add($valueName.(empty($value)? '':'<em>=&gt;</em>'),'',false);
		static::addPlain('</div>','',false);
		
		//add value
		if (!is_array($value) && !is_object($value)) {
			if(is_bool($value)){
				$value=$value==true ? 'true' : 'false';
			}
			$value = '<div class="log-value">' . $value . '</div>';
		}
		static::add($value, '', false);

		
	}//-/
	

	
	
	/**
	 * add
	 * add log string content,automatically add time and ip at front.
	 * @param  $content       : log内容，不包括时间。时间自动标在每条log的最前面，用中括号包住。
	 * @param  $logfile       : logfile name, not including ext-name and path, just filename only. [default]='' ,means filename='log'.
     *                          path name get from $this->_log_dir;
     *                          ext-name get from self::LOG_FILE_EXT
     * @param  $flagAddPrefix : flag to determine add time and ip prefix or not. [default]=true, add prefix;
	 * @return bool           : true- success ; false- failed.
	 **/
	public static function add($content, $logfile='',$flagAddPrefix=true){
		
		//if not plain variables, then add log-value div.
		if( is_array($content) || is_object($content)){			
			$content='<div class="log-value">'.var_export($content, TRUE).'</div>';
		}
		
		//add prefix.
		$content=$flagAddPrefix? '<span class="time">['.date("Y-m-d H:i:s").']</span><span class="ip">{'.static::getIP().'}</span>'.$content.PHP_EOL : $content.PHP_EOL;
		
		//add log by addPlain method.
		return static::addPlain($content, $logfile);
		 		
		
	}//------/add
	
	
	/**
	*  addPlain 
	*  add plain text into log file.
	*  @param  $content : content to add directly, must be string.
     * @param  $logfile : logfile name.
     * @return bool     : true-success; false-failed.
	*/
	public static function addPlain($content, $logfile=''){
		
		//param check
		$logfile=trim($logfile);		
		$final_log_file=!empty($logfile) ? $logfile:'log';		
		if($final_log_file=='' || $content==''){
			return false;	
		}	
		//save to file
		return static::saveFile($content, $logfile);			
		
		
	}//-/
	
	/**
	*  saveFile
	*  @param  $content : content to add directly, must be string.
     * @param  $logfile : logfile name.
     * @return bool     : true-success; false- failed.
	*/
	protected static function saveFile($content, $logfile=''){
		//param check
		$logfile=trim($logfile);		
		$final_log_file=!empty($logfile) ? $logfile:'log';		
		$log_dir=$_SERVER['DOCUMENT_ROOT']."/".static::$_log_dir;
		
		if($final_log_file=='' || $content==''){//param is empty: return func.
			return false;	
		}
		
		//if log dir not exist, then make dir.
		if( ! is_dir($log_dir)){//判断目录是否存在，没有则建立。				
		   $re_md=mkdir($log_dir,0777,true);			
		   if ( !$re_md){
				//echo 'Exception: failed creating log dir';  
				return false;
		   }				 
						
		}
		//append log content to the log file
		$logFileName=$log_dir."/".$final_log_file.".".static::LOG_FILE_EXT;
		if(file_exists($logFileName) && filesize($logFileName)> static::MAX_SIZE){
			unlink($logFileName);
		}
	
		 $errLvl=error_reporting();
		 error_reporting(0);

		 $result=file_put_contents($logFileName, $content, FILE_APPEND);
		 error_reporting($errLvl);	 	
	     
		 return (bool)$result;
	}//-/	
	
	/**
	*  readFromEnd
	*  read as stream from end of file
	*  [not finished!]
	*/
	public static function readFromEnd( $lineNoFromEnd=1,$logfile=''){
		
		$final_log_file=$logfile!='' ? $logfile:'log';		
		$filename=$_SERVER['DOCUMENT_ROOT']."\\".static::$_log_dir."\\".$final_log_file.".".static::LOG_FILE_EXT;
		
		
	   	$handle = fopen( $filename, "rb" );
		
		$fileLen=filesize($filename);
		
		$loc=0;
		$re='';

		if( fseek( $handle, $loc, SEEK_END ) !== -1 ){//指向文件尾
		    
			
			$loc--;
			
			
			while ($lineNoFromEnd--){
				
				while($loc*(-1)<$fileLen){
				     fseek( $handle, $loc, SEEK_END );
					 $loc--;
					 $c=fgetc($handle);
					 $re=$c.$re;
					 if( ord($c)==10){
						//$re='<br/>'.$re; 
						break; 
					 }
					 //$re=$c.$re;
					 //echo ord($c).'-';
				}
				
				
				
			}
			
			//$re=str_replace(" ", "&nbsp;",$re);
			return $re;
			
		
		    
		
		}else{
		
			return NULL;
		
		}
		
	}//-/


    /**
     * read
     * read logfile and return content.
     * @param  $logfile    :file name.
     * @return bool|string :content in string. false-error
     */
	public static function read($logfile=''){
		$final_log_file=$logfile!='' ? $logfile:'log';	
		$filename=$_SERVER['DOCUMENT_ROOT']."\\".static::$_log_dir."\\".$final_log_file.".".static::LOG_FILE_EXT;
		try{
		    $errLvl=error_reporting();
		    error_reporting(0);
			$handle = fopen($filename, "r");
			error_reporting($errLvl);
		    if($handle!==false){
			  $contents = fread($handle, filesize ($filename));
			  return $contents;
			}else{
			  return '<div class="log-blank">N/A</div>';
			}
		}catch(Exception $e){
			return '<div class="log-blank">N/A</div>';
		}
		
		
	}//-/

    /**
     * view
     * view log content into screen.
     * @param string $logfile
     */
	public static function view($logfile=''){
		
		$content=static::read($logfile);
		//$content=str_replace(" ", "&nbsp;",$content);
		$content=str_replace(chr(10), "<br/>",$content);
		
		echo $content;
		
	}//-/


    /**
     * clear
     * clear log file and delete file.
     * @param string $logfile: logfile name.     *
     */
	public static function clear($logfile=''){
		
		$final_log_file=$logfile!='' ? $logfile:'log';	
		$filename=$_SERVER['DOCUMENT_ROOT']."\\".static::$_log_dir."\\".$final_log_file.".".static::LOG_FILE_EXT;
		
		unlink($filename);
		
	}//-/


    /**
     * getIP
     * get ip address.
     * @param string $valueFailed : default value returned when failed getting ip. default='Unknown IP'
     * @return string
     */
	public static function getIP($valueFailed='Unknown IP'){
		$ip=$valueFailed;
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
     * check if correct IP format
     * @param  $str : ip string
     * @return bool|false|int: true- is correct ip format. false- incorrect.
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
	

}//============/CLASS: Log
