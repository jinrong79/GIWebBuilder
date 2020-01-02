<?php
namespace j79frame\app\common;
use j79frame\lib\core\Operator;
use j79frame\lib\core\AuthVerifier;
use j79frame\lib\db\DBReader;
use j79frame\lib\util\Log;
/**
*  AuthVerifier
*  验证当前操作者对Model的操作权限。
* 
*/
class ModelAuthVerifier extends AuthVerifier {


    public $errorMsg='';  //error msg
	
	public function __construct(){
		
	}//-/
	

		
	/**
	*  verifyModel
	*  modelInterface层级的权限验证
	*  @param {string}    authType      : 验证的类型：shop- 店铺操作验证，adm-后台操作验证
	*  @param {string}    interfaceName : model-interface name string. pls refer to ModelInterface class
	*  @param {object}    curOperator   : current operator obj.
	*  @param {key-array} params        : params for this model-interface operations.
     * @return  bool: true- verify ok; false- not verified.
	*/						   
    public function verifyModel($authType, $interfaceName, $params, $curOperator){
		
		
		switch(strtolower($authType)){
			case 'shop':
			     return $this->_shopAuthorize($params,$curOperator,$interfaceName);
				 break;
            case 'shop_exp_template':
                return $this->_shopExpTempAuthorize($params,$curOperator,$interfaceName);
                break;
			case 'shoppro':
			     return $this->_shopproAuthorize($params,$curOperator,$interfaceName);
				 break;
		    case 'shoporder':
			     return $this->_shoporderAuthorize($params,$curOperator,$interfaceName);
				 break;
		    case 'adm':
			     return $this->_admAuthorize($params,$curOperator,$interfaceName);
				 break;

            case 'partner_bm':
                return $this->_partnerBMAuthorize($params,$curOperator,$interfaceName);
                break;
            case 'user_article':
                return $this->_userArticleAuthorize($params,$curOperator,$interfaceName);
                break;
            case 'community_manager_article_create':
                return $this->_communityManagerArticleAuthorize($params,$curOperator,$interfaceName,2);
                break;
            case 'community_manager_article_update':
                return $this->_communityManagerArticleAuthorize($params,$curOperator,$interfaceName,4);
                break;
            case 'community_manager_article_delete':
                return $this->_communityManagerArticleAuthorize($params,$curOperator,$interfaceName,8);
                break;

            case 'community_manager_article_menu':
                return $this->_communityManagerArticleMenuAuthorize($params,$curOperator,$interfaceName);
                break;
            case 'community_manager_member_modify':
                return $this->_communityManagerMemberAuthorize($params,$curOperator,$interfaceName,256);
                break;
            case 'community_manager_member_delete':
                return $this->_communityManagerMemberAuthorize($params,$curOperator,$interfaceName,512);
                break;

            case 'community_manager_info_mgt':
                return $this->_communityManagerAuthorize($params,$curOperator,$interfaceName,32768);
                break;

            case 'community_manager_bm_create':
                return $this->_communityManagerBmAuthorize($params,$curOperator,$interfaceName,32);
                break;
            case 'community_manager_bm_update':
                return $this->_communityManagerBmAuthorize($params,$curOperator,$interfaceName,64);
                break;
            case 'community_manager_bm_delete':
                return $this->_communityManagerBmAuthorize($params,$curOperator,$interfaceName,128);
                break;

            case 'community_manager_bmshop_create':
                return $this->_communityManagerBmShopAuthorize($params,$curOperator,$interfaceName,1024);
                break;
            case 'community_manager_bmshop_update':
                return $this->_communityManagerBmShopAuthorize($params,$curOperator,$interfaceName,2048);
                break;
            case 'community_manager_bmshop_delete':
                return $this->_communityManagerBmShopAuthorize($params,$curOperator,$interfaceName,4096);
                break;
            case 'community_manager_member_apply':
                return $this->_communityManagerMemberApplyAuthorize($params,$curOperator,$interfaceName,8192);
                break;

            case 'ws_apply_owner':
                return $this->_wsApplyOwnerAhthorize($params, $curOperator, $interfaceName);

		}		
		return false;		
		
		
	}//-/
	
