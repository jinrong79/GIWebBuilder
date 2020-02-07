/**
 *  j79BannerEditor
 *  地址选择器，包含街道，最高4级。
 *
 *  UI上需要的设置参数：
 *  注：当前插件div，必须指定唯一的id值。
 *
 *  save-path :           图片在服务器上的保存路径，相对于网站根的相对路径，必须以‘/'结尾。比如： ‘/data/img/’. 默认值为 ‘/data/’
 *  data-saver:           最终数据储存的控件id。纯id值，不带#号。 最终会把图片上传后的信息以json格式的字符串形式，存入此控件，必须为textarea控件。
 *  data-save-path：      最终数据存储时，存到数据结构的子节点上。这个节点的名称，由data-save-path。 默认为空，即直接存到根节点上。
 *  data-multi:           图片单选还是多选。存在此项，即多选，不存在则单选。单选时，file-max-count失效。
 *  file-max-count:       上传文件个数限制。多选时，默认值为5个。
 *  data-compress-width / data-compress-height:     图片压缩阀值，如果图片操作此值，服务器端进行压缩。如果宽高都设置了，就会压缩并裁剪。
 *  thumbnail-width / thumbnail-height:             图片生成缩略图时，缩略图的宽高。如果没有设置，就不产生缩略图。
 *
 *
 *
 *
 */
j79.loadCSS("/css/banner.editor.css");

