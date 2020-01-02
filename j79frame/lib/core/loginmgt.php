<?php
namespace j79frame\lib\core;

use j79frame\lib\db\DBReader;
use j79frame\lib\core\Operator;
use j79frame\lib\util\Log;


/**
* LoginMgt
*
* login management
* 
*
* @package  lib/lib/model
* @author     jin.rong <rong.king@foxmail.com>
* 
*/

class LoginMgt
{
	
	
	const TB_MAIN      = 'tb_user';
	
	const SE_SID_KEY   = 'sid';                 //session key name for sid in return data array	
	const SE_PREPARED  = 'login_prepared';	    //session key to check login prepared status.
	const FIELD_SHELVE = 'user_shelve';	        //field name for check account validation.
	
	
	const FIELD_USERNAME= 'user_name';         //db field name of username	
	const FIELD_PWD= 'user_pwd';               //db field name of password
	const FIELD_IDX= 'user_idx';               //db field name of user_idx
	const FIELD_MOBILE= 'user_mobile';         //db field name of user mobile

    const SE_OPERATOR_LEVEL='operator_level';



    /*

        public static $fieldUserName='user_name';     //数据库中, 用户名字段的名称
        public static $fieldPwd='user_pwd';                 //数据库中, 密码字段的名称
        public static $fieldIdx='user_idx';                    //数据库中, idx字段的名称
        public static $fieldMobileName='user_mobile';*/
	
		
	protected    $_table='tb_user';                       //主表名		
	protected    $_fields=array();                         //登陆时取得的用户信息字段	
	protected    $_userInfo=array();                    //从数据库读取的用户信息，关联数组，键名就是_fields数组给定的字段名。

    protected    $_preLoginData=array();

    protected    $_preLoginSessionKeys=array();

	
	
	public function __construct(){

	    //set session-key list for preLogin data to keep through login process:
	    $this->_preLoginSessionKeys=array(Operator::SE_OPERATOR_TERMINAL, Operator::SE_REC, Operator::SE_REC_PRO, Operator::SE_REC_PTYPE);

	    $this->_fields= $this->_getFields();     //登陆时取得的用户信息字段


	}//-/

    /**
     *  getLevel
     *  get  level.
     */
    public static function getLevel(){

        if(isset($_SESSION[static::SE_OPERATOR_LEVEL])){
            return intval($_SESSION[static::SE_OPERATOR_LEVEL]);
        }else{
            return 0;
        }

    }//-/
	
	/**
	*  _getFields
	*  return field name array of user info.
	*/
	protected function _getFields(){
		
		$result= array(static::FIELD_USERNAME, static::FIELD_PWD, static::FIELD_IDX, static::FIELD_SHELVE);
		return $result;
		
	}//-/
	
	
	/**
	*  prepareLogin  
	*  准备login，返回sid，用于登录。
	*
	*  @return {array} =array(
	*                                         'result'         => 1 success ;                            
  	*                                         'sid'              => session id.
	*                                       )
	*/
	public function prepareLogin(){
		
		$result=array();
		
		if(Operator::isSessionStarted()==false){
			session_start();				
		}

		Log::val('session preLogin:', $_SESSION);

		//保存登录前的session数据。
		$this->_savePreLoginData();


		session_regenerate_id(true);  //TODO: 有时发生错误，无法删除session对象。
		$curSID=session_id();

		$_SESSION=array();

        $this->_recoverPreLoginData();
		
		$_SESSION[static::SE_PREPARED]=1;
		
		$result['result']=1;			
		$result[static::SE_SID_KEY]=$curSID;



        Log::val('session after login prepare:', $_SESSION);
		
		\CONFIG::$OPERATOR=new Operator($curSID);
		
		return static::resultFormatter($result);
	}//-/

    /**
     * _savePreLoginData
     * 登录之前的session中，需要维持的数据，保存到$this->_dataBeforeLogin数组中。
     */
    protected function _savePreLoginData(){

        $this->_preLoginData=array();

        //$sessionKeys=array(Operator::SE_OPERATOR_TERMINAL, Operator::SE_REC, Operator::SE_REC_PRO, Operator::SE_REC_PTYPE);

        foreach($this->_preLoginSessionKeys as $sessionK){
            if(isset($_SESSION[$sessionK])){
                $this->_preLoginData[$sessionK]=$_SESSION[$sessionK];
            }
        }

    }//-/

