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
j79.loadCSS("/css/schedulepicker.css");

(function ($) {

    $.fn.schedulePicker = function () {

        var SELF = this;


        var STR_PLACEHOLDER = $(SELF).attr('placeholder') || '--请点击选择地址--';


        var DATA_SAVER = $(SELF).attr('data-saver') || '';

        if(DATA_SAVER!=''){
            $('#'+DATA_SAVER).hide();
        }


        /**
         *  saveData
         *  save selected data to data-savers.
         *
         */
        var saveData = function () {

            var result = new Array();

            $(SELF).find('.schedule-items li').each(function (i) {

                if(!$(this).attr('data-day')){
                    return ;
                }

                var curObj = {
                    "day": $(this).attr('data-day'),
                    "start": $(this).attr('data-start-time'),
                    "end": $(this).attr('data-end-time')

                };
                result.push(curObj);

            });

            if(DATA_SAVER!=''){
                $('#'+DATA_SAVER).val( j79.toJSONString(result) );
            }


        };//-/saveData


        /**
         *  build
         *  产生列表
         *
         */
        var build = function () {
            //绘制基本UI
            var ui = $(
                '<div class="title">'+STR_PLACEHOLDER+'</div>'+
                '<ul class="schedule-items" id="tab"><p>无</p></ul>' +
                '<div class="edit-panel">' +
                '<span class="day"><select id="schedule_day" name="schedule_day">' +

                '<option value="0" >全周</option>' +
                '<option value="9" >工作日(周一到周五)</option>' +
                '<option value="1" >周一</option>' +
                '<option value="2" >周二</option>' +
                '<option value="3" >周三</option>' +
                '<option value="4" >周四</option>' +
                '<option value="5" >周五</option>' +
                '<option value="6" >周六</option>' +
                '<option value="7" >周日</option>' +
                '</select></span>' +
                '<span class="time-start">开始时间<input type="number" id="schedule_start_hour" value="8" />:<input type="number" id="schedule_start_min" value="00" /></span>' +
                '<span class="time-end">结束时间<input type="number" id="schedule_end_hour" value="18" />:<input type="number" id="schedule_end_min" value="00" /></span>' +
                '<a class="btn btn-info btn-add" >添加</a></div>'
            );
            ui.appendTo(SELF);

            //attach click for add new item
            $(SELF).find('.btn-add').click(function (e) {

                var curDay = $(SELF).find('#schedule_day').val();
                if (curDay == '') {
                    alert('工作星期没有选择！');
                    return;
                }
                var curDayLabel = $(SELF).find('#schedule_day').find("option:selected").text();
                var curStartTime = $(SELF).find('#schedule_start_hour').val() + ':' + $(SELF).find('#schedule_start_min').val();
                var curEndTime = $(SELF).find('#schedule_end_hour').val() + ':' + $(SELF).find('#schedule_end_min').val();
                $(SELF).find('.schedule-items p').remove();
                $('<li data-day="' + curDay + '" data-start-time="' + curStartTime + '" data-end-time="' + curEndTime + '">' + curDayLabel + ' ' + curStartTime + ' ~ ' + curEndTime + '<a class="btn-del">删除</a></li>').appendTo($(SELF).find('.schedule-items'));

                saveData();
            });

        };//-/build

        var getDayLabel=function(dayId){
            dayId=parseInt(dayId);

            var labelArr=[
                '全周',
                '周一',
                '周二',
                '周三',
                '周四',
                '周五',
                '周六',
                '周七',
                '',
                '工作日(周一到周五)'
            ];
            return labelArr[dayId] ? labelArr[dayId] :'';

        };

        /**
         * readData
         * read data from data-savers.
         */
        var readData = function () {

            if(DATA_SAVER!='' && $('#'+DATA_SAVER).val() && $('#'+DATA_SAVER).val()!=''){


                var data=j79.toJSON($('#'+DATA_SAVER).val());



                $(SELF).find('.schedule-items *').remove();
                for(var i=0; i< data.length; i++){

                    var curDay = data[i].day;
                    var curDayLabel = getDayLabel(curDay);
                    var curStartTime = data[i].start;
                    var curEndTime =  data[i].end;
                    $('<li data-day="' + curDay + '" data-start-time="' + curStartTime + '" data-end-time="' + curEndTime + '">' + curDayLabel + ' ' + curStartTime + ' ~ ' + curEndTime + '<a class="btn-del">删除</a></li>').appendTo($(SELF).find('.schedule-items'));


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

    var class_name = "schedule-picker";
    var ctrlist = $('.' + class_name);

    for (i = 0; i < ctrlist.length; i++) {

        $("." + class_name + ":eq(" + i + ")").schedulePicker();


    }

});


