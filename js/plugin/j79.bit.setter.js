/**
 bitSetter
 *
 *
 */
j79.loadCSS("/css/j79.bit.setter.css");

(function ($) {

    $.fn.bitSetter = function () {


        var SELF = this;

        var SELF_ID = $(SELF).attr('id');


        var STR_PLACEHOLDER = $(SELF).attr('placeholder') || '请选择';


        var DATA_LIST=[];



        var DATA_SAVER = $(SELF).attr('data-saver') || '';

        /*
         DATA_SAVE_PATH: when it not empty, save generated data to a attribute named like DATA_SAVE_PATH of the object(DATA_SAVER data indicated)
         */
        var DATA_SAVE_PATH = $(SELF).attr('data-save-path') || '';

        if (DATA_SAVER != '') {
            $('#'+DATA_SAVER).hide();
        }

        //hide option list:
        $(SELF).find('option').hide();


        //html codes-------------------------------
        //UI html
        var HTML_UI= '' ;






        /**
         *  saveData
         *  save selected data to data-savers.
         *
         */
        var saveData = function () {

            var result = 0;


            //generate result:
            $(SELF).find('input[name='+SELF_ID+'_value_item]:checked').each(function(idx){
                var curValue=$(this).val();

                result=result | curValue;

            });




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

            //read option list:
            DATA_LIST=[];
            $(SELF).find('option').each(function(idx){

                DATA_LIST.push( {'value':$(this).attr('value'), 'label':$(this).text() } );

            });

            var itemData;
            for(var i=0; i<DATA_LIST.length;i++){
                itemData=DATA_LIST[i];
                HTML_UI+='<label class="checkbox-inline"><input type="checkbox" name="'+SELF_ID+'_value_item" value="'+itemData.value+'">'+itemData.label+'</label>';

            }

            //绘制基本UI
            var ui = $( HTML_UI);
            ui.appendTo(SELF);




            //btn-item-remove:
            $(SELF).delegate('input[type=checkbox]', 'click', null, function (event) {

                saveData();
            });





        };//-/build


        /**
         * readData
         * read data from data-savers.
         */
        var readData = function () {

            //read option list:


            if (DATA_SAVER != '' && $('#' + DATA_SAVER).val() && $('#' + DATA_SAVER).val() != '') {


                var data=$('#' + DATA_SAVER).attr('value-type')=='json' || DATA_SAVE_PATH!='' ? j79.toJSON($('#' + DATA_SAVER).val()): $('#' + DATA_SAVER).val();

                if (DATA_SAVE_PATH) {
                    data= data[DATA_SAVE_PATH] ? data[DATA_SAVE_PATH] : null;
                }

                if(data){

                    data=parseInt(data);
                    console.log('data='+data);
                    $(SELF).find('input[name='+SELF_ID+'_value_item]').each(function(idx){
                        var curValue=parseInt($(this).val());

                        if( curValue == (data & curValue) ){

                            $(this).attr('checked',true);
                        }

                    });


                }


            }


        };//-/




        //产生列表
        build();

        //读取预设值
        readData();


    }
})(jQuery)//-----------------------------------------


//设置所有class名为address-picker的项目。
$(document).ready(function () {

    var class_name = "bit-setter";
    var ctrlist = $('.' + class_name);

    for (i = 0; i < ctrlist.length; i++) {

        $("." + class_name + ":eq(" + i + ")").bitSetter();


    }

});


