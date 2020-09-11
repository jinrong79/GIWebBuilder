/**
 choiceBox
 *
 *
 */
j79.loadCSS("css/j79.img.area.selector.css");

(function ($) {

    $.fn.imgAreaSelector = function () {


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
           // $('#'+DATA_SAVER).hide();
        }



        var AREA_WIDTH_MIN = $(SELF).attr('area-width-min') || 40;
        var AREA_HEIGHT_MIN = $(SELF).attr('area-height-min') || 40;

        //when save data, area position data delta.
        var POS_DX=$(SELF).attr('position-delta-x') || 0;
        var POS_DY=$(SELF).attr('position-delta-y') || 0;

        var M_SX,M_SY //mouse_down start x,y relative to DOCUMENT.
        var ID_LIST=[];  //area div id list.

        var DRAG_DX, DRAG_DY; //area drag start deltaX,deltaY relative to mouse.

        var AREA_SW, AREA_SH; //area start width and height when resizing start


        //html codes-------------------------------
        //UI html
        var HTML_UI= '' ;


        var FLAG_AREA_DRAG_START=false;

        var FLAG_AREA_DRAW_START=false;

        var FLAG_AREA_RESIZE=null;


        var HANDLE_ON_ADD=$(SELF).attr('on-add') || '';
        var HANDLE_ON_DEL=$(SELF).attr('on-del') || '';
        var HANDLE_ON_CHANGE=$(SELF).attr('on-change') || '';
        var HANDLE_ON_SELECT=$(SELF).attr('on-select') || '';






        /**
         *  saveData
         *  save selected data to data-savers.
         *
         */
        var saveData = function () {

            var result = [];


            //generate result:
            var curItem;
            $(SELF).find('.area-selection').each(function(idx,ele){
                curItem={};
                curItem.id=$(this).attr('id');
                curItem.left=Math.round($(this).position().left - POS_DX);
                curItem.top=Math.round($(this).position().top - POS_DY);
                curItem.width=$(this).width();
                curItem.height=$(this).height();
                result.push(curItem);

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
                    $('#' + DATA_SAVER).val(j79.toJSONString(result));
                }

            }


        };//-/saveData








        /**
         *  build
         *  产生列表
         *
         */
        var build = function () {



            //绘制基本UI

            if(FLAG_VIEW_ONLY==false){



                HTML_UI='';

            }else{
                HTML_UI='';

            }

            $( HTML_UI).appendTo(SELF);


            var genID=function(){
                var t=new Date();
                return  t.getTime()+"_"+Math.round(Math.random()*1000000);
            };

            //mouse handler for draw area:
            $(SELF).mousedown(function(e){
                M_SX=e.pageX;
                M_SY=e.pageY;

                var rx=Math.round(M_SX-$(SELF).offset().left);
                var ry=Math.round(M_SY-$(SELF).offset().top);


                $(SELF).find('.area-selection-temp').remove();

                $('<div class="area-selection-temp" style="background:rgba(0,0,0,0.05);position:absolute;left:'+rx+'px;top:'+ry+'px;width:1px;height:1px"></div>').appendTo(SELF);
                FLAG_AREA_DRAW_START=true;

            });

            $(SELF).mousemove(function (e) {

                if(FLAG_AREA_DRAW_START){
                    var w=e.pageX-M_SX;
                    var h=e.pageY-M_SY;

                    //start mouse x,y
                    var sx=w<0 ? e.pageX :M_SX;
                    var sy=h<0 ? e.pageY :M_SY;

                    //start mouse x,y relative to SELF left-top
                    /*var rx=sx-Math.round($(SELF).offset().left);
                    var ry=sy-Math.round($(SELF).offset().top);*/

                    $(SELF).find('.area-selection-temp').offset({left:sx,top:sy});
                    $(SELF).find('.area-selection-temp').width(Math.abs(w)).height(Math.abs(h));
                }



            })

            var setupArea=function(e, flagTmp){
                //width, height
                var w=e.pageX-M_SX;
                var h=e.pageY-M_SY;

                //start mouse x,y
                var sx=w<0 ? e.pageX :M_SX;
                var sy=h<0 ? e.pageY :M_SY;

                //start mouse x,y relative to SELF left-top
                var rx=sx-Math.round($(SELF).offset().left);
                var ry=sy-Math.round($(SELF).offset().top);

                w=Math.abs(w);
                h=Math.abs(h);

                if(w<AREA_WIDTH_MIN && h<AREA_HEIGHT_MIN){
                    $(SELF).find('.area-selection-temp').remove();
                    FLAG_AREA_DRAW_START=false;
                    return;
                }

                w=w>AREA_WIDTH_MIN ? w : AREA_WIDTH_MIN;
                h=h>AREA_HEIGHT_MIN ? h : AREA_HEIGHT_MIN;

                var newID='imgArea_'+genID();
                ID_LIST.push(newID);

                //data-width="'+w+'" data-height="'+h+'" data-left="'+(rx-POS_DX)+'" data-top="'+(ry-POS_DY)+'"
                $('<div class="area-selection" id="'+newID+'"  style="background:rgba(0,0,0,0.3);position:absolute;left:'+rx+'px;top:'+ry+'px;width:'+w+'px;height:'+h+'px"><a class="del" style="user-select: none">X</a></div>').appendTo(SELF);
                $(SELF).find('.area-selection-temp').remove();
                FLAG_AREA_DRAW_START=false;

                //trigger event
                if(HANDLE_ON_ADD){
                    var runStr=HANDLE_ON_ADD.replace('id','"'+newID+'"');
                    //console.log(runStr)
                    eval(runStr);
                }

                saveData();

            }


            $(SELF).mouseup(function(e){
                if(FLAG_AREA_DRAW_START){
                    setupArea(e);
                }
                FLAG_AREA_DRAG_START=false;
                FLAG_AREA_RESIZE=null;
            });


            $(SELF).mouseleave(function(e){

                if(FLAG_AREA_DRAW_START){
                    setupArea(e);
                }
                FLAG_AREA_DRAG_START=false;
                FLAG_AREA_RESIZE=null;

            });

            //mouse handler for drag area and resize area:
            $(SELF).delegate('.area-selection','mousedown',null,function(e){

                FLAG_AREA_DRAG_START=true;
                DRAG_DX= e.pageX -$(this).offset().left;
                DRAG_DY= e.pageY -$(this).offset().top;

                M_SX=e.pageX;
                M_SY=e.pageY;

                $(this).appendTo(SELF);

                if(DRAG_DX> $(this).width()*0.8){
                    FLAG_AREA_RESIZE=FLAG_AREA_RESIZE || {};
                    FLAG_AREA_RESIZE.horizontal=true;
                    FLAG_AREA_DRAG_START=false;
                    AREA_SW=$(this).width();
                }

                if(DRAG_DY> $(this).height()*0.8){
                    FLAG_AREA_RESIZE=FLAG_AREA_RESIZE || {};
                    FLAG_AREA_RESIZE.vertical=true;
                    FLAG_AREA_DRAG_START=false;
                    AREA_SH=$(this).height();
                }


                if(HANDLE_ON_SELECT){
                    console.log('select')
                    var curId=$(this).attr('id');
                    var runStr=HANDLE_ON_SELECT.replace('id','"'+curId+'"');
                    //console.log(runStr)
                    eval(runStr);
                }


                e.stopPropagation();
                return false;
            });

            $(SELF).delegate('.area-selection','mousemove',null,function(e){

                if(FLAG_AREA_DRAG_START && !FLAG_AREA_RESIZE){
                    $(this).offset({
                        left: Math.round( e.pageX-DRAG_DX -$(this).offset().left) + $(this).offset().left,
                        top: Math.round(e.pageY-DRAG_DY -$(this).offset().top) + $(this).offset().top
                    });
                    saveData();
                    e.stopPropagation();
                    return false;
                }else if(FLAG_AREA_RESIZE){
                    if(FLAG_AREA_RESIZE.horizontal){
                        var DX=e.pageX - M_SX;
                        $(this).width(AREA_SW +DX);
                        if($(this).width()<AREA_WIDTH_MIN){
                            $(this).width(AREA_WIDTH_MIN);
                        }
                        saveData();


                    }
                    if(FLAG_AREA_RESIZE.vertical){
                        var DY=e.pageY - M_SY;
                        $(this).height(AREA_SH +DY);
                        if($(this).height()<AREA_HEIGHT_MIN){
                            $(this).height(AREA_HEIGHT_MIN);
                        }
                        saveData();

                    }
                }

            });

            $(SELF).delegate('.area-selection','mouseup',null,function(e){
                if(FLAG_AREA_DRAG_START) {
                    FLAG_AREA_DRAG_START = false;

                    e.stopPropagation();
                    return false;
                }
                FLAG_AREA_RESIZE=null;
            });

            $(SELF).delegate('.area-selection','mouseleave',null,function(e){
                if(FLAG_AREA_DRAG_START) {
                    FLAG_AREA_DRAG_START=false;
                    e.stopPropagation();
                    return false;
                }


            });


            $(SELF).delegate('.del','mousedown',null,function(e){

                if(e.button==0){
                    var curID=$(this).closest('.area-selection').attr('id');
                    $(this).closest('.area-selection').remove();

                    //trigger event
                    if(HANDLE_ON_DEL){
                        var runStr=HANDLE_ON_DEL.replace('id','"'+curID+'"');
                        console.log(runStr)
                        eval(runStr);
                    }

                    saveData();

                    e.stopPropagation();
                    return false;
                }



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




                }


            }


        };//-/




        //产生列表
        build();

        //读取预设值
        readData();
        saveData();


    }
})(jQuery)//-----------------------------------------


//设置所有class名为address-picker的项目。
$(document).ready(function () {

    var class_name = "img-area-selector";
    var ctrlist = $('.' + class_name);

    for (i = 0; i < ctrlist.length; i++) {

        $("." + class_name + ":eq(" + i + ")").imgAreaSelector();


    }

});