(function ($) {

    $.fn.j79BannerEditor = function () {

        //global vars:
        var SELF = this;
        var SELF_ID = $(SELF).attr('id');

        if(SELF_ID==''){
            SELF_ID='bannerEditor'+(Math.round( Math.random()*1000000));
            this.attr('id' , SELF_ID);
        }



        var FILE_SAVE_PATH = $(SELF).attr('save-path') || '/data/';



        var DATA_SAVER = $(SELF).attr('data-saver') || '';

        var CTR_TYPE=$(SELF).attr('data-ctr-type') || 0;  //CTR_TYPE: 图片必填与否。 0- 图片必填(默认值)；1- 图片和标题中必填一项即可，用于纯文字link。
        CTR_TYPE= parseInt(CTR_TYPE) || 0;


        var IMG_THUMB_W = $(SELF).attr('thumbnail-width') || 0;
        var IMG_THUMB_H = $(SELF).attr('thumbnail-height') || 0;

        var IMG_COMPRESS_W = $(SELF).attr('data-compress-width') || 0;
        var IMG_COMPRESS_H = $(SELF).attr('data-compress-height') || 0;




        var STR_PLACEHOLDER=$(SELF).attr('title') || '';

        var DEFAULT_MAX_COUNT=3;
        //MAX BANNER COUNT,DEFAULT =3
        var MAX_COUNT = $(SELF).attr('data-max-count') && parseInt($(SELF).attr('data-max-count'))>0 ? parseInt($(SELF).attr('data-max-count')) : DEFAULT_MAX_COUNT;



        /*
         DATA_SAVE_PATH: when it not empty, save generated data to a attribute named like DATA_SAVE_PATH of the object(DATA_SAVER data indicated)
         */
        var DATA_SAVE_PATH = $(SELF).attr('data-save-path') || '';

        if (DATA_SAVER != '') {
            $('#' + DATA_SAVER).hide();
        }

        var UI_LIST='.ad-list';
        var UI_EDITOR='.edit-panel';


        //html codes-------------------------------
        //UI html

        var imgCompStr='';
        imgCompStr += (IMG_COMPRESS_W>0 ? ' data-compress-width="'+IMG_COMPRESS_W+'" ': '');
        imgCompStr += (IMG_COMPRESS_H>0 ? ' data-compress-height="'+IMG_COMPRESS_H+'" ': '');

        var imgPlaceholder='title="图片上传，推荐大小为';
        imgPlaceholder += (IMG_COMPRESS_W>0 ? ' 宽：'+IMG_COMPRESS_W+'  ': '');
        imgPlaceholder += (IMG_COMPRESS_H>0 ? ' 高：'+IMG_COMPRESS_H+' ': '');
        imgPlaceholder +=' ，超出或不同比例的图片，会被强制压缩、裁剪。" ';

        //imgcompStr += IMG_COMPRESS_H>0 ? ' data-compress-height="'+IMG_COMPRESS_H+'" ': '';

        //var HTML_UI= '<form enctype="multipart/form-data" method="post" class="file-upload-ctr" name="'+SELF_ID+'_formFileUpload"><label style="display: inline-block;" class="button-face">选择文件<input style="display: none;" type="file" id="'+FILE_INPUT_ID+'" name="'+FILE_INPUT_ID+'" '+ATTR_MULTIPLE+'  /></label></form>' +

        var placeHolderImg=CTR_TYPE==0 ? '<span class="must-fill-tip">(必填)</span>' :'<br/><span class="must-fill-tip">(标题与图片选填一项)<span>';
        var placeHolderTitle=CTR_TYPE==0 ? '' :'<br/><span class="must-fill-tip">(标题与图片选填一项)</span>';

        var HTML_UI =
            '<div class="description">'+STR_PLACEHOLDER+'</div>'+
            '<div class="list-part"><h3><span class="float-r"><a  class="btn btn-sm btn-primary btn-add"><i class="glyphicon glyphicon-plus"></i> 添加</a></span>列表</h3><ol class="ad-list"></ol> </div>'+
            '<div class="edit-panel"><h3>添加/编辑</h3>' +
            '<div class="row input-item input-title">' +
            '<div class="col-md-4">广告链接URL<span class="must-fill-tip">(必填)</span>：</div>' +
            '<div class="col-md-8"><input type="text" id="'+SELF_ID+'_url" placeholder="链接地址，可以只输入域名以外的部分，从/符号开始。" /></div> ' +
            '</div>'+
            '<hr/>'+
            '<div class="row input-item input-img">' +
            '<div class="col-md-4">广告图片'+placeHolderImg+'：</div>' +
            '<div class="col-md-8"><div class="j79-img-uploader" data-save-single-value="yes" data-saver="'+SELF_ID+'_img" save-path="'+FILE_SAVE_PATH+'" '+imgCompStr+imgPlaceholder+' ></div></div> ' +
            '</div>'+

            '<input type="hidden" style="width: 100%;" id="'+SELF_ID+'_img" />'+



            '<div class="row input-item input-title">' +
            '<div class="col-md-4">广告标题'+placeHolderTitle+'：</div>' +
            '<div class="col-md-8"><input type="text" id="'+SELF_ID+'_title" placeholder="标题，可选填"/></div> ' +
            '</div>'+

            '<div class="row input-item input-title-sub">' +
            '<div class="col-md-4">广告副标题：</div>' +
            '<div class="col-md-8"><input type="text" id="'+SELF_ID+'_subtitle" placeholder="副标题，可选填"/></div> ' +
            '</div>'+

            '<div class="row operation"><div class="col-md-8 col-md-offset-4"><a  class="btn btn-primary btn-add"><i class="glyphicon glyphicon-ok"></i> 确认添加</a><a  class="btn btn-primary btn-edit"><i class="glyphicon glyphicon-ok"></i> 确认编辑</a><a  class="btn btn-default btn-close">关闭</a></div></div>'+

            '</div>' +
            '<div class="clear"></div>'
            ;


        //selected li html
        var HTML_IMG_MENU = '<div class="img-menu">' +
            '<a class="btn btn-edit" ><i class="glyphicon glyphicon-edit"></i> 编辑</a> ' +
            '<a class="btn btn-del" ><i class="glyphicon glyphicon-remove"></i> 删除</a><br/>' +
            '<a class="btn btn-up" ><i class="glyphicon glyphicon-chevron-up"></i> 上移</a> ' +
            '<a class="btn btn-down" >下移 <i class="glyphicon glyphicon-chevron-down"></i></a> ' +
            '</div>';

        var HTML_LI='<li url="[#url#]" title="[#title#]" img="[#img#]" subtitle="[#subtitle#]">'+
            HTML_IMG_MENU+
            '<span class="img-part"><a href="[#img#]" target="_blank"><img src="[#img#]"/></a></span>' +
            '<div class="title-part"><p>标题：[#title#]</p><p>副标题：[#subtitle#]</p><p>链接：<a href="[#url#]" target="_blank">[#url#]</a></p></div>' +
            '<div class="clear"></div>' +
            ' </li>';





        /**
         *  saveData
         *  save selected data to data-savers.
         *
         */
        var saveData = function () {

            var result = [];

            /*for(var i=0; i< FILE_UPLOADED_LIST.length;i++){
             if(FILE_UPLOADED_LIST[i]){
             result.push(FILE_UPLOADED_LIST[i]);
             }
             }*/
            var imgItem;
            $(SELF).find(UI_LIST+' li').each(function (idx) {

                var adImg=$(this).attr('img');
                var adTitle=$(this).attr('title');
                var adSubtitle=$(this).attr('subtitle');
                var adUrl=$(this).attr('url');

                if ((CTR_TYPE==0 &&  adImg &&  adUrl) || ( CTR_TYPE==1 && ( adImg || adTitle ) && adUrl   ) ) {
                    imgItem = {
                        "img": adImg,
                        "title": adTitle,
                        "url": adUrl,
                        "subtitle":adSubtitle,
                    };
                    result.push(imgItem);
                }


            });


            //generate result:

            if (DATA_SAVER != '') {

                if (DATA_SAVE_PATH) {
                    var dataObj = j79.toJSON($('#' + DATA_SAVER).val());
                    if (!dataObj) {
                        dataObj = {}
                    }
                    dataObj[DATA_SAVE_PATH] = result;
                    result = dataObj;

                }
                $('#' + DATA_SAVER).val(j79.toJSONString(result));
            }


        };//-/saveData

        /**
         * attachEvent
         * bind event handler by delegate:
         */
        var attachEvent=function(){
            //list:open add new editor:
            $(SELF).delegate(' .list-part .btn-add', 'click', null, function (e) {

                console.log('start open banner editor!');
                if( $(SELF).find(UI_LIST+' li').length>=MAX_COUNT ){
                    alert('上限已达，不能再添加了。');
                    return ;
                }

                var curImg=$(SELF).find(UI_EDITOR+' #'+SELF_ID+'_img').val();



                $(SELF).find(UI_EDITOR+' #'+SELF_ID+'_title').val('');
                $(SELF).find(UI_EDITOR+' #'+SELF_ID+'_url').val('');

                $(SELF).find(UI_EDITOR+' .btn-edit').hide();
                $(SELF).find(UI_EDITOR+' .btn-add').show();

                //current img not empty, then trigger 'change' action after set empty.
                if(curImg && curImg!=''){

                    $(SELF).find(UI_EDITOR+' #'+SELF_ID+'_img').val('');
                    $(SELF).find(UI_EDITOR+' #'+SELF_ID+'_img').trigger('change');
                }

                $(SELF).find(UI_EDITOR).show();
            });

            //list:open modify editor:
            $(SELF).delegate(' .list-part .btn-edit', 'click', null, function (e) {

                var li = $(this).closest('li');

                $(li).siblings().removeClass('active');
                $(li).addClass('active');

                var adImg=$(li).attr('img') || '';
                var adTitle=$(li).attr('title') || '';
                var adUrl=$(li).attr('url');
                var adSubtitle=$(li).attr('subtitle') || '';

                //open modify editor:
                if ((CTR_TYPE==0 &&  adImg &&  adUrl) || ( CTR_TYPE==1 && ( adImg || adTitle ) && adUrl   ) ) {
                    $(SELF).find(UI_EDITOR+' #'+SELF_ID+'_img').val(adImg);
                    $(SELF).find(UI_EDITOR+' #'+SELF_ID+'_img').trigger('change');
                    $(SELF).find(UI_EDITOR+' #'+SELF_ID+'_title').val(adTitle);
                    $(SELF).find(UI_EDITOR+' #'+SELF_ID+'_subtitle').val(adSubtitle);
                    $(SELF).find(UI_EDITOR+' #'+SELF_ID+'_url').val(adUrl);
                    $(SELF).find(UI_EDITOR+' .btn-edit').show();
                    $(SELF).find(UI_EDITOR+' .btn-add').hide();
                    $(SELF).find(UI_EDITOR).show();
                }

            });



            //editor: add new item
            $(SELF).delegate(UI_EDITOR+' .operation .btn-add', 'click', null, function (e) {


                if( $(SELF).find(UI_LIST+' li').length>=MAX_COUNT ){
                    alert('上限已达，不能再添加了。');
                    return ;
                }


                var adImg=$(SELF).find(UI_EDITOR+' #'+SELF_ID+'_img').val() || '';
                var adTitle=$(SELF).find(UI_EDITOR+' #'+SELF_ID+'_title').val() || '';
                var adSubtitle=$(SELF).find(UI_EDITOR+' #'+SELF_ID+'_subtitle').val() || '';
                var adUrl=$(SELF).find(UI_EDITOR+' #'+SELF_ID+'_url').val() || '';



                if( (CTR_TYPE==0 &&  adImg &&  adUrl) || ( CTR_TYPE==1 && ( adImg || adTitle ) && adUrl   )  ){

                    var adData={
                        "img": adImg,
                        "url": adUrl,
                        "title":adTitle,
                        "subtitle":adSubtitle
                    };






                    $( j79.setHtml(adData, HTML_LI)).appendTo($(SELF).find(UI_LIST));
                    saveData();

                    $(SELF).find(UI_EDITOR+' #'+SELF_ID+'_title').val('');
                    $(SELF).find(UI_EDITOR+' #'+SELF_ID+'_subtitle').val('');
                    $(SELF).find(UI_EDITOR+' #'+SELF_ID+'_url').val('');
                    $(SELF).find(UI_EDITOR+' #'+SELF_ID+'_img').val('');
                    $(SELF).find(UI_EDITOR+' #'+SELF_ID+'_img').trigger('change');


                    //$(SELF).find(UI_EDITOR).hide();

                }else{
                    alert('请填写完整信息后，再点击添加按钮！');

                }
            });

            //editor: modify existing item
            $(SELF).delegate(UI_EDITOR+' .operation .btn-edit', 'click', null, function (e) {


                var adImg=$(SELF).find(UI_EDITOR+' #'+SELF_ID+'_img').val()  || '';
                var adTitle=$(SELF).find(UI_EDITOR+' #'+SELF_ID+'_title').val() || '';
                var adSubtitle=$(SELF).find(UI_EDITOR+' #'+SELF_ID+'_subtitle').val() || '';
                var adUrl=$(SELF).find(UI_EDITOR+' #'+SELF_ID+'_url').val() || '';

                if( (CTR_TYPE==0 &&  adImg &&  adUrl) || ( CTR_TYPE==1 && ( adImg || adTitle ) && adUrl   ) ){

                    var adData={
                        "img": adImg,
                        "title": adTitle,
                        "subtitle":adSubtitle,
                        "url": adUrl
                    };

                    $(SELF).find(UI_LIST+' li.active').replaceWith(j79.setHtml(adData, HTML_LI)) ;
                    saveData();

                    $(SELF).find(UI_EDITOR).slideUp();

                }else{
                    alert('请填写完整信息后，再点击按钮！');

                }
            });

            //editor: close window
            $(SELF).delegate(UI_EDITOR+' .operation .btn-close', 'click', null, function (e) {

                $(SELF).find(UI_EDITOR).hide();
            });




            //list: li clicked, set active, set edit.
            $(SELF).delegate(UI_LIST+'  li', 'click', null, function (e) {
                $(this).siblings().removeClass('active');
                $(this).addClass('active');


            });




            //点击图片菜单，删除图片
            $(SELF).delegate(UI_LIST+' .btn-del', 'click', null, function (e) {

                $(this).closest('li').remove();
                saveData();
            });

            //点击图片菜单，前移
            $(SELF).delegate(UI_LIST+' .btn-up', 'click', null, function (e) {
                var li = $(this).closest('li');
                var preLi=$(li).prev('li');
                if(preLi.length>0){
                    $(li).insertBefore(preLi);
                    saveData();
                }
            });

            //点击图片菜单，后移
            $(SELF).delegate(UI_LIST+' .btn-down', 'click', null, function (e) {
                var li = $(this).closest('li');
                var nextLi=$(li).next('li');
                if(nextLi.length>0){
                    $(li).insertAfter(nextLi);
                    saveData();
                }
            });
        };

        /**
         *  build
         *  产生列表
         *
         */
        var build = function () {

            //绘制基本UI
            var ui = $(HTML_UI);
            $(SELF).empty();
            ui.appendTo(SELF);


            console.log('build ui ok!');

            $(SELF).find('.j79-img-uploader').j79ImgUploader();

            //FILE_LIST = [];









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


        //产生UI:
        build();
        //bind event handler:
        attachEvent();

        //读取预设值
        readData();


    }
})(jQuery)//-----------------------------------------


//设置所有class名为banner-editor的项目。
$(document).ready(function () {

    var class_name = "banner-editor";
    var ctrlist = $('.' + class_name);

    for (i = 0; i < ctrlist.length; i++) {

        $("." + class_name + ":eq(" + i + ")").j79BannerEditor();


    }

});


