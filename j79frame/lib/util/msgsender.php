<?php
namespace j79frame\lib\util;
use j79frame\lib\util\Log;

/**LogManager
*  log类
*	log文件名，可以指定，由file_name公开属性给定。也可在add操作时，临时设置。
*  log文件的目录，不可由外部制定，默认定位\log目录
*
*	@author: jin rong (rong.king@foxmail.com)
*  @method:
*  					add:  添加log。
*
**/
class MsgSender
{
	
	protected  $_serverID='admin';
    protected $_serverPw='password';

    const HOST_ADDRESS='127.0.0.1';
    const PORT='61613';


    protected $_errMsg='';

    public function  __construct()
    {


    }//-/


    public function __get($name){
        switch ($name){
            case 'errMsg':
            case 'errorMsg':
                return $this->_errMsg;
                break;
            default:
                return NULL;
        }
    }//-/


    /**
     * send
     * function to send msg through stomp.
     * @param $topicChannelName : topicChannel name. like: 'group.shop_1'  , 'user.12'
     * @param $params : sending data. format like:
     *                  'title'=> 'title text',
     *                  'content'=>'msg content text',
     *                  'data'=>array(  detail data structure. )
     * @return bool : true/false
     */
    public function send($topicChannelName, $params){

        //本地服务器，则跳过。
        $curDNS=$_SERVER['HTTP_HOST'];
        if(strcasecmp($curDNS,'localhost')==0){
            return true;
        }

        $user = $this->_serverID;
        $password = $this->_serverPw;
        $destination  = '/topic/'.$topicChannelName;
        $host = self::HOST_ADDRESS;
        $port = self::PORT ;
        $body = is_string($params) ? $params : json_encode($params);//  "title&##&content";
        //try {

            //Log::val('Msg Sender destination:',$destination);
            //Log::val('Msg Sender params:',$body);
            $url = 'tcp://'.$host.":".$port;

            try{
                $stomp = new \Stomp($url, $user, $password);
                $stomp->send($destination, $body);
            }catch(\Exception $e){
                Log::val('Msg Sender error:',$e->getMessage());
                $this->_errMsg=$e->getMessage();
                return false;
            }

            //echo 'send message success';
            return true;
        /*} catch(StompException $e) {
            //echo $e->getMessage();

            Log::val('Msg Sender error:',$e->getMessage());
            $this->_errMsg=$e->getMessage();
            return false;
        }*/
    }//-/

    /**
     * sendToUser
     * @param $userIdx
     * @param $params : refer to $this->send
     * @return bool
     */
    public function sendToUser($userIdx, $params){

        if(intval($userIdx)>0){
            $re=$this->send('topic.user.'.$userIdx, $params);
            return $re;

        }else{
            return false;
        }

    }//-/

    /**
     * sendToGroup
     * @param $groupName :  like "community.10" , means community group of idx=10
     * @param $params : refer to $this->send
     * @return bool
     */
    public function sendToGroup($groupName, $params){
        if(!empty($groupName)){
            $re=$this->send('topic.group.'.$groupName, $params);
            return $re;

        }else{
            return false;
        }
    }//-/




}//============/CLASS: MsgSender
