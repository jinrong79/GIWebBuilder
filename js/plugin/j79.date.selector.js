/**
 choiceBox
 *
 *
 */
j79.loadCSS("/css/j79.date.selector.css");

(function ($) {

    $.fn.dateSelector = function () {


        var SELF = this;

        var SELF_ID = $(SELF).attr('id');


        var STR_PLACEHOLDER = $(SELF).attr('placeholder') || '请选择';


        var DATA_LIST=[];


        var FLAG_VIEW_ONLY=this.hasClass('view-static') ? true : false;



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

            var result = null;


            //generate result:
            var curM=$(SELF).find('#'+SELF_ID+'_value_month').val();
            curM=curM<10? '0'+curM :''+curM;
            var curDay=$(SELF).find('#'+SELF_ID+'_value_day').val();
            curDay=curDay<10? '0'+curDay : ''+curDay;
            result=$(SELF).find('#'+SELF_ID+'_value_century_year').val()+''+
                $(SELF).find('#'+SELF_ID+'_value_year').val()+'-'+
                curM+'-'+curDay; // +'T00:00:00.000Z';







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




        var calDayMax=function(year,month){
            var dayMax= month<8 ? (30+ month%2) : (31- month%2);
            if(month==2){
                dayMax=year%4==0 ? 28:29;
            }
            return dayMax;
        };//-/calDayMax



        /**
         *  build
         *  产生列表
         *
         */
        var build = function () {

            /*//read option list:
            DATA_LIST=[];
            $(SELF).find('li').each(function(idx){

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

*/

            var curDate=new Date;
            var year=curDate.getFullYear()-23;
            var cent=Math.floor(year/100);
            var yearTen=year % 100 ;
            var month=curDate.getMonth()+1;
            var day=curDate.getDate();



            var htmlCentury='';
            var selected;
            for(var i=0;i<=29;i++){
                selected='';
                if(i==cent){
                    selected='selected';
                }
                htmlCentury+='<option value="'+i+'" '+selected+'>'+(i)+'</option>';
            }


            var htmlYear='';
            var curY='';
            for(i=0;i<=99;i++){
                curY=i<10? '0'+i:''+i;
                selected='';
                if(i==yearTen){
                    selected='selected';
                }

                htmlYear+='<option value="'+curY+'" '+selected+'>'+curY+'</option>';
            }




            var htmlMonth='';

            for(i=0;i<12;i++){

                selected='';
                if(i==month-1){
                    selected='selected';
                }

                htmlMonth+='<option value="'+(i+1)+'" '+selected+'>'+(i+1)+'</option>';
            }

            var htmlDay='';
            var dayMax=calDayMax(year,month);



            for(i=0;i<dayMax;i++){

                selected='';
                if(i==day-1){
                    selected='selected';
                }

                htmlDay+='<option value="'+(i+1)+'" '+selected+'>'+(i+1)+'</option>';
            }






            if(FLAG_VIEW_ONLY==false){

                //set select:
                /*$(SELF).delegate('li', 'click', null, function (event) {
                    $(this).siblings('li').removeClass('active');
                    $(this).addClass('active');
                    saveData();
                });*/

                HTML_UI='<div class="date-selector">' +
                    '<span class="item"><select class="form-control"  name="'+SELF_ID+'_value_century_year" id="'+SELF_ID+'_value_century_year">'+htmlCentury+'</select></span>'+
                    '<span class="item"><select class="form-control"  name="'+SELF_ID+'_value_year" id="'+SELF_ID+'_value_year">'+htmlYear+'</select><b>年</b></span>'+
                    '<span class="item"><select class="form-control"  name="'+SELF_ID+'_value_month" id="'+SELF_ID+'_value_month">'+htmlMonth+'</select><b>月</b></span>'+
                    '<span class="item"><select class="form-control"  name="'+SELF_ID+'_value_day" id="'+SELF_ID+'_value_day">'+htmlDay+'</select><b>日</b></span>'+
                    '</div>';

            }else{
                HTML_UI='';

            }

            $( HTML_UI).appendTo(SELF);



            var resetMaxDay=function(year,month){
                var dayMax=calDayMax(year,month);
                var curDayMax=$(SELF).find('#'+SELF_ID+'_value_day').find("option").length;

                if(curDayMax>dayMax){
                    for(var delI=0;delI<curDayMax-dayMax;delI++){
                        $(SELF).find('#'+SELF_ID+'_value_day option[value="'+(curDayMax-delI)+'"]').remove();
                    }
                }
                if(curDayMax<dayMax){
                    for(var delI=0;delI<dayMax-curDayMax;delI++){
                        $(SELF).find('#'+SELF_ID+'_value_day').append("<option value='"+(curDayMax+delI+1)+"'>"+(curDayMax+delI+1)+"</option>");
                    }
                }
            }



            $(SELF).delegate('#'+SELF_ID+'_value_century_year', 'change', null, function (event) {

                var curM=$(SELF).find('#'+SELF_ID+'_value_month').val();
                if(curM==2){
                    var curYear=$(SELF).find('#'+SELF_ID+'_value_century_year').val()+''+$(SELF).find('#'+SELF_ID+'_value_year').val();
                    resetMaxDay(curYear,curM);
                }

                saveData();
            });
            $(SELF).delegate('#'+SELF_ID+'_value_year', 'change', null, function (event) {

                var curM=$(SELF).find('#'+SELF_ID+'_value_month').val();
                if(curM==2){
                    var curYear=$(SELF).find('#'+SELF_ID+'_value_century_year').val()+''+$(SELF).find('#'+SELF_ID+'_value_year').val();
                    resetMaxDay(curYear,curM);
                }


                saveData();
            });
            $(SELF).delegate('#'+SELF_ID+'_value_month', 'change', null, function (event) {

                var curYear=$(SELF).find('#'+SELF_ID+'_value_century_year').val()+''+$(SELF).find('#'+SELF_ID+'_value_year').val();
                var curM=$(SELF).find('#'+SELF_ID+'_value_month').val();
                resetMaxDay(curYear,curM);

                saveData();
            });
            $(SELF).delegate('#'+SELF_ID+'_value_day', 'change', null, function (event) {
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

                    var dataTime=new Date(data);
                    //console.log(dataTime);

                    var centYear= Math.floor( dataTime.getFullYear()/100);
                    var tenYear=dataTime.getFullYear() % 100;
                    tenYear=tenYear<10 ? '0'+tenYear :''+tenYear;

                    $("#"+SELF_ID+'_value_century_year').val(""+centYear);

                    $("#"+SELF_ID+'_value_year').val(tenYear);

                    $("#"+SELF_ID+'_value_month').val(dataTime.getMonth()+1);

                    $("#"+SELF_ID+'_value_day').val(dataTime.getDate());

                    /*data=parseInt(data);
                    console.log('data='+data);
                    $(SELF).find('li').each(function(idx){
                        $(this).removeClass('active');
                        var curValue=parseInt($(this).attr('value'));

                        if( curValue == data ){

                            $(this).addClass('active');
                        }

                    });*/


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

    var class_name = "date-selector";
    var ctrlist = $('.' + class_name);

    for (i = 0; i < ctrlist.length; i++) {

        $("." + class_name + ":eq(" + i + ")").dateSelector();


    }

});


