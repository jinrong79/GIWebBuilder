<?php
namespace j79frame\lib\core;

use j79frame\lib\core\LoginMgt;
use j79frame\lib\core\Operator;

/**
 *  AuthVerifier
 *  验证当前操作者对Model的操作权限。
 *
 */
class AuthVerifier extends  j79obj
{

    //当前的操作者（登录的用户）

    protected $_globalAuthMap;  //全局权限判断用的列表。

    public $roles = array();      //verify后的结果，所需的roles数组。e.g.:=array('member & owner' , 'member & assistant')

    public $loginType;          //verify后的结果，所需登录的种类，后台登录，还是用户登录：
    //Operator::TYPE_ADMIN， Operator::TYPE_MEMBER.


    public $failedType = 0;      // 1- need member login; 2- need admin login; 3- need partner login:

    // 10- admin level restricted;    20- not exists action;  30- role not match


    public function __construct()
    {

        $this->loginType = Operator::TYPE_NOT_LOGINED;

        //ini auth map
        //有默认值：如果在此map上没有发现Model名，
        //               即采用如下默认值：
        //                                        SELECT     =>   ''
        //                                        CREATE    =>   'member'
        //                                        UPDATE    =>   'member & owner'
        //                                        PATCH      =>   'member & owner'
        //                                        DELETE     =>   'member & owner'
        //

        require(\CONFIG::$PATH_ROOT . "/global.auth.php");
        $this->_globalAuthMap = \CONFIG::$APP['authMap'];


        /*$this->_globalAuthMap=array(
                                                 'Model'  => array(  //指明是针对"Model"模型对象的方法权限设置。

                                                                              'SELECT'  => '', // 空字符串，表示 所有用户，包括未登录用户
                                                                              'CREATE' => 'member', // member 表示登录用户
                                                                              'UPDATE' => array('member & owner' , 'member & assistant'),
                                                                              //上面一行，意味着，权限允许的用户条件为 ，登录用户，同时为当前资源的主人用户， 或者  登录用户，同时为当前资源的协助管理者用户。
                                                                              'PATCH'   => array('member & owner' , 'member & assistant'),
                                                                              'DELETE'  => array('member & owner' )

                                                                            ),
                                                'BackendModel'=>array( //指明是针对"BackendModel"模型对象的方法权限设置。

                                                                              'SELECT'  => 'admin', //  admin, 表示登录管理员用户
                                                                              'CREATE' => 'admin & create', //  // 表示，需要登录管理员用户，且拥有create权限
                                                                              'UPDATE' => array('admin & create' , 'admin & update' ),
                                                                              //上面一行，意味着，权限允许的用户条件为 :1)登录管理员用户，且拥有全局create权限  或者 2）登录管理员用户，且拥有update权限
                                                                              'PATCH'   => array('admin & create' , 'admin & update' , 'admin & patch' ),
                                                                              'DELETE'  => array('admin & delete' )

                                                                            )

                                               );*/
    }//-/


    /**
     *  getGlobalAuthMap
     *  get global auth-map by including global.auth.php file.
     *  return {key-array} :  global auth map settings in  key array.
     */
    public static function getGlobalAuthMap()
    {
        require(\CONFIG::$PATH_ROOT . "/global.auth.php");
        return \CONFIG::$APP['authMap'];
    }//-/