    /**
     * _recoverPreLoginData
     * 回复登录前的数据到session中。
     */
    protected function _recoverPreLoginData(){

        if(empty($this->_preLoginData)) return;

        foreach($this->_preLoginData as $sKey=>$sValue){
            $_SESSION[$sKey]=$sValue;
        }

    }//-/
	
	
	/**
	*  doLogin
	*
	*  @param {string} userName : username string
	*  @param {string} pwd          : user pwd string, not MD5 masked.
	*  @param {string} sid            : sid get from prepareLogin
	*
	*  @return {array} =array(
	*                                         'result'         => 1 success ; 0 login failed.
	*                                         'error_code' => error code, please refer to function _verify. 
	*                                         'user_data'   => when success, return user data. when error , this key does not exist.
	*                                         'sid'              => session id.
	*                                       )
	*/
	public function doLogin($userName, $pwd, $sid){
		
		$result=array();
		
		if(trim($sid)==''){
			$result['result']=0;			
			$result['error_code']=1000;
			$result['msg']='Fatal: sid not provided! pls CREATE res to get sid.';
			return static::resultFormatter($result);
		}
		
		
		if(Operator::isSessionStarted()===false){			
		    session_id($sid);
			session_start();					
		}
		
		if(! isset($_SESSION[static::SE_PREPARED]) ||  intval($_SESSION[static::SE_PREPARED])<1 ){
			$result['result']=0;			
			$result['error_code']=999;
			$result['msg']='Fatal: login res not prepared! pls CREATE login res first.';
			return static::resultFormatter($result);
		}
		
		
		$re=$this->_verify($userName, $pwd);
		if($re==1){//pwd valid,then do further login
            Log::val('sid befor proL:',session_id());
            Log::val('session before processLogin:', $_SESSION);

            //保存登录前的session数据。
            $this->_savePreLoginData();

			$this->_processLogin($sid);
			
			$result['result']=1;			
			$result['data']=$this->_userInfo;
			$result[static::SE_SID_KEY]=session_id();
			//$result['session_data']=$_SESSION;
			$result['msg']='login OK!';
			//Log::add($_SESSION);
	        //Log::add(session_id());
		
			
		}else{//not valid, return false.
			$result['result']=0;
			$result['error_code']=$re;	
			$result['msg']='failed when check db-login!';		
				
			
		}
		
		return static::resultFormatter($result);
		
		
	}//-/
	
	
	/**
	*  doLogout
	*
	*  @param {string} sessionId   : session id
	*
	*  @return {array} =array(
	*                                         'result'         => 1 success ; 0 failed.
	*                                         'error_code' => error code, please refer to function _verify. 
	*                                         'msg'            =>  error msg

	*                                       )
	*/
	public function doLogout($sessionId){

        //Log::val('session ID before LogOut:', session_id());
		
		$result=array();
		
		
		if(strlen(trim($sessionId))<=0){//if sid not provided
			//$result['result']=0;
			//$result['error_code']=1000;
			//$result['msg']='Fatal error: sid not provided!';
			//return static::resultFormatter($result);
			if(Operator::isSessionStarted()===false){
				session_start();		
			}
			$sessionId=session_id();
		}
		
		
		
		if(Operator::isSessionStarted()===false){
			
		    session_id($sessionId);
			session_start();	
			$result['session_process']='session is newly started!';
						
		}elseif( session_id()!=trim($sessionId)){	
			
			
			$result['current_sid']=session_id();			
			session_write_close();	
			
			session_id($sessionId);
			session_start();		
			$result['session_process']='session abort, and restarted!';						
		}

        //Log::val('session preLogOUT:', $_SESSION);
		//保存退出登录后，继续保留的session数据。
		$this->_savePreLoginData();

		
		
		// 重置会话中的所有变量
		//$_SESSION = array();
		session_unset(); 
       



		// 如果要清理的更彻底，那么同时删除会话 cookie
		// 注意：这样不但销毁了会话中的数据，还同时销毁了会话本身
		/*if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
		}*/

		//回复跨登录保留的session 数据。
		$this->_recoverPreLoginData();

        /*Log::val('session ID after LogOut:', session_id());
        Log::val('session after LogOut:', $_SESSION);*/

		// 最后，销毁会话
		//session_destroy();
		
		$result['result']=1;
		$result['error_code']=0;
		$result['msg']='logout successfully';		
		return static::resultFormatter($result);
		
		
	}//-/
	
	
	/**
	*  _verify
	*
	*  verify username and pwd,
	*  if valid, set $this->_userInfo into db row data from user table.
	*
	*  @param {string} userName   : username string
	*  @param {string} pwd        : user pwd string, not MD5 masked.	
	*  @return {int}:
	*                         1:  valid username and pwd
	*                         0:  username and pwd not match
	*                        -1:  username dose not exist
	*                        -2:  error when db reading
	*                        -3:  username is empty.
	*                        -4:  username format is not correct
	*                        -5:  user account is forbidden.
	*/
	protected function _verify($userName, $pwd){
	
		$userName=trim($userName);		
		if( ! is_string($userName) ||  strlen($userName)<=0 ){
			return -3;	
		}
		
		//username format check:
		$pt='/(^[a-zA-Z]+[a-zA-Z0-9]*$)|(^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+[\.a-zA-Z]*$)/';		
		if( !(preg_match($pt,$userName)>0)){			
			return -4;  //username format is not correct
		}
		
		$userName=addslashes($userName);			
		$pwd=trim($pwd);		
		
		$dbr=new DBReader();
		//$data=array();
		
		
		//$data['select_from']=static::TB_MAIN . " where ".static::FIELD_USERNAME."='$userName'";
		//$data['fields']=$this->_fields;
		$re=$dbr->readLine($this->_fields,static::TB_MAIN . " where ".static::FIELD_USERNAME."='$userName'");
		
		if($re!==false){
			
			if(count($re)>0){
				$row=$re;
				$pwdDB=$row[static::FIELD_PWD];
				$shelve=$row[static::FIELD_SHELVE];
				
				if( intval($shelve)==0){//user account is forbidden. 
				  return -5;	
				}
				
				if(strcmp(md5($pwd), $pwdDB)==0){//match, verify ok.						
						$this->_userInfo=$row;
						unset($this->_userInfo[static::FIELD_PWD]);//remove pwd field from info list for the sake of safety.
						return 1;						
				}else{//name and pwd does not match						
						return 0;							
				}			
				
			}else{//user name not exist.
				return -1;	
			}			
			
		}else{//error when db reading.
			 return -2;
		}
		
	}//-/
	
