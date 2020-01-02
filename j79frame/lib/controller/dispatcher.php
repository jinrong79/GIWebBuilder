<?php
namespace j79frame\lib\controller;
use j79frame\lib\core\Operator;
use j79frame\lib\core\AuthVerifier;
use j79frame\lib\controller\DataFormat;
use j79frame\lib\controller\DataSource;
use j79frame\lib\util\HttpRequest;
use j79frame\lib\util\Lang;
use j79frame\lib\util\Log;
/**
*  Dispatcher
*  对http请求进行分发，一切请求的入口。
*  使用方法：实例化本类，并执行dispatch()方法。
*                 $dp=new Dispatcher();
*                 $dp->dispatch();
*/
class Dispatcher
{
	
	const RESULT_FORMAT_JSON=0;
	const RESULT_FORMAT_WEBPAGE=1;
    const RESULT_FORMAT_XML=5;
	
	
	const TERMINAL_TYPE_PC=0;
	const TERMINAL_TYPE_ANDROID=5;
	const TERMINAL_TYPE_IOS=6;
    const TERMIAL_TYPE_MOBILEWEB=1;
	
	// not found page url relative to \CONFIG::$PATH_WEBPAGE
	const PAGE_NOTFOUND='/page/notfound.php'; 
	
	
	// not authorized page url relative to \CONFIG::$PATH_WEBPAGE
	const PAGE_NOTAUTH='/page/notauthorized.php'; 
	
	protected $_targetName='';   //当前目标Model类名
	protected $_actionName='';            //当前目标action名
	protected $_params=array();                  //当前参数集
	protected $_curOperator=NULL;     //当前操作者对象
	
	protected $_terminalType=''; // 终端  0- PC ；1- mobile web   5- android app； 6- iOS app；

    protected $_recUserIdx=0; //单品分享人idx

	protected $_returnFormat=''; // 0- RESULT_FORMAT_JSON; 1- RESULT_FORMAT_WEBPAGE;
	
	protected $_flagDirectViewer=false; //true-直接打开template，名为$this->_targetName

    protected $_startTime=0; //开始dispatch的UNIX时间戳：（January 1 1970 00:00:00 GMT）起的当前时间的秒数。
	
	
	public function __construct(){

		
	}//-/
	
	/**
	* getAction
	* 返回当前http操作的操作名
	* 可以进行转意，比如：http GET => SELECT 
	* 当前是通过http参数中的action值来进行判断。
	*/
	public function getAction(){				
		/*
		//use in RESTful dispatcher
		$httpaction=$_SERVER['REQUEST_action'];	
		switch(strtoupper($httpaction)){
			case 'GET':
				return 'select';
			case 'POST':
			    return 'create';
			case 'PUT':
			    return 'update';
			case 'PATCH':
			    return 'patch';
			case 'DELETE':
			    return 'delete';
			case 'HEAD':
			    return 'head';
			case 'OPTIONS':
			    return 'options';
			default:
			    return $httpaction;
			
		}*/
		
		//use in normal dispatcher		
		$this->_actionName=$this->_actionName=='' ? strtolower(static::getParamValue('action')) : $this->_actionName;			
		
		return $this->_actionName;
		
	}//-/
	
	/**
	* getTarget
	* 返回当前model对象的类名
	* @return {string}: 当前目标model的类名
	*/
	public function getTarget(){		

		$this->_targetName=$this->_targetName=='' ? strtolower(static::getParamValue('target')) : $this->_targetName;			
		return $this->_targetName;
		
	}//-/


    /**
     * getRec
     * get reommendor user idx.
     */
    public function getRec(){

        $this->_recUserIdx=\CONFIG::$OPERATOR->getRec();
        return $this->_recUserIdx;
    }//-/

	
	/**
	* getParams
	* 返回当前的参数集数组，剔除action和target键值。
	* @return {array}: 返回当前的参数集数组
	*/
	public function getParams(){
		
		/*$params=	$_REQUEST;
		if(array_key_exists('action', $params)){
			unset($params['action']); 
		}
		if(array_key_exists('target', $params)){
			unset($params['target']);
		}	*/
		
		$this->_params=$_REQUEST; //$params;
		
		return $this->_params;
		
	}//-/
	
