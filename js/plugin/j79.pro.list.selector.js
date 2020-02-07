/**
 *  proListSelector
 *  地址选择器，包含街道，最高4级。
 *
 *  UI上需要的设置参数：
 *                 data-category : pro list category   [default: not set]
 *                 data-sor : pro list sort            [default: not set]
 *                 data-shop : pro list of shop_idx    [default: not set]
 *                 data-div : pro list of division id  [default: not set]
 *
 *                 data-amount : max amount of products to select. [default: 6]
 *
 *                 data-ptype : pro ptype, [default 0]
 *
 *                 placeholder   : 用于帮助信息显示
 *
 *
 *
 *                 class=“pro-list-selector”
 *
 *
 */
j79.loadCSS("/css/pro.list.selector.css");

(function ($) {

    $.fn.proListSelector = function () {


        var SELF = this;

        var SELF_ID = $(SELF).attr('id');


        var STR_PLACEHOLDER = $(SELF).attr('placeholder') || '请选择商品';


        var PRO_CATEGORY = $(SELF).attr('data-category') || '';
        var PRO_SORT = $(SELF).attr('data-sort') || '';
        var PRO_SHOP = $(SELF).attr('data-shop') || '';
        var PRO_DIV = $(SELF).attr('data-div') || '';

        var PRO_AMOUNT = $(SELF).attr('data-amount') || 6;

        var PRO_PTYPE = parseInt($(SELF).attr('data-ptype')) || 0;


        var DATA_SAVER = $(SELF).attr('data-saver') || '';

        /*
         DATA_SAVE_PATH: when it not empty, save generated data to a attribute named like DATA_SAVE_PATH of the object(DATA_SAVER data indicated)
         */
        var DATA_SAVE_PATH = $(SELF).attr('data-save-path') || '';

        if (DATA_SAVER != '') {
            //$('#'+DATA_SAVER).hide();
        }


        var SELF_PAGE_OBJ = {};

        //html codes-------------------------------
        //UI html
        var HTML_UI= '<h3>已选择的产品列表: <span>最多选择 <b>' + PRO_AMOUNT + '</b>  (已选择:<span class="label label-danger" id="proSelectedAmount">0</span>) - <a id="dLabel" class="btn btn-info btn-select-pro" data-target="#"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">选择商品 <span class="caret"></span></a></span></h3>' +
            '<div class="dropdown">' +
            ''+
            '<div class="dropdown-menu pro-list-part ">' +
            '<div class="pro-list-toolbar"></div>' +
            '<div class="pro-list-container">' +
            '<ul class="pro-list-select" max-select-length="' + PRO_AMOUNT + '"></ul>' +
            '</div>' +
            '<div class="pro-list-buttonbar"><a class="btn btn-sm btn-primary btn-close-select"><i class="glyphicon glyphicon-remove"></i> 关闭选择窗口</a> </div>' +
            '</div>' +
            '</div>' +
            '<div class="pro-list-selected-container"><ul class="pro-list-selected"></ul></div>' ;

        //selected li html
        var HTML_SELECTED_LI = '<li class="col-md-2 selected-item" item-id="[#pro_idx#]">' +
            '<span class="thumb-img"><img src="[#pro_simg#]" title="[#pro_name#]" /></span>' +
            '<h3>[#pro_name#]</h3>' +
            '<div class="price-part">￥<b class="price">[#pro_price#]</b></div>' +
            '<div>ID编号:<span style="font-weight: bold;">[#pro_idx#]</span> | <a href="[#edit_link#]" target="_blank">[编辑]</a></div>' +
            '<div class="btn-part btn-group btn-group-justified"><a class="btn btn-sm btn-default btn-item-up"><i class="glyphicon glyphicon-arrow-up"></i></a><a class="btn btn-sm btn-default btn-item-down"><i class="glyphicon glyphicon-arrow-down"></i></a><a class="btn btn-sm btn-default btn-item-remove"><i class="glyphicon glyphicon-remove"></i></a><a class="btn btn-sm btn-default" href="[#pro_link#]" target="_blank"><i class="glyphicon glyphicon-eye-open"></i></a></div>' +
            '<div class="clear"></div>' +
            '</li>';

        //view li html
        var HMTL_VIEW_LI= '<li class="col-md-2 col-sm-12" item-id="[#pro_idx#]"><div>' +
            '<div class="col-md-12 col-sm-12 thumb-img">  ' +
            '<a class="btn-set-selected"><img src="[#pro_simg_url#]" title="[#pro_name#]" /></a>' +
            '</div>' +
            '<div class="col-md-12 col-sm-12 title">  ' +
            '<h3 title="[#pro_name#]">[#pro_name#]</h3>  ' +
            '<div>ID:<span class="label label-primary">[#pro_idx#]</span> | <a class="btn btn-link" href="/com.php?target=[#pro_detail_target#]&amp;idx=[#pro_idx#]" target="_blank">[ 预览 ]</a></div>' +

            '<p class="price">￥ <b>[#pro_price#]</b></p>' +
            '</div>' +
            '</div></li>';


        var UI_LIST='.pro-list-selected';

        /**
         *  saveData
         *  save selected data to data-savers.
         *
         */
        var saveData = function () {

            var result = [];


            //generate result:
            $(SELF).find('.pro-list-selected li').each(function(){
                result.push({
                    "pro_idx": $(this).attr('item-id'),
                    "pro_simg": $(this).find('.thumb-img img').attr('src'),
                    "pro_name":$(this).find('h3').text(),
                    "pro_ptype":PRO_PTYPE,
                    "pro_price":$(this).find('.price').text(),
                    "pro_url":  PRO_PTYPE==1 ? '/com.php?target=fpro_detail&idx='+$(this).attr('item-id') :'/com.php?target=pro_detail&idx='+$(this).attr('item-id')
                });
            });




            if (DATA_SAVER != '') {

                if (DATA_SAVE_PATH) {
                    var dataObj = j79.toJSON($('#' + DATA_SAVER).val());
                    if (!dataObj) {
                        dataObj = {}
                    }
                    dataObj[DATA_SAVE_PATH] = result;
                    result = dataObj;

                }
                $('#' + DATA_SAVER).val(j79.toJSONString(result));
            }


        };//-/saveData


        var viewProList=function(data, totalAmount, curPage, perPage){
            $(SELF).find('.pro-list-select').empty();
            $(SELF).find('.pro-list-toolbar').empty();

            var itemData;
            for (var i = 0; i < data.length; i++) {
                itemData = data[i];
                itemData.pro_detail_target = parseInt(itemData.ptype) == 1 ? 'fpro_detail' : 'pro_detail';
                itemData.pro_simg_url = parseInt(itemData.ptype) == 1 ? j79App.URL_PRODUCT_F_SIMG + '/' + itemData.pro_simg : j79App.URL_PRODUCT_SIMG + '/' + itemData.pro_simg;

                $(j79.setHtml(itemData, HMTL_VIEW_LI)).appendTo($(SELF).find('.pro-list-select'))

            }

            var totalAmount = totalAmount || PRO_AMOUNT;

            SELF_PAGE_OBJ.setPage = function (newPage) {
                loadProList(newPage);
            };//-/

            //set page:
            console.log('#' + SELF_ID);
            j79.viewPager(Math.ceil(totalAmount / perPage), curPage, '#' + SELF_ID + ' .pro-list-toolbar', SELF_PAGE_OBJ);
        };//-/


        var loadProList = function (cur_page) {


            var postData = {
                target: PRO_PTYPE == 1 ? 'AdmFProUpdater' : 'AdmProUpdater', //server dispatching target, must exists.
                action: 'SELECT',
                page: cur_page,
                per_page: PRO_AMOUNT*2,

            };

            if (PRO_CATEGORY) {
                postData.category = PRO_CATEGORY;
            }
            if (PRO_SORT) {
                postData.sort = PRO_SORT;
            }
            if (PRO_SHOP) {
                postData.shop = PRO_SHOP;
            }
            if (PRO_DIV) {
                postData.div = PRO_DIV;
            }

            j79.post({
                data: postData,
                title: '产品列表',
                actionSuccess: function (result) {
                    if (result && result.data) {

                        viewProList(result.data, result.total_amount, cur_page, postData.per_page);




                    }
                },
                actionFailed: function (result) {
                    console.log('pro list failed loading.');
                    console.log(result);
                    //alert(result.error_code + ' | ' + result.msg);
                },


            });
        };//-/





        /**
         *  build
         *  产生列表
         *
         */
        var build = function () {



            //绘制基本UI
            var ui = $( HTML_UI);
            ui.appendTo(SELF);

            //open pro-list to select:
            $(SELF).find('.btn-select-pro').click(function (e) {


                $(this).toggleClass('opened');

                //var btnLabel= $(this).hasClass('opened') ? '关闭列表' : '选择商品';
               //$(this).text(btnLabel);
                $(SELF).find('.pro-list-part').toggle();


            });




            //close select list:
            $(SELF).delegate('.pro-list-part  .btn-close-select', 'click', null, function (event) {
                $(SELF).find('.pro-list-part').hide();
            });



            //btn-item-remove:
            $(SELF).delegate('.pro-list-selected .btn-item-remove', 'click', null, function (event) {
                var curLi = $(this).parents().closest('li');

                var curIdx=$(curLi).attr('item-id');
                $(curLi).remove();
                saveData();

                //unset selction for pro-list-select:
                $(SELF).find('.pro-list-select li[item-id='+curIdx+'] ').removeClass('selected');

                //set selected amount:
                $(SELF).find('#proSelectedAmount').text($(SELF).find(".pro-list-selected li").length);

            });

            //btn-item-up
            $(SELF).delegate('.pro-list-selected .btn-item-up', 'click', null, function (event) {

                var curLi = $(this).parents().closest('li');


                var prevLi = $(curLi).prev('li');
                if (prevLi.length > 0) {
                    $(curLi).insertBefore(prevLi);
                    saveData();

                } else {
                    alert('无法再往上移动了！');
                }

            });
            //btn-item-down
            $(SELF).delegate('.pro-list-selected .btn-item-down', 'click', null, function (event) {

                var curLi = $(this).parents().closest('li');

                var prevLi = $(curLi).next('li');
                if (prevLi.length > 0) {
                    $(curLi).insertAfter(prevLi);
                    saveData();

                } else {
                    alert('无法再往下移动了！');
                }

            });


            //selected-list li click: to set active.
            $(SELF).delegate('.pro-list-selected li', 'click', null, function (event) {

                $(this).siblings().removeClass('active');
                $(this).addClass('active');



            });


            //add selected item:
            $(SELF).delegate('.pro-list-select li .btn-set-selected', 'click', null, function (e) {




                var curLi = $(this).parents().closest('li');
                var curIdx=$(curLi).attr('item-id');
                var curV = $(curLi).hasClass('selected');
                var curUl = $(this).parents().closest('ul.pro-list-select');

                $(curLi).toggleClass('selected');

                //unselect:
                if(curV){
                    if($(SELF).find('.pro-list-selected li[item-id='+curIdx+']').length>0){
                        $(SELF).find('.pro-list-selected li[item-id='+curIdx+']').remove();
                    }

                    var proSelectedLen = $(curUl).find("li.selected").length;

                    $(SELF).find('#proSelectedAmount').text(proSelectedLen);
                    saveData();
                    return;
                }



                //set selelct:
                if( $(SELF).find('.pro-list-selected li[item-id='+curIdx+']').length>0 ){
                    return ;
                }



                //set currently selected amount no.:
                var proSelectedLen = $(curUl).find("li.selected").length;
                $(SELF).find('#proSelectedAmount').text(proSelectedLen);


                var curItem = {};
                curItem.pro_idx = curIdx;
                curItem.pro_name = $(curLi).find('.title h3').text();
                curItem.pro_price = $(curLi).find('.price b').text();
                curItem.pro_simg = $(curLi).find('.thumb-img img').attr('src');
                curItem.pro_link=$(curLi).find('.btn-link').attr('href');

                curItem.edit_link= PRO_PTYPE==0 ? '/com.php?target=adm%2Fpro_edit&idx='+curIdx: '/com.php?target=adm%2Ffpro_edit&idx='+curIdx;



                $(j79.setHtml(curItem, HTML_SELECTED_LI)).appendTo($(SELF).find('.pro-list-selected'));

                saveData();


            });   //find('.pro-list-select li')


            //load prolist:
            loadProList(1);


        };//-/build


        /**
         * readData
         * read data from data-savers.
         */
        var readData = function () {

            if (DATA_SAVER != '' && $('#' + DATA_SAVER).val() && $('#' + DATA_SAVER).val() != '') {


                var data = j79.toJSON($('#' + DATA_SAVER).val());

                if (DATA_SAVE_PATH) {
                    data= data[DATA_SAVE_PATH] ? data[DATA_SAVE_PATH] : null;
                }

                if (data) {

                    //如果是单个对象，不是数组，那么转成含有此对象的数组：
                    if(!j79.isArray(data) ){
                        //console.log('not arra');
                        data=[data];
                    }

                    var dataItem;
                    for (var i = 0; i < data.length; i++) {
                        dataItem = data[i];

                        var dataIdx=dataItem.pro_idx || '';


                        if( dataIdx ){

                            $( j79.setHtml(dataItem, HTML_SELECTED_LI)).appendTo($(SELF).find(UI_LIST));

                        }

                    }

                }


            }


        };//-/


        //attach click selected-item to re-open selection:
        $(SELF).delegate('.schedule-items .btn-del', 'click', null, function (e) {
            $(this).closest('li').remove();
            saveData();
        });

        //产生列表
        build();

        //读取预设值
        readData();


    }
})(jQuery)//-----------------------------------------


//设置所有class名为address-picker的项目。
$(document).ready(function () {

    var class_name = "pro-list-selector";
    var ctrlist = $('.' + class_name);

    for (i = 0; i < ctrlist.length; i++) {

        $("." + class_name + ":eq(" + i + ")").proListSelector();


    }

});


