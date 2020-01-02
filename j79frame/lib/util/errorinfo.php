<?php
namespace j79frame\lib\util;
/**=================CLASS: ErrorManager
 *  error信息类
 *	@author: jin rong (rong.king@foxmail.com)
 *  @attribute
 *		    list：返回出错信息列表数组，只读
 *  		amount: 返回出错信息总数，只读
 *  @method
 *		    add : 添加错误信息。
 *  		getList: 返回错误信息列表数组
 **/
class ErrorInfo
{
	
	protected $_array_err; // 出错信息数组, 对应的只读属性名称：list	
	
	public $flag_log; // 是否记录log文件。0：不记录（默认） ； 其他: 记录。
	public $log_file; // log文件名。仅仅文件名，不包括目录和扩展名。


	public function __construct($flag_log=1, $log_file=''){
		
		$this->flag_log=$flag_log!=0?  1: 0 ;	
		$this->log_file=$log_file!=''?  $log_file: 'error_log' ;		
		$this->_array_err=array();
		
			
	}//-/
	
	public function __get($name){
		
		switch($name){
			
			case "list":
				return $this->_array_err;
				break;
			case "amount":
				return count($this->_array_err);
				break;
		}
	}//----/__get
	
	/**
	* add
	* 添加出错信息
	* @param error_text {string}: 出错信息文本。
	**/
	public function add($error_text){
		if($error_text!=''){
			
			array_push($this->_array_err, $error_text);
			
			if($this->flag_log!=0 && $this->log_file!='' ){//写log
			
				Log::add($error_text, $this->log_file);
				//$log=new LogManager($this->log_file);
				//$log->add($error_text);
			}
				
		}
		
	}//-/add	
	
	/**
	* getList
	* 返回出错信息列表数组
	* return {array/boolean}  array:出错信息列表数组; false:出错
	**/
	public function getList(){
		if(is_array($this->_array_err) ){			
			return $this->_array_err;				
		}else{
			return false;	
		}
		
	}//-/getList	
	

}//======================/CLASS: ErrorInfo