	/**
	* getTerminalType
	* 返回当前请求发起终端的类型。 
	* @return {int}: 返回当前请求发起终端的类型，0- pc； 5- android； 6- iOS
	*/
	public function getTerminalType(){


        /*if(static::getParamValue('terminal','')==''){
            if(Operator::isSessionStarted() ){

            }
        }

		$this->_terminalType=$this->_terminalType=='' ? intval(static::getParamValue('terminal')) : intval($this->_terminalType);
        $this->_terminalType=empty($this->_terminalType)? 0: $this->_terminalType;*/


        $this->_terminalType=\CONFIG::$OPERATOR->getTerminalType();
		
		return $this->_terminalType;
		
	}//-/


	
	
	/**
	* getReturnFormat
	* 返回值的格式 
	* @return {int}: 返回值的格式 ，0- RESULT_FORMAT_WEBPAGE ; 1- RESULT_FORMAT_JSON 
	*/	
	public function getReturnFormat(){
		
		$this->_returnFormat=$this->_returnFormat=='' ? intval(static::getParamValue('format')) : $this->_returnFormat;	
		
		return $this->_returnFormat;		
	}//-/
	
	
	/**
	* getFlagDirectViewer
	* return flag of direct view .
	* when http  request param 'viewer'=1, then directly view page 
	* @return {bool}: true-- direct view. false--not. 
	*/	
	public function getFlagDirectViewer(){
		
		
		$this->_flagDirectViewer=intval(static::getParamValue('viewer'))==1? true : $this->_flagDirectViewer;	
		
		return $this->_flagDirectViewer;		
	}//-/


