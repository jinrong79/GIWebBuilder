<?php
//全局Model Interface列表，用于设置每个model操作的权限。
\CONFIG::$APP['MIsetting']= array(

    //interface名，唯一，必须全部小写。
    'shop_pro_add' => array(
        'model' => '\\j79frame\\app\\model\\FProduct',
        //model的类名
        'method' => 'add',
        //model的function名
        'label' => '店铺新产品添加',
        //说明信息
        'auth_type' => 'shop',
        //权限验证的类型，当前为shop权限验证。


    ),

    'shop_pro_edit' => array(
        'model' => '\\j79frame\\app\\model\\FProduct',
        'method' => 'update',
        'label' => '店铺产品修改',
        'auth_type' => 'shoppro',

    ),

    'shop_pro_delete' => array(
        'model' => '\\j79frame\\app\\model\\FProduct',
        'method' => 'delete',
        'label' => '店铺产品删除',
        'auth_type' => 'shoppro',

    ),

    'shop_pro_offshelve' => array(
        'model' => '\\j79frame\\app\\model\\FProduct',
        'method' => 'offShelve',
        'label' => '店铺产品下架',
        'auth_type' => 'shoppro',

    ),

    'shop_pro_onshelve' => array(
        'model' => '\\j79frame\\app\\model\\FProduct',
        'method' => 'onShelve',
        'label' => '店铺产品上架',
        'auth_type' => 'shoppro',

    ),

    'shop_pro_setshelve' => array(
        'model' => '\\j79frame\\app\\model\\FProduct',
        'method' => 'patchShelve',
        'label' => '店铺产品上架',
        'auth_type' => 'shoppro',

    ),

    'shop_pro_cat_update' => array(
        'model' => '\\j79frame\\app\\model\\FProduct',
        'method' => 'setCategory',
        'label' => '店铺产品的分类调整',
        'auth_type' => 'shoppro',

    ),

    'shop_pro_menu_update' => array(
        'model' => '\\j79frame\\app\\model\\FProduct',
        'method' => 'setMenu',
        'label' => '店铺产品的店铺菜单调整',
        'auth_type' => 'shoppro',

    ),

    'shop_pro_get' => array(
        'model' => '\\j79frame\\app\\model\\FProduct',
        'method' => 'get',
        'label' => '店铺产品可修改详细信息',
        'auth_type' => 'shoppro',

    ),

    'shop_pro_list' => array(
        'model' => '\\j79frame\\app\\model\\FProduct',
        'method' => 'getList',
        'label' => '店铺产品列表',
        'auth_type' => 'shop',

    ),


    'shop_offshelve' => array(
        'model' => '\\j79frame\\app\\model\\Shop',
        'method' => 'offShelve',
        'label' => '店铺暂停营业',
        'auth_type' => 'shop',

    ),

    'rshop_verify' => array(
        'model' => '\\j79frame\\app\\model\\updater\\RShopUpdater',
        'method' => 'checkShopClaim',
        'label' => '实体店铺信息认证',

    ),


    'shop_owner_get' => array(
        'model' => '\\j79frame\\app\\model\\updater\\ShopUpdater',
        'method' => 'get',
        'label' => '店铺详细信息',
        'auth_type' => 'shop',
        //权限验证的类型，当前为shop权限验证。


    ),
    'shop_owner_list' => array(
        'model' => '\\j79frame\\app\\model\\lister\\ShopList',
        'method' => 'get',
        'label' => '拥有的店铺列表',
        'auth_type' => 'shop',
        //权限验证的类型，当前为shop权限验证。


    ),
    'shop_owner_edit' => array(
        'model' => '\\j79frame\\app\\model\\updater\\ShopUpdater',
        'method' => 'update',
        'label' => '店铺修改',
        'auth_type' => 'shop',
        //权限验证的类型，当前为shop权限验证。


    ),

    'shop_owner_onshelve' => array(
        'model' => '\\j79frame\\app\\model\\updater\\ShopUpdater',
        'method' => 'onShelve',
        'label' => '店铺上架',
        'auth_type' => 'shop',


    ),

    'shop_owner_offshelve' => array(
        'model' => '\\j79frame\\app\\model\\updater\\ShopUpdater',
        'method' => 'offShelve',
        'label' => '店铺下架',
        'auth_type' => 'shop',


    ),

    'shop_owner_set_cat' => array(
        'model' => '\\j79frame\\app\\model\\updater\\ShopUpdater',
        'method' => 'setCategory',
        'label' => '店铺设置分类',
        'auth_type' => 'shop',


    ),

    'shop_owner_set_bankcard' => array(
        'model' => '\\j79frame\\app\\model\\updater\\ShopUpdater',
        'method' => 'setBankCard',
        'label' => '店铺设置分类',
        'auth_type' => 'shop',


    ),

    //shop menu:

    'shop_menu_update' => array(
        'model' => '\\j79frame\\app\\model\\ShopMenuXMLUpdater',
        'method' => 'update',
        'label' => '店铺菜单的更新',
        'auth_type' => 'shop',

    ),
    //community article menu:
    'community_article_menu_update' => array(
        'model' => '\\j79frame\\app\\model\\CommunityArticleMenuXMLUpdater',
        'method' => 'update',
        'label' => '社区文章菜单的更新',
        'auth_type' => 'adm|community_manager_article_menu',

    ),

    //shop order:
    'shop_order_list' => array(
        'model' => '\\j79frame\\app\\model\\Order',
        'method' => 'getShopOrders',
        'label' => '店铺订单列表',
        'auth_type' => 'shop',

    ),

    'shop_order_get' => array(
        'model' => '\\j79frame\\app\\model\\Order',
        'method' => 'getShopOrderDetail',
        'label' => '店铺订单详细信息',
        'auth_type' => 'shoporder',

    ),

    'shop_order_get_summary' => array(
        'model' => '\\j79frame\\app\\model\\Order',
        'method' => 'getSummary',
        'label' => '获取店铺订单summary信息',
        'auth_type' => 'shoporder',

    ),

    'shop_order_get_items' => array(
        'model' => '\\j79frame\\app\\model\\Order',
        'method' => 'getOrderItems',
        'label' => '获取店铺订单的商品列表',
        'auth_type' => 'shoporder',

    ),

    'shop_order_update' => array(
        'model' => '\\j79frame\\app\\model\\Order',
        'method' => 'update',
        'label' => '店铺订单修改',
        'auth_type' => 'shoporder',

    ),

    'shop_order_set_shipped' => array(
        'model' => '\\j79frame\\app\\model\\Order',
        'method' => 'setShipped',
        'label' => '店铺订单发货',
        'auth_type' => 'shoporder',

    ),

    'shop_order_cancel' => array(
        'model' => '\\j79frame\\app\\model\\Order',
        'method' => 'cancelOrderByShop',
        'label' => '店铺订单关闭',
        'auth_type' => 'shoporder',

    ),

    'shop_order_get_statistic' => array(
        'model' => '\\j79frame\\app\\model\\Order',
        'method' => 'statisticShop',
        'label' => '店铺订单统计',
        'auth_type' => 'shop',


    ),

    'shop_order_owned_statistic' => array(
        'model' => '\\j79frame\\app\\model\\Order',
        'method' => 'statisticOwned',
        'label' => '店主所有店铺订单统计',

    ),

    'shop_order_get_amount' => array(
        'model' => '\\j79frame\\app\\model\\Order',
        'method' => 'getShopOrderAmount',
        'label' => '店铺订单总数',
        'auth_type' => 'shop',


    ),

    'shop_order_get_amount_all' => array(
        'model' => '\\j79frame\\app\\model\\Order',
        'method' => 'getShopOrderAmountAll',
        'label' => '我的所有店铺订单总数',


    ),

    //shop exp template:

    'shop_exp_template_list'=>array(
        'model' => '\\j79frame\\app\\model\\ShopExpTemplate',
        'method' => 'getList',
        'label' => '店铺运费模板列表',
        'auth_type' => 'shop',
    ),

    'shop_exp_template_get'=>array(
        'model' => '\\j79frame\\app\\model\\ShopExpTemplate',
        'method' => 'getDetail',
        'label' => '店铺运费模板详细',
        'auth_type' => 'shop_exp_template',
    ),

    'shop_exp_template_add'=>array(
        'model' => '\\j79frame\\app\\model\\ShopExpTemplate',
        'method' => 'add',
        'label' => '店铺运费模板添加',
        'auth_type' => 'shop',
    ),

    'shop_exp_template_update'=>array(
        'model' => '\\j79frame\\app\\model\\ShopExpTemplate',
        'method' => 'update',
        'label' => '店铺运费模板修改',
        'auth_type' => 'shop_exp_template',
    ),

    'shop_exp_template_delete'=>array(
        'model' => '\\j79frame\\app\\model\\ShopExpTemplate',
        'method' => 'delete',
        'label' => '店铺运费模板删除',
        'auth_type' => 'shop_exp_template',
    ),

    'shop_exp_template_set_default'=>array(
        'model' => '\\j79frame\\app\\model\\ShopExpTemplate',
        'method' => 'setDefault',
        'label' => '店铺运费模板-设置为默认',
        'auth_type' => 'shop_exp_template',
    ),
    'shop_exp_template_unset_default'=>array(
        'model' => '\\j79frame\\app\\model\\ShopExpTemplate',
        'method' => 'unsetDefault',
        'label' => '店铺运费模板-去掉默认标记',
        'auth_type' => 'shop_exp_template',
    ),








    'user_bankcard_get' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BankCardUpdater',
        'method' => 'get',
        'label' => '取得用户绑定的银行卡详细信息',


    ),

    'user_bankcard_list' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BankCardUpdater',
        'method' => 'getList',
        'label' => '取得用户绑定的银行卡列表',


    ),

    'user_bankcard_add' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BankCardUpdater',
        'method' => 'add',
        'label' => '用户添加银行卡',


    ),

    'user_bankcard_update' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BankCardUpdater',
        'method' => 'update',
        'label' => '修改用户银行卡',


    ),

    'user_get_user_info' => array(
        'model' => '\\j79frame\\app\\model\\User',
        'method' => 'getOpenUserList',
        'label' => '取得其他用户的公开信息',
    ),

    'user_change_pwd' => array(
        'model' => '\\j79frame\\app\\model\\User',
        'method' => 'changePwd',
        'label' => '修改用户密码',
    ),

    'user_update_info' => array(
        'model' => '\\j79frame\\app\\model\\User',
        'method' => 'updateInfo',
        'label' => '修改用户信息',
    ),


    //load apply:
    'user_loanapply_get' => array(
        'model' => '\\j79frame\\app\\model\\LoanApplication',
        'method' => 'get',
        'label' => '取得用户的贷款申请信息',


    ),
    'user_loanapply_list' => array(
        'model' => '\\j79frame\\app\\model\\LoanApplication',
        'method' => 'getList',
        'label' => '取得用户的贷款申请列表',


    ),
    'user_loanapply_add' => array(
        'model' => '\\j79frame\\app\\model\\LoanApplication',
        'method' => 'add',
        'label' => '添加用户的贷款申请信息',


    ),
    'user_loanapply_update' => array(
        'model' => '\\j79frame\\app\\model\\LoanApplication',
        'method' => 'update',
        'label' => '修改用户的贷款申请信息',
    ),

    'user_loanapply_cancel' => array(
        'model' => '\j79frame\app\model\updater\LoanApplyUpdater',
        'method' => 'cancel',
        'label' => '取消用户的贷款申请信息',


    ),

    //user estate info:
    'user_estate_get' => array(
        'model' => '\\j79frame\\app\\model\\updater\\EstateUpdater',
        'method' => 'get',
        'label' => '取得用户的房产信息',


    ),
    'user_estate_list' => array(
        'model' => '\\j79frame\\app\\model\\updater\\EstateUpdater',
        'method' => 'getList',
        'label' => '取得用户的房产列表',


    ),
    'user_estate_add' => array(
        'model' => '\\j79frame\\app\\model\\updater\\EstateUpdater',
        'method' => 'add',
        'label' => '添加用户的房产信息',


    ),
    'user_estate_delete' => array(
        'model' => '\\j79frame\\app\\model\\updater\\EstateUpdater',
        'method' => 'delete',
        'label' => '删除用户的房产信息',


    ),

    //user vehicle info:
    'user_vehicle_get' => array(
        'model' => '\\j79frame\\app\\model\\updater\\VehicleUpdater',
        'method' => 'get',
        'label' => '取得用户的车辆信息',


    ),
    'user_vehicle_list' => array(
        'model' => '\\j79frame\\app\\model\\updater\\VehicleUpdater',
        'method' => 'getList',
        'label' => '取得用户的车辆列表',


    ),
    'user_vehicle_add' => array(
        'model' => '\\j79frame\\app\\model\\updater\\VehicleUpdater',
        'method' => 'add',
        'label' => '添加用户的车辆信息',


    ),
    'user_vehicle_delete' => array(
        'model' => '\\j79frame\\app\\model\\updater\\VehicleUpdater',
        'method' => 'delete',
        'label' => '删除用户的车辆信息',


    ),

    ////user rshop for loan apply:
    'user_loanrshop_get' => array(
        'model' => '\\j79frame\\app\\model\\updater\\LoanRShopUpdater',
        'method' => 'get',
        'label' => '取得用户的申请贷款的店铺信息',


    ),
    'user_loanrshop_list' => array(
        'model' => '\\j79frame\\app\\model\\updater\\LoanRShopUpdater',
        'method' => 'getList',
        'label' => '取得用户的申请贷款的店铺信息列表',


    ),
    'user_loanrshop_add' => array(
        'model' => '\\j79frame\\app\\model\\updater\\LoanRShopUpdater',
        'method' => 'add',
        'label' => '添加申请贷款的店铺信息',


    ),
    'user_loanrshop_delete' => array(
        'model' => '\\j79frame\\app\\model\\updater\\LoanRShopUpdater',
        'method' => 'delete',
        'label' => '删除申请贷款的店铺信息',

    ),


    //user bmservice:
    'bmservice_add' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'add',
        'label' => '便民服务添加',


    ),
    'bmservice_update' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'update',
        'label' => '便民服务添加',

    ),
    'bmservice_get' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'getDetail',
        'label' => '取得便民服务详细信息',

    ),
    'bmservice_list' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'getListByOwner',
        'label' => '取得便民服务详细信息',

    ),

    'bmservice_onshelve' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'onShelveByOwner',
        'label' => '便民服务上架',

    ),

    'bmservice_offshelve' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'offShelveByOwner',
        'label' => '取得便民服务下架',

    ),

    'bmservice_delete' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'delete',
        'label' => '删除便民服务',


    ),


    //user pro_sh:
    'pro_sh_add' => array(
        'model' => '\\j79frame\\app\\model\\ProductSH',
        'method' => 'add',
        'label' => '二手商品添加',


    ),
    'pro_sh_update' => array(
        'model' => '\\j79frame\\app\\model\\ProductSH',
        'method' => 'update',
        'label' => '二手商品添加',

    ),
    'pro_sh_get' => array(
        'model' => '\\j79frame\\app\\model\\ProductSH',
        'method' => 'getDetail',
        'label' => '取得二手商品详细信息',

    ),
    'pro_sh_list' => array(
        'model' => '\\j79frame\\app\\model\\ProductSH',
        'method' => 'getListByOwner',
        'label' => '取得二手商品详细信息',

    ),

    'pro_sh_onshelve' => array(
        'model' => '\\j79frame\\app\\model\\ProductSH',
        'method' => 'onShelveByOwner',
        'label' => '二手商品上架',

    ),

    'pro_sh_offshelve' => array(
        'model' => '\\j79frame\\app\\model\\ProductSH',
        'method' => 'offShelveByOwner',
        'label' => '取得二手商品下架',

    ),

    'pro_sh_delete' => array(
        'model' => '\\j79frame\\app\\model\\ProductSH',
        'method' => 'delete',
        'label' => '删除二手商品',


    ),



    //user_article manager
    'user_article_manager_get' => array(
        'model' => '\\j79frame\\app\\model\\UserArticle',
        'method' => 'getDetail',
        'label' => '前台-管理员-取得文章内容详细',
        'auth_type' => '',


    ),
    'user_article_manager_list' => array(
        'model' => '\\j79frame\\app\\model\\UserArticle',
        'method' => 'getList',
        'label' => '前台-管理员-取得文章列表',
        'auth_type' => '',

    ),
    'user_article_manager_add' => array(
        'model' => '\\j79frame\\app\\model\\UserArticle',
        'method' => 'addFront',
        'label' => '前台-管理员-发布内容',
        'auth_type' => 'community_manager_article_create',

    ),

    'user_article_manager_update' => array(
        'model' => '\\j79frame\\app\\model\\UserArticle',
        'method' => 'update',
        'label' => '前台-管理员-修改文章内容',
        'auth_type' => 'user_article|community_manager_article_update',

    ),

    'user_article_manager_delete' => array(
        'model' => '\\j79frame\\app\\model\\UserArticle',
        'method' => 'delete',
        'label' => '前台-管理员-删除文章内容',
        'auth_type' => 'user_article|community_manager_article_delete',

    ),

    'user_article_manager_onshelve' => array(
        'model' => '\\j79frame\\app\\model\\UserArticle',
        'method' => 'onShelve',
        'label' => '前台-管理员-文章内容上线',
        'auth_type' => 'community_manager_article_update',

    ),
    'user_article_manager_offshelve' => array(
        'model' => '\\j79frame\\app\\model\\UserArticle',
        'method' => 'offShelve',
        'label' => '前台-管理员-文章内容下线',
        'auth_type' => 'community_manager_article_update',

    ),
    'user_article_manager_setcategory' => array(
        'model' => '\\j79frame\\app\\model\\UserArticle',
        'method' => 'setCategory',
        'label' => '前台-管理员-文章内容设置分类',
        'auth_type' => 'user_article|community_manager_article_update',

    ),


    //community
    'community_get' => array(
        'model' => '\\j79frame\\app\\model\\Community',
        'method' => 'getDetail',
        'label' => '前台-管理员-取得文章内容详细',
        'auth_type' => '',


    ),
    'community_list' => array(
        'model' => '\\j79frame\\app\\model\\Community',
        'method' => 'getList',
        'label' => '前台-管理员-取得文章列表',
        'auth_type' => '',

    ),


    //community-user manager
    'community_manager_member_get' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUser',
        'method' => 'getDetail',
        'label' => '社区管理员-取得社区会员详细内容',
        'auth_type' => 'community_manager_member_modify',


    ),
    'community_manager_member_list' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUser',
        'method' => 'getList',
        'label' => '社区管理员-取得社区会员列表',
        'auth_type' => 'community_manager_member_modify',

    ),
    'community_manager_member_add' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUser',
        'method' => 'add',
        'label' => '社区管理员-添加社区会员',
        'auth_type' => 'community_manager_member_modify',

    ),

    'community_manager_member_update' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUser',
        'method' => 'update',
        'label' => '社区管理员-修改社区会员',
        'auth_type' => 'community_manager_member_modify',

    ),

    'community_manager_member_delete' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUser',
        'method' => 'delete',
        'label' => '社区管理员-删除社区会员',
        'auth_type' => 'community_manager_member_delete',

    ),


    'community_manager_member_set_member' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUser',
        'method' => 'setMember',
        'label' => '社区管理员-设置为正式会员状态',
        'auth_type' => 'community_manager_member_modify',

    ),



    //community-user-apply manager
    'community_manager_member_apply_get' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUserApply',
        'method' => 'getDetail',
        'label' => '社区管理员-取得社区会员申请详细内容',
        'auth_type' => '',


    ),
    'community_manager_member_apply_list' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUserApply',
        'method' => 'getList',
        'label' => '社区管理员-取得社区会员申请列表',
        'auth_type' => '',

    ),
    'community_manager_member_apply_add' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUserApply',
        'method' => 'add',
        'label' => '社区管理员-添加社区会员申请',
        'auth_type' => '',

    ),

    'community_manager_member_apply_update' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUserApply',
        'method' => 'update',
        'label' => '社区管理员-修改社区会员申请',
        'auth_type' => 'community_manager_member_apply',

    ),

    'community_manager_member_apply_delete' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUserApply',
        'method' => 'delete',
        'label' => '社区管理员-删除社区会员申请',
        'auth_type' => 'community_manager_member_apply',

    ),


    'community_manager_member_apply_approve' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUserApply',
        'method' => 'approve',
        'label' => '社区管理员-会员申请通过',
        'auth_type' => 'community_manager_member_apply',

    ),
    'community_manager_member_apply_reject' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUserApply',
        'method' => 'reject',
        'label' => '社区管理员-会员申请拒绝',
        'auth_type' => 'community_manager_member_apply',

    ),




