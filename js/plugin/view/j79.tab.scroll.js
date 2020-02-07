/**
 *
 *
 *
 */
j79.loadCSS("/css/j79.tab.scroll.css");

(function ($) {

    $.fn.j79TabScroll = function () {

        //global vars:
        var SELF = this;
        var SELF_ID = $(SELF).attr('id');

        if(SELF_ID==''){
            SELF_ID='j79TabScroll'+(Math.round( Math.random()*1000000));
            this.attr('id' , SELF_ID);
        }

        var SCROLL_VELOCITY=15;

        var CONTENT_BAND_WIDTH=0;
        var CONTENT_LENGTH=0;

        var STATUS_SCROLLING=false;

        //var SCROLL_CONTENT=$(SELF).find('.scroll-content');


        var attachHandler=function(){

            $(SELF).delegate('.scroll-arrow-up','click',null, function(e){
                if(STATUS_SCROLLING){
                    return;
                }

                console.log('clicked accpet');

                var curX= $(SELF).find('.nav').position().left;
                var curY= $(SELF).find('.nav').position().top;




                //get current tab
                var curTabWidth= $(SELF).find('.nav li.active').prev().width();


                //tab show by bootstrap tab:
                $(SELF).find('.nav li.active').prev().find('a').tab('show');


                if(curX<0){
                    $(SELF).find('.nav').animate({"left":(curX+curTabWidth)+'px'}, 500,'linear', function(){
                        STATUS_SCROLLING=false;
                    });
                    STATUS_SCROLLING=true;
                }




            });

            $(SELF).delegate('.scroll-arrow-down','click',null, function(e){


                if(STATUS_SCROLLING){
                    return;
                }

                console.log('clicked accpet');

                var curX= $(SELF).find('.nav').position().left;
                var curY= $(SELF).find('.nav').position().top;



                //get current tab
                var curTabWidth= $(SELF).find('.nav li.active').width();



                //tab show by bootstrap tab:
                $(SELF).find('.nav li.active').next().find('a').tab('show');




                if(curX> $(SELF).width() - CONTENT_LENGTH){
                    $(SELF).find('.nav').animate({"left":(curX-curTabWidth)+'px'}, 500,'linear', function(){
                        STATUS_SCROLLING=false;
                    });
                    STATUS_SCROLLING=true;
                }



            });



        };//-/

        var build=function(){

            $(SELF).find('.nav').css('width','9999px');

            CONTENT_BAND_WIDTH=$(SELF).find('.nav').height()+1;

            $(SELF).css('position','relative');
            $(SELF).css('min-height',CONTENT_BAND_WIDTH+'px');

            $(SELF).find('.nav').css('position','absolute');
            $(SELF).find('.nav').css('left','0px');
            $(SELF).find('.nav').css('top','0px');

            var lastItemOffset=$(SELF).find('.nav').children(':last-child').offset();

            console.log($(SELF).find('.nav').children(':last-child'));
            console.log(lastItemOffset);

            CONTENT_LENGTH=lastItemOffset.left + $(SELF).find('.nav').children(':last-child').width();

            $('<a class="scroll-arrow-up"></a><a class="scroll-arrow-down"></a> ').appendTo(SELF);

            //set arrow height
            $(SELF).find('.scroll-arrow-up').css('height', CONTENT_BAND_WIDTH+'px');
            $(SELF).find('.scroll-arrow-down').css('height', CONTENT_BAND_WIDTH+'px');



            attachHandler();


        };//-/

        build();



    }
})(jQuery)//-----------------------------------------


//设置所有class名为banner-editor的项目。
$(document).ready(function () {

    var class_name = "tab-scroll";
    var ctrlist = $('.' + class_name);

    for (i = 0; i < ctrlist.length; i++) {

        $("." + class_name + ":eq(" + i + ")").j79TabScroll();


    }

});