    /**
     * addVisitLog
     * 每次restfull访问的数据，存log。
     */
    public function addVisitLog(){

        $curOp=\CONFIG::$OPERATOR;

        $dataRec=array();
        $dataRec['time']=date("Y-m-d H:i:s");
        $dataRec['ip']=\GF::getIP();
        $dataRec['operator_idx']=$curOp->getId();
        $dataRec['operator_type']=$curOp->getType();
        $dataRec=array_merge($dataRec,$this->_params );


        $curData=isset($_SESSION['operator_log']) && !empty($_SESSION['operator_log']) ? $_SESSION['operator_log'] :'';

        $curData.=json_encode($dataRec).PHP_EOL;

        if(strlen($curData)>150){
            //Log::add($_SESSION['operator_log'], 'userlog'.mt_rand(100,110), false );
            Log::addPlain($curData, 'userlog'.mt_rand(100,199));
            $_SESSION['operator_log']='';
        }else{
            $_SESSION['operator_log']=$curData;
        }

        /*if(isset($_SESSION['operator_log']) && !empty($_SESSION['operator_log'])){

            $curData=json_decode($_SESSION['operator_log'], true);
            if($curData!==false && is_array($curData)){
                $curData=array_push($curData, $dataRec);
                $_SESSION['operator_log']=json_encode($curData);
            }else{
                $_SESSION['operator_log']=json_encode(array($dataRec));
            }

        }else{
            $_SESSION['operator_log']=json_encode(array($dataRec));
        }

        if(strlen($_SESSION['operator_log'])>150){
            //Log::add($_SESSION['operator_log'], 'userlog'.mt_rand(100,110), false );
            Log::addPlain($_SESSION['operator_log'], 'userlog999' );
            $_SESSION['operator_log']='';
        }
        */



    }//-/
	
	
	/**
	* dispatch
	* 入口执行函数。开始根据http request执行相应的controller的action
	* @return {mix} result data in json or array. format depends on _resultHandler();
	*/	
	public function dispatch(){



       

        
		$this->_startTime=microtime(true); //设置开始dispatch的时间戳：UNIX时间戳。
		
		//get params
        $actionName=$this->getAction();
		$targetName=$this->getTarget();
		
		//Log::add('start-dispatch('.$targetName.'->['.$actionName.']):'.\CONFIG::GET_SPENT_TIME());
		

		$classFullName='\\j79frame\\app\\controller\\' . $targetName;
		$params=$this->getParams();
		
		
		//get current operator
		$this->_curOperator=new Operator($params);
		//set current operator to global var
		\CONFIG::$OPERATOR=$this->_curOperator;

        Log::val('dispatch target:', $targetName.'->'.$actionName);
        Log::val('session ID when dispatch:', session_id());
        //Log::val('session when dispatch:', $_SESSION);

        //get terminal type.
        $termType=$this->getTerminalType();
        $returnFormat=$this->getReturnFormat();

        //add visit log:
        $this->addVisitLog();


		
		//langauge current setting:		
		$curLan=$this->_curOperator->getLangId();
		\CONFIG::$LANG=is_null($curLan) ? Lang::DEFAULT_LANG_IDX : intval($curLan);
		//langauge object		
		\CONFIG::$LANG_MGT=new Lang();
		
		
		
		
		//handle direct view		 
		if($targetName!='' && $actionName==''){
			
			return  $this->_resultHandler($this->_processDirectView());	
		}
		
		//if not direct view, then do router.
		if( $targetName!='' && $actionName!=''){//if target and action is valid:

			Log::add('=================controller========================');
			Log::add('name:'.$classFullName.' | action : '.$actionName);
		    Log::val('controller param:',$params);
			
		    //when dispatch:
			$curTarget=new $classFullName($params);
			
			$re=$curTarget->commander($actionName, $params, $this->_curOperator);
			
			if( is_null($re) || !is_array($re)){
				$re=array();
				$re['result']=0;
            	$re['error_code']=404;
            	$re['msg']='Fatal error: not supported action!';
				
				return  $this->_resultHandler($re);
				
				
			}else{					
		
				return $this->_resultHandler($re);
			}
			
		}else{//if target and action is empty.
			
			$re=array();
			$re['result']=0;
            $re['error_code']=1;
            $re['msg']='Fatal error: target name or action name not provided!';
			
			return  $this->_resultHandler($re);
		}	
		 
		
	}//-/
	
	
	/**
	*  _processDirectView
	*  
	*  @return {bool/key-array}: false -  means no direct view
	*                            array -  means direct view. 
	*                                     data is in typical return format for $this->_resultHandler.
	*/
	protected function _processDirectView(){
	    
		$this->_flagDirectViewer=true; 
		
		$re=array();
		$targetName=$this->getTarget();	
	    $this->_returnFormat= static::RESULT_FORMAT_WEBPAGE;
				
		//verify authorization
		$auth=new AuthVerifier();
		$reAuth=$auth->verifyDirectViewPage($targetName, $this->_curOperator);
		
		
		if($reAuth===false){//not authorized:
		
		  
		
		  if($auth->loginType!=Operator::TYPE_NOT_LOGINED){
			  $re['need_login_type']=$auth->loginType;
		  }
		  				  
		  $re['result']=0;
		  $re['error_code']=2000;
		  $re['msg']='Fatal error: this action is not authorized to the operator!';
		  
		}else{//authorized: then get page full url and set as view_template.			
		  
		  
		  $re['result']=1;
		  $re['error_code']=0;

            $urlPrefix= $this->_terminalType==self::TERMIAL_TYPE_MOBILEWEB  ?    \CONFIG::$PATH_MOBILE_WEBPAGE  :   \CONFIG::$PATH_WEBPAGE;
            //echo $urlPrefix. '/' . strtolower($targetName) . '.php';
		    $viewPageFull=realpath($urlPrefix. '/' . strtolower($targetName) . '.php');
		  
		  
		  if($viewPageFull===false){
			  $viewPageFull=\CONFIG::$PATH_WEBPAGE. static::PAGE_NOTFOUND;
		  }
		  $re['view_template']=$viewPageFull;
		}
		 
			
		
		return $re;
			
	}//-/
	
	
	/**
	* getParamValue
	* 获得指定keyname的键值，如果不存在，则用default值取代。
	*
	* @param {string} keyname : 参数名称
	* @param {string} default     : 缺省返回值
	*
	* @return {string}                :参数具体取值。
	*/
	public static function getParamValue($keyname, $default=''){
		
		if(array_key_exists($keyname, $_REQUEST)){
			return 	trim($_REQUEST[$keyname]);
		}else{
			return $default;	
		}
		
	}//-/
	
	
	/**
	*  _resultHandler
	*  对返回过程进行处理。
	*  如果是json格式，直接输出。
	*  如果是webpage，则引入template页面显示。
	*  
	*/	
	protected function _resultHandler($result){		
		
		//$curtime=microtime();
		//\CONFIG::$DEBUG_TIME.='dispatch-get result:'.$curtime.' | ';
		//Log::add(\CONFIG::$DEBUG_TIME);
		//Log::add('END-dispatch:'.\CONFIG::GET_SPENT_TIME());
        //Log::addVal('_resultHandler',$result );

        $result['end_time']=microtime(true);//添加unix纪年时间戳：Unix 纪元（January 1 1970 00:00:00 GMT）起的当前时间的秒数。
        $result['start_time']=$this->_startTime;
		
		if($this->_returnFormat== static::RESULT_FORMAT_JSON){//if return JSON
		  
			$finalRe=  json_encode( $result);
			echo $finalRe;
			return $finalRe;
		
		}else if($this->_returnFormat== static::RESULT_FORMAT_WEBPAGE){//else if webpage view
		
		  
			//set current language.
			$curLangIdx=HttpRequest::getURLValue('lang', HttpRequest::VALUE_TYPE_INT, NULL);
			if(!is_null($curLangIdx)){
				
				\CONFIG::$LANG=$curLangIdx;
				
			}
			
			//handle memeber/admin login return with from_url
			if( (strcasecmp( $this->getTarget(), 'userlgsrv')==0 || strcasecmp( $this->getTarget(), 'admlgsrv')==0 || strcasecmp( $this->getTarget(), 'managerlgsrv')==0) && (strcasecmp($this->getAction(),'update')==0 || strcasecmp($this->getAction(),'delete')==0 ) && intval($result['result'])==1 ){
				
				$params=$this->_params;
				
				if(strcasecmp($this->getAction(),'delete')==0){//LogOut:
					  
					  if(strcasecmp( $this->getTarget(), 'admlgsrv')==0){
					  
					      header('Location:'.\CONFIG::$URL_HOME_ADMIN);
					      exit;
				      }else if(strcasecmp( $this->getTarget(), 'managerlgsrv')==0){
						  header('Location:'.\CONFIG::$URL_HOME_PARTNER);
					      exit;						  
					  }else{

                          header('Location:'.\CONFIG::$URL_HOME);
                          //header('Location: /com.php?target=pro_list');
                        exit;
                    }
					  
				}else{//LogIn:
				  
				  if(array_key_exists('from_url', $params) ){
				      //Log::addVal('from_url origin',$params['from_url'] );
					  //Log::addVal('from_url url edcode',urldecode($params['from_url']) );

                      $urlBack=trim($params['from_url']);
                      $urlBack= empty($urlBack) ? '/com.php?targe=user_home': $urlBack;

					  header('Location:'.urldecode($urlBack));
					  exit;
				  }
				}
			}
			
			
			
		  
		  
		  //if not authorized access:
		  if(intval($result['result'])==0 && array_key_exists('error_code', $result) && intval($result['error_code'])==2000){
			  
			  if( array_key_exists('need_login_type', $result)){//if need login
			  
				  
				  $curpage=$_SERVER["REQUEST_URI"];			  
				  
				  if(intval($result['need_login_type'])==Operator::TYPE_ADMIN){//change page to admin login
				  
					 //header('Location: /com.php?target=AdmLgSrv&action=CREATE&format=1&from_url='.urlencode($curpage));
					 header('Location: /com.php?target=adm%2Flogin&from_url='.urlencode($curpage));
					 exit;
					 
				  }else if(intval($result['need_login_type'])==Operator::TYPE_MEMBER){//change page to member login
				  
					 header('Location: /com.php?target=login&from_url='.urlencode($curpage));
					 exit;
				  }
				  
				  
			  }
			  //if not need login, just other reason to forbidden:
			  $templateFile=realpath(\CONFIG::$PATH_WEBPAGE. static::PAGE_NOTAUTH);
			  if($templateFile!==false){				
					DataSource::$RESULT=$result;  //set DataSource
					
					
														
					require $templateFile;	 
			  }else{
					echo '<h1>Fatal Error: No page template found!</h1>';   
			  }
			 
			   
		  }else{//authorized access: 
			  
			  
			  if($this->_flagDirectViewer==true){//direct page view setting:
				  
				  $templateFile=realpath(array_key_exists('view_template', $result)?$result['view_template']:'');	
				  
			  }else{//normal page view setting:
		
				  $templateFile=realpath(\CONFIG::$URL_WEBPAGE . '/' . strtolower($this->_targetName) . '_' . strtolower($this->_actionName) . '.php');
					  
				  if($templateFile===false){	
					$templateFile=realpath(\CONFIG::$URL_WEBPAGE. static::PAGE_NOTFOUND);
				  }				
				  
			  }
			  
			  //if found template page, then include file to view.
			  if($templateFile!==false && $templateFile!='' ){				
				
				  DataSource::$RESULT=$result;  //set DataSource
				  
								
				  require $templateFile;	 
				
			  }else{				  
				  echo '<h1>Fatal Error: No page template found!</h1>'; 
			  }
		  }		  
		  return;
		  		  
	   }elseif($this->_returnFormat== static::RESULT_FORMAT_XML){//xml

            $xmlStr=\GF::getKey('xml', $result);
            $xmlStr=empty($xmlStr) ? '': $xmlStr;



            echo <<<XMLHTML
<?xml version="1.0" encoding="utf-8"?>
$xmlStr
XMLHTML;
            return $result;



       }else{//else not defined just return result data.
		  
		   return $result;
	   }

		
	}//-/
		
	
}//=/