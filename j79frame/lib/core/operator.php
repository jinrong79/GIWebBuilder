<?php
namespace j79frame\lib\core;
use j79frame\lib\db\DBReader;
use j79frame\lib\util\Log;

/**
* Operator
* 操作者的基础类。
*
* @author     jin.rong <rong.king@foxmail.com>
 
*/
class Operator extends j79obj {
	
	// 行为对应的数字编码。因为数据库中存储的是此数字，而非SELECT等字符串。
	const CODE_SELECT     =1; 
	const CODE_CREATE     =2;
	const CODE_UPDATE     =3;
	const CODE_PATCH      =4;
	const CODE_DELETE     =5;
	const CODE_HEAD       =6;
    const CODE_OPTIONS    =7;
	
	const TYPE_NOT_LOGINED =0;
	const TYPE_MEMBER =1;
	const TYPE_ADMIN =2;
    const TYPE_PARTNER_BM=11;
	
	const SE_OPERATOR_NAME ='operator_name';
	const SE_OPERATOR_IDX  ='operator_idx';
	const SE_OPERATOR_TYPE ='operator_type';
	
	const SE_OPERATOR_LANG ='operator_lang';

    const SE_OPERATOR_TERMINAL='operator_terminal';



	
	const SE_SID='sid'; 
	
	const KEY_SID='sid'; //key name of session id in http request data.
	
	
	const SESSION_DATA_MAX=1024;  //max length of session value
	const SESSION_KEYNAME_MAX=64;  //max length of session key name.
	
	
	//短信验证码
	const SE_SMS_VCODE='sms_valid_code';
	const SE_SMS_MOBILE='sms_valid_mobile';
	const SE_SMS_SEND_TIME='sms_send_time';
	

	
	protected $_sessionId=0; //session id, 0- 未设置
	
	protected $_terminalType=0; //operator terminal type.

	
	protected $_smsExpireTime=60; //in sec.

    protected $_params=NULL;
		
	/**
	*  __construct
	*  build Operator
	*
	*  @param {key-array/string}  params : http request data with key-array format 
	*                                      or session id in string format.
	*/
	public function __construct($params){

        //get terminal type:
        //$this->_terminalType=isset($params['terminal']) && intval($params['terminal'])>0 ?  intval($params['terminal']) :0;

        //get session id:
		$sessionId='';

		if(is_array($params)){
            $this->_params=$params;
		    $sessionId= array_key_exists(static::KEY_SID, $params) ? trim($params[static::KEY_SID]): '';
		   
		}elseif(is_string($params)){
			
		   $sessionId=trim($params);
		}
		
		if( $sessionId==''){
				
			static::startSession();	
			$this->_sessionId=session_id();
					
	
		}else{
			
			$this->_sessionId=$sessionId;
			static::startSession($sessionId);			
			
		}

		Log::val('sid when operator construct:',$this->_sessionId);

        //get terminal type:
        //1- if params has no terminal var, but session stored operator_terminal,then use it as result
		if(!isset($params['terminal']) && isset($_SESSION[static::SE_OPERATOR_TERMINAL])){
            $this->_terminalType=intval($_SESSION[static::SE_OPERATOR_TERMINAL]);
        }else{//2- else: if params has terminal var , then use it as result and stores it into session; if no params and session, then return default[0] and store it in session.
            $this->_terminalType=isset($params['terminal']) && intval($params['terminal'])>0 ?  intval($params['terminal']) :0;
            $_SESSION[static::SE_OPERATOR_TERMINAL]=$this->_terminalType;
        }



		
	}//-/

    /**
     * getVarAcrossReqAndSession
     * get var across session and  request.
     * 1- if params has no $paramName, but session stored o$sessionKey,then use it as result
     * 2-
     * @param $paramName
     * @param $params
     * @param $sessionKey
     * @return null
     */
    public static function getVarAcrossReqAndSession($paramName, $params, $sessionKey){
        $result=NULL;

        //if has paramName in params and not empty: get it and set it to session.
        if(isset($params[$paramName]) && !empty($params[$paramName])){

            $result=$params[$paramName];
            $_SESSION[$sessionKey]=$result;

        }else{//if params not have valid var:

            if(isset($_SESSION[$sessionKey])){//if session has value of $sessionKey: then get it as result.
                $result=$_SESSION[$sessionKey];
            }
        }
        return $result;

    }//-/






	
	/**
	*  saveSMSVCode
	*  save sms validation code sending info in session.
	*/
	public function saveSMSVCode($mobile, $code, $sendTime){
	   	static::startSession($this->_sessionId);
		$_SESSION[static::SE_SMS_MOBILE]=$mobile;
		$_SESSION[static::SE_SMS_VCODE]=$code;
		$_SESSION[static::SE_SMS_SEND_TIME]=$sendTime;
	}//-/


	
	/**
	*  verifySMSVCode
	*  verify sms vcode in session.
	*  @param {string/int} mobile
	*  @param {string/int} code
	*  @param {string/int} expireTime : in seconds. default=60 seconds.
	*/
	public function verifySMSVCode($mobile, $code, $expireTime=60){

        static::startSession($this->_sessionId);
		$ss_mobile=$_SESSION[static::SE_SMS_MOBILE];

		$ss_code= $_SESSION[static::SE_SMS_VCODE];
		$ss_sendtime=$_SESSION[static::SE_SMS_SEND_TIME];		
		
		
		/*Log::val('ss_mobile',$ss_mobile);
		Log::val('ss_code',$ss_code);
		Log::val('incoming code',$code);
		
		Log::val('ss_sendtime',$ss_sendtime);*/
		
		
		$startT=strtotime($ss_sendtime); 		
		$dif=time()-$startT;
		
		
		/*Log::val('ss_time_diff',$dif);
		Log::val('exp time',$expireTime);	*/		
		
			
		if( $dif > $expireTime ){			
		   $result=-1;
		}else if($mobile!=$ss_mobile || $code!=$ss_code){		   
		   $result=-2; //not match	
		}else{
		   $result=0; //match and not expired.
		}
		
		/*Log::val('result:',$result);*/	
		
		return $result;
		
		
		
		
	}//-/



	
	
