/**
 *  j79FormBuilder
 *
 *  @param {object} params : settings for form builder.
 *                           ={
*                              uiForm         : form ui container selecotr like '#form1',
*                              urlXML         : url for formmat xml.
*                              data           : form data
*                              actionFinished : function when finished.
*                              flagEdit       : define action is edit or add-new.
*                              flagView       : define static-view  without any edit control.
*                             )
*
 *  xml文件格式：
     每一项样例：
         <item  id="password" lineEnd="yes">
                // id: 每个item的id，仅仅是用来区分在xml文件里的item； lineEnd: 输入项，是否独立成行。
            <label class="col-md-2 control-label">密码</label>
                   // 输入项的标题文字。 class会赋值给标题文字的div上。
            <controls class="col-md-10">
                      // 输入控件组部分，里面可以多个控件，class会赋值给控件部分的div上。
                <control required="yes" node="password"  type="password" id="password" placeholder="密码，6个字及以上">
                            // 单个控件的设定：
                            // require： 是否必填项
                            // node:  编辑时，读取现有值的键名
                            // id:  form中输入控件的id
                            // type: 控件类型：
                                     text ，hidden ， password ，select，checkbox，textarea， imguploader，richtext-editor，
                                     choice-box，date-selector， bit-setter，tree-link-selector， tree-editor，address-picker，
                                     db-selector，item-list-selector，item-editor 。。。
                            // placeholder: 控件提示文字

                    <validator type="regExp" msg="必须为6个字及以上25个字以下,英文，数字，横线，下划线">
                        //验证器：
                        //type： regExp | expression  -- 正则验证 | 表达式直接执行验证
                        <![CDATA[^\s*[a-zA-Z0-9\-_]{6,25}\s*$]]>
                             //cdata里面的文字，就是验证用的正则代码或者逻辑表达式js代码
                             //如果是表达式验证：里面就是 逻辑结果表达式。
                                             比如: $('#text1').val()==5
                                             注意：请使用单引号。
                    </validator>
                </control>

            </controls>
         </item>

*/
function j79FormBuilder(params) {

    this.nodeName = 'item';
    this.uiForm = params.uiForm;
    this.urlXML = params.urlXML;
    this.data = params.data || null;
    this.actionFinished = params.actionFinished;
    this.flagEdit = params.flagEdit || false;
    this.flagView=params.flagView || false;
    /*
     this.ctrSettings=new Array(


     {//text control
     name       :'text',
     element    :'input',
     attributes :new Array({key:'type', value:'text'})
     },

     {//text control
     name       :'hidden',
     element    :'input',
     attributes :new Array({key:'type', value:'hidden'})
     },


     {//db-selector
     name       :'db-selector',
     element    :'div',
     class      :'db-selector',

     }



     );*/

    //this.valueGenerators=params.valueGenerators;


}//-/


