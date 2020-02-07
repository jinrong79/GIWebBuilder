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
j79.loadCSS("/css/addpicker.css");

(function($){

    $.fn.timeInputer= function(){

        var SELF=this;

        var DATA_SAVER=$(SELF).attr('data-saver') || '';
        if(DATA_SAVER!=''){
            $('#'+DATA_SAVER).hide();
        }




        /**
         *  saveData
         *  save selected data to data-savers.
         *
         */
        var saveData=function(){

            var timeH= $(SELF).find('.time_h').val();
            var timeM=$(SELF).find('.time_m').val();
            if( parseInt(timeH)>=0 && parseInt(timeM)>=0 && DATA_SAVER!=''){
                $('#'+DATA_SAVER).val(parseInt(timeH)+':'+parseInt(timeM));
            }

        };//-/saveData





        /**
         *  build
         *  绘制基本UI
         *
         */
        var build=function(container){


            var ui=$(
                '<div class="time-inputer-area">'+
                '<input class="time_h" type="number" value="0" max="23" /><em>:</em><input class="time_m" type="number" value="0" max="59" />'+
                '</div>'

            );
            ui.appendTo(SELF);

            //attach change handler:
            $(SELF).find('.time_h').change(function(e){
                if($(this).val()>23){
                    $(this).val(23);
                }
                if($(this).val()<0){
                    $(this).val(0);
                }
                if($(this).val()<=23 && $(this).val()>=0){
                    saveData();
                }
            });
            $(SELF).find('.time_m').change(function(e){
                if($(this).val()>59){
                    $(this).val(59);
                }
                if($(this).val()<0){
                    $(this).val(0);
                }
                if($(this).val()<=59 && $(this).val()>=0){

                    saveData();
                }
            });




        };//-/build



        /**
         * readData
         * read data from data-savers.
         */
        var readData= function(){
            if(DATA_SAVER!=''){
               var originData=$('#'+DATA_SAVER).val();

                var dataParts=originData.split(':');
                if(dataParts.length==2){
                    $(SELF).find('.time_h').val(parseInt(dataParts[0]));
                    $(SELF).find('.time_m').val(parseInt(dataParts[1]));
                }

            }



        };//-/






        //产生列表
        build(SELF);

        //读取预设值
        readData();








    }
})(jQuery)//-----------------------------------------