    /**
     * verify
     * verify authorization for the certain action towards  a given model object instance requested by the operator.
     * use _auth_map to find the authorization string, and check validation.
     *
     * @param {Controller} targetObj          :   targeted controller to operate.
     * @param {string}     appliedActionName  :   applied action name (to targeted model, it is often the name of method)
     * @param {Operater}   curOperator        :   current operator who applied this action request.
     *
     * @return {bool}  true :authorized  ; false: not authorized.
     */
    public function verify($targetObj, $appliedActionName, $curOperator)
    {


        //获取当前Model对象的全局权限规则
        //require (\CONFIG::$PATH_ROOT."/global.auth.php");

        $re = true;


        /*//admin level basic check:
        if (is_subclass_of($targetObj, "\\j79frame\\lib\\controller\\AdmController")) {

            $opsLvl = LoginMgt::getLevel();
            switch (strtoupper($appliedActionName)) {
                case 'SELECT':
                    $re = $opsLvl >= 1 ? true : false;
                    break;
                case 'CREATE':
                    $re = $opsLvl >= 3 ? true : false;
                    break;
                case 'UPDATE' :
                    $re = $opsLvl >= 3 ? true : false;
                    break;
                case 'PATCH'  :
                    $re = $opsLvl >= 2 ? true : false;
                    break;
                case 'DELETE' :
                    $re = $opsLvl >= 4 ? true : false;
                    break;
                case 'HEAD'   :
                case 'OPTIONS':
                    $re = true;
                    break;
                default:
                    $re = true;

            }

            if ($re === false) {
                $this->failedType = 10;  //adm level restricted.
                return false;
            }

        }*/

        //verify by global auth setting map:
        $globalAuthReg = $this->_globalAuthMap;
        if (array_key_exists(get_class($targetObj), $globalAuthReg)) {//全局权限设定里面，存在当前model的设定


            $authReg = $globalAuthReg[get_class($targetObj)];


            if (is_array($authReg) && array_key_exists(strtoupper($appliedActionName), $authReg)) {//存在当前action方法的权限设定


                //获取当前方法的权限规则数据.
                //格式举例:
                //        curAuth= array('member & owner' , 'member & assistant')
                //		  意味着，权限赋予2个情况:
                //        1）登录用户，同时是本Model的Owner ；
                //        2）登录用户，同时是本Model的assistant（协助管理者，子账号概念)
                $curAuth = $authReg[strtoupper($appliedActionName)];

                //verify authorization with curAuth data.
                $re = $this->_verifyRoles($curAuth, $curOperator, $targetObj);

                if ($re === false) {

                    return false;
                }

            }

        }

        if($re===false){
            return false;
        }

        //读取model自身定义的局部权限设定
        $this->failedType = 20;
        //$re = $re != false ? $targetObj->isAllowed($appliedActionName, $curOperator) : false;

        $re =$targetObj->isAllowed($appliedActionName, $curOperator);

        return $re;


    }//-/

    /**
     *  verifyDirectViewPage
     *
     *  verify authorization of direct view page.
     * @param {string}   pageName    : page name not include '.php'.
     * @param {operator} curOperator : current operator.
     * @return {bool}                : valid or not. true- authorized access; false- not authorized access.
     *
     */
    public function verifyDirectViewPage($pageName, $curOperator)
    {

        $globalAuthReg = $this->_globalAuthMap;
        $re = true;
        if (array_key_exists(strtolower($pageName), $globalAuthReg)) {//全局权限设定里面，存在当前model的设定
            $curAuth = $globalAuthReg[strtolower($pageName)];
            //verify authorization with curAuth data.
            $re = $this->_verifyRoles($curAuth, $curOperator);
        }
        return $re;
    }//-/

    /**
     *  _verifyRoles
     *  verify authorization by roles data.
     *
     * @param {key-array} roles       : roles data. Format like:
     *                                   array('member & owner' , 'member & assistant')
     *                                   意味着，权限赋予2个情况:
     *                                   1）登录用户，同时是本Model的Owner ；
     *                                   2）登录用户，同时是本Model的assistant（协助管理者，子账号概念)
     * @param {Operater}  curOperator : current operator who applied this action request.
     * @param {object}    targetObj   : control target obj.
     *                                   default[NULL]
     *
     */
    protected function _verifyRoles($roles, $curOperator, $targetObj = NULL)
    {

        if (!is_array($roles)) {
            $curAuth = array($roles);
        }

        $this->roles = $curAuth;
        $re = true;

        for ($i = 0; $i < count($curAuth); $i++) {

            $roles = preg_split('/[\s]*&[\s]*/', $curAuth[$i]);
            if (count($roles) <= 0) continue;

            for ($j = 0; $j < count($roles); $j++) {

                switch (strtolower($roles[$j])) {

                    case 'member':
                        $re = $re && ($curOperator->getType() == Operator::TYPE_MEMBER);
                        $this->failedType = 1;
                        $this->loginType = Operator::TYPE_MEMBER;
                        break;
                    case 'admin':

                        $re = $re && ($curOperator->getType() == Operator::TYPE_ADMIN);
                        $this->failedType = 2;
                        $this->loginType = Operator::TYPE_ADMIN;

                        break;
                    case 'owner':
                        $this->failedType = 30;
                        if (!is_null($targetObj)) {
                            $re = $re && ($targetObj->isOwner($curOperator));
                        } else {
                            $re = false;
                        }
                        break;
                    case 'assistant':
                        $this->failedType = 30;
                        if (!is_null($targetObj)) {
                            $re = $re && ($targetObj->isAssistant($curOperator));
                        } else {
                            $re = false;
                        }
                        break;

                }

                if ($re === false)
                    break;

            }

            if ($re === false)
                break;

        }
        return $re;


    }//-/


}//=/