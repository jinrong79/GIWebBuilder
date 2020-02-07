// admin product list

/**
 *  j79List
 *  list UI handler class.
 *
 *  @usage:
 1)initialize and view list:

 var listSetting={ ... ...}
 var list1=new j79List(listSetting);
 list1.ini();

 2)reload list:
 //load page 5:
 list1.read( { page: 5});

 //load category=100000000 list:
 list1.read( { category: 100000000});

 *  data foramt when initialize:params={
 
									  ui            : { 
														uiList    : '#itemList' ,
														uiBtn     : '.list-btn-group' ,
														uiCategory: '#listCategory' ,			
														uiSort    : '.list-sort' ,
														uiPager   : '.list-pager',
														uiSearch  : '.list-search'
														pageTitle : 'List',   //page title
														itemTitle : 'Item'    //item name viewed in interaction ui
														editorTarget : editor target url, like "adm%Ffro_edit"，
														urlAdd      : '', //url to add new item.
														                  //when urlAdd or urlEdit is empty, then use editorTarget.
														urlEdit     : '',          
			                                                         //  '/com.php?tatget=fproduct&action=UPDATE&idx=[#idx#]', 
									                                 //url to edit new items. [#idx#] will be replaced by item idx.
														
														toolbarSetting: setting of toolbar. look like:
														                Array(
																		   {'key':'del','title':'删除'  },
																		   {'key':'add','title':'添加'  }
																		
																		)
														
														searchSetting: search key settings as object. look like.
																	   {				             
																		 'pro_idx'  : '产品ID',
																		 'pro_name' : '产品名称',
																	   
																	   }
														
														sortSetting :  sort bar setting as array. look like:
																	   Array(
																		  {key:'time',  title:'时间', values:'down'},
																		  {key:'price', title:'价格', values:'down|up'},
																		  {key:'sales', title:'销量', values:'down|up'}
																	   ),
														uiFilter      : '.list-filter-board',   // default: .list-filter-board
														filterSetting :    list filter.
                                                                           Array(
                                                                                { 'title':'街道'，
                                                                                  'data' : [{'value':XXX, 'label':'选项1'}，{'value':XXX, 'label':'选项2'}, ... ],
                                                                                  'key'  : 'street',   // when value-from-to type, key is array. array[0]=from key. array[1]= to key.
                                                                                  'type' : 0,          //'0- list select; 1- value from to , like price
                                                                                }

                                                                           )

													   },
									  target        :  server target when posting data to.
									  itemGenerator :  function to view a single line of list: 
													   function(rowData, idx)
									  category_xml  :  category xml url
									  category_key  :  'category'  //param key name when do category sorting
									  div,category  :  initial div and category settings.
									  perPage       :

					             }
 *
 *
 */
