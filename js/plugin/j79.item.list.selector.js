/**
 j79ItemListSelector
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
j79.loadCSS("/css/j79.item.list.selector.css");

(function ($) {

    $.fn.j79ItemListSelector = function () {


        var SELF = this;

        var SELF_ID = $(SELF).attr('id');
        if(SELF_ID==''){
            SELF_ID='bannerEditor'+(Math.round( Math.random()*1000000));
            this.attr('id' , SELF_ID);
        }


        var STR_PLACEHOLDER = $(SELF).attr('title') || $(SELF).attr('placeholder') || '选择相应记录，产生列表。';



        var LIST_DATA_RESOURCE=$(SELF).attr('data-target') || '';  //server loading target, like 'AdmProUpdater'.

        var LIST_DATA_LOAD_PARAMS=$(SELF).attr('data-params') || '';   //server loading params, format: 'category=10000&sort=timedown'

        var LIST_DATA_LOAD_PARAMS_OBJ={};  //server loading params in object format.

        var LIST_CATEGORY_XML=$(SELF).attr('data-category-xml') || '';

        var DATA_POST={};  //data to post to server

        var CATEGORY_INI=''; //开始category idx值。



        //取得内容部html，显示li的template html。
        // 例如：<li item-id="[#pro_idx#]">
        //          <div class="img"><img class="data-simg-url" src="/data/img/product/fsimg/[#pro_simg#]" /></div>
        //          <div class="input-item"><input type="text" class="data-name" value="[#pro_name#]"/></div>
        //          <div class="data-price">[#pro_price#]</div>
        //     </li>
        //     其中，data-name，data-price，data-simg-url名称会被用于提取数据的根据key。
        //          [#pro_simg#],[#pro_name#],[#pro_price#]是数据源中数据的键值标示。
        //          item-id="[#pro_idx#]"必须存在于li上，用于辨识数据项。其中的pro_idx根据数据源变化，item-id必须固定。
        var HTML_VIEW_LI=$(SELF).html();
        $(SELF).empty();

        //"data-key-items" attr: data key items setting, format like: "img,data-simg|name,data-name"
        //                       “最终数据keyname,显示列表item中输入表单/节点的classname” 格式来表述一个数据项，
        //                       其中，如果取值的classname指向的元素为input或textarea的时候，就取val()；如果是img，取src属性值；如果都不是，就取内部html值text()
        //                       多个数据项目，用“|”来分割。
        var dataKeyItemsStr=$(SELF).attr('data-key-items') || '';


        var DATA_KEY_ITEMS={};

        //parse key item settings into array.
        var parseKeySettings=function(){

            if(dataKeyItemsStr!=''){

                var params=dataKeyItemsStr.split('|');
                var curParam, paramItem, paramKey, paramValue;
                for(var i=0; i<params.length; i++){
                    curParam=params[i];
                    paramItem=curParam.split(',');
                    paramKey=paramItem[0];
                    paramValue=paramItem.length>1 ?paramItem[1] : 0;
                    DATA_KEY_ITEMS[paramKey]=paramValue;
                    if(paramKey.toLowerCase()=='category'){
                        CATEGORY_INI=paramValue;
                    }
                }

            }

        };
        //parse key item settings.
        parseKeySettings();



        var PRO_AMOUNT = $(SELF).attr('data-amount') || 6;



        var DATA_SAVER = $(SELF).attr('data-saver') || '';

        /*
         DATA_SAVE_PATH: when it not empty, save generated data to a attribute named like DATA_SAVE_PATH of the object(DATA_SAVER data indicated)
         */
        var DATA_SAVE_PATH = $(SELF).attr('data-save-path') || '';

        if (DATA_SAVER != '') {
            $('#'+DATA_SAVER).hide();
        }


        var SELF_PAGE_OBJ = {};


        var categorySelectCtr=LIST_CATEGORY_XML!='' ? '<div class="tree-selector" data-lock-ini="1" data-ini-idx="'+CATEGORY_INI+'" data-expend-mode="1" title="" data-xml="'+LIST_CATEGORY_XML+'" data-saver="'+SELF_ID+'_category_id" id="'+SELF_ID+'_treeSelector" name="'+SELF_ID+'_treeSelector"></div><input type="hidden" id="'+SELF_ID+'_category_id" name="'+SELF_ID+'_category_id" value="'+CATEGORY_INI+'"> ':'';

        //html codes-------------------------------
        //UI html
        var HTML_UI= '<div class="description">'+STR_PLACEHOLDER+'</div>' +
            '<h3>已选择的列表: <span>最多选择 <b>' + PRO_AMOUNT + '</b>  (已选择:<span class="label label-danger" id="proSelectedAmount">0</span>) - ' +
            '<a id="dLabel" class="btn btn-info btn-select-pro" data-target="#"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">打开选择窗 <span class="caret"></span></a>' +
            '<a  class="btn btn-default btn-clear-selected">清空已选择列表</a>' +
            /*'<a  class="btn btn-default btn-load-data">Load</a>' +*/
            '</span>' +
            '</h3>' +
            '<div class="dropdown">' +
            ''+
            '<div class="dropdown-menu item-list-part ">' +
            '<div class="item-list-navi"><div class="cat-select-part"><b>过滤条件</b><span>分类:</span>'+categorySelectCtr+'</div><div class="search-part"><span>搜索：</span><input id="'+SELF_ID+'_searchValue" name="'+SELF_ID+'_searchValue" type="text" placeholder="输入名称或idx来进行查询..." /><a class="btn btn-primary btn-sm btn-search"><i class="glyphicon glyphicon-search"></i> 搜索</a> | <a class="btn btn-info btn-sm btn-search-reset"><i class="glyphicon glyphicon-refresh"></i> 重置条件</a></div></div>' +
            '<div class="item-list-toolbar"></div>' +
            '<div class="item-list-container">' +
            '<ul class="item-list-view" max-select-length="' + PRO_AMOUNT + '"></ul>' +
            '</div>' +
            '<div class="item-list-buttonbar"><a class="btn btn-sm btn-primary btn-close-select"><i class="glyphicon glyphicon-remove"></i> 关闭选择窗口</a> </div>' +
            '</div>' +
            '</div>' +
            '<div class="item-list-selected-container"><ol class="item-list-selected"></ol></div>' ;






        var HTML_SELECTED_LI_MENU='<div class="btn-part btn-group">' +
            '<a class="btn btn-sm btn-default btn-item-up" title="上移(move up)"><i class="glyphicon glyphicon-arrow-up"></i> 上移</a>' +
            '<a class="btn btn-sm btn-default btn-item-down" title="下移(move down)"><i class="glyphicon glyphicon-arrow-down"></i> 下移</a>' +
            '<a class="btn btn-sm btn-default btn-item-remove"  title="移除(remove)"><i class="glyphicon glyphicon-remove"></i> 移除</a>' +
            '</div><div class="clear"></div>';

        //selected li html
        var $tempLi=$(HTML_VIEW_LI);
        $(HTML_SELECTED_LI_MENU).appendTo($tempLi);
        //$tempLi.find('input').attr('disabled','disabled');
        var HTML_SELECTED_LI =$tempLi.prop("outerHTML");




        var UI_LIST='.item-list-selected';

        var UI_LIST_VIEW='.item-list-view';

        var UI_LIST_VIEW_PART='.item-list-part';

        /**
         *  saveData
         *  save selected data to data-savers.
         *
         */
        var saveData = function () {

            var result = [];

            //generate result:
            $(SELF).find(UI_LIST+' li').each(function(){

                var targetKey,sourceKey,tagName;



                var curIdx=$(this).attr('item-id') || '';
                var curResult=curIdx=='' ? {} : {"idx":curIdx};

                for (var p in DATA_KEY_ITEMS) { //loop key items to get data.

                    targetKey=p;
                    sourceKey='.'+DATA_KEY_ITEMS[p];

                    if( $(this).find(sourceKey).length>0){
                        tagName=($(this).find(sourceKey).get(0).tagName).toUpperCase();
                        if(tagName=='INPUT'){
                            curResult[targetKey]=$(this).find(sourceKey).val();
                        }else if(tagName=='IMG'){
                            curResult[targetKey]=$(this).find(sourceKey).attr('src');
                        }else if(tagName=='A'){
                            curResult[targetKey]=$(this).find(sourceKey).attr('href');
                        }else{
                            curResult[targetKey]=$(this).find(sourceKey).text();
                        }
                    }
                }

                result.push(curResult);
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

        /**
         * viewItemList
         * @param data
         * @param totalAmount
         * @param curPage
         * @param perPage
         */
        var viewItemList=function(data, totalAmount, curPage, perPage){
            $(SELF).find(UI_LIST_VIEW).empty();
            $(SELF).find('.item-list-toolbar').empty();

            var itemData;
            for (var i = 0; i < data.length; i++) {
                itemData = data[i];
                //itemData.pro_detail_target = parseInt(itemData.ptype) == 1 ? 'fpro_detail' : 'pro_detail';
                //itemData.pro_simg_url = parseInt(itemData.ptype) == 1 ? j79App.URL_PRODUCT_F_SIMG + '/' + itemData.pro_simg : j79App.URL_PRODUCT_SIMG + '/' + itemData.pro_simg;

                $(j79.setHtml(itemData, HTML_VIEW_LI)).appendTo($(SELF).find(UI_LIST_VIEW))

            }

            var totalAmount = totalAmount || PRO_AMOUNT;

            SELF_PAGE_OBJ.setPage = function (newPage) {
                loadItemList(newPage);
            };//-/

            //set page:
            console.log('#' + SELF_ID);
            j79.viewPager(Math.ceil(totalAmount / perPage), curPage, '#' + SELF_ID + ' .item-list-toolbar', SELF_PAGE_OBJ);
        };//-/


        /**
         * parseParams
         * parse params of list loading server target, and save params in object format in LIST_DATA_LOAD_PARAMS_OBJ
         */
        var parseParams=function(){


            LIST_DATA_LOAD_PARAMS_OBJ={};
            //解析params：
            if(LIST_DATA_LOAD_PARAMS!=''){

                var params=LIST_DATA_LOAD_PARAMS.split('&');
                var curParam, paramItem, paramKey, paramValue;
                for(var i=0; i<params.length; i++){
                    curParam=params[i];
                    paramItem=curParam.split('=');
                    paramKey=paramItem[0];
                    paramValue=0;
                    if(paramItem.length>1){
                        paramValue=paramItem[1];
                    }
                    LIST_DATA_LOAD_PARAMS_OBJ[paramKey]=paramValue;
                }

            }
        };

        /**
         * loadItemList
         * load view item list.
         * @param cur_page: load page.
         */
        var loadItemList = function (cur_page) {

            if(LIST_DATA_RESOURCE==''){
                return;
            }

            cur_page=cur_page || 1;



            DATA_POST=$.extend(DATA_POST, LIST_DATA_LOAD_PARAMS_OBJ);
            DATA_POST.action='SELECT';
            DATA_POST.target=LIST_DATA_RESOURCE;
            DATA_POST.per_page=PRO_AMOUNT*2<10 ? 10 : PRO_AMOUNT*2;
            DATA_POST.page=cur_page;


            j79.post({
                data: DATA_POST,
                title: 'Item',
                actionSuccess: function (result) {
                    if (result && result.data) {
                        viewItemList(result.data, result.total_amount, cur_page, DATA_POST.per_page);
                    }
                },
                actionFailed: function (result) {
                    console.log('list failed loading.');
                    console.log(result);

                }

            });
        };//-/

        var loadItemListByCondition=function(){
            if(LIST_DATA_RESOURCE==''){
                return;
            }

            DATA_POST.page=1;

            j79.post({
                data: DATA_POST,
                title: 'Item',
                actionSuccess: function (result) {
                    if (result && result.data) {
                        viewItemList(result.data, result.total_amount, 1, DATA_POST.per_page);
                    }
                },
                actionFailed: function (result) {
                    console.log('list failed loading.');
                    console.log(result);

                }

            });
        };





        /**
         *  build
         *  产生列表
         *
         */
        var build = function () {



            //绘制基本UI
            var ui = $( HTML_UI);
            ui.appendTo(SELF);

            if(LIST_CATEGORY_XML!=''){
                $('#'+SELF_ID+'_treeSelector').treeSelector();
            }

            //open pro-list to select:
            $(SELF).find('.btn-select-pro').click(function (e) {


                $(this).toggleClass('opened');

                //var btnLabel= $(this).hasClass('opened') ? '关闭列表' : '选择商品';
               //$(this).text(btnLabel);
                $(SELF).find(UI_LIST_VIEW_PART).toggle();


            });




            //close select list:
            $(SELF).delegate(UI_LIST_VIEW_PART+' .btn-close-select', 'click', null, function (event) {
                $(SELF).find(UI_LIST_VIEW_PART).hide();
            });

            //change category:
            $(SELF).delegate('#'+SELF_ID+'_category_id', 'change', null, function (event) {
                var newCatId=$('#'+SELF_ID+'_category_id').val();
                if(newCatId && newCatId!=''){
                    DATA_POST.category=newCatId;
                    loadItemListByCondition();

                }
            });


            //search:
            $(SELF).delegate('.item-list-part .btn-search', 'click', null, function (event) {
                var searchVal=$('#'+SELF_ID+'_searchValue').val();
                if(searchVal && searchVal!=''){
                    DATA_POST.searchValue=searchVal;
                    loadItemListByCondition();

                }
            });

            //search-reset:
            $(SELF).delegate('.item-list-part .btn-search-reset', 'click', null, function (event) {


                    delete DATA_POST.searchValue;
                    delete DATA_POST.category;
                    $('#'+SELF_ID+'_searchValue').val('');
                    $('#'+SELF_ID+'_category_id').val('');
                    $('#'+SELF_ID+'_treeSelector').trigger('change');
                    loadItemListByCondition();


            });


            //btn-item-remove:
            $(SELF).delegate(UI_LIST+' .btn-item-remove', 'click', null, function (event) {
                var curLi = $(this).parents().closest('li');

                var curIdx=$(curLi).attr('item-id');
                $(curLi).remove();
                saveData();

                //unset selction for pro-list-select:
                $(SELF).find(UI_LIST_VIEW+' li[item-id='+curIdx+'] ').removeClass('selected');

                //set selected amount:
                $(SELF).find('#proSelectedAmount').text($(SELF).find(UI_LIST+" li").length);

            });

            //btn-item-up
            $(SELF).delegate(UI_LIST+' .btn-item-up', 'click', null, function (event) {

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
            $(SELF).delegate(UI_LIST+' .btn-item-down', 'click', null, function (event) {

                var curLi = $(this).parents().closest('li');

                var prevLi = $(curLi).next('li');
                if (prevLi.length > 0) {
                    $(curLi).insertAfter(prevLi);
                    saveData();

                } else {
                    alert('无法再往下移动了！');
                }

            });

            //input change in selected list li:
            $(SELF).delegate(UI_LIST+' input', 'change', null, function (event) {

                saveData();

            });


            //selected-list li click: to set active.
            $(SELF).delegate(UI_LIST+' li', 'click', null, function (event) {

                $(this).siblings().removeClass('active');
                $(this).addClass('active');



            });


            //add selected item:
            $(SELF).delegate(UI_LIST_VIEW+' li', 'click', null, function (e) {




                var curLi = this;
                var curIdx=$(curLi).attr('item-id');
                var curV = $(curLi).hasClass('selected');
                var curUl = $(this).parents().closest(UI_LIST_VIEW);

                var proSelectedLen = $(UI_LIST).find("li").length;
                if(PRO_AMOUNT<=proSelectedLen){
                    alert('选择个数已达上限，无法添加！ Max amount exceeded, can not add more selection!');
                    return;
                }

                $(curLi).toggleClass('selected');

                //unselect:
                if(curV){
                    if($(SELF).find(UI_LIST+' li[item-id='+curIdx+']').length>0){
                        $(SELF).find(UI_LIST+' li[item-id='+curIdx+']').remove();
                    }



                    $(SELF).find('#proSelectedAmount').text(proSelectedLen);
                    saveData();
                    return;
                }



                //set selelct:
                if( $(SELF).find(UI_LIST+' li[item-id='+curIdx+']').length>0 ){
                    return ;
                }

                //clone current item to selected list:
                var $curItem =$(curLi).clone();
                $curItem.removeClass('selected');
                $(HTML_SELECTED_LI_MENU).appendTo($curItem);
                $curItem.appendTo($(SELF).find(UI_LIST));

                //set currently selected amount no.:
                var proSelectedLen = $(UI_LIST).find("li").length;
                $(SELF).find('#proSelectedAmount').text(proSelectedLen);



                saveData();


            });//-/

            //input click prevent popup
            $(SELF).delegate(UI_LIST_VIEW+' input', 'click', null, function (e) {
                e.stopPropagation();
            });

            //input click prevent popup
            $(SELF).delegate('.btn-clear-selected', 'click', null, function (e) {
                $(UI_LIST).empty();
                $(UI_LIST_VIEW).find('li.selected').removeClass('selected');
                $(SELF).find('#proSelectedAmount').text('0');
                saveData();
            });

            //readData
            $(SELF).delegate('.btn-load-data', 'click', null, function (e) {

                readData();
            });




            //load prolist:
            loadItemList(1);


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

                    console.log('loading data exists:');

                    //如果是单个对象，不是数组，那么转成含有此对象的数组：
                    if(!j79.isArray(data) ){

                        data=[data];
                    }

                    var dataItem;
                    var $curLi;
                    for (var i = 0; i < data.length; i++) {
                        dataItem = data[i];


                        var targetKey,sourceKey,tagName;

                        $curLi=$(HTML_SELECTED_LI);


                        var curIdx=dataItem.idx || ''; //get idx. key name must be 'idx'

                        if(curIdx=='')
                            continue;


                        $curLi.attr('item-id', curIdx);

                        for (var p in DATA_KEY_ITEMS) { //loop key items to get data.

                            targetKey=p;
                            sourceKey='.'+DATA_KEY_ITEMS[p];

                            if( $curLi.find(sourceKey).length>0 && dataItem[targetKey] ){
                                tagName=($curLi.find(sourceKey).get(0).tagName).toUpperCase();
                                if(tagName=='INPUT'){
                                    $curLi.find(sourceKey).val(dataItem[targetKey]);
                                }else if(tagName=='IMG'){
                                    $curLi.find(sourceKey).attr('src', dataItem[targetKey]);
                                }else if(tagName=='A'){
                                    $curLi.find(sourceKey).attr('href', dataItem[targetKey]);
                                }else{
                                    $curLi.find(sourceKey).text(dataItem[targetKey]);
                                }
                            }
                        }

                        $curLi.appendTo($(SELF).find(UI_LIST));



                    }

                }


            }


        };//-/


        //attach click selected-item to re-open selection:
        $(SELF).delegate('.schedule-items .btn-del', 'click', null, function (e) {
            $(this).closest('li').remove();
            saveData();
        });

        //解析server params
        parseParams();

        //产生列表
        build();

        //读取预设值
        readData();


    }
})(jQuery)//-----------------------------------------


//设置所有class名为item-list-selector的项目。
$(document).ready(function () {

    var class_name = "item-list-selector";
    var ctrlist = $('.' + class_name);

    for (i = 0; i < ctrlist.length; i++) {

        $("." + class_name + ":eq(" + i + ")").j79ItemListSelector();


    }

});


