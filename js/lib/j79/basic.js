/**
 *  btnLoading
 *  set btn disabled, and set class 'btn-loading' to current element.
 */
(function($) {
    $.fn.btnLoading = function() {
        $(this).attr('disabled', 'disabled');
        $(this).addClass('btn-loading');
    };
    $.fn.btnReset = function() {
        $(this).removeAttr('disabled');
        $(this).removeClass('btn-loading');
    };
})(jQuery);
//-/ 



/**
 *  j79
 *  new Namespace.
 *  including frequently used functions.
 */
(function() {
    var _NS = function() {};

    /**
	  *  j79.post
	  *  post data to j79frame-server and get result, response according to result data.
	  *  
	 
	  *  @param {object}  postData : param data
	  *                              {
	  *                                  	    
	  *                                data          :  //post data in object.attr foramt.
	  *                                               {         
	  *                                                 target: //server dispatching target, must exists.
	  *                                                 ... ...
	  *                                               },
	  *                                                 
	  *                                title         : //alert window title.
	  *                                actionSuccess : //function handler call when success.
	  *                                actionFailed  : //function handler call when failed.
	  *                                actionErr     : //function handler call when connect error.
	  *                                disabled      : //array of disalbed btn selector 
	  *                                                  (string,like".btn-delete") 
	  *                               }		
		  
	  
	  */
    _NS.prototype.post = function(params) {



        var data = {
            title: '操作',
            disabled: []

        };

        $.extend(data, params);



        var postData = data.data;
        postData.format = 0;
        var uiTitle = data.title || '与服务器通讯';
        var handleSuccess = data.actionSuccess;
        var handleFailed = data.actionFailed;
        var handleErr = data.actionErr;
        var disabledList = data.disabled;

        if (!postData || !postData.target) {
            console.log('format error');
            return false;
        }

        var resetDisabled = function(dList) {
            if (dList && dList instanceof Array) {
                for (var di = 0; di < dList.length; di++) {
                    if ($(dList[di])) {

                        $(dList[di]).removeAttr('disabled');
                        $(dList[di]).removeClass('btn-loading');
                    }
                }
            }
        };

        var setDisabled = function(dList) {
            if (dList && dList instanceof Array) {
                for (var di = 0; di < dList.length; di++) {
                    if ($(dList[di])) {

                        $(dList[di]).attr('disabled', 'disabled');
                        $(dList[di]).addClass('btn-loading');
                    }
                }
            }
        };

        //disable btns.
        setDisabled(disabledList);

        /*console.log('postData:');
        try{
            console.log(JSON.parse( postData));
        }catch(e){
            console.log( postData);
        }*/
        console.log("Post-Data ["+postData.target+'] :');
        console.log( postData);

        $.post('/com.php', postData,
                function(data, status) {

                    //console.log("Result status: ");
                    //console.log(status);

                    var jsonData;


                    //try to parse result into json format 
                    try {
                        jsonData = JSON.parse(data);

                        console.log("Result-Data ["+postData.target+"]: ");
                        console.log(jsonData);



                    } catch (err) { //result format is not json

                        console.log("Result raw data: ");
                        console.log(data);

                        resetDisabled(disabledList);
                        if (typeof handleFailed == 'function') {
                            handleFailed();
                        } else {
                            if (j79App && j79App.DEBUG_OFF && j79App.DEBUG_OFF == 1) {
                                console.log(uiTitle + '发生错误，返回错误格式！');
                            } else {
                                alert(uiTitle + '发生错误，返回错误格式！');
                            }
                        }
                        return;

                    }




                    var resultMain = jsonData.result;

                    if (typeof resultMain != 'undefined' && resultMain == 1) { //server returned result, claiming success	(result=1)					  
                        //reset disabled.
                        //console.log(disabledList);
                        resetDisabled(disabledList);

                        if (typeof handleSuccess == 'function') {
                            handleSuccess(jsonData);
                        } else {
                            //alert(uiTitle+'操作成功！');
                            console.log(uiTitle + '操作成功！');
                        }



                    } else { //server returned result, but claiming failure( 'result'=0)			



                        //reset disabled.
                        resetDisabled(disabledList);

                        //if exists failed type:
                        if(jsonData.failed_type && jsonData.error_code && jsonData.error_code==2000){

                            //alert('failed type'+jsonData.failed_type);

                            switch (Number(jsonData.failed_type)){
                                case 1:
                                    //window.location.href = '/com.php?target=login&from_url=' + encodeURIComponent(window.location.href);
                                    console.log(uiTitle + '需要用户登录！');
                                    //return;
                                    break;
                                case 2:
                                    window.location.href = '/com.php?target=adm%2Flogin&from_url=' + encodeURIComponent(window.location.href);
                                    console.log(uiTitle + '需要管理员登录！');
                                    return;
                                    break;
                                case 3:
                                    window.location.href = '/com.php?target=partner%2Flogin&from_url=' + encodeURIComponent(window.location.href);
                                    console.log(uiTitle + '需要合作方管理员登录！');
                                    return;
                                    break;
                                case 10:
                                    alert('管理员权限等级过低，无权进行当前操作！');
                                    return;
                                    break;

                            }



                        }


                        if (typeof handleFailed == 'function') {
                            handleFailed(jsonData);
                        } else {

                            if (jsonData.need_login_type) {
                                window.location.href = '/com.php?target=login&from_url=' + encodeURIComponent(window.location.href);
                                console.log(uiTitle + '需要登录！');
                            } else {

                                if (j79App && j79App.DEBUG_OFF && j79App.DEBUG_OFF == 1) {
                                    console.log(uiTitle + '发生错误，请稍后再试！');
                                } else {
                                    alert(uiTitle + '发生错误，请稍后再试！');
                                }
                            }
                            //alert(uiTitle+'发生错误，请稍后再试！');
                        }

                    }


                }) //-/post
            .error(function(data, status, e) { //error when communicating with server

                console.log(uiTitle + '时，连接服务器出错，请稍后再试');
                console.log('status:'+status);
                console.log('data:');
                console.log(data);
                console.log(e);


                if (typeof handleErr == 'function') {
                    handleErr();
                } else if (typeof handleFailed == 'function') {
                    handleFailed();
                } else {
                    if (j79App && j79App.DEBUG_OFF && j79App.DEBUG_OFF == 1) {
                        console.log(uiTitle + '时，连接服务器出错，请稍后再试');
                    } else {
                        alert(uiTitle + '时，连接服务器出错，请稍后再试');
                    }

                    //alert(uiTitle+'时，连接服务器出错，请稍后再试');
                }


            });
        return true;
    }; //-/post

    _NS.prototype.trim=function(strContet){
        return strContet.replace(/(^\s*)|(\s*$)/g,"");
    };//-/trim

    /**
     *  isId
     *  verify given id data to find whether it match id or id list format(numbers seperated by ',')
     *  @param {mix} id : given id data
     *  @return {bool}  : true -  is number or number list seprated by ','
     *                    false-  not id format.
     */
    _NS.prototype.isId = function(id) {

        if (!id) {
            return false;
        }
        var checkValid = new RegExp('(^[0-9]+$)|(^[0-9]+(,[0-9]+)*$)');
        if (checkValid.test(id)) {
            return true;
        } else {
            return false;
        }

    }; //-/isId

    /**
     *  toJSON
     *  get json from jsonString
     *  @params {string/object}
     *  @return object/null/false:
     *           object:
     *           null: empty or input data is not string or object
     *           false: parse error.
     *
     */
    _NS.prototype.toJSON = function(jsonStr) {
        if (!jsonStr) {
            return null;
        }
        var dataObj;
        if((typeof jsonStr=='string') && jsonStr.constructor==String){
            try{
                dataObj= eval("(" + jsonStr + ")");
            }catch(e){
                dataObj=false;
            }

        }else if(typeof jsonStr=='object'){
            dataObj=jsonStr;
        }else{
            dataObj=null;
        }

        return dataObj;

    }; //-/toJSON

    /**
     *  toJSONString
     *  get string form json object
     *  if empty then return '';
     */
    _NS.prototype.toJSONString = function(jsonObj) {
        if (!jsonObj) {
            return '';
        }
        var jsonStr = $.toJSON(jsonObj);
        if (jsonStr == '[]') {
            return '';
        }
        return jsonStr;

    }; //-/toJSONString

    /**
    *  geSelectOptions
    *  generate option string for select-control building by optionObj data.
    *  use optionObj attribute name as value, attribute value as text.
    */
    _NS.prototype.geSelectOptions = function(optionObj, flagViewEmpty, currentValue) {
        var optStr = '';
        if(flagViewEmpty && flagViewEmpty===true){
            optStr='<option value="">--请选择--</option>';
        }
        var curValue;
        var curIdx;
        var curLabel;
        for (var p in optionObj) { // 方法 
            if (typeof(optionObj[p]) != "function") {
                curValue=optionObj[p];
                if(typeof(curValue)=="object"){
                    curIdx=curValue.value || '';
                    curLabel=curValue.label || '';
                }else{
                    curIdx=p;
                    curLabel=curValue;
                }
                var selectedStr='';
                if(currentValue && currentValue!='' && currentValue==curIdx){
                    selectedStr=' selected ';
                }
                optStr += '<option value="' + curIdx + '" '+selectedStr+'>' + curLabel + "</option>";
            }
        }
        return optStr;
    }; //-/geSelectOptions

    /**
     * getStringByValue
     * get string by value from valueData object.
     * curValue is key to valueData.
     * e.g.:
     *       curValue='1',
     *       valueData={'0':'text2', '1':'text1'}
     *       =>
     *       return 'text1'
     *
     *       if curValue is null or undefined. then get first value of valueData.
     *
     * @param curValue
     * @param valueData
     * @returns {string} : if no match, then return ''.
     */
    _NS.prototype.getStringByValue=function(curValue, valueData){

        if( valueData[curValue]){
            return valueData[curValue];
        }else{

            if(typeof curValue =='undefined' || curValue==null){

                for (var p in valueData) {
                    if (typeof(valueData[p]) != "function") {
                        return valueData[p];
                        break;
                    }
                }

            }

            return '';
        }

    };//-/



    /**
     * getValueExists
     * 如果curValue存在并且不是null，返回existValue, 反之defaultValue.
     *
     *
     * @param curValue
     * @param existValue
     * @param defaultValue
     * @returns {*}
     */
    _NS.prototype.getValueExists=function(curValue, existValue, defaultValue){


        if( typeof curValue=='undefined' || curValue==null || curValue.toLocaleString()=='null' || curValue.toLocaleString()=='undefined' || curValue=='') {
            return defaultValue;
        }else{
            return existValue;
        }

    };//-/getValueExists

    /**
     * getBitNameList
     * 根据currentValue，返回位状态值的li列表。
     * @param optionObj
     * @param currentValue
     * @returns {string}
     */
    _NS.prototype.getBitNameList= function(optionObj, currentValue) {

        var optStr = '';

        var curValue;
        var curIdx;
        var curLabel;
        for (var p in optionObj) { // 方法
            if (typeof(optionObj[p]) != "function") {
                curValue=optionObj[p];
                if(typeof(curValue)=="object"){
                    curIdx=curValue.value || '';
                    curLabel=curValue.label || '';
                }else{
                    curIdx=p;
                    curLabel=curValue;
                }

                if(currentValue && ( (curIdx>0 &&  (currentValue & curIdx)==curIdx) || (curIdx==0 &&  currentValue==0 ))){
                    optStr += '<li data-bit-value="' + curIdx + '" >' + curLabel + "</li>";
                }

            }
        }

        return optStr;


    };//-/getBitNameList

    /**
     *  parseIdx
     *  parse input idxVar into idx array.
     *  if invalid return array of length=0;
     */
    _NS.prototype.parseIdx = function(idxVar) {

        var idxString = idxVar.toString();
        var result = [];

        if (idxString != '') {

            var idxArr = idxString.split(',');
            if (idxArr.length > 0) {

                for (var i = 0; i < idxArr.length; i++) {

                    if (Number(idxArr[i]) > 0) {
                        result.push(Number(idxArr[i]));
                    }
                }
            }


        }
        return result;


    }; //-/parseIdx

    /**
     * getTreeHtml
     * generate tree html.
     * @param currentNode
     * @param ui
     * @param curValue
     * @param depth
     * @param depthLimit
     * @returns {*}
     */
    _NS.prototype.getTreeHtml=function(currentNode, ui, curValue, depth, depthLimit){


        var node_text_name='name';
        var node_value_name='id';
        var node_name='item';

        var SELF=this;

        depthLimit=depthLimit || 0;

        //depth limit:
        if(depthLimit>0 && depth> depthLimit){
            return ui;
        }

        //hide attribute:
        if($(currentNode).attr('hide')){
            return ui;
        }


        if($(currentNode).children().length>0){

            ui+='<ul>';

            $(currentNode).children(node_name).each(function(i){

                var nodeText=$(this).attr(node_text_name);
                var nodeValue=$(this).attr(node_value_name);

                if(!$(this).attr('hide')){//not hide:
                    var classN='';

                    //class of which has children.
                    if($(this).children().length>0){
                        classN='closed';
                    }

                    //check if current.
                    var emClass='';
                    if( nodeValue==curValue){
                        classN+=' cur ';
                        emClass=' class="checked"';

                        $(wrap_obj).find('.input_region').val(nodeText);
                        $('#'+data_saver).attr('label',nodeText);
                    }

                    //build current ui li html start:
                    ui+='<li class="'+classN+'"><b class="node"><a id="'+nodeValue+'" name="'+nodeText+'">'+nodeText+'<em'+emClass+'></em></a></b>';

                    if(!$(this).attr('hide')){
                        ui=SELF.getTreeHtml(this, ui, curValue, depth+1,depthLimit); //递归
                    }
                    ui+='</li>';  // li close
                }




            });

            ui+='</ul>';  //close ul
        }




        return ui;

    };//-/getTreeHtml

    /**
     *  inArray
     *  check value whether in arr or not. 
     */
    _NS.prototype.inArray = function(arr, value) {

        var i = arr.length;
        while (i--) {
            if (arr[i] === value) {
                return true;
            }
        }
        return false;
    }; //-/inArray


    /**
     *  setHtml
     *  set html by replacing "[#attribute-name#]" to data[attribute-name] value.
     */
    _NS.prototype.setHtml = function(data, htmlStr) {
        var resultHtml = htmlStr;
        var reg;
        var v;
        if(typeof data =='undefined' || data==null || typeof resultHtml =='undefined' || resultHtml==null || resultHtml=='' ){
            return htmlStr;
        }
        for (var p in data) { // 方法 
            if (typeof(data[p]) != "function") {
                reg = new RegExp("\\[#" + p + "#\\]", "g");
                v = data[p] == null ? '' : data[p];
                resultHtml = resultHtml.replace(reg, v);
            }
        }
        return resultHtml;

    }; //-/setHtml


    /**
     *  getURLParam
     *  get page url param value by param key name.
     *  @param {string} name : page url param key-name.
     *  @return {string}     : param value. 
     */
    _NS.prototype.getURLParam = function(name) {

        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        //console.log('param sep:');
        //console.log(r);		  
        if (r != null)
            return decodeURI(r[2]);
        return null;
    }; //-/getURLParam


    /**
     *  getURLObj
     *  get url params list as object.
     *  each param name as object attribute name.
     *  each param value as object attribute value.
     */
    _NS.prototype.getURLObj = function() {
        var urlString = window.location.search.substr(1);

        var urlObject = null;
        if (typeof urlString != 'undefined' && urlString != '') {
            urlObject = {};
            var urlArray = urlString.split("&");
            for (var i = 0, len = urlArray.length; i < len; i++) {
                var urlItem = urlArray[i];
                var item = urlItem.split("=");
                urlObject[item[0]] = item[1];
            }
        }

        if (urlObject == null) {
            urlObject = null;
        }

        return urlObject;


    };//-/getURLObj

    /**
     * replaceURLParam
     * replace current page url param by name and value.
     * @param name
     * @param value
     * @returns {string}
     */
    _NS.prototype.replaceURLParam=function(name, value){

        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");



        var r = window.location.search.substr(1).replace(reg,'');
        console.log('param set:');
        console.log(r);
        if (r != null){

            return  '/com.php?'+r+'&'+name+'='+value;


        }
        return  '/com.php?'+name+'='+value;


    };//-/replaceURLParam




    _NS.prototype.getFileName = function(fileFullName) {
        var pos = fileFullName.lastIndexOf('/');
        return fileFullName.substring(pos + 1);

    }; //-/getFileName


    /**
     *  loadCSS
     *  add css file link to head.
     *  @param {string} urlCSS : url of css file.
     *
     */
    _NS.prototype.loadCSS = function(urlCSS) {

        if (urlCSS && $('head').find('link[href="' + urlCSS + '"]').length <= 0) {
            $("<link>")
                .attr({
                    rel: "stylesheet",
                    type: "text/css",
                    href: urlCSS
                }).insertAfter("head title:first");

            //$('head').prepend($link1);


            //.appendTo("head");
        }


    }; //-/loadCSS





    /**
     *  addUnique
     *  add value to array if it not exists in current array.
     *  @param {array} targetArray : target array to add.
     *  @param {mix}   value       : value to add to array.
     *  
     *
     */
    _NS.prototype.addUnique = function(targetArray, value) {


        if (!_NS.prototype.isArray(targetArray)) {
            return;
        }

        var amount = targetArray.length;
        var flagExists = false;
        for (var i = 0; i < amount; i++) {
            if (targetArray[i] == value) {
                flagExists = true;
                break;
            }
        }

        if (!flagExists) {
            targetArray.push(value);
        }



    }; //-/addUnique
















    /**
     *  isArray
     *  return true-is array; false-not array;
     */
    _NS.prototype.isArray = function(obj) {

        return Object.prototype.toString.call(obj) === '[object Array]';
    }; //-/isArray

    /**
     *  isString
     *  return true-is string; false-not string;
     */
    _NS.prototype.isString = function(str){
        return (typeof str=='string') && str.constructor==String;
    };//-/-/isString






    /**
     *  viewModal
     *  create modal window and view. if already exists id element then set title, bodyHtml, btnHtml and show it.
     *  @param {object} settins : setting data. has attribute: title | bodyHtml | btnHtml | className | size='small'
     *  @param {string} id      : modal win id. if not set id, will generate by random number and return id.
     *  @return {string}        : id of modal window. 
     */
    _NS.prototype.viewModal = function(settings, id) {

        id = id || 'modalWindow' + Math.round(100 * Math.random());

        //ini modal window element
        if ($('#' + id).length <= 0) { //if id element not exists in document, then create modal element and append it to end of body.

            className=(settings && settings.className) || '';
            sizeClass=(settings && settings.size && settings.size=='small') ? 'modal-sm' : '';
            title = (settings && settings.title) || '提示';
            strBodyHtml = (settings && settings.bodyHtml) || '<p>... ...</p>';
            strBtnHtml = (settings && settings.btnHtml) || '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
            var $uiModal = $(
                '<div class="modal fade '+className+'" id="' + id + '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">' +
                '<div class="modal-dialog '+sizeClass+'" role="document">' +
                '<div class="modal-content">' +
                '<div class="modal-header">' +
                '   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                '   <h4 class="modal-title" id="myModalLabel">' + title + '</h4>' +
                '</div>' +
                '<div class="modal-body">' + strBodyHtml + '</div>' +
                '<div class="modal-footer">' + strBtnHtml +'</div>' +
                '</div></div></div>');
            $uiModal.appendTo('body');
        } else { //if exists certain id element.		  

            if (settings) {
                if (settings.title) {
                    $('#' + id).find('.modal-title').html(settings.title);
                }
                if (settings.bodyHtml) {
                    $('#' + id).find('.modal-body').html(settings.bodyHtml);
                }
                if (settings.btnHtml) {
                    $('#' + id).find('.modal-footer').html(settings.btnHtml);
                }

            }
        }
        //show modal window
        $('#' + id).modal('show');
        return id;

    }; //-/viewModal

    /**
     * mwConfirm
     * create and view modal-window confirm.
     * @param id         : modal id
     * @param title      : modal title
     * @param detailHtml : modal body html
     * @param handleYes  : function handler when YES clicked.
     * @param handleNo   : function handler when NO clicked, can be null.
     */
    _NS.prototype.mwConfirm = function(id, title,detailHtml, handleYes, handleNo) {

        var settings={
            "size":"small",
            "title": '<i class="glyphicon glyphicon-question-sign"></i> '+title,
            "bodyHtml": detailHtml,
            "btnHtml":'<a class="btn btn-primary btn-yes"><i class="glyphicon glyphicon-ok"></i> 是  Yes</a><a class="btn btn-default btn-no"><i class="glyphicon glyphicon-remove"></i> 否  No</a>'
        };
        j79.viewModal(settings, id);

        $('#'+id+' .modal-footer').find('.btn-yes').click(function (e) {
            $('#'+id).modal('hide');
            if(handleYes){
                handleYes();
            }
        });

        $('#'+id+' .modal-footer').find('.btn-no').click(function (e) {
            $('#'+id).modal('hide');
            if(handleNo){
                handleNo();
            }
        });

    };//-/

    /**
    * mwInform
    * create and view modal-window inform.
    * @param id         : modal id
    * @param title      : modal title
    * @param detailHtml : modal body html
    * @param handleFunc : function handler when closed.
    */
    _NS.prototype.mwInform = function(id, title,detailHtml, handleFunc) {
        var settings={
            "size":"small",
            "title": '<i class="glyphicon glyphicon-info-sign"></i> '+title,
            "bodyHtml": detailHtml,
            "btnHtml":'<a class="btn btn-primary btn-ok-close">关闭 | Close</a>'
        };
        j79.viewModal(settings, id);

        $('#'+id+' .modal-footer').find('.btn-ok-close').click(function (e) {
            $('#'+id).modal('hide');
            if(handleFunc){
                handleFunc();
            }
        });

    };//-/


    _NS.prototype.mwLoading = function(id, title, handleFunc) {
        var settings={
            "size":"small",
            "title": '<i class="glyphicon glyphicon-info-sign"></i> '+title,
            "bodyHtml": '<div class="loading">' +
            '<p>正在提交更新，请耐心等待...</p>' +
            '<div class="sk-wave">' +
            '<div class="sk-rect sk-rect1"></div>' +
            '<div class="sk-rect sk-rect2"></div>' +
            '<div class="sk-rect sk-rect3"></div>' +
            '<div class="sk-rect sk-rect4"></div>' +
            '<div class="sk-rect sk-rect5"></div>' +
            '</div></div>',
            "btnHtml":'<b></b>'
        };
        j79.viewModal(settings, id);

        $('#'+id+' .modal-footer').find('.btn-ok-close').click(function (e) {
            $('#'+id).modal('hide');
            if(handleFunc){
                handleFunc();
            }
        });

    };//-/


    /**
     *  viewPager
     *  view pager bar.
     */
    _NS.prototype.viewPager = function(pageTotal, pageCur, containerSelector, listObj) {

        if ($(containerSelector) && pageTotal && listObj) {

            var viewTotal = 4;

            pageCur = pageCur || 1;

            var pageList = '';



            var startPage = pageCur > viewTotal ? pageCur - viewTotal + 1 : 1;

            var viewPageTotal = startPage + viewTotal - 1 < pageTotal ? startPage + viewTotal - 1 : pageTotal;


            for (var i = startPage; i <= viewPageTotal; i++) {

                pageList += '<li';

                if (pageCur == i) {
                    pageList += ' class="active"';
                }
                pageList += '><a  class="page-no">' + (i) + '</a></li>';
            }
            if (viewPageTotal < pageTotal) {
                pageList += '...<li><a  class="page-no">' + (pageTotal) + '</a></li>'
            }
            var $ui = $(
                '<nav class="pager-bar">' +
                '  <ul class="pager">' +
                '    <li class="total-amount">共<b class="total-amount-no"></b>条结果</li>' +
                '    <li ><a class="previous"  aria-label="Previous">&lt;&lt; Prev</a></li>' +

                pageList +
                '    <li ><a class="next"  aria-label="Previous">Next &gt;&gt;</a></li>' +
                '    <li ><input type="text" class="go-page-no" name="goPageNo" id="goPageNo"  value="' + pageCur + '"/><a class="go-page"  aria-label="go-page">Go <i class="glyphicon glyphicon-play"></i></a></li>' +
                '</ul></nav>'

            );
            $ui.appendTo(containerSelector);



            //attach action handler:
            //page prev			 
            $(containerSelector).find('.pager-bar A.previous').click(function(e) {

                //console.log('preve page---------');

                if (listObj) {

                    if (pageCur > 1) {
                        listObj.setPage(--pageCur);
                    }
                }

            });
            //page next
           $(containerSelector).find('.pager-bar A.next').click(function(e) {

                if (listObj) {
                    if (pageCur + 1 <= pageTotal) {
                        console.log('listObj:');
                        console.log(listObj);
                        listObj.setPage(++pageCur);
                    }
                }

            });
            //detail page no click
           $(containerSelector).find('.pager-bar A.page-no').click(function(e) {

                if (listObj) {

                    console.log('pager object:');
                    console.log(listObj);

                    if (Number($(this).text()) >= 1) {
                        pageCur = Number($(this).text());
                        listObj.setPage(pageCur);
                    }
                }

            });

            //go page number click
           $(containerSelector).find('.pager-bar A.go-page').click(function(e) {

                console.log(listObj);
                if (listObj) {
                    var goPageNo = Number($ui.find('#goPageNo').val());
                    if (goPageNo >= 1 && goPageNo <= pageTotal) {
                        pageCur = goPageNo;
                        listObj.setPage(goPageNo);
                    }
                }

            });

        }


    }; //-/viewPager

    /**
     *  viewLoading
     *  view loading animation with css3
     */
    _NS.prototype.viewLoading = function(containerSelector) {

        if ($(containerSelector)) {



            $(containerSelector).prepend('<div class="loading">' +
                //'<p>载入中，请稍等</p>'+
                '<div class="sk-wave">' +
                '<div class="sk-rect sk-rect1"></div>' +
                '<div class="sk-rect sk-rect2"></div>' +
                '<div class="sk-rect sk-rect3"></div>' +
                '<div class="sk-rect sk-rect4"></div>' +
                '<div class="sk-rect sk-rect5"></div>' +
                '</div></div>');


        }
    }; //-/viewLoading

    /**
     *  hideLoading
     *  remove loading animation
     */
    _NS.prototype.hideLoading = function(containerSelector) {

        if ($(containerSelector)) {

            $(containerSelector).find('.loading').remove();


        }
    }; //-/hideLoading

    /**
     *  setCookie
     *  set cookie
     *  @param {string}     name  : cookie keyname
     *  @param {string/int} value : cookie value
     *  @param {int}        time  : cookie expire time in min.
     *
     */
    _NS.prototype.setCookie = function(name, value, time) {

        var exp = new Date();
        exp.setTime(exp.getTime() + time * 60000);
        document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString();

    }; //-/


    /**
     *  getCookie
     *  @param {string} name  : cookie key name.  
     *  @return {null/string} : if no cookie, then return null, else return cookie value.
     */
    _NS.prototype.getCookie = function(name) {
        var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");
        if (arr = document.cookie.match(reg)) {
            return unescape(arr[2]);
        } else {
            return null;
        }
    }; //-/


    /**
     *  deleteCookie
     *  set expire time to delete cookie.
     */
    _NS.prototype.deleteCookie = function(name) {
        var exp = new Date();
        exp.setTime(exp.getTime() - 1);
        var cval = getCookie(name);
        if (cval != null)
            document.cookie = name + "=" + cval + ";expires=" + exp.toGMTString();
    }; //-/

    /**
     * isMobile
     * check if user use mobile to visit site.
     * @return {bool}  is mobile or not.
     */
    _NS.prototype.isMobile=function(){
        var sUserAgent= navigator.userAgent.toLowerCase();

        var bIsIpad= sUserAgent.match(/ipad/i) == "ipad";

        var bIsIphoneOs= sUserAgent.match(/iphone os/i) == "iphone os";

        var bIsMidp= sUserAgent.match(/midp/i) == "midp";

        var bIsUc7= sUserAgent.match(/rv:1.2.3.4/i) == "rv:1.2.3.4";

        var bIsUc= sUserAgent.match(/ucweb/i) == "ucweb";

        var bIsAndroid= sUserAgent.match(/android/i) == "android";

        var bIsCE= sUserAgent.match(/windows ce/i) == "windows ce";

        var bIsWM= sUserAgent.match(/windows mobile/i) == "windows mobile";

        if (bIsIpad || bIsIphoneOs || bIsMidp || bIsUc7 || bIsUc || bIsAndroid || bIsCE || bIsWM) {

            return true;

        }
        return false;
    };//-/






    //set namespace
    window.j79 = new _NS();
})(); //-/