	protected function  _wsApplyOwnerAhthorize($data, $curOperator, $interfaceName){


        $userIdx=$curOperator->getId();
        $idx=\GF::getValue('idx', $data);

        if(empty($idx)){ //apply_idx not provided. return false.
            $this->errorMsg='idx is empty.';
            return false;
        }

        $idxArr=\GF::parseIdx($idx); //if parse idx error, return false.
        if($idxArr===false){
            $this->errorMsg='parse idx error.';
            return false;
        }

        $idxSQL=\GF::parseIdxToSQL($idx);


        $dbr=new DBReader();
        $reCount=$dbr->readAmount("SELECT apply_idx FROM tb_ws_apply where user_idx=$userIdx AND apply_idx $idxSQL");
        if($reCount===false){//db read error, return false.
            return false;
        }

        //get idx amount.
        $idxArr= is_array($idxArr) ? $idxArr : array($idxArr);
        $idxAmount=count( $idxArr);

        //compare db result and idx amount.
        return $reCount==$idxAmount;


    }//-/
	
	/**
	*  _shopAuthorize
	*/
	protected function _shopAuthorize($data, $curOperator, $interfaceName){
		
		
		
		if(!isset($data['shop']) || intval($data['shop'])<=0){
			return false;
		}
		
		
		
		$shopIdx=intval($data['shop']);
		$userIdx=$curOperator->getId();	
		
		
		$dbr=new DBReader();		
		$result=$dbr->readLine(array('shop_idx','owner_idx'), "tb_shop WHERE shop_idx=$shopIdx");
		
		if($result===false){//if error in db reading, then return false
			return false;
		}
		if(empty($result)){//if shop not exists then return true,			
			return true;
		}
		
		$ownerIdx=isset($result['owner_idx'])? intval($result['owner_idx']) :0;		
		
		return $ownerIdx==$userIdx ? true : false;
		
		
	}//-/

    /**
     * _userArticleAuthorize
     * 用户内容的作者验证。
     * @param $data
     * @param $curOperator
     * @param $interfaceName
     * @return bool
     */
    protected function _userArticleAuthorize($data, $curOperator, $interfaceName){



        if(!isset($data['idx']) || intval($data['idx'])<=0){
            return false;
        }

        $idx=intval($data['idx']);
        $userIdx=intval($curOperator->getId());
        if($userIdx<=0){ //not login:
            return false;
        }


        $dbr=new DBReader();
        $result=$dbr->readLine(array('article_idx','article_author'), "tb_user_article WHERE article_idx=$idx");
        if($result===false){//if error in db reading, then return false
            return false;
        }
        if(empty($result)){//if  not exists then return true,
            return true;
        }

        $ownerIdx=isset($result['article_author'])? intval($result['article_author']) :0;

        return $ownerIdx==$userIdx ? true : false;


    }//-/

    /**
     * _communityManagerAuthorize
     * @param $data
     * @param $curOperator
     * @param $interfaceName
     * @param $role
     * @return bool
     */
    protected function _communityManagerAuthorize($data, $curOperator, $interfaceName, $role){

        return $this->_checkCommunityRoles($data, $curOperator, $role);

    }

    /**
     * _checkCommunityRoles
     * 社区权限值验证
     * @param $data
     * @param $curOperator
     * @param $role
     * @return bool
     */
    protected function _checkCommunityRoles($data, $curOperator, $role){
        //get user idx.
        $userIdx=intval($curOperator->getId());
        if($userIdx<=0) {
            return false;
        }

        //get community
        $communityIdx=\GF::getValue('data.community_idx', $data);
        if(is_null($communityIdx)){
            $communityIdx=\GF::getValue('community_idx', $data);
        }
        if(is_null($communityIdx)) {
            $communityIdx=\GF::getValue('community', $data);
        }
        if(is_null($communityIdx)) {
            Log::val('auth-community community idx is missing');
            return false;
        }

        $sql="select user_community_role from  tb_community_user where  user_idx=$userIdx AND user_community_role & $role = $role ";
        Log::val('auth-community manager - sql:', $sql);
        $dbr=new DBReader();
        $resultAmount=$dbr->readAmount($sql);
        if($resultAmount===false){
            return false;
        }
        return intval($resultAmount)>0 ? true :false;

    }