	/**
	*  getTerminalType
	*  get operator's terminal type.
	*/
	public function getTerminalType(){

		return $this->_terminalType;
	}//-/




	
		  
    /**
	* getId
	* 返回当前operator的用户id
	*
	* @return {int}: 用户id: 0-未登录； >0：用户idx
	*/
	public function getId(){
		//static::startSession($this->_sessionId);
		$value=isset( $_SESSION[static::SE_OPERATOR_IDX]) ?  intval($_SESSION[static::SE_OPERATOR_IDX]) : 0;		
		return $value;
	}//-/
	
	/**
	* getName
	* 返回当前operator的用户名
	*
	* @return {string}:  empty -> not logined; other string -> user name; 
	*/
	public function getName(){
		//static::startSession($this->_sessionId);
		$value=isset( $_SESSION[static::SE_OPERATOR_NAME]) ?  trim($_SESSION[static::SE_OPERATOR_NAME]) : '';		
		return $value;
	}//-/



	
	 /**
	* getType
	* 返回当前operator的类型
	* @return {int}: 0-未登录；  1- 前台 member; 2 - 后台 admin
	*/
	public function getType(){
		//static::startSession($this->_sessionId);
		$value=isset( $_SESSION[static::SE_OPERATOR_TYPE]) ?  intval($_SESSION[static::SE_OPERATOR_TYPE]) : static::TYPE_NOT_LOGINED;		
		return $value;
	}//-/

	
	/**
	* getSessionId
	* 返回当前operator的session Id
	*/
	public function getSessionId(){
		return $this->_sessionId;
	}//-/
	
	
	/**
	* getLangId
	* 返回当前operator的语言Id
	*/
	public function getLangId(){
		$value=isset( $_SESSION[static::SE_OPERATOR_LANG]) ?  intval($_SESSION[static::SE_OPERATOR_LANG]) : NULL;
		return $value;
	}//-/
	
	/**
	* setLangId
	* 设置当前operator的语言Id
	*/
	public function setLangId($value){
		$_SESSION[static::SE_OPERATOR_LANG]=intval($value);
	}//-/
		
	
	/**
	* startSession
	*
	* 判断session是否已经开启，以及当前session id是否为与给定的sessionId相同。
	* 如果session没有开启，则以sessionId开启新会话。
	* 如果session已经开启，而当前session id不同于给定的sessionId，那么终止当前会话，以sessionId开启新会话。
	*/	
	public static  function startSession($sessionId=''){
	    
		//when not get sid from REQUEST, sessionId is empty:
		if(trim($sessionId)==''){

			//Log::add('when not get sid from REQUEST, sessionId is empty:');

			if(static::isSessionStarted()===false){
				$errLvl=error_reporting();
			    error_reporting(0);
				//Log::add('start new session!');
				session_start();
				//Log::add(session_id());
				error_reporting($errLvl);
			}					
			return;	
		}


		//Log::add('sessionId is not empty:'.$sessionId);

		//receive sid, sessionId not empty:
		if(static::isSessionStarted()===false){//if session not started, then start with given SID.

            //Log::add('session not started, start session with give sid!');
            session_id($sessionId);
			session_start();

		}elseif( session_id()!=trim($sessionId)){//if session already started, but current SID and provided SID not the same:		


            //Log::add('session started, but current sid not the same with give SID: close current, start with new session with given SID');
			//Log::add('cur session:'.session_id());
			//Log::add('given session:'.$sessionId);




			session_unset();
            session_write_close();

            /*$errLvl=error_reporting();
            error_reporting(0);*/
			//session_destroy();
           /* error_reporting($errLvl);*/

            session_id($sessionId);
			session_start();					
		}


		$_SESSION['last_access_time']=time();




	}//-/
	
	
	/**
	* _isSessionStarted
	* 返回session是否开启会话。
	*
	* @return bool : true 开启了； false: 没有开启
	*/
	public static function isSessionStarted(){
	    if ( version_compare(phpversion(), '5.4.0', '>=') ) {
			$started=session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
			Log::add('is session started? '.($started ? 'yes':'no'));
            return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
        } else {
			$started=session_id() === '' ? FALSE : TRUE;
			Log::add('is session started? '.($started ? 'yes':'no'));
            return session_id() === '' ? FALSE : TRUE;
        }	
	}//-/
	
	

	

	
	
	
   
}//=/