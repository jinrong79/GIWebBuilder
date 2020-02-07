/**
 *  addressPicker
 *  地址选择器，包含街道，最高4级。
 *
 *  UI上需要的设置参数：
 *                 data-saver:  textarea which carry the json string of data
 *
 *
 *
 *
 *                 class=“express-fee-setter”
 *
 *
 */
j79.loadCSS("/css/express.fee.setter.css");

(function($){

    $.fn.expressFeeSetter= function(){

        var SELF=this;

        var CITY_DEFAULT_FIRST_UNIT=3;
        var CITY_DEFAULT_FIRST_FEE=5;
        var CITY_DEFAULT_OVER_UNIT=1;
        var CITY_DEFAULT_OVER_FEE=1;

        var STATE_DEFAULT_FIRST_UNIT=3;
        var STATE_DEFAULT_FIRST_FEE=8;
        var STATE_DEFAULT_OVER_UNIT=1;
        var STATE_DEFAULT_OVER_FEE=1.5;

        var OTHERS_DEFAULT_FIRST_UNIT=3;
        var OTHERS_DEFAULT_FIRST_FEE=10;
        var OTHERS_DEFAULT_OVER_UNIT=1;
        var OTHERS_DEFAULT_OVER_FEE=2;

        var CAL_TYPE=0; //计算方式。0- 按重量； 1- 按件

        var CAL_TYPE_SAVER=this.attr('data-type-saver');




        var DATA_SAVER = this.attr('data-saver');

        if(DATA_SAVER!=''){
            //$('#'+DATA_SAVER).hide();
        }

        if(CAL_TYPE_SAVER!=''){
            //$('#'+CAL_TYPE_SAVER).hide();
        }

        var STR_PLACEHOLDER=$(SELF).attr('placeholder') || '--请点击选择地址--';

        /*var DIV_DATA=[
            ['1','东北',['22','23','21']],
            ['2','华北',['11','12','13','14','37','15']],
            ['3','华东',['31','32','33','34','36']],
            ['4','华中',['43','42','41']],
            ['5','华南',['44','45','35','46']],
            ['6','西南',['50','53','52','51','54']],
            ['7','西北',['61','62','65','64','63' ]],
            ['8','港澳台',['81','82','71' ]]


        ];*/

        var saveData=function(){

            var resultData={
                "currentCity":{},
                "currentState":{},
                "others":{},
                "calType":0,
            };

            var v;

            var _setData=function(selector){
                var result={};
                var data=[3,0,1,0];
                console.log(selector);
                console.log($(SELF).find(selector).length);

                if($(SELF).find(selector).length>0){
                    var selectorList=[
                        selector+' #first_unit',
                        selector+' #first_fee',
                        selector+' #over_unit',
                        selector+' #over_fee'
                    ];

                    for(var i=0; i<selectorList.length;i++){
                        v=$(SELF).find(selectorList[i]).val();
                        console.log(v);
                        if( !isNaN(v)){
                            data[i]=v;
                        }
                    }
                }

                result.title=$(SELF).find(selector+' .title').text();
                result.values=data;
                if($(SELF).find(selector).hasClass('disabled')){
                    result.freeShip=1;
                }else{
                    delete result.freeShip;
                }

                return result;

            };


            resultData.currentCity=_setData('.current-city');
            resultData.currentState=_setData('.current-state');
            resultData.others=_setData('.others');
            resultData.calType= $(SELF).find(".cal-type input[name='calType']:checked").val();


            if(DATA_SAVER!=''){
                $('#'+DATA_SAVER).val( j79.toJSONString(resultData));

            }




        };



    var build=function(){

       $('<div class="col-md-12 cal-type">' +
           '<span>运费计算方式：</span>'+
           '<label><input type="radio" name="calType" checked="checked" value="0"/> 按重计费</label>| '+
           '<label><input type="radio" name="calType" value="1"/> 按件计费</label>'+
           '</div>').appendTo(SELF);

        var htmlInputSetValue='<div class="row exp-input-set [#class#]">' +

            '<div class="col-md-12 title">[#title#]</div>'+

            '<div class="col-md-12 sub-title"> 自动计算：</div>'+
                '<div class="row">'+
            '<div class="col-md-2 col-xs-4 ar">首重</div>'+
            '<div class="col-md-2 col-xs-4"><input type="number" id="first_unit" name="first_unit" value="[#value1#]" /></div>'+
            '<div class="col-md-2 col-xs-4">公斤以内，</div>'+
            '<div class="col-md-1 col-xs-4 ar">运费</div>'+
            '<div class="col-md-2 col-xs-4"><input type="number" id="first_fee" name="first_fee" value="[#value2#]" /></div>'+
            '<div class="col-md-2 col-xs-4">元；</div>'+
                '</div>'+
            '<div class="row">'+
            '<div class="col-md-2 col-xs-4 ar">超重时， 每</div>'+
            '<div class="col-md-2 col-xs-4"><input type="number" id="over_unit" name="over_unit" value="[#value3#]" /></div>'+
            '<div class="col-md-2 col-xs-4">公斤，</div>'+
            '<div class="col-md-1 col-xs-4 ar">增加</div>'+
            '<div class="col-md-2 col-xs-4"><input type="number" id="over_fee" name="over_fee" value="[#value4#]" /></div>'+
            '<div class="col-md-2 col-xs-4">元</div>'+
            '</div>'+
            '<div class="col-md-12 free-ship-check"><label><input class="chkbox-free-ship" type="checkbox" id="free_ship" name="free_ship" /> 包邮</label></div>'+

            '</div>';


        var settingData;

        //current city:
        settingData={
            "title":'市内',
            "class":'current-city',
            "value1":CITY_DEFAULT_FIRST_UNIT,
            "value2":CITY_DEFAULT_FIRST_FEE,
            "value3":CITY_DEFAULT_OVER_UNIT,
            "value4":CITY_DEFAULT_OVER_FEE

        };

        var outH=j79.setHtml(settingData,htmlInputSetValue);
        console.log(outH);
        $(outH).appendTo(SELF);

        //current state:
        settingData={
            "title":'延边州内',
            "class":'current-state',
            "value1":STATE_DEFAULT_FIRST_UNIT,
            "value2":STATE_DEFAULT_FIRST_FEE,
            "value3":STATE_DEFAULT_OVER_UNIT,
            "value4":STATE_DEFAULT_OVER_FEE

        };
        $( j79.setHtml(settingData,htmlInputSetValue) ).appendTo(SELF);

        //OTHERS:
        settingData={
            "title":'全国其他城市',
            "class":'others',
            "value1":OTHERS_DEFAULT_FIRST_UNIT,
            "value2":OTHERS_DEFAULT_FIRST_FEE,
            "value3":OTHERS_DEFAULT_OVER_UNIT,
            "value4":OTHERS_DEFAULT_OVER_FEE

        };
        $( j79.setHtml(settingData,htmlInputSetValue) ).appendTo(SELF);


        //attach event of input change
        $(SELF).delegate('input[type="number"]','change',null, function(e){
            saveData();
        });

        //attach event of radio
        $(SELF).delegate(' input[type="radio"]','change',null, function(e){
            saveData();
        });

        //attach event of checkbox free-ship
        $(SELF).delegate('.chkbox-free-ship','change',null, function(e){

            var curSet=$(this).closest('.exp-input-set');

            if($(this).get(0).checked){
                $(curSet).addClass('disabled');
                $(curSet).find('input[type="number"]').attr('disabled','disabled');

            }else{
                $(curSet).removeClass('disabled');
                $(curSet).find('input[type="number"]').removeAttr('disabled');
            }


            saveData();
        });





    };

    var setCalType=function(calType){
        //$(SELF).find('.cal-type input').removeAttr('checked');



        if(calType && parseInt(calType)==1){
            CAL_TYPE=1;

            $(SELF).find(".cal-type input[name='calType'][value='1']").prop('checked','true');
        }else{
            CAL_TYPE=0;
            $(SELF).find(".cal-type input[name='calType'][value='0']").prop('checked','true');

        }
    };

    var readData=function(){



        if(DATA_SAVER!=''){

            var curData=$('#'+DATA_SAVER).val();

            if(curData!=''){
                var jData=j79.toJSON(curData);
                var curData, curValues;
                if(jData.currentCity){//本市内
                    curData=jData.currentCity;
                    if(curData.title){
                        $(SELF).find('.current-city .title').text(curData.title);
                    }
                    curValues=curData.values || null;
                    $(SELF).find('.current-city #first_unit').val(curValues && curValues.length>=1? curValues[0] : CITY_DEFAULT_FIRST_UNIT  );
                    $(SELF).find('.current-city #first_fee').val(curValues && curValues.length>=2? curValues[1] : CITY_DEFAULT_FIRST_FEE  );
                    $(SELF).find('.current-city #over_unit').val(curValues && curValues.length>=3? curValues[2] : CITY_DEFAULT_OVER_UNIT  );
                    $(SELF).find('.current-city #over_fee').val(curValues && curValues.length>=4? curValues[3] : CITY_DEFAULT_OVER_FEE  );
                }

                if(jData.currentState){//本州内
                    curData=jData.currentState;
                    if(curData.title){
                        $(SELF).find('.current-city .title').text(curData.title);
                    }
                    curValues=curData.values || null;
                    $(SELF).find('.current-state #first_unit').val(curValues && curValues.length>=1? curValues[0] : STATE_DEFAULT_FIRST_UNIT  );
                    $(SELF).find('.current-state #first_fee').val(curValues && curValues.length>=2? curValues[1] : STATE_DEFAULT_FIRST_FEE  );
                    $(SELF).find('.current-state #over_unit').val(curValues && curValues.length>=3? curValues[2] : STATE_DEFAULT_OVER_UNIT  );
                    $(SELF).find('.current-state #over_fee').val(curValues && curValues.length>=4? curValues[3] : STATE_DEFAULT_OVER_FEE  );
                }

                if(jData.others){//其他地区的设置：
                    curData=jData.others;
                    if(curData.title){
                        $(SELF).find('.current-city .title').text(curData.title);
                    }
                    curValues=curData.values || null;
                    $(SELF).find('.others #first_unit').val(curValues && curValues.length>=1? curValues[0] : OTHERS_DEFAULT_FIRST_UNIT  );
                    $(SELF).find('.others #first_fee').val(curValues && curValues.length>=2? curValues[1] : OTHERS_DEFAULT_FIRST_FEE  );
                    $(SELF).find('.others #over_unit').val(curValues && curValues.length>=3? curValues[2] : OTHERS_DEFAULT_OVER_UNIT  );
                    $(SELF).find('.others #over_fee').val(curValues && curValues.length>=4? curValues[3] : OTHERS_DEFAULT_OVER_FEE  );

                }

                //if(jData.calType){//运费计算方式

                    setCalType(jData.calType);
                //}

            }else{
                saveData();
            }

        }

        //运费计算方式： 单独输入组件
        if(CAL_TYPE_SAVER!=''){
            var curCalType=$('#'+CAL_TYPE_SAVER).val();
            setCalType(curCalType);
        }else{
            setCalType(0);
        }

    };



        //产生列表
        build();

        //读取预设值
        readData();








    }
})(jQuery)//-----------------------------------------


//设置所有class名为address-picker的项目。
$(document).ready(function(){

    var class_name="express-fee-setter";
    var ctrlist = $('.'+class_name);

    for(i=0;i<ctrlist.length;i++)
    {

        $("."+class_name+":eq("+i+")").expressFeeSetter();


    }

});