    protected function _communityManagerArticleMenuAuthorize($data, $curOperator, $interfaceName){
        return $this->_checkCommunityRoles($data, $curOperator, 16);
    }

    /**
     * _communityManagerArticleAuthorize
     * 社区内容管理权限验证
     * @param $data
     * @param $curOperator
     * @param $interfaceName
     * @param $role {int} : 操作权限id，参考GSetting.COMMUNITY_ROLES
     * @return bool
     */
    protected function _communityManagerArticleAuthorize($data, $curOperator, $interfaceName, $role){




        $jSQL="Select community_idx  From tb_user_article where  article_idx";

        return $this->_communityRoleCheckByJoinTb($data,$curOperator,$role,$jSQL);




    }//-/

    /**
     * _communityManagerBmAuthorize
     * 社区便民服务管理权限验证
     * @param $data
     * @param $curOperator
     * @param $interfaceName
     * @param $role
     * @return bool
     */
    protected function _communityManagerBmAuthorize($data, $curOperator, $interfaceName, $role){

       /* $idxSQL='';
        $idxArr=array('1');

        $userIdx=intval($curOperator->getId());
        if($userIdx<=0) {
            return false;
        }

        if($role==32){// if role is create:
            return $this->_checkCommunityRoles($data, $curOperator, $role);
        }

        //other action that need idx:
        if(!isset($data['idx'])){
            return false;
        }
        $idxArr=\GF::parseIdx($data['idx']);
        $idxSQL=\GF::parseIdxToSQL($data['idx']);
        if($idxSQL==''){
            return false;
        }

        $sql="select user_community_role from (Select community_idx  From tb_pro_service where  pro_idx $idxSQL) as tb1 LEFT JOIN  (select community_idx, user_idx, user_community_role from tb_community_user where user_idx=$userIdx ) as tb2
           ON tb1.community_idx=tb2.community_idx  
           where user_community_role & $role = $role ";

        $dbr=new DBReader();
        //Log::val('auth-community bm manager-sql:', $sql);
        $resultAmount=$dbr->readAmount($sql);
        if($resultAmount===false){
            return false;
        }

        return $resultAmount== count($idxArr) ? true : false;*/

        $jSQL="Select community_idx  From tb_pro_service LEFT JOIN tb_product_f ON tb_pro_service.pro_idx=tb_product_f.pro_idx LEFT JOIN tb_shop_service ON tb_product_f.shop_idx=tb_shop_service.shop_idx where  tb_pro_service.pro_idx";

        return $this->_communityRoleCheckByJoinTb($data,$curOperator,$role,$jSQL);




    }//-/


    protected function _communityManagerBmShopAuthorize($data, $curOperator, $interfaceName, $role){



        $jSQL="Select community_idx  From tb_shop_service  where  shop_idx";

        return $this->_communityRoleCheckByJoinTb($data,$curOperator,$role,$jSQL);




    }//-/


    protected function _communityManagerMemberAuthorize($data, $curOperator, $interfaceName, $role){




        $jSQL="Select community_idx  From tb_community_user where  user_idx";

        return $this->_communityRoleCheckByJoinTb($data,$curOperator,$role,$jSQL);

    }//-/

    protected function _communityManagerMemberApplyAuthorize($data, $curOperator, $interfaceName, $role){


        $jSQL="Select community_idx  From tb_community_user_apply where  apply_idx";

        return $this->_communityRoleCheckByJoinTb($data,$curOperator,$role,$jSQL);

    }//-/