j79FormBuilder.prototype = {


    /**
     *  ini
     */
    ini: function (flagViewOrNot) {

        var selfObj = this;
        console.log(this.urlXML);

        if(typeof flagViewOrNot =='undefined'){
            flagViewOrNot=selfObj.flagView;
        }

        //读取xml，初始化
        $.get(this.urlXML, function (data) {
            console.log('got xml file');
            /*console.log('FORM-XML=========');
            console.log(data);*/

            selfObj.viewForm(data, selfObj.data, selfObj.flagEdit, flagViewOrNot);
        });

    },//-/ini

    setStringByData:function(strValue, data){
        var resultStr = strValue;
        var reg;
        var v;
        for (var p in data) { // 方法
            if (typeof(data[p]) != "function") {
                reg = new RegExp("\\[#" + p + "#\\]", "g");
                v = data[p] == null ? '' : data[p];
                resultStr = resultStr.replace(reg, v);
            }
        }
        return resultStr;

    },//-/


    /**
     *  viewForm
     *  @param {xml}  xmlData   : form setting xml.
     *  @param {obj}  valueData : initial data in obj format. attribute name is key name.
     *  @param {bool} flagEdit  : flag to define action as edit or addnew.
     *                            if not exists, then decide by valueData is empty or not.
     *                                           empty valueData means add.
     *  disalbe-edit : only view when edit.
     *
     */
    viewForm: function (xmlData, valueData, flagEdit, flagView) {
        var selfObj = this;

        //console.log(xmlData);


        var startXML = $(xmlData).children()[0];


        if (typeof flagEdit == 'undefined') {

            if (typeof  selfObj.flagEdit == 'undefined') {
                flagEdit = $.isEmptyObject(valueData) ? false : true;
            } else {
                flagEdit = selfObj.flagEdit;
            }


        }

        console.log('this form is ' + (flagEdit == true ? 'Edit' : 'Addnew'));

        var preLoadJs = new Array(j79App.pagePathPrefix+'/js/lib/j79/form.ini.js');//array of js need pre-loaded.


        var $flagStartFormGroup = true;
        var $uiItem = $('<div class="form-group"></div>');
        //console.log("get here");


        $(this.uiForm).empty();


        //console.log(startXML);
        $(startXML).children(this.nodeName).each(function (i) {


            //check whether current item is seperator item:
            var flagSep = $(this).attr('sep') ? true : false;
            if (flagSep) {//if seperator item:

                var hrTitle = ($(this).attr('title') || '') == '' ? '' : $(this).attr('title');
                var uiStr = hrTitle != '' ? '<h3>' + hrTitle + '</h3>' : '<hr/>';

                $uiItem = $(uiStr);
                $uiItem.appendTo($(selfObj.uiForm));
                return;

            }

            //检查是否另起一行，form-group：
            if ($(this).attr('lineEnd')) {
                $flagStartFormGroup = true;
                $uiItem = $('<div class="form-group"></div>');
            }

            //des:
            if($(this).children('des').length>0){
                var ctrDes = $(this).children('des')[0];
                ctrDes = ctrDes ? $(ctrDes).text() : '';
                //ctrDes = ctrDes.replace(/\s/g, '');

                $('<div class="form-des">'+ctrDes+'</div>').appendTo($uiItem);
            }



            //label:
            if ($(this).children('label').length > 0) {
                var itemLabel = $(this).children('label');
                var labelClass = $(itemLabel).attr('class');
                var labelTitle = $(itemLabel).text();

                var $uiLabel = $('<label class="' + labelClass + '">' + labelTitle + '</label>');
                $uiLabel.appendTo($uiItem);
            }


            //controls
            var ctrList = $(this).children('controls');
            var ctrListClass = $(ctrList).attr('class') || '';


            $uiCtrList = $('<div class="' + ctrListClass + '"></div>');

            $(ctrList).children('text').each(function (i) {
                //var ctrId = $(this).
               var htmlText= $(this).text();
                $('<div class="form-control-static">'+htmlText+'</div>' ).appendTo($uiCtrList);
            });





            $(ctrList).children('control').each(function (i) {

                var CUR_ITEM=this;
                var ctrId = $(this).attr('id');
                var ctrType = $(this).attr('type');
                var strRequire = $(this).attr('required') ? 'required' : '';

                if (strRequire != '') {

                    $uiItem.find('label').append('<b class="must-fill" title="必填项！"></b>');
                }

                var getAttr=function(attrName){
                    return ($(CUR_ITEM).attr(attrName) || '') == '' ? '' : ' '+attrName+'="' + $(CUR_ITEM).attr(attrName) + '" ';
                };


                var desText = ($(this).attr('placeholder') || '') == '' ? '' : $(this).attr('placeholder');

                var ctrPlaceHolder = desText == '' ? '' : 'placeholder="' + desText + '"';

                var ctrTitle = ' title="' + desText + '" ';


                //validator
                var ctrValidtor = $(this).children('validator')[0];

                strValidtor = ctrValidtor ? $(ctrValidtor).text() : '';
                strValidtor = strValidtor.replace(/\s/g, '');





                if(ctrValidtor && $(ctrValidtor).attr('type')=='regExp'){
                    strValidtor = strValidtor == '' ? '' : 'validator="' + strValidtor + '"';

                }else if(ctrValidtor && $(ctrValidtor).attr('type')=='expression'){
                    strValidtor = strValidtor == '' ? '' : 'validator-expression="' + strValidtor + '"';

                }
                strValidtorMsg = $(ctrValidtor).attr('msg') ? 'validator-msg="' + $(ctrValidtor).attr('msg') + '"' : '';





                //data-saver:
                var dataSaverId = ($(this).attr('data-saver') || '') == '' ? '' : $(this).attr('data-saver');

                var dataSaver = dataSaverId == '' ? '' : 'data-saver="' + dataSaverId + '"';


                var valueDefault = ($(this).attr('default') || '') == '' ? '' : $(this).attr('default');

                $ctrItem = null;

                //get current field
                var curField = $(this).attr('node') || '';

                //value-type
                var valueTypeTitle = ($(this).attr('value-type') || '') == '' ? '' : $(this).attr('value-type');
                var valueType = valueTypeTitle != '' ? ' value-type="' + valueTypeTitle + '" ' : '';


                //valueConstant  - 恒量

                var valueConstant=$(this).attr('value-constant') || '';


                //get current value
                var curValue = valueData && curField != '' ? valueData[curField] : '';
                curValue = typeof (curValue) == 'undefined' || curValue === null ? '' : curValue;

                if(valueConstant!=''){
                    curValue=valueConstant;
                }

                if (valueTypeTitle.toLowerCase() == 'json' && typeof(curValue) == 'object') {
                    console.log('[JSON]' + curField);
                    curValue = $.toJSON(curValue);
                } else {
                    curValue = curValue.toString();
                }


                var flagStatic = $(this).attr('disable-edit') ? true : false;

                var flagLocked=  $(this).attr('lock-edit') ? true : false;

                var strDisalbed = ''; //用于设置控件disabled
                var strFormInput = ' form-input '; //用于控件属性，表示submit的时候读取。

                //如果存在submit-ignore，那么不设置“form-input”
                if(typeof $(this).attr('submit-ignore')!=='undefined'){
                    strFormInput=' ';
                }


                if ((flagEdit && flagStatic)) {
                    strDisalbed = ' disabled="disabled" ';
                    strFormInput = '';
                }

                if(flagEdit && flagLocked){
                    strDisalbed = ' disabled="disabled" ';
                }




                var viewClass= flagView ? ' view-static' :"";




                switch (ctrType.toLowerCase()) {


                    //text, hidden ctr:
                    case 'text':
                    case 'hidden':

                        var strDefault = curValue != '' ? ' value="' + curValue + '" ' : ' value="' + valueDefault + '" ';


                        $ctrItem = $('<input class="form-control" ' + ctrTitle + strDisalbed + strFormInput + ' ' + valueType + ' ' + strRequire + ' type="' + ctrType.toLowerCase() + '" name="' + ctrId + '" id="' + ctrId + '" ' + ctrPlaceHolder + '  ' + strValidtor + ' ' + strValidtorMsg + '  ' + strDefault + '/>');


                        if(flagView && ctrType.toLowerCase()!='hidden'){
                            $ctrItem=$('<p class="form-control-static">'+curValue+'</p>');


                        }




                        break;


                    //password:
                    case 'password':

                        var strDefault = '';


                        $ctrItem = $('<input class="form-control" ' + strFormInput + ' ' + ctrTitle + strRequire + ' type="password" name="' + ctrId + '" id="' + ctrId + '" ' + ctrPlaceHolder + '  ' + strValidtor + ' ' + strValidtorMsg + '  />');

                        if (flagEdit && flagStatic) {
                            $ctrItem = $('<p class="form-control-static">' + curValue + '</p>');
                        }

                        if(flagView){
                            $ctrItem=$('<p class="form-control-static">***</p>');
                        }


                        break;




                    //select:
                    case 'select':

                        if(flagView){
                            strDisalbed=' disabled="disabled" ';
                        }

                        $ctrItem = $('<select class="form-control '+viewClass+'" ' + strFormInput + ' ' + strDisalbed + ctrTitle + strRequire + '  name="' + ctrId + '" id="' + ctrId + '" ' + ctrPlaceHolder + '  ' + strValidtor + ' ' + strValidtorMsg + '  />');

                        optValues = $(this).children('values')[0];

                        if ($(optValues).attr('generator')) {

                            var genCode = $(optValues).attr('generator');
                            var optStr = eval(genCode);
                            if (optStr != '') {
                                optStr = optStr.replace('value="' + curValue + '"', 'value="' + curValue + '" selected ');
                                $(optStr).appendTo($ctrItem);
                                break;
                            }

                            break;
                        }

                        $(optValues).children('option').each(function (i) {

                            if (curValue != '') {
                                if (curValue == $(this).attr('value')) {
                                    $(this).attr('selected', 'selected');
                                } else {
                                    $(this).removeAttr('selected');
                                }

                            }

                            var optStr = $(this).prop("outerHTML");
                            //console.log( optStr);
                            $(optStr).appendTo($ctrItem);

                        });




                        break;


                    //json-value-setter
                    case 'json-value-setter':

                        //var strDefault= curValue !='' ? ' value="'+curValue+'" ' : ' value="'+valueDefault+'" ';






                        ctrItemStr = '<div class="json-value-setter  '+viewClass+'" '+strDisalbed + ctrTitle + dataSaver+ ' id="' + ctrId +'"  ' +
                            getAttr('json-struct-def')+
                            getAttr('sub-key-name')+
                            getAttr('flag-transfer-boolean')+

                            '>';


                        ctrItemStr +='</div>';

                        $ctrItem = $(ctrItemStr);


                        //preload js
                        j79.addUnique(preLoadJs, '/js/plugin/j79.json.value.setter.js');


                        break;



                    //checkbox
                    case 'checkbox':

                        //var strDefault= curValue !='' ? ' value="'+curValue+'" ' : ' value="'+valueDefault+'" ';


                        ctrItemStr = '<div class="ctr-checkbox-list'+viewClass+'">';

                        optValues = $(this).children('values')[0];
                        $(optValues).children('value').each(function (i) {
                            flagChecked = '';
                            if (valueDefault == $(this).text()) {
                                flagChecked = ' checked ';
                            }
                            var curValueStr = ' value="' + $(this).text() + '" ';
                            ctrItemStr += '<span><input  ' + strFormInput + strDisalbed + ctrTitle + ' ' + valueType + ' ' + strRequire + ' type="' + ctrType.toLowerCase() + '" name="' + ctrId + '" id="' + ctrId + '"   ' + curValueStr + ' ' + flagChecked + '/>' + $(this).attr('label') + '</span>';

                        });
                        ctrItemStr +='</div>';

                        $ctrItem = $(ctrItemStr);

                        break;

                    //radio
                    case 'choice-box':

                        //var strDefault= curValue !='' ? ' value="'+curValue+'" ' : ' value="'+valueDefault+'" ';


                        ctrItemStr = '<div class="choice-box '+viewClass+'" ' +  strDisalbed + ctrTitle + dataSaver + '" id="' + ctrId +'"><ul>';

                        optValues = $(this).children('values')[0];
                        $(optValues).children('value').each(function (i) {
                            flagChecked = '';
                            if (valueDefault == $(this).text()) {
                                flagChecked = ' class="active" ';
                            }
                            var curValueStr = ' value="' + $(this).text() + '" ';
                            ctrItemStr += '<li '+ flagChecked +curValueStr + '>' + $(this).attr('label') + '</li>';

                        });
                        ctrItemStr +='</ul></div>';
                        $ctrItem = $(ctrItemStr);

                        //preload js
                        j79.addUnique(preLoadJs, '/js/plugin/j79.choice.box.js');

                        break;


                    case 'date-selector':

                        //var strDefault= curValue !='' ? ' value="'+curValue+'" ' : ' value="'+valueDefault+'" ';

                        var flagIncludeTime = $(this).attr('include-time') ? ' include-time="include-time" ':'';



                        ctrItemStr = '<div class="date-selector '+viewClass+'" ' +  strDisalbed + ctrTitle + flagIncludeTime + dataSaver + '" id="' + ctrId +'">';


                        ctrItemStr +='</div>';
                        $ctrItem = $(ctrItemStr);

                        //preload js
                        j79.addUnique(preLoadJs, '/js/plugin/j79.date.selector.js');

                        break;

                    //textarea
                    case 'textarea':

                        var strDefault = curValue != '' ? curValue  :  valueDefault ;

                        var ctrHeight = ($(this).attr('height') || '') == '' ? '' : ' style="height:' + $(this).attr('height') + 'px" ';

                        $ctrItem = $('<textarea class="form-control" ' + valueType + ' ' + ctrHeight + ' form-input ' + strRequire + ctrTitle + '  name="' + ctrId + '" id="' + ctrId + '" ' + ctrPlaceHolder + '  ' + strValidtor + ' ' + strValidtorMsg + '  >' + strDefault + '</textarea>');

                        if (flagEdit && flagStatic) {
                            $ctrItem = $('<p class="form-control-static">' + strDefault + '</p>');
                        }

                        if(flagView && valueTypeTitle!='json'){
                            $ctrItem = $('<p class="form-control-static">' + strDefault + '</p>');
                        }

                        break;
                    //bit-setter:
                    case 'bit-setter':

                        $ctrItem = $('<div class="bit-setter '+viewClass+'" '  + ctrTitle + '  id="' + ctrId + '" ' + ctrPlaceHolder+'  ' + dataSaver+'  >' + curValue + '</div>');
                        j79.addUnique(preLoadJs, '/js/plugin/j79.bit.setter.js');

                        optValues = $(this).children('values')[0];

                        //option is generated:
                        if ($(optValues).attr('generator')) {

                            var genCode = $(optValues).attr('generator');
                            var optStr = eval(genCode);
                            if (optStr != '') {
                                optStr = optStr.replace('value="' + curValue + '"', 'value="' + curValue + '" selected="selected" ');
                                $(optStr).appendTo($ctrItem);
                                break;
                            }

                            break;
                        }

                        //option is listed
                        $(optValues).children('option').each(function (i) {

                            if (curValue != '') {
                                if (curValue == $(this).attr('value')) {
                                    $(this).attr('selected', 'selected');
                                } else {
                                    $(this).removeAttr('selected');
                                }

                            }

                            var optStr = $(this).prop("outerHTML");

                            $(optStr).appendTo($ctrItem);

                        });

                        break;

                    //imguploader:
                    case 'imguploader':


                            var dataSavePath = ($(this).attr('save-path') || '') == '' ? '' : 'save-path="' + $(this).attr('save-path') + '"';
                            dataSavePath=selfObj.setStringByData(dataSavePath, valueData);

                            var thumbW = ($(this).attr('thumbnail-width') || '') == '' ? '' : 'thumbnail-width="' + $(this).attr('thumbnail-width') + '"';

                            var thumbH = ($(this).attr('thumbnail-height') || '') == '' ? '' : 'thumbnail-height="' + $(this).attr('thumbnail-height') + '"';

                            var compW = ($(this).attr('data-compress-width') || '') == '' ? '' : 'data-compress-width="' + $(this).attr('data-compress-width') + '"';
                            var compH = ($(this).attr('data-compress-height') || '') == '' ? '' : 'data-compress-height="' + $(this).attr('data-compress-height') + '"';

                            var fileMaxCount = ($(this).attr('file-max-count') || '') == '' ? '' : 'file-max-count="' + $(this).attr('file-max-count') + '"';


                            var flagMulti = ($(this).attr('data-multi') || '') == '' ? '' : 'data-multi="data-multi"';

                            var ctrTitle = ($(this).attr('placeholder') || '') == '' ? '' : 'title="' + $(this).attr('placeholder') + '"';




                            /*$ctrItem = $('<div class="img-uploader'+viewClass+'" ' + strDisalbed + ctrTitle +
                                dataSaver + ' ' + dataSavePath + ' ' + flagMulti + ' ' + fileMaxCount + ' ' + compW + ' ' + compH + ' ' + thumbW + ' ' + thumbH + ' ' + ctrTitle + ' id="' + ctrId + '"  name="' + ctrId + '"></div>');

                            //preload js
                            j79.addUnique(preLoadJs, '/js/plugin/j79.dragsort.js');
                            j79.addUnique(preLoadJs, '/js/plugin/webuploader/webuploader.js');
                            j79.addUnique(preLoadJs, '/js/plugin/j79.webuploader.ui.js');*/


                        $ctrItem = $('<div class="j79-img-uploader' + viewClass + '" ' + strDisalbed + ctrTitle +
                            getAttr('data-save-path')+
                            getAttr('data-save-single-value')+
                            dataSaver + ' ' + dataSavePath + ' ' + flagMulti + ' ' + fileMaxCount + ' ' + compW + ' ' + compH + ' ' + thumbW + ' ' + thumbH + ' ' +  ' id="' + ctrId + '"  name="' + ctrId + '"></div>');
                        //preload js
                        j79.addUnique(preLoadJs, '/js/plugin/j79.img.uploader.js');



                        break;

                    //banner-editor
                    case 'banner-editor':

                        var ctrTitle = ($(this).attr('placeholder') || '') == '' ? '' : 'title="' + $(this).attr('placeholder') + '"';

                        $ctrItem = $('<div class="banner-editor' + viewClass + '" ' + strDisalbed + ctrTitle +
                            getAttr('data-save-path')+
                            getAttr('data-max-count')+
                            getAttr('save-path')+
                            getAttr('data-ctr-type')+
                            getAttr('data-compress-width')+
                            getAttr('data-compress-height')+
                            dataSaver + ' ' +  ' id="' + ctrId + '"  name="' + ctrId + '"></div>');
                        //preload js
                        j79.addUnique(preLoadJs, '/js/plugin/j79.img.uploader.js');
                        j79.addUnique(preLoadJs, '/js/plugin/j79.banner.editor.js');




                        break;
                    //tree-link-selector
                    case 'tree-link-selector':

                        var ctrTitle = ($(this).attr('placeholder') || '') == '' ? '' : 'title="' + $(this).attr('placeholder') + '"';

                        $ctrItem = $('<div class="tree-link-selector ' + viewClass + '" ' + strDisalbed + ctrTitle +
                            getAttr('data-save-path')+
                            getAttr('data-max-count')+
                            getAttr('data-xml')+
                            getAttr('link-url')+

                            dataSaver + ' ' +  ' id="' + ctrId + '"  name="' + ctrId + '"></div>');
                        //preload js

                        j79.addUnique(preLoadJs, '/js/plugin/j79.tree.selector.js');
                        j79.addUnique(preLoadJs, '/js/plugin/j79.tree.link.selector.js');



                        break;
                    //richtext-editor:
                    case 'richtext-editor':

                        if(flagView){
                            $ctrItem = $('<p class="form-control-static">' + curValue + '</p>');
                        }else{

                            var strStyle = ($(this).attr('height') || '') == '' ? '' : 'style="height:' + $(this).attr('height') + 'px"';
                            var strToolbarIdx = ($(this).attr('toolbar-set-idx') || '') == '' ? '' : 'toolbar-set-idx="' + $(this).attr('toolbar-set-idx') + '"';
                            var strImgPath = ($(this).attr('img-path') || '') == '' ? '' : 'img-path="' + $(this).attr('img-path') + '"';
                            var strImgMaxSize = ($(this).attr('img-max-size') || '') == '' ? '' : 'img-max-size="' + $(this).attr('img-max-size') + '"';

                            var ctrTitle = ($(this).attr('placeholder') || '') == '' ? '' : 'title="' + $(this).attr('placeholder') + '"';


                            $ctrItem = $('<div class="richtext-editor" ' + strDisalbed + ' ' + ctrTitle + ' ' + strStyle + ' ' + strImgPath + ' ' + strImgMaxSize + ' ' + dataSaver + '  ' + strToolbarIdx + ' id="' + ctrId + '"  name="' + ctrId + '" ' + ctrTitle + '></div>');

                            //preload js

                            j79.addUnique(preLoadJs, '/js/plugin/umeditor/umeditor.js');
                            j79.addUnique(preLoadJs, '/js/plugin/j79.umeditor.ui.js');
                        }






                        break;

                    //tree-selector
                    case 'tree-selector':

                        var viewDepth = ($(this).attr('view-depth') || '') == '' ? '' : ' view-depth="' + $(this).attr('view-depth') + '" ';

                        var flagAllOpened=  ($(this).attr('all-opened') || '') == '' ? '' : ' all-opened="' + $(this).attr('all-opened') + '" ';


                        var strUrlXML = ($(this).attr('data-xml') || '') == '' ? '' : ' data-xml="' + $(this).attr('data-xml') + '" ';
                        strUrlXML=selfObj.setStringByData(strUrlXML, valueData);

                        var strEditorLink = ($(this).attr('editor-link') || '') == '' ? '' : ' editor-link="' + $(this).attr('editor-link') + '" ';
                        strEditorLink=selfObj.setStringByData(strEditorLink, valueData);



                        $ctrItem = $('<div class="tree-selector '+viewClass+'" ' + strDisalbed + ctrTitle + strUrlXML + strEditorLink +flagAllOpened + viewDepth + ' ' + dataSaver +
                            getAttr('data-expend-mode')+
                            '  id="' + ctrId + '"  name="' + ctrId + '" ' + ctrTitle + '></div>');


                        //preload js
                        j79.addUnique(preLoadJs, '/js/plugin/j79.tree.selector.js');


                        break;

                    //tree-editor
                    case 'tree-editor':




                        var strUrlXML = ($(this).attr('data-xml') || '') == '' ? '' : ' data-xml="' + $(this).attr('data-xml') + '" ';

                        strUrlXML=selfObj.setStringByData(strUrlXML, valueData);

                        var strAllowIdEdit= ($(this).attr('data-allow-id-edit') || '') == '' ? '' : ' data-allow-id-edit="yes" ';

                        $ctrItem = $('<div class="tree-editor" ' + strDisalbed + ctrTitle + strUrlXML+ strAllowIdEdit +  ' ' + dataSaver + '  id="' + ctrId + '"  name="' + ctrId + '" ></div>');


                        //preload js
                        j79.addUnique(preLoadJs, '/js/plugin/j79.tree.editor.js');


                        break;



                    //express-fee-setter
                    case 'express-fee-setter':


                        var strDataTypeSaver = ($(this).attr('data-type-saver') || '') == '' ? '' : '  data-type-saver="' + $(this).attr('data-type-saver') + '"  ';


                        $ctrItem = $('<div class="express-fee-setter '+viewClass+'" ' + strDisalbed +   ' ' + dataSaver + strDataTypeSaver+ '  id="' + ctrId + '"  name="' + ctrId + '" ' + ctrTitle + '></div>');




                        //preload js
                        j79.addUnique(preLoadJs, '/js/plugin/j79.express.fee.setter.js');


                        break;


                    //address-picker
                    case 'address-picker':

                        var startLvl = ($(this).attr('start-lvl') || '') == '' ? '' : ' start-lvl="' + $(this).attr('start-lvl') + '" ';

                        var dataSaver1 = ($(this).attr('data-saver-lv1') || '') == '' ? '' : ' data-saver-lv1="' + $(this).attr('data-saver-lv1') + '" ';
                        var dataSaver2 = ($(this).attr('data-saver-lv2') || '') == '' ? '' : ' data-saver-lv2="' + $(this).attr('data-saver-lv2') + '" ';
                        var dataSaver3 = ($(this).attr('data-saver-lv3') || '') == '' ? '' : ' data-saver-lv3="' + $(this).attr('data-saver-lv3') + '" ';
                        var dataSaver4 = ($(this).attr('data-saver-lv4') || '') == '' ? '' : ' data-saver-lv4="' + $(this).attr('data-saver-lv4') + '" ';


                        $ctrItem = $('<div class="address-picker '+viewClass+'" ' + strDisalbed + dataSaver1 + dataSaver2 + dataSaver3 + dataSaver4 + ' ' + startLvl + '  id="' + ctrId + '"  name="' + ctrId + '" ' + ctrTitle + '></div>');


                        //preload js
                        j79.addUnique(preLoadJs, '/js/plugin/j79.address.picker.js');


                        break;

                    //item-editor
                    case 'item-editor':


                        $ctrItem = $('<div class="item-editor '+viewClass+'" ' + strDisalbed + ctrTitle + dataSaver + '  id="' + ctrId + '"  name="' + ctrId + '"  ' + ctrPlaceHolder + '></div>');


                        //preload js
                        j79.addUnique(preLoadJs, '/js/plugin/j79.item.editor.js');


                        break;

                    //optionEditor
                    //item-editor
                    case 'schedule-picker':


                        $ctrItem = $('<div class="schedule-picker '+viewClass+'" ' + strDisalbed + ctrTitle + dataSaver + '  id="' + ctrId + '"  name="' + ctrId + '"  ' + ctrPlaceHolder + '></div>');


                        //preload js
                        j79.addUnique(preLoadJs, '/js/plugin/j79.schedule.picker.js');


                        break;

                    //optionEditor
                    case 'option-editor':

                        var optList = ($(this).attr('option-list') || '') == '' ? '' : 'option-list="' + $(this).attr('option-list') + '"';
                        var btnOpenLabel = ($(this).attr('btn-open-label') || '') == '' ? '' : 'btn-open-label="' + $(this).attr('btn-open-label') + '"';

                        var targetLabel = ($(this).attr('target-label') || '') == '' ? '' : 'target-label="' + $(this).attr('target-label') + '"';

                        $ctrItem = $('<div class="option-editor '+viewClass+'" ' + strDisalbed + ctrTitle + optList + ' ' + btnOpenLabel + ' ' + targetLabel + ' default-value="' + valueDefault + '" ' + dataSaver + '  id="' + ctrId + '"  name="' + ctrId + '" ' + ctrTitle + '></div>');

                        //preload js
                        j79.addUnique(preLoadJs, '/js/plugin/j79.option.editor.js');

                        break;


                    //db-selector
                    case 'db-selector':

                        var dbUrl = ($(this).attr('data-url') || '') == '' ? '' : 'db-url="' + $(this).attr('data-url') + '"';
                        dbUrl=selfObj.setStringByData(dbUrl, valueData);

                        console.log('db-selector data:');
                        console.log(valueData);
                        console.log('db-selector db url:');
                        console.log(dbUrl);

                        var textName = ($(this).attr('text-name') || '') == '' ? '' : 'text-name="' + $(this).attr('text-name') + '"';
                        var fieldTitle = ($(this).attr('field-title') || '') == '' ? '' : 'field-title="' + $(this).attr('field-title') + '"';
                        var fieldIdx = ($(this).attr('field-idx') || '') == '' ? '' : 'field-idx="' + $(this).attr('field-idx') + '"';
                        var urlAddnew = ($(this).attr('url-addnew') || '') == '' ? '' : 'url-addnew="' + $(this).attr('url-addnew') + '"';

                        urlAddnew=selfObj.setStringByData(urlAddnew, valueData);


                        $ctrItem = $('<div class="db-selector '+viewClass+'" ' + strDisalbed + ctrTitle +
                            dataSaver + ' ' + dbUrl + ' ' + ctrPlaceHolder + ' id="' + ctrId + '" ' + textName + ' ' + fieldTitle + ' ' + fieldIdx + ' ' + urlAddnew + ' name="' + ctrId + '"></div>');

                        //preload js

                        j79.addUnique(preLoadJs, '/js/plugin/j79.db.selector.js');

                        break;

                    //pro-list-selector
                    case 'pro-list-selector':




                        $ctrItem = $('<div class="pro-list-selector '+viewClass+'" ' + strDisalbed + ctrTitle + dataSaver +
                            getAttr('data-save-path')+
                            getAttr('data-ptype')+
                            getAttr('data-category')+
                            getAttr('data-sort')+
                            getAttr('data-div')+
                            getAttr('data-shop')+
                            getAttr('data-amount')+
                            '  id="' + ctrId + '"  name="' + ctrId + '"  ' + ctrPlaceHolder + '></div>');


                        //preload js
                        j79.addUnique(preLoadJs, '/js/plugin/j79.pro.list.selector.js');


                        break;

                    //pro-list-selector
                    case 'item-list-selector':


                        var innerHtml =$(this).children('inner_html').length>0 ? $(this).children('inner_html')[0]: false;

                        var ctrInnerHtml=innerHtml ? $(innerHtml).text() : '';


                        $ctrItem = $('<div class="item-list-selector '+viewClass+'" ' + strDisalbed + ctrTitle + dataSaver +
                            getAttr('data-save-path')+
                            getAttr('data-key-items')+
                            getAttr('data-target')+
                            getAttr('data-params')+
                            getAttr('data-amount')+
                            getAttr('data-category-xml')+
                            '  id="' + ctrId + '"  name="' + ctrId + '"  ' + ctrPlaceHolder + '>'+ctrInnerHtml+'</div>');


                        //preload js
                        j79.addUnique(preLoadJs, '/js/plugin/j79.tree.selector.js');
                        j79.addUnique(preLoadJs, '/js/plugin/j79.item.list.selector.js');


                        break;

                }


                if ($ctrItem) {
                    $ctrItem.appendTo($uiCtrList);
                }

            });//-/control loop end.
            if($uiCtrList.children().length>0){

                $uiCtrList.appendTo($uiItem);
            }

            if ($flagStartFormGroup == true) {	// 判断是否为一个form-group的开始，是则加入列表
                $flagStartFormGroup = false;
                $uiItem.appendTo($(selfObj.uiForm));
            }


            //

        });


        //load pre-load js
        var jsAmount = preLoadJs.length;
        var jsCurIdx = 0;

        var actionPreJsLoaded = function () {
            if (jsCurIdx < jsAmount - 1) {
                jsCurIdx++;
                console.log('start load js:' +  j79App.pagePathPrefix+ preLoadJs[jsCurIdx]);
                $.getScript(j79App.pagePathPrefix+ preLoadJs[jsCurIdx], actionPreJsLoaded);

            } else {//when js loading finished, call  actionFinishJsLoading:
                selfObj.actionFinishJsLoading();
            }
        };
        if (jsAmount > 0) {
            console.log('start load js:' + preLoadJs[0]);
            $.getScript(preLoadJs[0], actionPreJsLoaded);

        }


    },//-/viewForm

    /**
     *  actionPreJsLoaded
     */
    actionFinishJsLoading: function () {
        console.log('form setup finished. try to call bind action...');

        //setup on-blur validating
        /*$('body').delegate('input[form-input]','blur', null, function(e){
            $(this).validate();
        });*/


       /* $('[form-input]').blur(function () {
            $(this).validate();
        });*/

        if (this.actionFinished) {
            this.actionFinished();
        }

    },//-/actionFinishJsLoading

    /*viewData: function(formData){






     },//-/isValid
     */


};//-/