	/**
	*  _processLogin
	*  do some session initialization.
	*
	*/
	protected function _processLogin($sid){
		
		if(Operator::isSessionStarted()==false){
			session_start($sid);	
			//session_regenerate_id(true);
			
		}else{	
		    $_SESSION = array();			
			session_destroy();
			session_write_close();					
			session_start($sid);	
			
		}
		
		$_SESSION = array();
		
		$this->_setSession();		
		
		
	}//-/
	
	/**
	*  _setSession
	*  set session values of user info after login.
	*  [need over-write in sub-class]
	*/
	protected function _setSession(){

        $this->_recoverPreLoginData();
		
		if(is_array($this->_userInfo) && count($this->_userInfo)>0){		
			
			$_SESSION[Operator::SE_OPERATOR_NAME]=$this->_userInfo[ static::FIELD_USERNAME] ;
			$_SESSION[Operator::SE_OPERATOR_IDX]= intval($this->_userInfo[static::FIELD_IDX]) ;
			$_SESSION[Operator::SE_OPERATOR_TYPE]=Operator::TYPE_MEMBER;
			$_SESSION[static::SE_PREPARED]=2;

		}




        Log::val('session after login:', $_SESSION);
		
	}//-/
	
	
	
	
	
	
	
	
	
	
	

	
}//=/
