/**
 * Created by jinrong on 2017/3/20.
 */

/**
 * j79Ani:
 * 1）把css3动画关键帧直接写在html元素的属性（ani-frames）里面，把css3的animation的参数，即时间，动画进行函数，delay，播放次数等值，直接写在属性(ani-params)来，生成css3动画。
 * 2）通过自定义时间“aniStart”来触发动画开始。
 * 3）属性“ani-flag-everytime”来规定，是否每次都重新播放。1- 每次重新播放；0【默认】- 重新触发时，不再播放动画。
 *
 * html元素上的属性：
 * - class ：含有ani
 * - ani-frames: css3动画关键帧定义字符串，例如 “0% {width:10%} 100%{width:100%}”
 * - ani-params: css3动画参数，例如："3s ease-out 1.7s 1"  （时间，动画进行函数，delay，播放次数）。顺序和设置值与css的animation设置相同。
 * - ani-flag-everytime: 重新激活时，是否重新播放动画。 1- 每次重新播放；0【默认】- 重新触发时，不再播放动画。
 *
 * 使用举例：
         <div class="title ani"
            ani-frames="0% {bottom:-20%;opacity: 0;} 100%{ bottom:35%; opacity: 1;}"
            ani-params="1.2s ease-out 0s 1" style="bottom:35%;">掌上成宝</div>
 */

(function ($) {

    $.fn.j79Ani = function () {


        var SELF = this;

        var SELF_ID = $(SELF).attr('id') || '';

        var ANI_PARAMS=$(SELF).attr('ani-params') || '';  //css3 动画参数：3s ease-out 1.7s 1 （时间，动画进行函数，delay，播放次数）。与css的animation设置相同。

        var ANI_FRAMES=$(SELF).attr('ani-frames') || '';  //css3 动画keyframe设定。包含了"0% {...} 100% {...}" 类型的字符串。

        var ANI_CALLBACK_FINISHED=$(SELF).attr('ani-callback-finished') || '';  //动画播放完毕后，执行的js code 字符串。

        var ANI_FLAG_PLAY_EVERYTIME=$(SELF).attr('ani-flag-everytime') && parseInt($(SELF).attr('ani-flag-everytime'))==1 ? true : false;

        var ANI_FRAME_ID= $(SELF).attr('ANI_FRAME_ID') || '';




        //解析参数：
        var parseParams=function(){


            ANI_PARAMS=$(SELF).attr('ani-params') || '';
            ANI_FRAMES=$(SELF).attr('ani-frames') || '';
            ANI_FRAME_ID= $(SELF).attr('ANI_FRAME_ID') || '';
            if(ANI_CALLBACK_FINISHED!=''){

                $(SELF).on('webkitAnimationEnd',null,null,function(){
                    console.log('ani end!!!!!!');
                    eval(ANI_CALLBACK_FINISHED);
                });
            }


        };

        //append keyframe style
        var appendToStyle=function(){


            if(ANI_FRAMES!=''){
                var d =new Date();//'19299321793912'; //new Date();

                var randName=d.getUTCMilliseconds()+Math.round(Math.random()*100000);//d.getUTCMilliseconds()+Math.round(Math.random()*100000);
                var keyframeName= 'kf_'+ (SELF_ID=='' ? randName : SELF_ID);

                //alert('ani001');
                $(SELF).attr('ANI_FRAME_ID',keyframeName);

                str="@keyframes "+keyframeName+" {"+ANI_FRAMES+"}"+" @-webkit-keyframes "+keyframeName+" {"+ANI_FRAMES+"}";
                var nod = document.createElement('style');
                //alert('ani002');
                nod.type='text/css';
                if(nod.styleSheet){         //ie下
                    nod.styleSheet.cssText = str;
                    //alert('ani003');
                } else {
                    nod.appendChild(document.createTextNode(str)) ;      //或者写成 nod.innerHTML = str;
                    //alert('ani004');
                }
                document.getElementsByTagName('head')[0].appendChild(nod);
                //alert('ani005');
            }


        };

        //播放动画处理
        var process=function(){
            parseParams();



            if(ANI_PARAMS!='' && ANI_FRAMES!='' && ANI_FRAME_ID!=''){


                //check whether play everytime:
                if(ANI_FLAG_PLAY_EVERYTIME==false && $(SELF).attr('ani-played') && parseInt($(SELF).attr('ani-played'))==1){
                    return;
                }


                $(SELF).css('animation', ANI_FRAME_ID+' '+ANI_PARAMS);
                $(SELF).css('-webkit-animation', ANI_FRAME_ID+' '+ANI_PARAMS);


                $(SELF).css('animation-play-state','running');
                $(SELF).css('-webkit-animation-play-state', 'running');
                $(SELF).css('animation-fill-mode','forwards');
                $(SELF).css('-webkit-animation-fill-mode', 'forwards');

                $(SELF).attr('ani-played','1');

            }

        };

        //触发绑定
        $(this).bind("aniStart", function () {
            //alert('start ani');
            process();
        });

        parseParams();
        appendToStyle();

    }
})(jQuery)//-----------------------------------------