//community bmshop
    'community_bmshop_get' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'get',
        'label' => '社区-管理员-便民服务主体详细信息读取',
        'auth_type' => '',
    ),
    'community_bmshop_list' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'getList',
        'label' => '社区-管理员-便民服务主体列表读取',
        'auth_type' => '',
    ),
    'community_bmshop_add' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'addByCommunity',
        'label' => '社区-管理员-便民服务主体添加',
        'auth_type' => 'community_manager_bmshop_create',


    ),
    'community_bmshop_edit' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'updateByAdm',
        'label' => '社区-管理员-便民服务主体修改',
        'auth_type' => 'community_manager_bmshop_update',


    ),

    'community_bmshop_onshelve' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'onShelve',
        'label' => '社区-管理员-便民服务主体上架',
        'auth_type' => 'community_manager_bmshop_update',


    ),

    'community_bmshop_offshelve' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'offShelve',
        'label' => '社区-管理员-便民服务主体上架',
        'auth_type' => 'community_manager_bmshop_update',


    ),

    'community_bmshop_set_cat' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'setCategory',
        'label' => '社区-管理员-便民服务主体设置分类',
        'auth_type' => 'community_manager_bmshop_update',


    ),

    'community_bmshop_delete' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'delete',
        'label' => '社区-管理员-便民服务主体永久删除',
        'auth_type' => 'community_manager_bmshop_delete',


    ),

    'community_bmshop_open' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'setOpened',
        'label' => '社区-管理员-便民服务主体正式开店',
        'auth_type' => 'community_manager_bmshop_update',

    ),


    //community bmservice:
    'community_bmservice_add' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'addByCommunity',
        'label' => '社区-服务添加',
        'auth_type' => 'community_manager_bm_create',


    ),
    'community_bmservice_update' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'updateByCommunity',
        'label' => '社区-服务添加',
        'auth_type' => 'shoppro|community_manager_bm_update',

    ),
    'community_bmservice_get' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'getDetail',
        'label' => '取得社区-服务详细信息',


    ),
    'community_bmservice_list' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'getListByCommunity',
        'label' => '取得社区-服务列表',

    ),

    'community_bmservice_onshelve' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'onShelveByCommunity',
        'label' => '社区-服务上架',
        'auth_type' => 'community_manager_bm_update',

    ),

    'community_bmservice_offshelve' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'offShelveByCommunity',
        'label' => '社区-服务下架',
        'auth_type' => 'shoppro|community_manager_bm_update',

    ),

    'community_bmservice_delete' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'deleteByCommunity',
        'label' => '删除社区-服务',
        'auth_type' => 'community_manager_bm_delete',


    ),


    //community manager modify community


    'community_manager_get' => array(
        'model' => '\\j79frame\\app\\model\\Community',
        'method' => 'getDetail',
        'label' => '社区-详细信息',
        'auth_type' => '',

    ),

    'community_manager_update' => array(
        'model' => '\\j79frame\\app\\model\\Community',
        'method' => 'update',
        'label' => '社区-详细信息-修改',
        'auth_type' => 'community_manager_info_mgt',

    ),


    //manager get user info


    'manager_user_get' => array(
        'model' => '\\j79frame\\app\\model\\UserMgt',
        'method' => 'getDetailRestricted',
        'label' => '社区管理员-取得平台会员详细内容',
        'auth_type' => 'community_manager_member_modify',


    ),
    'manager_user_list' => array(
        'model' => '\\j79frame\\app\\model\\UserMgt',
        'method' => 'getListRestricted',
        'label' => '社区管理员-取得平台会员列表',
        'auth_type' => '',

    ),










    //ws_apply

    'ws_apply_add' => array(
        'model' => '\\j79frame\\app\\model\\WSApply',
        'method' => 'add',
        'label' => '微商申请添加',



    ),
    'ws_apply_update' => array(
        'model' => '\\j79frame\\app\\model\\WSApply',
        'method' => 'update',
        'label' => '微商申请更新',
        'auth_type' => 'ws_apply_owner',


    ),
    'ws_apply_get' => array(
        'model' => '\\j79frame\\app\\model\\WSApply',
        'method' => 'getDetail',
        'label' => '取得微商申请详细信息',
        'auth_type' => 'ws_apply_owner',


    ),
    'ws_apply_list' => array(
        'model' => '\\j79frame\\app\\model\\WSApply',
        'method' => 'getList',
        'label' => '取得微商申请列表',

    ),

    'ws_apply_cancel' => array(
        'model' => '\\j79frame\\app\\model\\WSApply',
        'method' => 'cancel',
        'label' => '微商申请取消',
        'auth_type' => 'ws_apply_owner',


    ),





    //gps test
    'gpstest_get' => array(
        'model' => '\\j79frame\\app\\model\\GPSTest',
        'method' => 'getDetail',
        'label' => 'GPS测试详细',



    ),
    'gpstest_list' => array(
        'model' => '\\j79frame\\app\\model\\GPSTest',
        'method' => 'getList',
        'label' => '取得GPS测试列表',


    ),
    'gpstest_add' => array(
        'model' => '\\j79frame\\app\\model\\GPSTest',
        'method' => 'add',
        'label' => '添加GPS测试',


    ),





















    //----------------------------------------------------------------------- admin model interface -----------------------------------------------//
    'adm_pro_list' => array(
        'model' => '\\j79frame\\app\\model\\lister\\ProductList',
        'method' => 'get',
        'label' => '后台-自营产品列表',
        'auth_type' => 'adm',


    ),

    'adm_pro_get' => array(
        'model' => '\\j79frame\\app\\model\\Product',
        'method' => 'get',
        'label' => '后台-自营产品详细信息',
        'auth_type' => 'adm',


    ),

    'adm_pro_add' => array(
        'model' => '\\j79frame\\app\\model\\Product',
        'method' => 'add',
        'label' => '后台-自营产品添加',
        'auth_type' => 'adm',


    ),

    'adm_pro_edit' => array(
        'model' => '\\j79frame\\app\\model\\Product',
        'method' => 'update',
        'label' => '后台-自营产品修改',
        'auth_type' => 'adm',


    ),

    'adm_pro_onshelve' => array(
        'model' => '\\j79frame\\app\\model\\Product',
        'method' => 'onShelve',
        'label' => '后台-自营产品上架',
        'auth_type' => 'adm',


    ),
    'adm_pro_offshelve' => array(
        'model' => '\\j79frame\\app\\model\\Product',
        'method' => 'offShelve',
        'label' => '后台-自营产品下架',
        'auth_type' => 'adm',


    ),

    'adm_pro_cat_set' => array(
        'model' => '\\j79frame\\app\\model\\Product',
        'method' => 'setCategory',
        'label' => '后台-自营产品下架',
        'auth_type' => 'adm',


    ),

    'adm_pro_delete' => array(
        'model' => '\\j79frame\\app\\model\\Product',
        'method' => 'deleteByAdm',
        'label' => '后台-自营产品下架',
        'auth_type' => 'adm',


    ),

    'adm_fpro_list' => array(
        'model' => '\\j79frame\\app\\model\\lister\\FProductList',
        'method' => 'get',
        'label' => '后台-平台产品列表',
        'auth_type' => 'adm',


    ),

    'adm_fpro_get' => array(
        'model' => '\\j79frame\\app\\model\\FProduct',
        'method' => 'get',
        'label' => '后台-平台产品详细信息',
        'auth_type' => 'adm',


    ),

    'adm_fpro_add' => array(
        'model' => '\\j79frame\\app\\model\\FProduct',
        'method' => 'add',
        'label' => '后台-平台产品添加',
        'auth_type' => 'adm',


    ),

    'adm_fpro_edit' => array(
        'model' => '\\j79frame\\app\\model\\FProduct',
        'method' => 'update',
        'label' => '后台-平台产品修改',
        'auth_type' => 'adm',


    ),

    'adm_fpro_onshelve' => array(
        'model' => '\\j79frame\\app\\model\\FProduct',
        'method' => 'onShelve',
        'label' => '后台-平台产品上架',
        'auth_type' => 'adm',


    ),
    'adm_fpro_offshelve' => array(
        'model' => '\\j79frame\\app\\model\\FProduct',
        'method' => 'offShelve',
        'label' => '后台-平台产品下架',
        'auth_type' => 'adm',


    ),

    'adm_fpro_cat_set' => array(
        'model' => '\\j79frame\\app\\model\\FProduct',
        'method' => 'setCategory',
        'label' => '后台-平台产品下架',
        'auth_type' => 'adm',


    ),

    'adm_fpro_delete' => array(
        'model' => '\\j79frame\\app\\model\\FProduct',
        'method' => 'deleteByAdm',
        'label' => '后台-平台产品下架',
        'auth_type' => 'adm',


    ),
    //admin shop:
    'adm_shop_get' => array(
        'model' => '\\j79frame\\app\\model\\updater\\ShopUpdater',
        'method' => 'get',
        'label' => '后台-平台店铺详细信息读取',
        'auth_type' => 'adm',


    ),
    'adm_shop_list' => array(
        'model' => '\\j79frame\\app\\model\\Lister\\ShopList',
        'method' => 'get',
        'label' => '后台-平台店铺详细信息读取',
        'auth_type' => 'adm',


    ),
    'adm_shop_add' => array(
        'model' => '\\j79frame\\app\\model\\updater\\ShopUpdater',
        'method' => 'addByAdm',
        'label' => '后台-店铺添加',
        'auth_type' => 'adm',


    ),
    'adm_shop_edit' => array(
        'model' => '\\j79frame\\app\\model\\updater\\ShopUpdater',
        'method' => 'updateByAdm',
        'label' => '后台-店铺修改',
        'auth_type' => 'adm',


    ),

    'adm_shop_onshelve' => array(
        'model' => '\\j79frame\\app\\model\\updater\\ShopUpdater',
        'method' => 'onShelve',
        'label' => '后台-店铺上架',
        'auth_type' => 'adm',


    ),

    'adm_shop_offshelve' => array(
        'model' => '\\j79frame\\app\\model\\updater\\ShopUpdater',
        'method' => 'offShelve',
        'label' => '后台-店铺上架',
        'auth_type' => 'adm',


    ),

    'adm_shop_set_cat' => array(
        'model' => '\\j79frame\\app\\model\\updater\\ShopUpdater',
        'method' => 'setCategory',
        'label' => '后台-店铺设置分类',
        'auth_type' => 'adm',


    ),

    'adm_shop_delete' => array(
        'model' => '\\j79frame\\app\\model\\updater\\ShopUpdater',
        'method' => 'delete',
        'label' => '后台-店铺永久删除',
        'auth_type' => 'adm',


    ),

    'adm_shop_open' => array(
        'model' => '\\j79frame\\app\\model\\updater\\ShopUpdater',
        'method' => 'setOpened',
        'label' => '后台-店铺正式开店',
        'auth_type' => 'adm',


    ),

    //admin bm-shop
    'adm_bmshop_get' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'get',
        'label' => '后台-便民服务主体详细信息读取',
        'auth_type' => 'adm',
    ),
    'adm_bmshop_list' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'getList',
        'label' => '后台-便民服务主体列表读取',
        'auth_type' => 'adm',
    ),
    'adm_bmshop_add' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'addByAdm',
        'label' => '后台-便民服务主体添加',
        'auth_type' => 'adm',


    ),
    'adm_bmshop_edit' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'updateByAdm',
        'label' => '后台-便民服务主体修改',
        'auth_type' => 'adm',


    ),

    'adm_bmshop_onshelve' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'onShelve',
        'label' => '后台-便民服务主体上架',
        'auth_type' => 'adm',


    ),

    'adm_bmshop_offshelve' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'offShelve',
        'label' => '后台-便民服务主体上架',
        'auth_type' => 'adm',


    ),

    'adm_bmshop_set_cat' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'setCategory',
        'label' => '后台-便民服务主体设置分类',
        'auth_type' => 'adm',


    ),

    'adm_bmshop_delete' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'delete',
        'label' => '后台-便民服务主体永久删除',
        'auth_type' => 'adm',


    ),

    'adm_bmshop_open' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'setOpened',
        'label' => '后台-便民服务主体正式开店',
        'auth_type' => 'adm',

    ),


    //admin bmservice
    'adm_bmservice_add' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'addByAdm',
        'label' => '后台-便民服务添加',
        'auth_type' => 'adm',


    ),
    'adm_bmservice_update' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'updateByAdm',
        'label' => '后台-便民服务添加',
        'auth_type' => 'adm',
    ),
    'adm_bmservice_get' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'getDetail',
        'label' => '后台-取得便民服务详细信息',
        'auth_type' => 'adm',
    ),
    'adm_bmservice_list' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'getListByAdm',
        'label' => '后台-取得便民服务详细信息',
        'auth_type' => 'adm',
    ),

    'adm_bmservice_onshelve' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'onShelve',
        'label' => '后台-便民服务上架',
        'auth_type' => 'adm',
    ),

    'adm_bmservice_offshelve' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'offShelve',
        'label' => '后台-取得便民服务下架',
        'auth_type' => 'adm',
    ),

    'adm_bmservice_delete' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'deleteByAdm',
        'label' => '后台-删除便民服务',
        'auth_type' => 'adm',

    ),


    //admin rshop
    'adm_rshop_get' => array(
        'model' => '\\j79frame\\app\\model\\updater\\RShopUpdater',
        'method' => 'get',
        'label' => '后台-实体店铺详细信息读取',
        'auth_type' => 'adm',


    ),

    'adm_rshop_list' => array(
        'model' => '\\j79frame\\app\\model\\updater\\RShopUpdater',
        'method' => 'getList',
        'label' => '后台-实体店铺详细信息读取',
        'auth_type' => 'adm',


    ),

    'adm_rshop_add' => array(
        'model' => '\\j79frame\\app\\model\\updater\\RShopUpdater',
        'method' => 'add',
        'label' => '后台-实体店铺信息添加',
        'auth_type' => 'adm',


    ),

    'adm_rshop_edit' => array(
        'model' => '\\j79frame\\app\\model\\updater\\RShopUpdater',
        'method' => 'update',
        'label' => '后台-实体店铺信息修改',
        'auth_type' => 'adm',


    ),

    'adm_rshop_delete' => array(
        'model' => '\\j79frame\\app\\model\\updater\\RShopUpdater',
        'method' => 'delete',
        'label' => '后台-实体店铺信息修改',
        'auth_type' => 'adm',


    ),

    'adm_rshop_set_claimcreated' => array(
        'model' => '\\j79frame\\app\\model\\updater\\RShopUpdater',
        'method' => 'setClaimedAndCreated',
        'label' => '后台-实体店铺设置为已经认领并且建立',
        'auth_type' => 'adm',


    ),

    'adm_rshop_set_created' => array(
        'model' => '\\j79frame\\app\\model\\updater\\RShopUpdater',
        'method' => 'setCreated',
        'label' => '后台-实体店铺设置为已经建立',
        'auth_type' => 'adm',


    ),
    'adm_rshop_unset_claimcreated' => array(
        'model' => '\\j79frame\\app\\model\\updater\\RShopUpdater',
        'method' => 'unsetClaimedAndCreated',
        'label' => '后台-实体店铺设置为已经认领并且建立',
        'auth_type' => 'adm',


    ),

    'adm_ordergroup_list' => array(
        'model' => '\\j79frame\\app\\model\\OrderGroup',
        'method' => 'getList',
        'label' => '后台-订单组列表',
        'auth_type' => 'adm',


    ),
    'adm_ordergroup_get_amount' => array(
        'model' => '\\j79frame\\app\\model\\OrderGroup',
        'method' => 'getOrderAmount',
        'label' => '后台-订单总数',
        'auth_type' => 'adm',


    ),

    'adm_order_list' => array(
        'model' => '\\j79frame\\app\\model\\Order',
        'method' => 'getList',
        'label' => '后台-订单列表',
        'auth_type' => 'adm',


    ),


    'adm_order_get_statistic' => array(
        'model' => '\\j79frame\\app\\model\\Order',
        'method' => 'statistic',
        'label' => '后台-订单统计',
        'auth_type' => 'adm',


    ),

    'adm_order_get_amount' => array(
        'model' => '\\j79frame\\app\\model\\Order',
        'method' => 'getOrderAmount',
        'label' => '后台-订单总数',
        'auth_type' => 'adm',


    ),

    'adm_order_get_items' => array(
        'model' => '\\j79frame\\app\\model\\Order',
        'method' => 'getOrderItems',
        'label' => '后台-获取订单的商品列表',
        'auth_type' => 'adm',

    ),

    'adm_order_get' => array(
        'model' => '\\j79frame\\app\\model\\Order',
        'method' => 'getDetail',
        'label' => '后台-订单详细',
        'auth_type' => 'adm',


    ),

    'adm_order_update' => array(
        'model' => '\\j79frame\\app\\model\\Order',
        'method' => 'update',
        'label' => '后台-订单修改',
        'auth_type' => 'adm',

    ),


    'adm_order_set_shipped' => array(
        'model' => '\\j79frame\\app\\model\\Order',
        'method' => 'setShipped',
        'label' => '后台-订单发货',
        'auth_type' => 'adm',


    ),

    'adm_order_cancel' => array(
        'model' => '\\j79frame\\app\\model\\Order',
        'method' => 'cancelOrder',
        'label' => '后台-订单关闭',
        'auth_type' => 'adm',


    ),

    'adm_order_delete' => array(
        'model' => '\\j79frame\\app\\model\\Order',
        'method' => 'deleteOrder',
        'label' => '后台-订单删除',
        'auth_type' => 'adm',


    ),

    //adm loan apply mgt:
    'adm_loanapply_get' => array(
        'model' => '\\j79frame\\app\\model\\LoanApplication',
        'method' => 'get',
        'label' => '后台-取得用户的贷款申请信息',
        'auth_type' => 'adm',


    ),
    'adm_loanapply_list' => array(
        'model' => '\\j79frame\\app\\model\\LoanApplication',
        'method' => 'getList',
        'label' => '后台-取得用户的贷款申请列表',
        'auth_type' => 'adm',

    ),
    'adm_loanapply_add' => array(
        'model' => '\\j79frame\\app\\model\\LoanApplication',
        'method' => 'add',
        'label' => '后台-添加用户的贷款申请信息',
        'auth_type' => 'adm',

    ),
    'adm_loanapply_update' => array(
        'model' => '\\j79frame\\app\\model\\LoanApplication',
        'method' => 'updateByAdm',
        'label' => '后台-修改用户的贷款申请信息',
        'auth_type' => 'adm',
    ),
    'adm_loanapply_cancel' => array(
        'model' => '\\j79frame\\app\\model\\updater\\LoanApplyUpdater',
        'method' => 'cancel',
        'label' => '后台-取消用户的贷款申请信息',
        'auth_type' => 'adm',
    ),

    'adm_loanapply_verified' => array(
        'model' => '\\j79frame\\app\\model\\updater\\LoanApplyUpdater',
        'method' => 'setVerified',
        'label' => '后台-用户的贷款申请资料验证完毕',
        'auth_type' => 'adm',
    ),

    'adm_loanapply_refused' => array(
        'model' => '\\j79frame\\app\\model\\updater\\LoanApplyUpdater',
        'method' => 'setRefused',
        'label' => '后台-用户的贷款申请资料验证完毕',
        'auth_type' => 'adm',
    ),

    'adm_loanapply_approved' => array(
        'model' => '\\j79frame\\app\\model\\updater\\LoanApplyUpdater',
        'method' => 'setApproved',
        'label' => '后台-用户的贷款申请资料验证完毕',
        'auth_type' => 'adm',
    ),


    'adm_loanapply_delete' => array(
        'model' => '\\j79frame\\app\\model\\updater\\LoanApplyUpdater',
        'method' => 'deleteByAdm',
        'label' => '后台-删除用户的贷款申请信息',
        'auth_type' => 'adm',


    ),

    //article admin
    'adm_article_get' => array(
        'model' => '\\j79frame\\app\\model\\updater\\ArticleUpdater',
        'method' => 'get',
        'label' => '后台-取得文章内容详细',
        'auth_type' => 'adm',


    ),
    'adm_article_list' => array(
        'model' => '\\j79frame\\app\\model\\updater\\ArticleUpdater',
        'method' => 'getList',
        'label' => '后台-取得文章列表',
        'auth_type' => 'adm',

    ),
    'adm_article_add' => array(
        'model' => '\\j79frame\\app\\model\\updater\\ArticleUpdater',
        'method' => 'add',
        'label' => '后台-发布内容',
        'auth_type' => 'adm',

    ),

    'adm_article_update' => array(
        'model' => '\\j79frame\\app\\model\\updater\\ArticleUpdater',
        'method' => 'update',
        'label' => '后台-修改文章内容',
        'auth_type' => 'adm',

    ),

    'adm_article_delete' => array(
        'model' => '\\j79frame\\app\\model\\updater\\ArticleUpdater',
        'method' => 'delete',
        'label' => '后台-删除文章内容',
        'auth_type' => 'adm',

    ),

    'adm_article_onshelve' => array(
        'model' => '\\j79frame\\app\\model\\updater\\ArticleUpdater',
        'method' => 'onShelve',
        'label' => '后台-文章内容上线',
        'auth_type' => 'adm',

    ),
    'adm_article_offshelve' => array(
        'model' => '\\j79frame\\app\\model\\updater\\ArticleUpdater',
        'method' => 'offShelve',
        'label' => '后台-文章内容下线',
        'auth_type' => 'adm',

    ),
    'adm_article_setcategory' => array(
        'model' => '\\j79frame\\app\\model\\updater\\ArticleUpdater',
        'method' => 'setCategory',
        'label' => '后台-文章内容设置分类',
        'auth_type' => 'adm',

    ),



    //ws-apply admin
    'adm_ws_apply_get' => array(
        'model' => '\\j79frame\\app\\model\\WSApply',
        'method' => 'getDetail',
        'label' => '后台-取得微商申请内容详细',
        'auth_type' => 'adm',


    ),
    'adm_ws_apply_list' => array(
        'model' => '\\j79frame\\app\\model\\WSApply',
        'method' => 'getList',
        'label' => '后台-取得微商申请列表',
        'auth_type' => 'adm',

    ),
    'adm_ws_apply_add' => array(
        'model' => '\\j79frame\\app\\model\\WSApply',
        'method' => 'add',
        'label' => '后台-添加微商申请',
        'auth_type' => 'adm',

    ),

    'adm_ws_apply_update' => array(
        'model' => '\\j79frame\\app\\model\\WSApply',
        'method' => 'update',
        'label' => '后台-修改微商申请内容',
        'auth_type' => 'adm',

    ),

    'adm_ws_apply_delete' => array(
        'model' => '\\j79frame\\app\\model\\WSApply',
        'method' => 'delete',
        'label' => '后台-删除微商申请内容',
        'auth_type' => 'adm',

    ),

    'adm_ws_apply_reject' => array(
        'model' => '\\j79frame\\app\\model\\WSApply',
        'method' => 'reject',
        'label' => '后台-微商申请驳回',
        'auth_type' => 'adm',

    ),
    'adm_ws_apply_approve' => array(
        'model' => '\\j79frame\\app\\model\\WSApply',
        'method' => 'approve',
        'label' => '后台-微商申请通过',
        'auth_type' => 'adm',

    ),


    //pro_rec admin

    'adm_pro_rec_list' => array(
        'model' => '\\j79frame\\app\\model\\ProRec',
        'method' => 'getList',
        'label' => '后台-取得分享商品点击记录列表',
        'auth_type' => 'adm',

    ),


    'adm_pro_rec_delete' => array(
        'model' => '\\j79frame\\app\\model\\ProRec',
        'method' => 'delete',
        'label' => '后台-删除分享商品点击记录内容',
        'auth_type' => 'adm',

    ),

    'adm_pro_rec_invalid' => array(
        'model' => '\\j79frame\\app\\model\\ProRec',
        'method' => 'setInvalid',
        'label' => '后台-分享商品点击记录设置为失效',
        'auth_type' => 'adm',

    ),

    //rec_order_item

    'adm_rec_order_item_list'=> array(
        'model' => '\\j79frame\\app\\model\\ProRecOrderItem',
        'method' => 'getList',
        'label' => '后台-取得分享商品下单记录列表',
        'auth_type' => 'adm',

    ),


    'adm_rec_order_item_delete' => array(
        'model' => '\\j79frame\\app\\model\\ProRecOrderItem',
        'method' => 'delete',
        'label' => '后台-删除分享商品下单记录内容',
        'auth_type' => 'adm',

    ),

    'adm_rec_order_item_clear' => array(
        'model' => '\\j79frame\\app\\model\\ProRecOrderItem',
        'method' => 'setClear',
        'label' => '后台-分享商品下单记录设置为已结算',
        'auth_type' => 'adm',

    ),

   //ws_share admin

    'adm_ws_share_get' => array(
        'model' => '\\j79frame\\app\\model\\WSShare',
        'method' => 'getDetail',
        'label' => '后台-取得分享商品记录详细',
        'auth_type' => 'adm',


    ),

    'adm_ws_share_list' => array(
        'model' => '\\j79frame\\app\\model\\WSShare',
        'method' => 'getList',
        'label' => '后台-取得分享商品记录列表',
        'auth_type' => 'adm',

    ),
    'adm_ws_share_delete' => array(
        'model' => '\\j79frame\\app\\model\\WSShare',
        'method' => 'delete',
        'label' => '后台-删除分享商品记录内容',
        'auth_type' => 'adm',

    ),
    'adm_ws_share_add' => array(
        'model' => '\\j79frame\\app\\model\\WSShare',
        'method' => 'add',
        'label' => '后台-添加分享商品记录',
        'auth_type' => 'adm',

    ),




    //gps test admin
    'adm_gpstest_get' => array(
        'model' => '\\j79frame\\app\\model\\GPSTest',
        'method' => 'getDetail',
        'label' => '后台-GPS测试详细',
        'auth_type' => 'adm',


    ),
    'adm_gpstest_list' => array(
        'model' => '\\j79frame\\app\\model\\GPSTest',
        'method' => 'getList',
        'label' => '后台-取得GPS测试列表',
        'auth_type' => 'adm',

    ),
    'adm_gpstest_add' => array(
        'model' => '\\j79frame\\app\\model\\GPSTest',
        'method' => 'add',
        'label' => '后台-添加GPS测试',
        'auth_type' => 'adm',

    ),



    'adm_gpstest_delete' => array(
        'model' => '\\j79frame\\app\\model\\GPSTest',
        'method' => 'delete',
        'label' => '后台-删除GPS测试',
        'auth_type' => 'adm',

    ),




    //user-article admin
    'adm_user_article_get' => array(
        'model' => '\\j79frame\\app\\model\\UserArticle',
        'method' => 'getDetail',
        'label' => '后台-取得用户文章内容详细',
        'auth_type' => 'adm',


    ),
    'adm_user_article_list' => array(
        'model' => '\\j79frame\\app\\model\\UserArticle',
        'method' => 'getList',
        'label' => '后台-取得用户文章列表',
        'auth_type' => 'adm',

    ),
    'adm_user_article_add' => array(
        'model' => '\\j79frame\\app\\model\\UserArticle',
        'method' => 'add',
        'label' => '后台-发布内容',
        'auth_type' => 'adm',

    ),

    'adm_user_article_update' => array(
        'model' => '\\j79frame\\app\\model\\UserArticle',
        'method' => 'update',
        'label' => '后台-修改用户文章内容',
        'auth_type' => 'adm',

    ),

    'adm_user_article_delete' => array(
        'model' => '\\j79frame\\app\\model\\UserArticle',
        'method' => 'delete',
        'label' => '后台-删除用户文章内容',
        'auth_type' => 'adm',

    ),

    'adm_user_article_onshelve' => array(
        'model' => '\\j79frame\\app\\model\\UserArticle',
        'method' => 'onShelve',
        'label' => '后台-用户文章内容上线',
        'auth_type' => 'adm',

    ),
    'adm_user_article_offshelve' => array(
        'model' => '\\j79frame\\app\\model\\UserArticle',
        'method' => 'offShelve',
        'label' => '后台-用户文章内容下线',
        'auth_type' => 'adm',

    ),
    'adm_user_article_setcategory' => array(
        'model' => '\\j79frame\\app\\model\\UserArticle',
        'method' => 'setCategory',
        'label' => '后台-用户文章内容设置分类',
        'auth_type' => 'adm',

    ),


    //adm mall exp template

    'adm_mall_exp_template_list'=>array(
        'model' => '\\j79frame\\app\\model\\ExpTemplate',
        'method' => 'getList',
        'label' => '自营商城运费模板列表',
        'auth_type' => 'adm',
    ),

    'adm_mall_exp_template_get'=>array(
        'model' => '\\j79frame\\app\\model\\ExpTemplate',
        'method' => 'getDetail',
        'label' => '自营商城运费模板详细',
        'auth_type' => 'adm',
    ),

    'adm_mall_exp_template_add'=>array(
        'model' => '\\j79frame\\app\\model\\ExpTemplate',
        'method' => 'add',
        'label' => '自营商城运费模板添加',
        'auth_type' => 'adm',
    ),

    'adm_mall_exp_template_update'=>array(
        'model' => '\\j79frame\\app\\model\\ExpTemplate',
        'method' => 'update',
        'label' => '自营商城运费模板修改',
        'auth_type' => 'adm',
    ),

    'adm_mall_exp_template_delete'=>array(
        'model' => '\\j79frame\\app\\model\\ExpTemplate',
        'method' => 'delete',
        'label' => '自营商城运费模板删除',
        'auth_type' => 'adm',
    ),


    //adm shop exp template

    'adm_shop_exp_template_list'=>array(
        'model' => '\\j79frame\\app\\model\\ShopExpTemplate',
        'method' => 'getList',
        'label' => '自营商城运费模板列表',
        'auth_type' => 'adm',
    ),

    'adm_shop_exp_template_get'=>array(
        'model' => '\\j79frame\\app\\model\\ShopExpTemplate',
        'method' => 'getDetail',
        'label' => '自营商城运费模板详细',
        'auth_type' => 'adm',
    ),

    'adm_shop_exp_template_add'=>array(
        'model' => '\\j79frame\\app\\model\\ShopExpTemplate',
        'method' => 'add',
        'label' => '自营商城运费模板添加',
        'auth_type' => 'adm',
    ),

    'adm_shop_exp_template_update'=>array(
        'model' => '\\j79frame\\app\\model\\ShopExpTemplate',
        'method' => 'update',
        'label' => '自营商城运费模板修改',
        'auth_type' => 'adm',
    ),

    'adm_shop_exp_template_delete'=>array(
        'model' => '\\j79frame\\app\\model\\ShopExpTemplate',
        'method' => 'delete',
        'label' => '自营商城运费模板删除',
        'auth_type' => 'adm',
    ),




    //community admin
    'adm_community_get' => array(
        'model' => '\\j79frame\\app\\model\\Community',
        'method' => 'getDetail',
        'label' => '后台-取得社区详细内容详细',
        'auth_type' => 'adm',


    ),
    'adm_community_list' => array(
        'model' => '\\j79frame\\app\\model\\Community',
        'method' => 'getList',
        'label' => '后台-取得社区列表列表',
        'auth_type' => 'adm',

    ),
    'adm_community_add' => array(
        'model' => '\\j79frame\\app\\model\\Community',
        'method' => 'add',
        'label' => '后台-添加社区',
        'auth_type' => 'adm',

    ),

    'adm_community_update' => array(
        'model' => '\\j79frame\\app\\model\\Community',
        'method' => 'update',
        'label' => '后台-修改社区',
        'auth_type' => 'adm',

    ),

    'adm_community_delete' => array(
        'model' => '\\j79frame\\app\\model\\Community',
        'method' => 'delete',
        'label' => '后台-删除社区',
        'auth_type' => 'adm',

    ),

    'adm_community_set_opened' => array(
        'model' => '\\j79frame\\app\\model\\Community',
        'method' => 'setOpened',
        'label' => '后台-社区正式建立',
        'auth_type' => 'adm',

    ),

    'adm_community_set_status' => array(
        'model' => '\\j79frame\\app\\model\\Community',
        'method' => 'setStatus',
        'label' => '后台-社区状态',
        'auth_type' => 'adm',

    ),


    //community-user admin
    'adm_community_user_get' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUser',
        'method' => 'getDetail',
        'label' => '后台-取得社区会员详细内容',
        'auth_type' => 'adm',


    ),
    'adm_community_user_list' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUser',
        'method' => 'getList',
        'label' => '后台-取得社区会员列表',
        'auth_type' => 'adm',

    ),
    'adm_community_user_add' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUser',
        'method' => 'add',
        'label' => '后台-添加社区会员',
        'auth_type' => 'adm',

    ),

    'adm_community_user_update' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUser',
        'method' => 'update',
        'label' => '后台-修改社区会员',
        'auth_type' => 'adm',

    ),

    'adm_community_user_delete' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUser',
        'method' => 'delete',
        'label' => '后台-删除社区会员',
        'auth_type' => 'adm',

    ),


    'adm_community_user_set_status' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUser',
        'method' => 'setStatus',
        'label' => '后台-社区会员状态',
        'auth_type' => 'adm',

    ),

    'adm_community_user_set_member' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUser',
        'method' => 'setMember',
        'label' => '后台-设置为正式会员状态',
        'auth_type' => 'adm',

    ),




    //community-user-apply admin
    'adm_community_user_apply_get' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUserApply',
        'method' => 'getDetail',
        'label' => '后台-取得会员申请信息详细内容',
        'auth_type' => 'adm',


    ),
    'adm_community_user_apply_list' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUserApply',
        'method' => 'getList',
        'label' => '后台-取得会员申请信息列表',
        'auth_type' => 'adm',

    ),
    'adm_community_user_apply_add' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUserApply',
        'method' => 'add',
        'label' => '后台-添加会员申请信息',
        'auth_type' => 'adm',

    ),

    'adm_community_user_apply_update' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUserApply',
        'method' => 'update',
        'label' => '后台-修改会员申请信息',
        'auth_type' => 'adm',

    ),

    'adm_community_user_apply_delete' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUserApply',
        'method' => 'delete',
        'label' => '后台-删除会员申请信息',
        'auth_type' => 'adm',

    ),


    'adm_community_user_apply_approve' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUserApply',
        'method' => 'approve',
        'label' => '后台-会员申请信息审批通过',
        'auth_type' => 'adm',

    ),

    'adm_community_user_apply_reject' => array(
        'model' => '\\j79frame\\app\\model\\CommunityUserApply',
        'method' => 'reject',
        'label' => '后台-会员申请信息审批失败',
        'auth_type' => 'adm',

    ),



    //admin login-log
    'adm_login_log_get' => array(
        'model' => '\\j79frame\\app\\model\\LoginLog',
        'method' => 'getDetail',
        'label' => '后台-取得登录信息详细内容',
        'auth_type' => 'adm',


    ),
    'adm_login_log_list' => array(
        'model' => '\\j79frame\\app\\model\\LoginLog',
        'method' => 'getList',
        'label' => '后台-取得登录信息列表',
        'auth_type' => 'adm',


    ),
    'adm_login_log_delete' => array(
        'model' => '\\j79frame\\app\\model\\LoginLog',
        'method' => 'delete',
        'label' => '后台-登录信息删除',
        'auth_type' => 'adm',


    ),

    //admin loan-pro
    'adm_loanpro_get' => array(
        'model' => '\\j79frame\\app\\model\\LoanPro',
        'method' => 'getDetail',
        'label' => '后台-取得贷款产品详细信息',
        'auth_type' => 'adm',


    ),
    'adm_loanpro_list' => array(
        'model' => '\\j79frame\\app\\model\\LoanPro',
        'method' => 'getList',
        'label' => '后台-取得贷款产品列表',
        'auth_type' => 'adm',

    ),
    'adm_loanpro_add' => array(
        'model' => '\\j79frame\\app\\model\\LoanPro',
        'method' => 'add',
        'label' => '后台-发布贷款产品',
        'auth_type' => 'adm',

    ),

    'adm_loanpro_update' => array(
        'model' => '\\j79frame\\app\\model\\LoanPro',
        'method' => 'update',
        'label' => '后台-修改贷款产品内容',
        'auth_type' => 'adm',

    ),

    'adm_loanpro_delete' => array(
        'model' => '\\j79frame\\app\\model\\LoanPro',
        'method' => 'delete',
        'label' => '后台-删除贷款产品',
        'auth_type' => 'adm',

    ),

    'adm_loanpro_onshelve' => array(
        'model' => '\\j79frame\\app\\model\\LoanPro',
        'method' => 'onShelve',
        'label' => '后台-贷款产品上线',
        'auth_type' => 'adm',

    ),
    'adm_loanpro_offshelve' => array(
        'model' => '\\j79frame\\app\\model\\LoanPro',
        'method' => 'offShelve',
        'label' => '后台-贷款产品下线',
        'auth_type' => 'adm',

    ),

    //manager admin
    'adm_manager_get' => array(
        'model' => '\\j79frame\\app\\model\\Manager',
        'method' => 'getDetail',
        'label' => '后台-取得第三方管理员内容详细',
        'auth_type' => 'adm',


    ),
    'adm_manager_list' => array(
        'model' => '\\j79frame\\app\\model\\Manager',
        'method' => 'getList',
        'label' => '后台-取得第三方管理员列表',
        'auth_type' => 'adm',

    ),
    'adm_manager_add' => array(
        'model' => '\\j79frame\\app\\model\\Manager',
        'method' => 'add',
        'label' => '后台-添加第三方管理员',
        'auth_type' => 'adm',

    ),

    'adm_manager_update' => array(
        'model' => '\\j79frame\\app\\model\\Manager',
        'method' => 'update',
        'label' => '后台-修改第三方管理员',
        'auth_type' => 'adm',

    ),

    'adm_manager_delete' => array(
        'model' => '\\j79frame\\app\\model\\Manager',
        'method' => 'delete',
        'label' => '后台-删除第三方管理员',
        'auth_type' => 'adm',

    ),

    'adm_manager_onshelve' => array(
        'model' => '\\j79frame\\app\\model\\Manager',
        'method' => 'onShelve',
        'label' => '后台-第三方管理员上线',
        'auth_type' => 'adm',

    ),
    'adm_manager_offshelve' => array(
        'model' => '\\j79frame\\app\\model\\Manager',
        'method' => 'offShelve',
        'label' => '后台-第三方管理员下线',
        'auth_type' => 'adm',

    ),

    //xml cat:
    'adm_xml_cat_update' => array(
        'model' => '\\j79frame\\app\\model\\XMLCatUpdater',
        'method' => 'update',
        'label' => '后台-分类xml更新',
        'auth_type' => 'adm',

    ),

    //partner ===================================================================

    //admin bm-shop
    'partner_bmshop_get' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'get',
        'label' => '后台-便民服务主体详细信息读取',
        'auth_type' => 'partner_bm',

    ),

    'partner_bmshop_list' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'getList',
        'label' => '后台-便民服务主体列表读取',
        'auth_type' => 'partner_bm',

    ),


    'partner_bmshop_add' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'addByAdm',
        'label' => '后台-便民服务主体添加',
        'auth_type' => 'partner_bm',


    ),
    'partner_bmshop_edit' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'updateByAdm',
        'label' => '后台-便民服务主体修改',
        'auth_type' => 'partner_bm',


    ),

    'partner_bmshop_onshelve' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'onShelve',
        'label' => '后台-便民服务主体上架',
        'auth_type' => 'partner_bm',


    ),

    'partner_bmshop_offshelve' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'offShelve',
        'label' => '后台-便民服务主体上架',
        'auth_type' => 'partner_bm',


    ),

    'partner_bmshop_set_cat' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'setCategory',
        'label' => '后台-便民服务主体设置分类',
        'auth_type' => 'partner_bm',


    ),

    'partner_bmshop_delete' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'delete',
        'label' => '后台-便民服务主体永久删除',
        'auth_type' => 'partner_bm',


    ),

    'partner_bmshop_open' => array(
        'model' => '\\j79frame\\app\\model\\updater\\BMShopUpdater',
        'method' => 'setOpened',
        'label' => '后台-便民服务主体正式开店',
        'auth_type' => 'partner_bm',

    ),


    //partner bmservice
    'partner_bmservice_add' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'addByAdm',
        'label' => '后台-便民服务添加',
        'auth_type' => 'partner_bm',


    ),
    'partner_bmservice_update' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'updateByAdm',
        'label' => '后台-便民服务添加',
        'auth_type' => 'partner_bm',
    ),
    'partner_bmservice_get' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'getDetail',
        'label' => '后台-取得便民服务详细信息',
        'auth_type' => 'partner_bm',
    ),
    'partner_bmservice_list' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'getListByAdm',
        'label' => '后台-取得便民服务详细信息',
        'auth_type' => 'partner_bm',
    ),

    'partner_bmservice_onshelve' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'onShelve',
        'label' => '后台-便民服务上架',
        'auth_type' => 'partner_bm',
    ),

    'partner_bmservice_offshelve' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'offShelve',
        'label' => '后台-取得便民服务下架',
        'auth_type' => 'partner_bm',
    ),

    'partner_bmservice_delete' => array(
        'model' => '\\j79frame\\app\\model\\BMService',
        'method' => 'deleteByAdm',
        'label' => '后台-删除便民服务',
        'auth_type' => 'partner_bm',

    ),

    //partner loanapply
    'partner_loanapply_get' => array(
        'model' => '\\j79frame\\app\\model\\LoanApplication',
        'method' => 'get',
        'label' => '合作方后台-取得用户的贷款申请信息',
        'auth_type' => 'partner',


    ),
    'partner_loanapply_list' => array(
        'model' => '\\j79frame\\app\\model\\LoanApplication',
        'method' => 'getList',
        'label' => '合作方后台-取得用户的贷款申请列表',
        'auth_type' => 'partner',

    ),


);//--------/$GLOBAL_MODEL_INTERFACE


$GLOBAL_MANAGER_GROUP=array(
    'manager_loan_jilin'=>array(
        'partner_loanapply_list',
        'partner_loanapply_get',
    )
);//-----------/$GLOBAL_MANAGER_GROUP