function j79CoreList(params) {


    this.ui = {
        uiContainer:'#listPart',
        uiList: '#itemList',
        uiBtn: '.list-btn-group',
        uiCategory: '#listCategory',
        uiSort: '.list-sort',
        uiPager: '.list-pager',
        uiSearch: '.list-search',
        uiFilter: '.list-filter-board',
        pageTitle: 'List',   //page title
        itemTitle: 'Item',    //item name viewed in interaction ui
        editorTarget: 'adm_edit',   //when urlAdd or urlEdit is empty, then use editorTarget.
        urlAdd: '',          //url to add new item.
        urlEdit: '',
        //'/com.php?tatget=fproduct&action=UPDATE&idx=[#idx#]',
        //url to edit new items. [#idx#] will be replaced by item idx.
        toolbarSetting: Array(
            {'key': 'delete', 'title': '删除', 'icon': 'glyphicon glyphicon-remove'},
            {'key': 'add', 'title': '添加', 'icon': 'glyphicon glyphicon-plus'}
        )
    };

    this.categoryKey= (params && params.category_key) || 'category';

    //params setting.
    this.params = {
        per_page: params.perPage || 12,
        page: 1
    };

    var urlParam = j79.getURLObj();


    /* if(urlParam && urlParam.searchValue){
     urlParam.searchValue=encodeURIComponent(urlParam.searchValue);
     } */

    delete urlParam.target;
    delete urlParam.action;

    this.params = $.extend(this.params, urlParam);
    //console.log('ini params:');
    //console.log(this.params);
    //this.params.page=j79.getURLParam('page') && parseInt(j79.getURLParam('page'))>0 ? parseInt(j79.getURLParam('page')) :1;

    /*if(j79.getURLParam('div') && parseInt(j79.getURLParam('div'))>0 ){
     this.params.div=j79.getURLParam('div');
     }*/

    //parse received params:
    if (params) {

        if (params.ui) {
            this.ui = $.extend(this.ui, params.ui);
        }

        console.log('ui in core list:');
        console.log(this.ui);

        /*this.ui.uiList= params.ui.uiList ?  params.ui.uiList  :  this.ui.uiList;
         this.ui.uiBtn=params.ui.uiBtn ? params.ui.uiBtn : ;
         this.ui.uiCategory=params.ui.uiCategory ? params.ui.uiCategory: ;
         this.ui.uiSort=params.ui.uiSort ? params.ui.uiSort :;
         this.ui.uiPager=params.ui.uiPager ? params.ui.uiPager :;
         this.ui.uiSearch=params.ui.uiSearch ? params.ui.uiSearch :;		*/

        if (params.itemGenerator) {
            this.itemGenerator = params.itemGenerator;
        }

        this.category_xml = params.category_xml ? params.category_xml : '';

        /*if (params.category_xml) {
         this.category_xml = params.category_xml;
         }*/

        if (params.actionAfterView) {
            this.actionAfterView = params.actionAfterView;
        }


        delete params.ui;
        delete params.itemGenerator;

        this.params = $.extend(this.params, params);

        delete this.params.actionAfterView;
        delete this.params.category_xml;
        delete this.params.category_key;
        delete this.params.perPage;


    }

    //setup global disabledList
    this.disabledList = [];

    //setup global var:totalAmount (list total records amount)
    this.totalAmount = 0;

}//-/

/**
 *  j79List methods
 */
