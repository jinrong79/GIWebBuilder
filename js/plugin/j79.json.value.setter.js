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


        var DATA_LIST=[];


        var FLAG_VIEW_ONLY=this.hasClass('view-static') ? true : false;


        var SUB_KEY=$(SELF).attr('sub-key-name') || 'sub';

        var URL_JSON_STRUCT=$(SELF).attr('json-struct-def') || '';

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

            var result = null;


            //generate result:
            var curM=$(SELF).find('#'+SELF_ID+'_value_month').val();
            curM=curM<10? '0'+curM :''+curM;
            var curDay=$(SELF).find('#'+SELF_ID+'_value_day').val();
            curDay=curDay<10? '0'+curDay : ''+curDay;
            result=$(SELF).find('#'+SELF_ID+'_value_century_year').val()+''+
                $(SELF).find('#'+SELF_ID+'_value_year').val()+'-'+
                curM+'-'+curDay; // +'T00:00:00.000Z';



            if(FLAG_TIME_INCLUDE){
                result+=' '+$(SELF).find('#'+SELF_ID+'_value_hh').val()+":"+$(SELF).find('#'+SELF_ID+'_value_mm').val()+":"+$(SELF).find('#'+SELF_ID+'_value_ss').val()
            }




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
                    $('#' + DATA_SAVER).val(result);
                }

            }


        };//-/saveData






        /**
         *  build
         *  产生列表
         *
         */
        var build = function () {



            var loadStructJson = function (handleFinished) {

                $.get(URL_JSON_STRUCT).success(function (result) {

                    SELF.STRUCT_JSON = result;
                    if (handleFinished && typeof handleFinished == 'function') {
                        handleFinished(result);
                    }
                }).error(function(result){
                    console.log('xml load error');


                });
            };//-/

            var getTreeHtml=function(curData, htmlStr){

                htmlStr=htmlStr || '';

                for(let key in curData){

                    if(curData[key][SUB_KEY]){
                        htmlStr+='<li key="'+key+'"><b>'+curData[key].label+'</b><ul>';
                        htmlStr=getTreeHtml(curData[key][SUB_KEY], htmlStr)
                        htmlStr+='</ul></li>';
                    }else{
                        htmlStr+='<li key="'+key+'">'+curData[key].label+'</li>';
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
                    let treeHtml=getTreeHtml(SELF.STRUCT_JSON,'')

                    let toolbarHtml='<div class="toolbar btn-group"><a href="" class="btn btn-default btn-selelct-all">选择全部</a> <a href="" class="btn btn-default btn-unselelct-all">取消全部</a> </div>'




                    HTML_UI='<div class="json-value-setter-container"><ul>' +
                        toolbarHtml+
                        treeHtml+
                        '</ul></div>';

                }else{
                    HTML_UI='';

                }

                $(HTML_UI).appendTo(SELF);

            });


            $(SELF).delegate('li','click', null,function (e) {

                let curValue=$(this).attr('value') && $(this).attr('value')==1? 1 : 0;

                console.log("click")

                curValue=curValue==1? 0 :1;


                if($(this).find("ul").length>0){

                }else{
                    $(this).attr('value',curValue);
                }

            })


















        };//-/build


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

                        for(let key in data){

                        }

                    }


                    //如果是单个对象，不是数组，那么转成含有此对象的数组：
                    if(!j79.isArray(data) ){
                        //console.log('not arra');
                        data=[data];
                    }

                    var imgItem;
                    for (var i = 0; i < data.length; i++) {
                        imgItem = data[i];

                        var adImg=imgItem.img || '';
                        var adUrl=imgItem.url || '';
                        var adTitle=imgItem.title || '';

                        if(!imgItem.title){
                            imgItem.title='';
                        }
                        if(!imgItem.subtitle){
                            imgItem.subtitle='';
                        }

                        if( (CTR_TYPE==0 &&  adImg &&  adUrl) || ( CTR_TYPE==1 && ( adImg || adTitle ) && adUrl   ) ){

                            $( j79.setHtml(imgItem, HTML_LI)).appendTo($(SELF).find(UI_LIST));

                        }

                    }

                }


            }


        };//-/




        //产生列表
        build();

        //读取预设值
        //readData();
        //saveData();


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


