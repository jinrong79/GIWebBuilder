<?php
namespace j79frame\lib\controller;
/**
*  DataFormat
*  对Model或者Controller类返回结果进行格式标准化。  
*/
class DataFormat
{

    /**
     * ModelResultFormatter
     * 对Model类对象的标准操作返回的结果数据，进行格式化，传递给controller类对象。
     *
     * @param mixed $result  : result to handle.
     *                         null  - return 404 code. means no response
     *                         false - return as error, set with errorCode and msg.
     * @param int $errorCode : error code.
     * @param string $msg    : msg string
     * @return array         : return data;
     */
	public static function ModelResultFormatter($result, $errorCode=0, $msg=''){

		
		
		if( !is_array($result)){
			
			if($result===false || is_null($result)){
				$result=array();
				$result['result']=0;
				$result['error_code']=$errorCode;			
				$result['msg']=$msg; 	
			
		    }else{
				$result=array();
				$result['result']=1;
                $result['error_code']=0;
				$result['msg']=$msg; 	
			}
			
		}
		
		$result['format']='model'; //表明，已经格式化为model格式。
		//change key 'records' to 'data';
		if(array_key_exists('records', $result)){
		    $result['data']=$result['records'];
			unset($result['records']);
		}
		
		//以后可以根据接口的需求，转换成RESTful的返回结果。
		return $result;	
	}//-/


    /**
     * ControllerReturnFormatter
     * 对Controller类对象的返回结果数据，进行格式化，传递给最终接口。
     * @param mixed $result  : result to handle.
     *                         null  - return 404 code. means no response
     *                         false - return as error, set with errorCode and msg.
     * @param int $errorCode : error code.
     * @param string $msg    : msg string
     * @return array         : return data;
     */
	public static function ControllerReturnFormatter($result, $errorCode=0, $msg=''){		
		

		
		if($result===false || is_null($result)){
			
			$result=array();
			$result['result']=0;
			$result['error_code']=$errorCode;			
			$result['msg']=$msg; 	
			
		}else if($result===true){
            $result=array();
            $result['result']=1;
            $result['error_code']=0;
            $result['msg']=$msg;
        }
		
		if(!is_array($result)){
			$result=array();
			$result['result']=0;
            $result['error_code']=404;
            $result['msg']='Fatal error: result is not in valid formmat';
		}
		
		
		$result['format']='controller'; //表明，已经格式化为controller格式。
		
		//当前没有做任何改动，直接返回。
		//以后可以根据接口的需求，转换成RESTful的返回结果。
		return $result;	
	}//-/
	
	
}//=/