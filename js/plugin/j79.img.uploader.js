/**
 *  proListSelector
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
j79.loadCSS("/css/j79.img.uploader.css");

(function ($) {

    $.fn.j79ImgUploader = function () {

        //global vars:
        var SELF = this;
        var SELF_ID = $(SELF).attr('id');

        var FILE_CTR_NAME = 'file_field';
        var SERVER_URL = '/com.php?target=ImgUploadSrv&action=UPDATE';
        var DEFAULT_MAX_FILE_COUNT = 5;


        var FILE_SAVE_PATH = $(SELF).attr('save-path') || '/data/';

        var STR_PLACEHOLDER = $(SELF).attr('placeholder') || $(SELF).attr('title') || '请选择上传的图片';

        var DATA_SAVER = $(SELF).attr('data-saver') || '';






        var IMG_THUMB_W = $(SELF).attr('thumbnail-width') || 0;
        var IMG_THUMB_H = $(SELF).attr('thumbnail-height') || 0;

        var IMG_COMPRESS_W = $(SELF).attr('data-compress-width') || 0;
        var IMG_COMPRESS_H = $(SELF).attr('data-compress-height') || 0;


        var ATTR_MULTIPLE = $(SELF).attr('data-multi') ? ' multiple="multiple" ' : '';

        var MAX_FILE_COUNT = $(SELF).attr('file-max-count') ? parseInt($(SELF).attr('file-max-count')) : DEFAULT_MAX_FILE_COUNT;
        MAX_FILE_COUNT = $(SELF).attr('data-multi') ? MAX_FILE_COUNT : 1;
        MAX_FILE_COUNT = MAX_FILE_COUNT <= 0 ? 1 : MAX_FILE_COUNT;

        var FILE_LIST = [];


        /*
         DATA_SAVE_PATH: when it not empty, save generated data to a attribute named like DATA_SAVE_PATH of the object(DATA_SAVER data indicated)
         */
        var DATA_SAVE_PATH = $(SELF).attr('data-save-path') || '';

        /*
        DATA_SAVE_SINGLE_VALUE: save img url in single string value if true.
         */
        var DATA_SAVE_SINGLE_VALUE = $(SELF).attr('data-save-single-value') ? true : false ;
        var SEP='|'; //DATA_SAVE_SINGLE_VALUE=true时候，存多个图片路径的分割符号。

        if (DATA_SAVER != '') {
            $('#' + DATA_SAVER).hide();
        }


        var SELF_PAGE_OBJ = {};

        var CUR_FILE_IDX = 0;

        //get not existed file-input id:
        var FILE_INPUT_ID = SELF_ID + '_' + Math.round(Math.random() * 1000000000);
        while ($('#' + FILE_INPUT_ID).length > 0) {
            FILE_INPUT_ID = SELF_ID + '_' + Math.round(Math.random() * 1000000000);
        }


        //uploaded file list:

        var FILE_UPLOADED_LIST = [];
        var FILE_UPLOAD_ERROR_LIST = [];

        var TOTAL_ID_BASE = 0;

        //html codes-------------------------------
        //UI html
        //var HTML_UI= '<form enctype="multipart/form-data" method="post" class="file-upload-ctr" name="'+SELF_ID+'_formFileUpload"><label style="display: inline-block;" class="button-face">选择文件<input style="display: none;" type="file" id="'+FILE_INPUT_ID+'" name="'+FILE_INPUT_ID+'" '+ATTR_MULTIPLE+'  /></label></form>' +
        var HTML_UI = '<div class="place-holder">' +

            STR_PLACEHOLDER +
            '</div>' +
            '<ul class="img-preview-list">' +
            '<label  class="button-face">+<input style="display: none;" type="file" id="' + FILE_INPUT_ID + '" name="' + FILE_INPUT_ID + '" ' + ATTR_MULTIPLE + '  /></label>' +
            '</ul> ';

        //selected li html
        var HTML_IMG_MENU = '<div class="img-menu">' +
            '<a class="btn btn-view" ><i class="glyphicon glyphicon-eye-open"></i> 查看</a> ' +
            '<a class="btn btn-del" ><i class="glyphicon glyphicon-remove"></i> 删除</a> ' +
            '<a class="btn btn-up" ><i class="glyphicon glyphicon-chevron-left"></i> 前移</a> ' +
            '<a class="btn btn-down" >后移 <i class="glyphicon glyphicon-chevron-right"></i></a> ' +
            '</div>';

        var HTML_IMG_LI = '<li id="' + FILE_INPUT_ID + '_imgli_[#cur_idx#]">' +
            '<span class="loading-progress">' +
            '<div class="spinner">' +
            '<div class="double-bounce1"></div>' +
            '<div class="double-bounce2"></div>' +
            '</div>' +
            '</span>' +
            '<span class="img"><img width="100%" [#src_part#] id="' + FILE_INPUT_ID + '_img_[#cur_idx#]" />' +
            HTML_IMG_MENU +
            '</span></li>';


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
            var singleValue='';
            var sep='';

            $(SELF).find('.img-preview-list li .img').each(function (idx) {
                if ($(this).attr('data-url') && $(this).attr('data-url') != 'undefined' && $(this).attr('data-url') != '') {

                    if(DATA_SAVE_SINGLE_VALUE){
                        singleValue+=sep+$(this).attr('data-url');
                        sep=SEP;
                        return;
                    }

                    imgItem = {
                        "url": $(this).attr('data-url')
                    };
                    if ($(this).attr('data-thumb') && $(this).attr('data-thumb') != 'undefined' && $(this).attr('data-thumb') != '') {
                        imgItem.thumb = $(this).attr('data-thumb');
                    }

                    result.push(imgItem);
                }


            });

            if(DATA_SAVE_SINGLE_VALUE){
                result=  singleValue;
            }


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

                if(j79.isString(result)){
                    $('#' + DATA_SAVER).val(result);
                }else{
                    $('#' + DATA_SAVER).val(j79.toJSONString(result));
                }




            }


        };//-/saveData

        var finishUpload = function () {
            TOTAL_ID_BASE += FILE_LIST.length;
            //console.log(FILE_UPLOADED_LIST);
            saveData();
        };//


        /**
         * uploadfileByIdx
         * @param idx
         */
        var uploadFileByIdx = function (idx) {

            idx = idx && idx > 0 ? parseInt(idx) : 0;

            var formData = new FormData();
            formData.append(FILE_CTR_NAME, FILE_LIST[idx]);
            formData.append("save_path", FILE_SAVE_PATH);
            formData.append("thumb_width", IMG_THUMB_W);
            formData.append("thumb_height", IMG_THUMB_H);
            formData.append("compress_width", IMG_COMPRESS_W);
            formData.append("compress_height", IMG_COMPRESS_H);

            $(SELF).find('#' + FILE_INPUT_ID + '_imgli_' + (TOTAL_ID_BASE + idx) + ' .loading-progress').show();


            $.ajax({
                url: SERVER_URL,
                type: 'POST',
                data: formData,

                // 告诉jQuery不要去处理发送的数据
                processData: false,

                // 告诉jQuery不要去设置Content-Type请求头
                contentType: false,

                beforeSend: function () {
                    console.log("正在进行，请稍候");
                },
                success: function (responseStr) {

                    $(SELF).find('#' + FILE_INPUT_ID + '_imgli_' + (TOTAL_ID_BASE + idx) + ' .loading-progress').hide();


                    console.log('res:');
                    console.log(responseStr);

                    resultData = j79.toJSON(responseStr);
                    if (parseInt(resultData.result) == 1) {


                        if (!resultData.url || resultData.url == '') {
                            console.log("没有返回有效的文件名url，出错的文件编号：" + CUR_FILE_IDX);

                            $(SELF).find('#' + FILE_INPUT_ID + '_imgli_' + (TOTAL_ID_BASE + idx) + ' .loading-progress').addClass('error').show();

                            FILE_UPLOAD_ERROR_LIST.push(CUR_FILE_IDX);
                        } else {
                            console.log("成功上传：" + resultData.url);
                            var fileItem = {};
                            fileItem.url = resultData.url;
                            $(SELF).find('#' + FILE_INPUT_ID + '_imgli_' + (TOTAL_ID_BASE + idx) + ' .img').attr('data-id', (TOTAL_ID_BASE + idx));
                            $(SELF).find('#' + FILE_INPUT_ID + '_imgli_' + (TOTAL_ID_BASE + idx) + ' .img').attr('data-url', fileItem.url);
                            if (resultData.thumb_url) {
                                fileItem.thumb = resultData.thumb_url;
                                $(SELF).find('#' + FILE_INPUT_ID + '_imgli_' + (TOTAL_ID_BASE + idx) + ' .img').attr('data-thumb', fileItem.thumb);
                            }
                            FILE_UPLOADED_LIST.push(fileItem);


                        }


                    } else {
                        FILE_UPLOAD_ERROR_LIST.push(CUR_FILE_IDX);
                        $(SELF).find('#' + FILE_INPUT_ID + '_imgli_' + (TOTAL_ID_BASE + idx) + ' .loading-progress').addClass('error').show();
                        console.log("失败:" + resultData.state);
                    }

                    if (CUR_FILE_IDX >= FILE_LIST.length - 1) {
                        finishUpload();
                    } else {
                        CUR_FILE_IDX++;
                        uploadFileByIdx(CUR_FILE_IDX);
                    }

                },
                error: function (responseStr) {
                    //$(SELF).find('#'+FILE_INPUT_ID+'_imgli_'+(TOTAL_ID_BASE+idx)+' .loading-progress').hide();

                    $(SELF).find('#' + FILE_INPUT_ID + '_imgli_' + (TOTAL_ID_BASE + idx) + ' .loading-progress').addClass('error').show();

                    FILE_UPLOAD_ERROR_LIST.push(CUR_FILE_IDX);
                    console.log("error in connection");
                    if (CUR_FILE_IDX >= FILE_LIST.length - 1) {
                        finishUpload();
                    } else {
                        CUR_FILE_IDX++;
                        uploadFileByIdx(CUR_FILE_IDX);
                    }
                }
            });

        };//-/


        var startUpload = function () {

            FILE_LIST = document.getElementById(FILE_INPUT_ID).files;


            if (FILE_LIST.length <= 0) {
                return false;
            }
            CUR_FILE_IDX = 0;
            //FILE_UPLOADED_LIST=[];
            //FILE_UPLOAD_ERROR_LIST=[];

            uploadFileByIdx(CUR_FILE_IDX);


        };

        /**
         * attachEvent
         * attach all event by delegate.
         */
        var attachEvent=function(){

            //点击图片菜单，看大图：
            $(SELF).delegate(' .img .btn-view', 'click', null, function (e) {
                var imgUrl = $(this).closest('.img').attr('data-url');
                window.open(imgUrl);
            });

            //点击图片菜单，删除图片
            $(SELF).delegate(' .img .btn-del', 'click', null, function (e) {
                var imgSpan = $(this).closest('.img');
                var curIdx = Number($(imgSpan).attr('data-id'));

                delete FILE_UPLOADED_LIST[curIdx];

                $(imgSpan).closest('li').remove();

                //TOTAL_ID_BASE--;

                console.log('FILE_UPLOADED_LIST after del');
                console.log(FILE_UPLOADED_LIST);
                saveData();
            });

            //点击图片菜单，前移
            $(SELF).delegate(' .img .btn-up', 'click', null, function (e) {
                var li = $(this).closest('li');
                var preLi=$(li).prev('li');
                if(preLi.length>0){
                    $(li).insertBefore(preLi);
                    saveData();
                }
            });

            //点击图片菜单，后移
            $(SELF).delegate(' .img .btn-down', 'click', null, function (e) {
                var li = $(this).closest('li');
                var nextLi=$(li).next('li');
                if(nextLi.length>0){
                    $(li).insertAfter(nextLi);
                    saveData();
                }
            });

            //点击图片,打开图片菜单
            $(SELF).delegate(' .img', 'click', null, function (e) {
                $(this).find('.img-menu').show();

            });
            $(SELF).delegate(' .img', 'mouseleave', null, function (e) {
                $(this).find('.img-menu').hide();

            });
            $(SELF).delegate(' .img', 'mouseenter', null, function (e) {
                $(this).find('.img-menu').show();

            });


            //图片选择结束后触发：选择完毕直接上传。
            $(SELF).delegate('#' + FILE_INPUT_ID, 'change', null, function (e) {
                var fileList = document.getElementById(FILE_INPUT_ID).files;

                //console.log('TOTAL_ID_BASE:'+TOTAL_ID_BASE)
                //console.log('fileList.length:'+fileList.length)

                if (MAX_FILE_COUNT < $(SELF).find('.img-preview-list li').length + fileList.length) {
                    alert('只能上传' + MAX_FILE_COUNT + '个文件，当前选择无效。');
                    return false;
                }



                //图片预览:
                if (typeof FileReader != 'undefined') {

                    var viewImgPreview = function (idx) {
                        var fileUrl = fileList[idx];
                        var reader = new FileReader();
                        reader.onerror = function (e) {
                            console.log("读取异常....");
                            if (idx >= fileList.length - 1) {

                            } else {
                                idx++;
                                viewImgPreview(idx);
                            }

                        };
                        reader.onload = function (e) {

                            var tmpData = {
                                "cur_idx": TOTAL_ID_BASE + idx,
                                "src_part": ' '
                            };

                            $(j79.setHtml(tmpData, HTML_IMG_LI)).appendTo($(SELF).find('.img-preview-list'));

                            $(SELF).find('.img-preview-list .button-face').appendTo($(SELF).find('.img-preview-list'));

                            var img = document.getElementById(FILE_INPUT_ID + '_img_' + (TOTAL_ID_BASE + idx));
                            img.src = e.target.result;
                            //或者 img.src = this.result;  //e.target == this

                            if (idx >= fileList.length - 1) {

                            } else {
                                idx++;
                                viewImgPreview(idx);
                            }

                        };
                        reader.readAsDataURL(fileUrl)
                    };
                    viewImgPreview(0);
                }


                if (fileList.length > 0) {
                    startUpload();
                }
            });

            //action attach for start-upload
            $(SELF).delegate('.btn-start-upload', 'click', null, function (e) {
                if (document.getElementById(FILE_INPUT_ID).files.length > 0) {
                    startUpload();
                }

            });

        };


        /**
         *  build
         *  产生列表
         *
         */
        var build = function () {

            $(SELF).empty();

            //绘制基本UI
            var ui = $(HTML_UI);
            $(SELF).empty();
            ui.appendTo(SELF);

            FILE_LIST = [];



        };//-/build


        /**
         * readData
         * read data from data-savers.
         */
        var readData = function () {



            if (DATA_SAVER != '' && $('#' + DATA_SAVER).val() && $('#' + DATA_SAVER).val() != '') {

                var data =j79.toJSON($('#' + DATA_SAVER).val());

                if(data===false){
                    data=[
                        {"url":$('#' + DATA_SAVER).val()}
                    ];
                }





                if (DATA_SAVE_PATH) {
                    data = data[DATA_SAVE_PATH] ? data[DATA_SAVE_PATH] : null;
                }

                if (data) {
                    var imgItem;
                    var tmpData;
                    var uiImgLi;

                    console.log(data);

                    //如果是单个对象，不是数组，那么转成含有此对象的数组：
                    if( typeof data =='object' && !j79.isArray(data) ){
                        //console.log('not arra');
                        data=[data];

                        //console.log(data);
                    }else if( j79.isString(data) ){//如果是单个字符串，认为是图片url值。
                        data=[
                            {"url":data}
                        ];
                    }


                    for (var i = 0; i < data.length; i++) {
                        imgItem = data[i];
                        if (imgItem.url && imgItem.url != '') {
                            tmpData = {
                                "cur_idx": i,
                                "src_part": ' src="' + imgItem.url + '" '
                            };

                            uiImgLi = $(j79.setHtml(tmpData, HTML_IMG_LI));
                            $(uiImgLi).appendTo($(SELF).find('.img-preview-list'));
                            $(uiImgLi).find('.loading-progress').hide();
                            $(uiImgLi).find('.img')
                                .attr('data-id', i)
                                .attr('data-url', imgItem.url);
                            if (imgItem.thumb && imgItem.thumb != '') {
                                $(uiImgLi).find('.img')
                                    .attr('data-thumb', imgItem.thumb);
                            }

                            TOTAL_ID_BASE++;

                            $(SELF).find('.img-preview-list .button-face').appendTo($(SELF).find('.img-preview-list'));

                        }


                    }

                }


            }


        };//-/


        //产生UI
        build();
        attachEvent();

        //读取预设值
        readData();

        //如果DATA_SAVER 发生change事件，重新读取数据。
        if (DATA_SAVER != '') {

            $(document).delegate('#'+DATA_SAVER, "change", null, function(e){

                build();
                readData();
            });

        }




    }
})(jQuery)//-----------------------------------------


//设置所有class名为address-picker的项目。
$(document).ready(function () {

    var class_name = "j79-img-uploader";
    var ctrlist = $('.' + class_name);

    for (i = 0; i < ctrlist.length; i++) {

        $("." + class_name + ":eq(" + i + ")").j79ImgUploader();


    }

});


