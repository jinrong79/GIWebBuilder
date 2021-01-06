/**
 * imgAreaSelector
 * [plugin]
 * usage: 1. include this js in html.
 *        2. css file url must be tuned.
 *        3. add class "img-area-selector" to any div you want to run as imgAreaSelector.
 *           important: this div position must be "absolute".
 *                      image laid in back, can be placed before this imgAreaSelector div.
 *        4. add textArea element as data-saver, and give its id to imgAreaSelector as data-saver value.
 *
 */
j79.loadCSS("css/j79.img.area.selector.css");  // this css file url must be tuned.

(function ($) {

    $.fn.imgAreaSelector = function () {


        let SELF = this;

        let SELF_ID = $(SELF).attr('id');

        let FLAG_VIEW_ONLY=this.hasClass('view-static') ? true : false;


        let DATA_SAVER = $(SELF).attr('data-saver') || '';

        /*
         DATA_SAVE_PATH: when it not empty, save generated data to a attribute named like DATA_SAVE_PATH of the object(DATA_SAVER data indicated)
         */
        let DATA_SAVE_PATH = $(SELF).attr('data-save-path') || '';

        if (DATA_SAVER != '') {
           // $('#'+DATA_SAVER).hide();
        }


        // area min width/height
        let AREA_WIDTH_MIN = $(SELF).attr('area-width-min') || 60;
        let AREA_HEIGHT_MIN = $(SELF).attr('area-height-min') || 40;

        // when save data, area position data delta offset value.
        let POS_DX=Number($(SELF).attr('position-delta-x') || 0);
        let POS_DY=Number($(SELF).attr('position-delta-y') || 0);


        let M_SX,M_SY; // mouse_down start x,y relative to DOCUMENT.
        let DRAG_DX, DRAG_DY; // area drag start deltaX,deltaY relative to mouse.
        let AREA_SW, AREA_SH; // area start width and height when resizing start


        let AREA_ID_LIST = []; // area id list array


        //UI html
        let HTML_UI= '' ;


        let FLAG_AREA_DRAG_START=false;
        let FLAG_AREA_DRAW_START=false;
        let FLAG_AREA_RESIZE=null;


        let HANDLE_ON_ADD=$(SELF).attr('on-add') || '';
        let HANDLE_ON_DEL=$(SELF).attr('on-del') || '';
        // let HANDLE_ON_CHANGE=$(SELF).attr('on-change') || '';
        let HANDLE_ON_SELECT=$(SELF).attr('on-select') || '';






        /**
         *  saveData
         *  save data to data-savers.
         *
         */
        let saveData = function () {

            var result = [];


            //loop area-selection to generate result list:
            let curItem, curEle;

            for(let i=0; i<AREA_ID_LIST.length;i++){
                curItem={};
                curItem.id=AREA_ID_LIST[i];
                curEle = $(SELF).find('#'+AREA_ID_LIST[i])[0]
                curItem.left=Math.round($(curEle).position().left - POS_DX);
                curItem.top=Math.round($(curEle).position().top - POS_DY);
                curItem.width=$(curEle).width();
                curItem.height=$(curEle).height();
                result.push(curItem);
            }

            // if data_saver exists, then save result into it.
            if (DATA_SAVER != '') {

                // if data_save_path exists, then save result into data_saver.data_save_path
                if (DATA_SAVE_PATH) {
                    var dataObj = j79.toJSON($('#' + DATA_SAVER).val());
                    if (!dataObj) {
                        dataObj = {}
                    }
                    dataObj[DATA_SAVE_PATH] = result;
                    result = dataObj;

                    $('#' + DATA_SAVER).val(JSON.stringify(result));

                }else{
                    $('#' + DATA_SAVER).val(JSON.stringify(result));
                }

            }


        };//-/saveData


        /**
         * resetViewNo
         * reset view-No. on selection div.
         */
        let resetViewNo = function(){

            for(let i=0; i< AREA_ID_LIST.length; i++){
                $('#'+AREA_ID_LIST[i]).find('.no').text(i+1)
            }
        };

        /**
         *  build
         *  产生列表
         *
         */
        let build = function () {



            //绘制基本UI

            if(FLAG_VIEW_ONLY==false){

                HTML_UI='';

            }else{
                HTML_UI='';

            }

            $( HTML_UI).appendTo(SELF);


            let genID=function(){
                var t=new Date();
                return  t.getTime()+"_"+Math.round(Math.random()*1000000);
            };




            // mouse handler for draw area-------
            // mouse down:
            $(SELF).mousedown(function(e){
                M_SX=e.pageX;
                M_SY=e.pageY;

                var rx=Math.round(M_SX-$(SELF).offset().left);
                var ry=Math.round(M_SY-$(SELF).offset().top);


                $(SELF).find('.area-selection-temp').remove();

                $('<div class="area-selection-temp" style="background:rgba(0,0,0,0.05);position:absolute;left:'+rx+'px;top:'+ry+'px;width:1px;height:1px"></div>').appendTo(SELF);
                FLAG_AREA_DRAW_START=true;

            });

            // mouse move:
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

            // add new area
            let setupArea=function(e, flagTmp){
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

                // ID_LIST.push(newID);
                AREA_ID_LIST.push(newID)

                //data-width="'+w+'" data-height="'+h+'" data-left="'+(rx-POS_DX)+'" data-top="'+(ry-POS_DY)+'"
                let newSelection = $('<div class="area-selection" id="'+newID+'"  style="background:rgba(0,0,0,0.3);position:absolute;left:'+rx+'px;top:'+ry+'px;width:'+w+'px;height:'+h+'px"><a class="del" style="user-select: none">X</a><b class="no">'+(AREA_ID_LIST.length)+'</b></div>')
                $(newSelection).appendTo(SELF);




                $(SELF).find('.area-selection-temp').remove();
                FLAG_AREA_DRAW_START=false;

                $(newSelection).siblings().removeClass('selected')
                $(newSelection).addClass('selected')


                saveData();

                //trigger event
                if(HANDLE_ON_ADD){
                    var runStr=HANDLE_ON_ADD.replace('id','"'+newID+'"');
                    //console.log(runStr)
                    eval(runStr);
                }

                if(HANDLE_ON_SELECT){
                    console.log('select')
                    var runStr=HANDLE_ON_SELECT.replace('id','"'+newID+'"');
                    //console.log(runStr)
                    eval(runStr);
                }



            }

            // mouse up:
            $(SELF).mouseup(function(e){
                if(FLAG_AREA_DRAW_START){
                    setupArea(e);
                }
                FLAG_AREA_DRAG_START=false;
                FLAG_AREA_RESIZE=null;
            });

            // mouse leave:
            $(SELF).mouseleave(function(e){

                if(FLAG_AREA_DRAW_START){
                    setupArea(e);
                }
                FLAG_AREA_DRAG_START=false;
                FLAG_AREA_RESIZE=null;

            });

            // mouse handler for drag area and resize area-------------
            // start drag:
            $(SELF).delegate('.area-selection','mousedown',null,function(e){

                FLAG_AREA_DRAG_START=true;
                DRAG_DX= e.pageX -$(this).offset().left;
                DRAG_DY= e.pageY -$(this).offset().top;

                M_SX=e.pageX;
                M_SY=e.pageY;

                $(this).appendTo(SELF);

                $(this).siblings().removeClass('selected')
                $(this).addClass('selected')

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

            // re-size or drag selection:
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

            // mouse-up on selection
            $(SELF).delegate('.area-selection','mouseup',null,function(e){
                if(FLAG_AREA_DRAG_START) {
                    FLAG_AREA_DRAG_START = false;

                    e.stopPropagation();
                    return false;
                }
                FLAG_AREA_RESIZE=null;
            });

            // mouse-leave on selection
            $(SELF).delegate('.area-selection','mouseleave',null,function(e){
                if(FLAG_AREA_DRAG_START) {
                    FLAG_AREA_DRAG_START=false;
                    e.stopPropagation();
                    return false;
                }
            });

            // delete click hanlder
            $(SELF).delegate('.del','mousedown',null,function(e){

                if(e.button==0){
                    var curID=$(this).closest('.area-selection').attr('id');
                    $(this).closest('.area-selection').remove();

                    if(AREA_ID_LIST.indexOf(curID)>=0){
                        AREA_ID_LIST.splice(AREA_ID_LIST.indexOf(curID),1)
                    }
                    resetViewNo();

                    saveData();

                    e.stopPropagation();

                    //trigger event
                    if(HANDLE_ON_DEL){
                        var runStr=HANDLE_ON_DEL.replace('id','"'+curID+'"');
                        // console.log(runStr)
                        eval(runStr);
                    }


                    return false;
                }
            });


        };//-/build


        /**
         * readData
         * read data from data-savers.
         */
        let readData = function () {

            //read option list:


            if (DATA_SAVER != '' && $('#' + DATA_SAVER).val()) {


                var data=$('#' + DATA_SAVER).attr('value-type') == 'json' || !DATA_SAVE_PATH ? JSON.parse($('#' + DATA_SAVER).val()): $('#' + DATA_SAVER).val();

                if (DATA_SAVE_PATH) {
                    data= data[DATA_SAVE_PATH] ? data[DATA_SAVE_PATH] : null;
                }

                if(data){
                    $(SELF).empty()
                    AREA_ID_LIST = []
                    let rx,ry,w,h,id
                    for(let i in data){
                        rx= Number(data[i].left || 0) + POS_DX
                        ry= Number(data[i].top || 0) + POS_DY
                        w= data[i].width || 50
                        h= data[i].height || 50
                        id=data[i].id ? data[i].id : 'imgArea_'+i;
                        AREA_ID_LIST.push(id)
                        $('<div class="area-selection" id="'+id+'"  style="background:rgba(0,0,0,0.3);position:absolute;left:'+rx+'px;top:'+ry+'px;width:'+w+'px;height:'+h+'px">' +
                            '<a class="del" style="user-select: none">X</a>' +
                            '<b class="no">'+AREA_ID_LIST.length+'</b>' +
                            '</div>').appendTo(SELF);

                    }


                }


            }


        };//-/




        //产生列表
        build();

        //读取预设值
        readData();
        saveData();

        if(DATA_SAVER){
           $('#'+DATA_SAVER).change(function(e){
               readData();
           })
        }



    }
})(jQuery)
// -----------------------------------------


//设置所有class名为address-picker的项目。
$(document).ready(function () {

    var class_name = "img-area-selector";
    var ctrlist = $('.' + class_name);

    for (i = 0; i < ctrlist.length; i++) {
        $("." + class_name + ":eq(" + i + ")").imgAreaSelector();
    }

});


