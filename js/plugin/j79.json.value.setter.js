/**
 choiceBox
 *
 *
 */
j79.loadCSS("/css/j79.json.value.setter.css");

(function ($) {

    $.fn.jsonValueSetter = function () {


        var SELF = this;

        var SELF_ID = $(SELF).attr('id');


        var STR_PLACEHOLDER = $(SELF).attr('placeholder') || '请选择';


        var DATA={};


        var FLAG_VIEW_ONLY=this.hasClass('view-static') ? true : false;


        var SUB_KEY=$(SELF).attr('sub-key-name') || 'sub';

        var URL_JSON_STRUCT=$(SELF).attr('json-struct-def') || '';

        var FLAG_TRANSFER_BOOLEAN=$(SELF).attr('flag-transfer-boolean') ? true : false;

        var STRUCT_JSON={};




        var DATA_SAVER = $(SELF).attr('data-saver') || '';

        /*
         DATA_SAVE_PATH: when it not empty, save generated data to a attribute named like DATA_SAVE_PATH of the object(DATA_SAVER data indicated)
         */
        var DATA_SAVE_PATH = $(SELF).attr('data-save-path') || '';

        if (DATA_SAVER != '') {
            $('#'+DATA_SAVER).hide();
        }




        //html codes-------------------------------
        //UI html
        var HTML_UI= '' ;






        /**
         *  saveData
         *  save selected data to data-savers.
         *
         */
        var saveData = function () {

            let result = null;

            //function to fill json value:
            var fillValue=function(curItem, curJson){
                curJson=curJson || {};



                let keyName=$(curItem).attr('key') || '';
                if(!$(curItem).hasClass('has-child')){//no sub item




                    let curV=$(curItem).attr('value') || 0;
                    if(FLAG_TRANSFER_BOOLEAN){
                        curV=curV==1 ? true : false;
                    }
                    if(keyName){
                        curJson[keyName]=curV;
                    }
                }else{//has sub item:



                    let curJ={};
                   $(curItem).find('ul>li').each(function (i) {

                       curJ= fillValue(this,curJ);
                   })
                    curJson[keyName]=curJ;

                }

                return curJson;

            };


            //generate result:

            $(SELF).find('.json-value-setter-container>ul>li').each(function(i){
                result=fillValue(this,result);
            });

            console.log(result);

            //save in data_saver.
            if (DATA_SAVER != '') {

                if (DATA_SAVE_PATH) {
                    var dataObj = j79.toJSON($('#' + DATA_SAVER).val());
                    if (!dataObj) {
                        dataObj = {}
                    }
                    dataObj[DATA_SAVE_PATH] = result;
                    result = dataObj;
                    $('#' + DATA_SAVER).val(j79.toJSONString(result));

                }else{
                    $('#' + DATA_SAVER).val(j79.toJSONString(result));
                }

            }


        };//-/saveData


        /**
         * readData
         * read data from data-savers.
         */
        var readData = function () {

            if (DATA_SAVER != '' && $('#' + DATA_SAVER).val() && $('#' + DATA_SAVER).val() != '') {


                var data = j79.toJSON($('#' + DATA_SAVER).val());
                if(data===false || data==null ){
                    return;
                }

                if (DATA_SAVE_PATH) {
                    data = data[DATA_SAVE_PATH] ? data[DATA_SAVE_PATH] : null;
                }

                if (data) {
                    if(typeof data =='object'){

                        DATA=data;
                    }

/*
                    //function to get value and fill in tree ui:
                    var getValueFillTree=function(curItemList,curJson, keyName){
                        curJson=curJson || {};
                        console.log(keyName);
                        console.log(curJson);
                        console.log(curItemList);


                        if(typeof curJson =='object'){

                            console.log('has sub')
                            for(let subkey in curJson){
                                getValueFillTree( $(curItemList).find('li[key="'+keyName+'"] ul')[0], curJson[subkey], subkey);
                            }

                        }else{
                            console.log('has no sub')
                            let curV=curJson;
                            if(FLAG_TRANSFER_BOOLEAN){
                                let curV=curJson===true || curJson==1 || curJson=='true' ? 1:0;
                            }
                            console.log('cur value')
                            console.log(curV);
                            console.log($(curItemList).find('li[key="'+keyName+'"]'))
                            $(curItemList).find('li[key="'+keyName+'"]').attr("value", curV);
                        }

                    };

                    if(typeof data =='object'){
                        let curList=$(SELF).find('.value-list');
                        console.log(curList);
                        console.log($(SELF).length);
                        let curL=$(".json-value-setter")[0];
                        $(curL).find("div").each(function(i){
                            console.log("find1:");
                            console.log(this);
                        });
                        //$(curList).empty();
                        for(let key in data){
                            getValueFillTree(curList,data[key],key);
                        }

                    }*/



                }


            }


        };//-/



        /**
         *  build
         *  产生列表
         *
         */
        var build = function () {



            var loadStructJson = function (handleFinished) {

                $.get(URL_JSON_STRUCT+'?rnd='+Math.random()).success(function (result) {

                    SELF.STRUCT_JSON = result;
                    if (handleFinished && typeof handleFinished == 'function') {
                        handleFinished(result);
                    }
                }).error(function(result){
                    console.log('xml load error');


                });
            };//-/

            //get json structure in tree view.
            var getTreeHtml=function(curStructData, htmlStr, curValueData){

                htmlStr=htmlStr || '';

                for(let key in curStructData){

                    if(curStructData[key][SUB_KEY]){
                        htmlStr+='<li key="'+key+'" class="has-child"><b>'+curStructData[key].label+'</b><ul>';
                        htmlStr=getTreeHtml(curStructData[key][SUB_KEY], htmlStr, curValueData[key])
                        htmlStr+='</ul></li>';
                    }else{

                        let curV=curValueData[key];
                        if(FLAG_TRANSFER_BOOLEAN){
                            curV=curV==true || curV==1 || curV=='true' ? 1: 0;
                        }

                        htmlStr+='<li key="'+key+'" value="'+curV+'">'+curStructData[key].label+'</li>';
                    }


                }

                return htmlStr;


            };


            loadStructJson(function (data) {
                //console.log(SELF.STRUCT_JSON);

                if(!FLAG_VIEW_ONLY){

                    //set select:
                    /*$(SELF).delegate('li', 'click', null, function (event) {
                        $(this).siblings('li').removeClass('active');
                        $(this).addClass('active');
                        saveData();
                    });*/
                    let treeHtml=getTreeHtml(SELF.STRUCT_JSON,'',DATA)

                    let toolbarHtml='<div class="toolbar btn-group"><a  class="btn btn-default btn-select-all">选择全部</a> <a class="btn btn-default btn-unselect-all">取消全部</a><span>点击选择下面权限模块... </span> </div>'




                    HTML_UI=toolbarHtml+'<div class="json-value-setter-container"><ul class="value-list">' +
                        treeHtml+
                        '</ul></div>';

                }else{
                    HTML_UI='';

                }

                $(HTML_UI).appendTo(SELF);

                saveData();



            });

            //click item to select/unselect
            $(SELF).delegate('li','click', null,function (e) {

                let curValue=$(this).attr('value') && $(this).attr('value')==1? 1 : 0;

                curValue=curValue==1? 0 :1;

                if($(this).find("ul").length>0){

                }else{
                    $(this).attr('value',curValue);
                    $(this).children('i').remove();
                    if(curValue==1){

                        $(this).append(' <i class="glyphicon glyphicon-ok"></i>')
                    }
                    saveData();
                }
            });

            //select all:
            $(SELF).delegate('.btn-select-all','click',null,function (e) {

                $(SELF).find('.json-value-setter-container li').each(function(i){

                   if(!$(this).hasClass('has-child')){
                       $(this).attr('value',1);
                       $(this).children('i').remove();
                       $(this).append(' <i class="glyphicon glyphicon-ok"></i>')
                   }
                });
                saveData();
            });
            //unselect all:
            $(SELF).delegate('.btn-unselect-all','click',null,function (e) {

                $(SELF).find('.json-value-setter-container li').each(function(i){

                    if(!$(this).hasClass('has-child')){
                        $(this).attr('value',0);
                        $(this).children('i').remove();

                    }
                });
                saveData();
            });



















        };//-/build












        //读取预设值
        readData();

        //产生列表
        build();




    }
})(jQuery)//-----------------------------------------


//设置所有class名为address-picker的项目。
$(document).ready(function () {

    var class_name = "json-value-setter";
    var ctrlist = $('.' + class_name);

    for (i = 0; i < ctrlist.length; i++) {

        $("." + class_name + ":eq(" + i + ")").jsonValueSetter();


    }

});


