<?php

//全局Model操作权限规则设置。
//$GLOBAL_AUTH_MAP
\CONFIG::$APP['authMap']=array(
    'Model' => array(  //指明是针对"Model"模型对象的方法权限设置。

        'SELECT' => '', // 空字符串，表示 所有用户，包括未登录用户
        'CREATE' => 'member', // member 表示登录用户
        'UPDATE' => array('member & owner', 'member & assistant'),
        //上面一行，意味着，权限允许的用户条件为 ，登录用户，同时为当前资源的主人用户， 或者  登录用户，同时为当前资源的协助管理者用户。
        'PATCH' => array('member & owner', 'member & assistant'),
        'DELETE' => array('member & owner')

    ),
    'BackendModel' => array(

        'SELECT' => 'admin', //  admin, 表示登录管理员用户
        'CREATE' => 'admin & create',
        'UPDATE' => array('admin & create', 'admin & update'),
        'PATCH' => array('admin & create', 'admin & update', 'admin & patch'),
        'DELETE' => array('admin & delete')

    ),
    'j79frame\app\controller\AdmProUpdater' => array(

        'SELECT' => 'admin',
        'CREATE' => 'admin',
        'UPDATE' => 'admin',
        'PATCH' => 'admin',
        'DELETE' => 'admin'

    ),
    'j79frame\app\controller\AdmFProUpdater' => array(

        'SELECT' => 'admin',
        'CREATE' => 'admin',
        'UPDATE' => 'admin',
        'PATCH' => 'admin',
        'DELETE' => 'admin'

    ),
    'j79frame\app\controller\AdmShopUpdater' => array(

        'SELECT' => 'admin',
        'CREATE' => 'admin',
        'UPDATE' => 'admin',
        'PATCH' => 'admin',
        'DELETE' => 'admin'

    ),
    'j79frame\app\controller\MyDeliverys' => array(

        'SELECT' => 'member',
        'CREATE' => 'member',
        'UPDATE' => 'member',
        'PATCH' => 'member',
        'DELETE' => 'member'

    ),
    'j79frame\app\controller\MyCart' => array(

        'SELECT' => 'member',
        'CREATE' => 'member',
        'UPDATE' => 'member',
        'PATCH' => 'member',
        'DELETE' => 'member'

    ),

    'j79frame\app\controller\MyFavPros' => array(

        'SELECT' => 'member',
        'CREATE' => 'member',
        'UPDATE' => 'member',
        'PATCH' => 'member',
        'DELETE' => 'member'

    ),
    'j79frame\app\controller\MyFavShops' => array(

        'SELECT' => 'member',
        'CREATE' => 'member',
        'UPDATE' => 'member',
        'PATCH' => 'member',
        'DELETE' => 'member'

    ),
    'j79frame\app\controller\MyShops' => array(

        'SELECT' => 'member',
        'CREATE' => 'member',
        'UPDATE' => 'member',
        'PATCH' => 'member',
        'DELETE' => 'member'

    ),

    'j79frame\app\controller\FMyCart' => array(

        'SELECT' => 'member',
        'CREATE' => 'member',
        'UPDATE' => 'member',
        'PATCH' => 'member',
        'DELETE' => 'member'

    ),
    'j79frame\app\controller\MyOrders' => array(

        'SELECT' => 'member',
        'CREATE' => 'member',
        'UPDATE' => 'member',
        'PATCH' => 'member',
        'DELETE' => 'member'

    ),
    'j79frame\app\controller\AdmAdminUpdater' => array(

        'SELECT' => 'admin',
        'CREATE' => 'admin',
        'UPDATE' => 'admin',
        'PATCH' => 'admin',
        'DELETE' => 'admin'

    ),
    'j79frame\app\controller\AdmOrderUpdater' => array(

        'SELECT' => 'admin',
        'CREATE' => 'admin',
        'UPDATE' => 'admin',
        'PATCH' => 'admin',
        'DELETE' => 'admin'

    ),
    'j79frame\app\controller\UserMgtSrv' => array(

        'SELECT' => 'member',
        'CREATE' => 'member',
        'UPDATE' => 'member',
        'PATCH' => 'member',
        'DELETE' => 'member'

    ),

    'j79frame\app\controller\VerifyShopSrv' => array(

        'SELECT' => 'member',
        'CREATE' => 'member',
        'UPDATE' => 'member',

    ),

    'j79frame\app\controller\UserArticleUpdater' => array(

        'SELECT' => 'member',
        'CREATE' => 'member',
        'UPDATE' => 'member',
        'PATCH'  => 'member',
        'DELETE' => 'member'

    ),

    /*  direct view page auth settings  */


    'adm/pro_edit' => 'admin',
    'adm/pro_list' => 'admin',
    'adm/fpro_edit' => 'admin',
    'adm/fpro_list' => 'admin',
    'adm/shop_edit' => 'admin',
    'adm/shop_list' => 'admin',
    'adm/rshop_list' => 'admin',
    'adm/rshop_edit' => 'admin',
    'adm/user_edit' => 'admin',
    'adm/user_list' => 'admin',
    'adm/admin_edit' => 'admin',
    'adm/admin_list' => 'admin',
    'adm/index' => 'admin',
    'adm/order_edit' => 'admin',
    'adm/order_list' => 'admin',
    'adm/db_fields' => 'admin',
    'adm/msgsend' => 'admin',
    'adm/homepage_edit_list' => 'admin',
    'adm/homepage_edit' => 'admin',
    'adm/edit' => 'admin',
    'adm/list' => 'admin',


    'mycart' => 'member',
    'user_home' => 'member',
    'myorder_add' => 'member',
    'myorder_cancel' => 'member',
    'myorders' => 'member',
    'myorder_detail' => 'member',
    'mywsorderitems' => 'member',
    'mydeliverys' => 'member',
    'mydelivery_add' => 'member',
    'myshops' => 'member',
    'myfavshops' => 'member',
    'myfavpros' => 'member',
    'mycommunity'=>'member',
    'owner_fpro_list' => 'member',
    'owner_fpro_edit' => 'member',
    'claim_shop' => 'member',
    'loan/apply' => 'member',
    'loan/myapplies' => 'member',
    'my_ws_apply_edit'=>'member',
    'user_task'=>'member',
    'user_task_rec'=>'member',



);