j79CoreList.prototype = {

    /**
     *  validate settings.
     *  @return {bool} : true -- valid settings. false -- invalid settings.
     */
    isValid: function () {
        if (!this.ui.uiList || !this.itemGenerator || !this.params || !this.params.target) {
            return false;
        } else {
            return true;
        }
    },//-/isValid


    /**
     *  ini
     */
    ini: function () {

        var selfObj = this;

        //page title
        $('.content-list-title #page_title').text(this.ui.pageTitle);

        if (!this.isValid()) {
            console.log('list cannot create due to invalid params!');
            return false;
        }

        //ini category tree:
        this.iniCategory();

        //ini search:
        this.iniSearch();

        //ini sort:
        this.iniSort();

        //ini btns
        this.iniToolbar();

        //ini filters
        this.iniFilter();

        //btn action: uiSort.btn click
        $(this.ui.uiSort + '  .btn').click(function (e) {


            var sortKey = $(this).attr('sort-key');
            var sortValue = $(this).find('input').val();

            valueStr = $(this).attr('sort-values');
            values = valueStr.split('|');

            if ($(this).hasClass('active') && values.length > 1) {

                if (sortValue == values[0]) {
                    $(this).find('i').removeClass('glyphicon-arrow-' + values[0]);
                    $(this).find('i').addClass('glyphicon-arrow-' + values[1]);
                    $(this).find('input').val(values[1]);
                    sortValue = values[1];

                } else {
                    $(this).find('i').removeClass('glyphicon-arrow-' + values[1]);
                    $(this).find('i').addClass('glyphicon-arrow-' + values[0]);
                    $(this).find('input').val(values[0]);
                    sortValue = values[0];
                }


            }
            //console.log(sortKey+sortValue);


            selfObj.params.page = 1;
            selfObj.params.sort = sortKey + sortValue;
            selfObj.read();

        });


        //btn of category view all
        $(this.ui.uiCategory + ' .btn-view-all').click(function (e) {
            delete selfObj.params[selfObj.categoryKey];
            selfObj.params.page = 1;
            selfObj.read();
            $(selfObj.ui.uiCategory + ' .tree-navi').treeNavigatorReset();
        });

        //category change action:
        $(this.ui.uiCategory + ' .input-current-category').change(function (e) {


            if (Number($(this).val()) > 0) {
                selfObj.params.page = 1;
                selfObj.params[selfObj.categoryKey]=Number($(this).val());
                //selfObj.params.category = Number($(this).val());
                //params.category=Number($(this).val());
                selfObj.read();

            }

        });

        //hadler for search
        var handlerSearch = function () {

            // alert('search');

            var searchValue = $(selfObj.ui.uiSearch + '  .input-search-value').val();
            var searchKey = $(selfObj.ui.uiSearch + '  .input-search-key').val();

            if (searchValue && searchKey) {


                var param = {};

                if ($(selfObj.ui.uiCategory + '  #curCat') && $(selfObj.ui.uiCategory + '  #curCat').val()) {
                    var curCat = Number($(selfObj.ui.uiCategory + '  #curCat').val());
                    if (curCat > 0) {
                        param.category = curCat;
                    }


                }
                selfObj.params.page = 1;
                selfObj.params.searchKey = searchKey;
                selfObj.params.searchValue = searchValue;

                selfObj.read();

            }
        };//-/handlerSearch

        $(this.ui.uiSearch).delegate('.btn-search','click',null, function (e) {

            handlerSearch();

        });

        /*$(this.ui.uiSearch + ' .btn-search').click(function (e) {
            handlerSearch();

        });*/

        $(this.ui.uiSearch + ' .input-search-value').keydown(function (e) {

            if (e.which == 13) {
                handlerSearch();
            }
        });
        //action for search reset
        $(this.ui.uiSearch + ' .btn-reset').click(function (e) {

            $(selfObj.ui.uiSearch + ' .input-search-value').val('');

            delete selfObj.params.searchKey;
            delete selfObj.params.searchValue;
            selfObj.params.page = 1;
            selfObj.read();
            //$(selfObj.ui.uiCategory+' .tree-navi').treeNavigatorReset();

        });


        //action attach: filter

        if (this.ui.uiFilter) {

            //filter-item.click:
            $(this.ui.uiFilter).delegate('.filter-item', 'click', null, function (e) {
                $(this).siblings().removeClass('active');
                $(this).addClass('active');

                var filterValue = $(this).attr('data-value');
                var filterItem = $(this).closest('.filter');
                var filterKey = $(filterItem).attr('data-key');

                if (filterValue == '' || typeof filterValue == 'undefined') {
                    delete selfObj.params[filterKey];
                } else {
                    selfObj.params[filterKey] = filterValue;
                }
                selfObj.params.page = 1;
                selfObj.read();


            });
            //btn-from-to.click:
            $(this.ui.uiFilter).delegate('.btn-from-to', 'click', null, function (e) {


                var filterItem = $(this).closest('.filter');

                var valueFrom = $(filterItem).find('.input-from').val();
                var valueTo = $(filterItem).find('.input-to').val();


                var filterKeyFrom = $(filterItem).attr('data-key-from');
                var filterKeyTo = $(filterItem).attr('data-key-To');

                if (parseInt(valueFrom) >= 0 && parseInt(valueTo) >= 0) {

                    selfObj.params[filterKeyFrom] = valueFrom;
                    selfObj.params[filterKeyTo] = valueTo;
                    selfObj.params.page = 1;
                    selfObj.read();
                }


            });
        }


        //read data from server and display on list container.
        this.read();

    },//-/int

    /**
     *  iniCategory
     *  category ui create.
     */
    iniCategory: function () {
        if (this.category_xml != '') {

            var $element = $('<div class="all"><a class="btn btn-link btn-view-all"><i class="glyphicon glyphicon-th-large"></i> 显示全部</a></div>' +
                '<div id="list_cat_tree" class="tree-navi" data-saver="curCat" data-xml="' + this.category_xml + '"><input type="hidden" class="input-current-category" id="curCat" name="curCat" value="" /></div>');

            $element.appendTo($(this.ui.uiCategory));
            $(this.ui.uiCategory).find('#list_cat_tree').treeNavigator();
        }


    },//-/iniCategory


    /**
     *  iniSearch
     *  setup search
     */
    iniSearch: function () {

        var optList = '';

        if (this.ui.searchSetting) {
            var searchSet = this.ui.searchSetting;
            for (var p in searchSet) { // 方法
                if (typeof ( searchSet [p]) != "function") {
                    optList += '<option value="' + p + '">' + searchSet[p] + '</option>';
                }
            }

        }
        if(optList==''){
            return;
        }
        var $element = $('<div class="input-group">' +
            '<div class="input-group-btn"><select class="form-control input-search-key" style="width:150px;"  id="search_key" name="search_key">' + optList + '</select></div>' +
            '<input type="text" class="form-control input-search-value" id="search_value" name="search_value" />' +
            '<div class="input-group-btn"><button type="button" class="btn btn-primary btn-search"><i class="glyphicon glyphicon-search"></i> 查找</button><button type="button" class="btn btn-default btn-reset" title="重置"><i class="glyphicon glyphicon-refresh"></i></button></div>' +
            '</div>');

        $element.appendTo($(this.ui.uiSearch));

    },//-/


    /**
     *  iniSort
     */
    iniSort: function () {
        if (this.ui.sortSetting) {

            var $btnGroup = $('<div class="btn-group" data-toggle="buttons"></div>');

            var curSortSet, valueStr, values;
            for (var i = 0; i < this.ui.sortSetting.length; i++) {

                curSortSet = this.ui.sortSetting[i];

                valueStr = curSortSet.values;
                values = valueStr.split('|');

                if (values && values.length > 0) {

                    $sort = $('<label class="btn btn-default btn-sort-' + curSortSet.key + '" sort-key="' + curSortSet.key + '" sort-values="' + valueStr + '">' +
                        '<input type="radio" name="sortOpt" id="sort' + curSortSet.key + '" autocomplete="off" value="' + values[0] + '">' + curSortSet.title + ' <i class="glyphicon glyphicon-arrow-' + values[0] + '"></i></label>');

                    $sort.appendTo($btnGroup);
                }
            }
            $btnGroup.appendTo($(this.ui.uiSort));
        }
    },//-/

    /**
     * iniFilter
     */
    iniFilter: function () {

        var selfObj = this;

        var curPageFilterValue;
        var curActiveClass='';

        if (selfObj.ui.filterSetting && selfObj.ui.filterSetting.length > 0) {
            var filterItem, filterDataItem, htmlFilterItem;
            htmlFilterItem = '';
            for (var i = 0; i < selfObj.ui.filterSetting.length; i++) {
                filterItem = selfObj.ui.filterSetting[i];

                if (!filterItem.type || filterItem.type == 0) {//type=0, select item type:

                    if (filterItem.data && filterItem.key && filterItem.title && filterItem.data.length > 0) {

                        curActiveClass='';
                        curPageFilterValue=j79.getURLParam(filterItem.key);

                        console.log(filterItem.key+' param value='+curPageFilterValue);

                        if(curPageFilterValue!=null){
                            curActiveClass='';
                        }else{
                            curActiveClass=' active' ;
                        }



                        htmlFilterItem += '<dl class="filter" data-key="' + filterItem.key + '"><dt>' + filterItem.title + '</dt><dd><a class="filter-item '+curActiveClass+'" data-value="">全部</a>';
                        for (var j = 0; j < filterItem.data.length; j++) {
                            filterDataItem = filterItem.data[j];
                            if (typeof filterDataItem.value != 'undefined' && typeof filterDataItem.label != 'undefined') {
                                curActiveClass='';
                                console.log('filter value:'+filterDataItem.value);
                                if(curPageFilterValue==filterDataItem.value){
                                    curActiveClass=' active' ;
                                }
                                htmlFilterItem += '<a class="filter-item '+curActiveClass+'" data-value="' + filterDataItem.value + '">' + filterDataItem.label + '</a>';
                            }
                        }
                        htmlFilterItem += '</dd></dl>';

                    }
                } else if (filterItem.type && filterItem.type == 1) {//type=1, value-from-to type:
                    var keyList = filterItem.key;
                    var defaultList = filterItem.default;

                    htmlFilterItem += '<dl class="filter" data-key-from="' + keyList[0] + '" data-key-to="' + keyList[1] + '">' +
                        '<dt>' + filterItem.title + '</dt>' +
                        '<dd>' +
                        '<input type="number" class="input-from" name="input_' + keyList[0] + '" id="input_' + keyList[0] + '"  value="' + defaultList[0] + '" />' +
                        ' ~ ' +
                        '<input type="number" class="input-to" name="input_' + keyList[1] + '" id="input_' + keyList[1] + '"  value="' + defaultList[1] + '" />' +
                        '<a class="btn-from-to">确定</a></dd></dl>';

                } else if (filterItem.type && filterItem.type == 2) {//type=2, time-from-to type:
                    var keyList = filterItem.key;
                    var defaultList = filterItem.default;

                    htmlFilterItem += '<dl class="filter" data-key-from="' + keyList[0] + '" data-key-to="' + keyList[1] + '">' +
                        '<dt>' + filterItem.title + '</dt>' +
                        '<dd>' +
                        '<span class="time-inputer" data-saver="input_' + keyList[0] + '"><input type="hidden" class="input-from" name="input_' + keyList[0] + '" id="input_' + keyList[0] + '" value="' + defaultList[0] + '" /></span>' +
                        ' &nbsp;&nbsp;~&nbsp;&nbsp; ' +
                        '<span  class="time-inputer" data-saver="input_' + keyList[1] + '"><input type="hidden" class="input-to" name="input_' + keyList[1] + '" id="input_' + keyList[1] + '" value="' + defaultList[1] + '" /></span>' +
                        '<a class="btn-from-to">确定</a></dd></dl>';

                }

            }

            if (htmlFilterItem != '' && selfObj.ui.uiFilter) {
                $(htmlFilterItem).appendTo(selfObj.ui.uiFilter);
            }

            //time-inputer ini

            var ctrlist =  $(selfObj.ui.uiFilter).find('.time-inputer');


            for(i=0;i<ctrlist.length;i++)
            {

                $(selfObj.ui.uiFilter).find(".time-inputer:eq("+i+")").timeInputer();

            }



        }


        //                   selfObj.ui.uiFilter

    },//-/
    /**
     * iniToolbar
     */
    iniToolbar: function () {
        var selfObj = this;

        //select all:
        var $selectAllBtn = $('<button class="btn btn-default btn-select-all"><i class="glyphicon glyphicon-check"></i> 选择全部</button>');
        $selectAllBtn.appendTo($(this.ui.uiBtn + ' .btn-group-selectall'));

        //attaching btn action
        $(this.ui.uiBtn + ' .btn-group-selectall').find('.btn-select-all').click(function (e) {

            selfObj.selectAll();
        });


        if (this.ui.toolbarSetting && this.ui.toolbarSetting.length > 0) {

            var curBtnSet;
            for (var i = 0; i < this.ui.toolbarSetting.length; i++) {

                curBtnSet = this.ui.toolbarSetting[i];

                curIcon = curBtnSet.icon ? '<i class="' + curBtnSet.icon + '"></i> ' : '';

                $('<button class="btn btn-default btn-' + curBtnSet.key + '">' + curIcon + curBtnSet.title + '</button>').appendTo($(this.ui.uiBtn + ' .btn-group-operation'));

            }


        }


        //multi-delete:
        /*var $btnDel=$('<button class="btn btn-danger btn-delete"><i class="glyphicon glyphicon-remove"></i> 删除</button>');
         $btnDel.appendTo($(this.ui.uiBtn+' .btn-group-operation'));*/
        $(this.ui.uiBtn + ' .btn-group-operation .btn-delete').click(function (e) {
            selfObj.deleteMulti();
        });

        $(this.ui.uiBtn + ' .btn-group-operation .btn-add').click(function (e) {
            var urlAdd = "/com.php?target=" + selfObj.ui.editorTarget;
            var curCategoryId=selfObj.params[selfObj.categoryKey] || 0;
            if (typeof selfObj.ui.urlAdd != 'undefined' && selfObj.ui.urlAdd != '') {
                urlAdd = selfObj.ui.urlAdd;
            }

            urlAdd = parseInt(curCategoryId)>0 ? urlAdd+'&'+selfObj.categoryKey+'='+curCategoryId : urlAdd;

            window.open(urlAdd);
        });


        //addnew:
        /*var $btnAdd=$('<a class="btn btn-default" href="/com.php?target='+this.ui.editorTarget+'" target="_blank"><i class="glyphicon glyphicon-plus"></i> 添加新'+this.ui.itemTitle+'</a>');
         $(this.ui.uiBtn+' .btn-group-operation').prepend($btnAdd) ;*/


    },//-/


    /**
     *  read
     *  read data from server
     *  @param {object} params : data for conditioning list.
     *                           e.g.: params.page=2  -> get page 2 of list.
     */
    read: function (params) {


        var selfObj = this;

        //check validation
        if (!this.isValid()) {
            console.log('list cannot create due to invalid params!');
            return false;
        }

        if (params) {
            this.params = $.extend(this.params, params);
        }

        //set post-data default value
        var dataPost = {
            page: 1,
            format: 0,
            action: 'SELECT',

        };

        //combine current param values to post data.
        $.extend(dataPost, this.params);

        //reset UI and view loading
        $(selfObj.ui.uiList).children().remove();
        j79.viewLoading(this.ui.uiList);

        console.log('core.list post data:');
        console.log(dataPost);

        //communicating with server:
        j79.post(
            {
                title: this.ui.itemTitle,
                data: dataPost,
                actionSuccess: function (result) {  //after success
                    j79.hideLoading(selfObj.ui.uiList);
                    if (result) {

                        //get total amount
                        if (result.total_amount && Number(result.total_amount) >= 1) {
                            selfObj.totalAmount = Number(result.total_amount);

                        }

                        //selfObj.page=selfObj.page;


                        if (result.data) {
                            //view list
                            selfObj.view(result.data);
                            selfObj.attachActionHandler();

                        }
                    }

                },


            });
    }, //-/read

    /**
     *  view
     *  view list to customer
     *  @param {array of object} proData : product list data get from server result['data'];
     */
    view: function (proData) {

        if (!this.isValid()) {
            console.log('list cannot create due to invalid params!');
            return false;
        }

        $(this.ui.uiList).children().remove();


        //if no result:
        if (proData.length == 0) {
            $listItem = $('<div class="list-none"><i class="glyphicon glyphicon-info-sign"></i> 没有结果</div>');
            $listItem.appendTo($(this.ui.uiList));
            $(this.ui.uiPager).find('.pager-bar').remove();
            return;
        }

        //view list
        for (var i = 0; i < proData.length; i++) {


            //generate list item element
            $listItem = this.itemGenerator(proData[i], i, this.ui.editorTarget);

            //append new item to list.
            $listItem.appendTo($(this.ui.uiList));


        }

        //view pager
        $(this.ui.uiPager).find('.pager-bar').remove();
        j79.viewPager(Math.ceil(this.totalAmount / this.params.per_page), this.params.page, this.ui.uiPager, this);
        $(this.ui.uiPager).find('.total-amount-no').text(this.totalAmount);


        //call action after view
        if (typeof(this.actionAfterView) == 'function') {
            this.actionAfterView(this);
        }


    },//-/view

    /**
     *  setPage
     *  set page no
     */
    setPage: function (newPage) {

        //console.log(listObj);
        this.params.page = newPage;
        this.read();

    },//-/


    /**
     *  attachActionHandler
     *  attach action handler to list items.
     */
    attachActionHandler: function () {
        var selfObj = this;
        //attach Delete
        this.attachActionDelete(this.ui.uiList);


        //attach li click to high-lighted:
        $(selfObj.ui.uiList + '>li').click(function (e) {
            $(this).siblings().removeClass('high-lighted');
            $(this).addClass('high-lighted');
        });

        //attach dbclick to select
        $(selfObj.ui.uiList + '>li').dblclick(function (e) {
            var curV = $(this).find('.select-part input:checkbox').prop('checked');
            if (curV) {
                $(this).find('.select-part input:checkbox').prop('checked', false);
                $(this).removeClass('selected');
            } else {
                $(this).find('.select-part input:checkbox').prop('checked', true);
                $(this).addClass('selected');
            }
        });

        //select item change class
        $(selfObj.ui.uiList).find('.select-part input:checkbox').click(function (e) {
            var curV = $(this).prop('checked');
            var curLi = $(this).parents().closest('li');
            console.log(curV);
            if (curV) {
                $(curLi).addClass('selected');
            } else {
                $(curLi).removeClass('selected');
            }
        });


    },//-/attachActionHandler


    /**
     *  attachActionDelete
     *  attach delete related action
     */
    attachActionDelete: function (uiList) {

        var selfObj = this;

        //attach Delete
        $(uiList + ' li .btn-delete').click(function (e) {

            var curLi = $(this).parent().closest('li');

            $(curLi).addClass('high-lighted');


            var curIdx = $(curLi).attr('item-id');

            selfObj.deleteItem(curIdx, uiList);

        });
    },//-/attachActionDelete


    /**
     *  deleteItem
     *  delete item by given idx.
     *  idx can be single idx number, or can be idx list seperated by ',' like:'12,13'
     */
    deleteItem: function (idx, uiList) {
        var selfObj = this;


        if (j79.isId(idx)) {

            /*var flagContinue=confirm("确认要删除吗？");
             if(!flagContinue){
             return false;

             }*/


            var funcDel = function () {

                var dataPost = {};
                dataPost.target = selfObj.params.target;
                dataPost.action = 'DELETE';
                dataPost.format = 0;
                dataPost.idx = idx;


                //generate disabled btn list:
                var disBtns = [];
                var arrIdx = idx.split(',');
                for (var i = 0; i < arrIdx.length; i++) {
                    disBtns.push(uiList + ' li[item-id=' + arrIdx[i] + '] .btn-delete');

                }
                if (arrIdx.length > 1) {
                    disBtns.push(selfObj.ui.uiBtn + ' .btn-delete');
                }

                //start to post to server:
                j79.post(
                    {
                        title: "删除" + selfObj.ui.itemTitle,
                        data: dataPost,
                        actionSuccess: function (result) {  //after success

                            var arrIdx = idx.split(',');
                            for (var i = 0; i < arrIdx.length; i++) {
                                console.log(uiList + ' li[item-id=' + arrIdx[i] + ']');
                                $(uiList + ' li[item-id=' + arrIdx[i] + ']').remove();
                            }

                        },

                        disabled: disBtns

                    });
            };//

            //var funcCancel=function(){
            //	$('#mdConfirmDel').modal('hide');
            //}//

            j79.mwConfirm('mdConfirmDel', '删除' + selfObj.ui.itemTitle, '确认要删除编号为[<b style="color:red">' + idx + '</b>]的' + selfObj.ui.itemTitle + '吗？', funcDel);


        }else {
            return false;
        }


    },//-/deleteItem


    /**
     *  getSelectedIdx
     *  get selected item idx list seperated by ','
     */
    getSelectedIdx: function () {

        var selectedItems = $(this.ui.uiList + " input[name='selectedItems']:checked").closest("li");

        var curItem;
        var idxList = '';
        var sep = '';
        for (i = 0; i < selectedItems.length; i++) {
            curItem = selectedItems[i];
            idxList += sep + parseInt($(curItem).attr('item-id'));
            sep = ',';
        }
        return idxList;

    },//-/getSelectedIdx


    /**
     *  selectAll
     *
     */
    selectAll: function () {

        if ($(this.ui.uiBtn + ' .btn-select-all').hasClass('active')) {
            $(this.ui.uiList + " input:checkbox").each(function () {
                $(this).prop('checked', false);
            });
            $(this.ui.uiBtn + ' .btn-select-all').removeClass('active');
        } else {
            $(this.ui.uiList + " input:checkbox").each(function () {
                $(this).prop('checked', true);
            });
            $(this.ui.uiBtn + ' .btn-select-all').addClass('active');
        }
    },//-/


    /**
     *  deleteMulti
     *  delete selected items.
     */
    deleteMulti: function () {


        var idxList = this.getSelectedIdx();
        if (idxList != '') {
            this.deleteItem(idxList, this.ui.uiList);

        } else {
            $(this.ui.uiBtn + ' .btn-delete').btnReset();
            //alert('请先选择要操作的项（勾选产品图左侧的框）！');
            j79.mwInform('mwInform', '提示', '请先选择要操作的项（勾选产品图左侧的框）！');
        }

    },//-/deleteMulti


};//=/j79List.prototype






