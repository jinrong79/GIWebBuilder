<?php
namespace j79frame\lib\controller;

use j79frame\lib\core\j79obj;
use j79frame\lib\util\Log;
use j79frame\lib\controller\DataFormat;
use j79frame\app\common\ModelAuthVerifier;
/**
*  DataFormat
*  对Model或者Controller类返回结果进行格式标准化。  
*/
class ModelInterface extends j79obj
{
	
	const AUTH_TYPE_DELIMITER='|';
    /**
	* ModelInterface
	* Controller只能通过ModelInterface访问Model
	*/
	
	protected static $_interfaces;
    /*
     * $_interfaces=array(

            //interface名，唯一。
            'shop_pro_add'=>array(
                                  'model' =>'\\j79frame\\app\\model\\FProduct',
                                  //model的类名
                                  'method'=>'add',
                                  //model的function名
                                  'label' =>'店铺新产品添加',
                                  //说明信息
                                  'auth_type'=>'shop',
                                  //权限验证的类型，当前为shop权限验证。
            ),
            ... ...

	  );
    */

    /**
     * AuthTypeList
     * @param $authTypeStr
     * @return array
     */
    public static function AuthTypeList($authTypeStr){
        if(empty($authTypeStr)){
            return array();
        }
        return explode(self::AUTH_TYPE_DELIMITER,$authTypeStr);
    }//-/


    /**
     * access
     * @param $interfaceName
     * @param $param
     * @param $curOp
     * @return array|bool
     */
	public static function access($interfaceName, $param, $curOp){

		require (\CONFIG::$PATH_ROOT."/global.mi.php");
		$miSetting=\CONFIG::$APP['MIsetting'];


		$interfaceName=strtolower($interfaceName);	
		
		Log::val('ModelInterface-interface name',$interfaceName);
		Log::val('ModelInterface-param:',$param);
		
		//param verification:
		if( empty($miSetting) ||  !isset($miSetting[strtolower($interfaceName)])){
		   return DataFormat::ModelResultFormatter(false,'10000', 'model interface setting is invalid or invalid interface call: interface Name:'.$interfaceName);	
		}   
		  
			
		//get current interface setting.
		$curSet=$miSetting[$interfaceName];
		$model=isset($curSet['model']) ? $curSet['model'] :NULL;
		$method=isset($curSet['method']) ? $curSet['method'] :NULL;
		//$auth=isset($curSet['auth']) ? $curSet['auth'] :0;
		$authType=isset($curSet['auth_type']) ? strtolower($curSet['auth_type']) :'';

        $authTypeArr=self::AuthTypeList($authType);
		
		$labelStr=isset($curSet['label']) ? $curSet['label'] :'';
		
		Log::val('ModelInterface-authType:',$authType);
        Log::val('ModelInterface-authType list:',$authTypeArr);
		
		//if model or method not exists, return error.
		if(is_null($model) || is_null($method)){
			return DataFormat::ModelResultFormatter(false,'10000', 'can not found model interface');
		}
		
		
		//需要验证:
		if(!empty($authTypeArr)){

            $authResult=false;

            foreach ($authTypeArr as $authTypeItem){
                Log::val('ModelInterface-authType current:',$authTypeItem);

                $authV=new ModelAuthVerifier();
                $authResult=  $authV->verifyModel($authTypeItem, $interfaceName, $param, $curOp);
                Log::val('ModelInterface-authType result:',$authResult);
                if($authResult){
                    break;
                }

            }

            if(!$authResult){//没有通过验证
                $re=array(
                    'result'=>0,
                    'error_code'=>2000,
                    'need_authority_type'=>$authTypeArr,
                    'msg'=>'unauthorized access to model interface. Failed authName:'.$authTypeItem.' ; msg from auth:'.$authV->errorMsg
                );
                return DataFormat::ModelResultFormatter( $re);
            }
		}

		
		//不需要验证: 直接放行

        //访问model的method方法。
		$modelObj=new $model();				   
		$result=call_user_func( array($modelObj,$method), $param);
		return DataFormat::ModelResultFormatter($result);
				
			
		
		  
		  
		     
    }//-/access


	   
	   	
	
	
}//====/