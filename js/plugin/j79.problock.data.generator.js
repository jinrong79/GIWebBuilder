/**
 *  addressPicker
 *  地址选择器，包含街道，最高4级。
 *
 *  UI上需要的设置参数：
 *                 data-saver-lv1 : 第一级选择的存储input的id
 *                 data-saver-lv2 : 第二级选择的存储input的id
 *                 data-saver-lv3 : 第三级选择的存储input的id
 *                 data-saver-lv4 : 第四级选择的存储input的id
 *
 *                 placeholder   : 用于帮助信息显示
 *
 *                 start-lvl     : start level. 指定的等级，锁定，不可修改。后面等级的地址可修改。
 *                                 比如：start-lvl="2",   data-saver-lv1="22"  data-saver-lv2="2224"
 *                                 那么 省，市，这2个等级，锁定，只能修改地区和街道。
 *
 *                 class=“address-picker”
 *
 *
 */
j79.loadCSS("/css/datagen.problock.css");

(function ($) {

    $.fn.dataGeneratorProBlock = function () {




        var SELF = this;

        var SELF_ID=$(SELF).attr('id');


        var STR_PLACEHOLDER = $(SELF).attr('placeholder') || '--请点击选择地址--';


        var PRO_CATEGORY = $(SELF).attr('data-pro-category') || '';
        var PRO_SORT = $(SELF).attr('data-pro-sort') || '';
        var PRO_SHOP = $(SELF).attr('data-pro-shop') || '';
        var PRO_DIV= $(SELF).attr('data-pro-div') || '';

        var PRO_AMOUNT=$(SELF).attr('data-pro-amount') || 6;

        var PRO_PTYPE = parseInt($(SELF).attr('data-pro-ptype')) || 0;


        var DATA_SAVER = $(SELF).attr('data-saver') || '';

        if (DATA_SAVER != '') {
            //$('#'+DATA_SAVER).hide();
        }


        var SELF_PAGE_OBJ={};



        /**
         *  saveData
         *  save selected data to data-savers.
         *
         */
        var saveData = function () {

            var result = [];


            if (DATA_SAVER != '') {
                $('#' + DATA_SAVER).val(j79.toJSONString(result));
            }


        };//-/saveData


        var loadProList=function(cur_page){


            var postData={
                target: PRO_PTYPE == 1 ? 'AdmFProUpdater' : 'AdmProUpdater', //server dispatching target, must exists.
                action:'SELECT',
                page:cur_page,
                per_page:PRO_AMOUNT,

            };

            if(PRO_CATEGORY){
                postData.category=PRO_CATEGORY;
            }
            if(PRO_SORT){
                postData.sort=PRO_SORT;
            }
            if(PRO_SHOP){
                postData.shop=PRO_SHOP;
            }
            if(PRO_DIV){
                postData.div=PRO_DIV;
            }

            j79.post({
                data: postData,
                title: '产品列表',
                actionSuccess: function (result) {
                    if(result &&  result.data ){

                        $(SELF).find('.pro-list-select').empty();
                        $(SELF).find('.pro-list-toolbar').empty();
                        var liHtml='<li class="col-md-6 col-sm-12" item-id="[#pro_idx#]"><div>' +
                            '<div class="col-md-12 col-sm-12 thumb-img">  ' +
                            '<div class="select-part">  <input type="checkbox" name="selectedItems" value="[#pro_idx#]"></div>  ' +
                            '<div class="img-part"><a class="btn btn-set-selected"><img src="[#pro_simg_url#]" title="[#pro_name#]" /></a></div>' +
                            '</div>' +
                            '<div class="col-md-12 col-sm-12 title">  ' +
                            '<h3 title="[#pro_name#]">[#pro_name#]</h3>  ' +
                            '<div>ID编号:<span class="label label-primary">[#pro_idx#]</span> | <a class="btn btn-link" href="/com.php?target=[#pro_detail_target#]&amp;idx=[#pro_idx#]" target="_blank">[ 预览 ]</a></div>' +

                            '<p class="price">￥ <b>[#pro_price#]</b></p>' +
                            '</div>' +
                            '</div></li>';
                        var itemData;
                        for(var i=0;i<result.data.length;i++){
                            itemData=result.data[i];
                            itemData.pro_detail_target = parseInt( itemData.ptype ) == 1 ? 'fpro_detail' :'pro_detail';
                            itemData.pro_simg_url = parseInt( itemData.ptype ) == 1 ? j79App.URL_PRODUCT_F_SIMG+'/'+itemData.pro_simg :  j79App.URL_PRODUCT_SIMG+'/'+itemData.pro_simg;

                            $(j79.setHtml(itemData, liHtml)).appendTo($(SELF).find('.pro-list-select'))

                        }

                        var totalAmount=result.total_amount  || PRO_AMOUNT;

                        SELF_PAGE_OBJ.setPage=function(newPage){
                            loadProList(newPage);
                        };//-/

                        //set page:
                        console.log('#'+SELF_ID);
                        j79.viewPager(Math.ceil(totalAmount / postData.per_page), cur_page, '#'+SELF_ID+' .pro-list-toolbar', SELF_PAGE_OBJ );


                    }
                },
                actionFailed: function (result) {
                    alert(result.error_code+' | '+result.msg);
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
            var ui = $(
                '<div class="titleName"><span>标题名称:</span><input id="main_title" name="main_title" /></div>' +
                '<hr/>' +


                '<h3>已选择的产品列表: <span>最多选择'+PRO_AMOUNT+' (已选择:<span class="label label-danger" id="proSelectedAmount">0</span>) - <a href="#" class="btn btn-info btn-select-pro">选择商品</a></span></h3>'+
                '<div class="pro-list-part">' +
                '<div class="pro-list-toolbar"></div>' +
                '<div class="pro-list-container">' +
                '<ul class="pro-list-select" max-select-length="'+PRO_AMOUNT+'"></ul>' +
                '</div>' +
                '</div>' +
                '<div class="pro-list-selected-container"><ul class="pro-list-selected"></ul></div>' +
                ''
            );
            ui.appendTo(SELF);




            $(SELF).find('.btn-select-pro').click(function(e){

                $(SELF).find('.pro-list-part').toggle();

            });


            //btn-item-remove:
            $(SELF).delegate( '.pro-list-selected .btn-item-remove', 'click', null, function(event){

                var curLi = $(this).parents().closest('li');
                $(curLi).remove();
            });

            //btn-item-up
            $(SELF).delegate( '.pro-list-selected .btn-item-up', 'click', null, function(event){

                var curLi = $(this).parents().closest('li');


                var prevLi=$(curLi).prev('li');
                if(prevLi.length>0){
                    $(curLi).insertBefore(prevLi);

                }else{
                    alert('无法再往上移动了！');
                }

            });
            //btn-item-down
            $(SELF).delegate( '.pro-list-selected .btn-item-down', 'click', null, function(event){

                var curLi = $(this).parents().closest('li');


                var prevLi=$(curLi).next('li');
                if(prevLi.length>0){
                    $(curLi).insertAfter(prevLi);

                }else{
                    alert('无法再往下移动了！');
                }

            });

            //add selected item:
            $(SELF).delegate('.pro-list-select li .select-part input:checkbox','click',null, function(e){
                var curV = $(this).prop('checked');
                var curLi = $(this).parents().closest('li');
                var curUl=$(this).parents().closest('ul.pro-list-select');

                var proSelectedLen = $(curUl).find(".select-part input:checkbox:checked").length;

                $(SELF).find('#proSelectedAmount').text(proSelectedLen);

                var curItem={};
                curItem.pro_idx=$(curLi).attr('item-id');
                curItem.pro_name=$(curLi).find('.title h3').text();
                curItem.pro_price=$(curLi).find('.price').text();
                curItem.pro_simg_url=$(curLi).find('.thumb-img img').attr('src');

                var liHtml='<li class="selected-item" item-id="[#pro_idx#]">' +
                    '<span class="thumb-img"><img src="[#pro_simg_url#]" title="[#pro_name#]" /></span>' +
                    '<h3>[#pro_name#]</h3>' +
                    '<div>[#pro_price#]</div>' +
                    '<div>ID编号:<span class="label label-primary">[#pro_idx#]</span></div>' +
                    '<div class="btn-part"><a class="btn btn-default btn-item-remove"><i class="glyphicon glyphicon-remove"></i> 移除</a> <a class="btn btn-default btn-item-up"><i class="glyphicon glyphicon-arrow-up"></i> 上移</a> <a class="btn btn-default btn-item-down"><i class="glyphicon glyphicon-arrow-down"></i> 下移</a></div>'+
                    '<div class="clear"></div>' +
                    '</li>';
                $(j79.setHtml(curItem, liHtml)).appendTo($(SELF).find('.pro-list-selected'));



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

    var class_name = "data-gen-problock";
    var ctrlist = $('.' + class_name);

    for (i = 0; i < ctrlist.length; i++) {

        $("." + class_name + ":eq(" + i + ")").dataGeneratorProBlock();


    }

});