    /**
     * _communityRoleCheckByJoinTb
     * check roles
     * @param $data
     * @param $curOperator
     * @param $role
     * @param $joinTbSQL {string} :  "Select community_idx  From tb_user_article where  article_idx“ . used when idx exists.
     * @return bool
     */
    protected function _communityRoleCheckByJoinTb($data, $curOperator,  $role, $joinTbSQL){

        $userIdx=intval($curOperator->getId());
        if($userIdx<=0) {
            return false;
        }
        $idx=\GF::getValue('idx', $data);

        //target idx exists:
        if(!is_null($idx)){

            $idxArr=\GF::parseIdx($data['idx']);
            $idxSQL=\GF::parseIdxToSQL($data['idx']);


            $sql="select user_community_role from ($joinTbSQL $idxSQL) as tb1 
                  LEFT JOIN  (select community_idx, user_community_role from tb_community_user where user_idx=$userIdx ) as tb2
                  ON tb1.community_idx=tb2.community_idx  
                  where user_community_role & $role = $role ";
            $dbr=new DBReader();
            Log::val('auth-community universal manager-sql:', $sql);
            $resultAmount=$dbr->readAmount($sql);
            if($resultAmount===false){
                return false;
            }
            return $resultAmount== count($idxArr) ? true : false;

        }

        //if idx not exists(add new/get list):
        return $this->_checkCommunityRoles($data, $curOperator, $role);


    }//-/

	
	/**
	*  _shopAuthorize
	*/
	protected function _shopproAuthorize($data, $curOperator, $interfaceName){
		
		
		
		if(!isset($data['idx']) || intval($data['idx'])<=0){
			return false;
		}
		
		
		
		$proIdx=intval($data['idx']);
		$userIdx=$curOperator->getId();	
		
		
		$dbr=new DBReader();		
		$result=$dbr->readValue('pro_idx', "tb_product_f WHERE pro_idx=$proIdx AND shop_idx IN ( SELECT  shop_idx from tb_shop WHERE owner_idx=$userIdx )");
		
		return $result>0 ? true : false;
		
		
	}//-/

    /**
     *  _shopAuthorize
     */
    protected function _shopExpTempAuthorize($data, $curOperator, $interfaceName){



        if(!isset($data['idx']) || intval($data['idx'])<=0){
            return false;
        }

        $Idx=intval($data['idx']);
        $userIdx=$curOperator->getId();

        $dbr=new DBReader();
        $result=$dbr->readValue('template_idx', "tb_exp_template WHERE template_idx=$Idx AND shop_idx IN ( SELECT  shop_idx from tb_shop WHERE owner_idx=$userIdx )");

        return $result>0 ? true : false;


    }//-/
	
	
	
	/**
	*  _shoporderAuthorize
	*/
	protected function _shoporderAuthorize($data, $curOperator, $interfaceName){
		
		
		
		if(!isset($data['idx']) || intval($data['idx'])<=0){
			return false;
		}
		
		
		
		$ItemIdx=intval($data['idx']);
		$userIdx=$curOperator->getId();	
		
		
		$dbr=new DBReader();		
		$result=$dbr->readValue('order_idx', "tb_order WHERE order_idx=$ItemIdx AND shop_idx IN ( SELECT  shop_idx from tb_shop WHERE owner_idx=$userIdx )");
		
		return $result>0 ? true : false;
		
		
	}//-/
	
	
	/**
	*  _shopAuthorize
	*/
	protected function _admAuthorize($data, $curOperator, $interfaceName){
		
		return $curOperator->getType()==Operator::TYPE_ADMIN ? true : false;
		
	}//-/

    /**
     *  _partnerBMAuthorize
     */
    protected function _partnerBMAuthorize($data, $curOperator, $interfaceName){

        return $curOperator->getType()==Operator::TYPE_PARTNER_BM? true : false;

    }//-/
	
	
	
	
	
	   
  
	
   
}//=/