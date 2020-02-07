/**
 * Created by jinrong on 2016/12/1.
 */

j79.loadCSS("/css/treeeditor.css");

(function ($) {

    $.fn.treeEditor = function (settings) {

        var node_name = "item"; //xml文档中，节点的标签名称
        var node_root_name = "items"; //xml文档中，root节点的标签名称
        var node_text_name = "name"; //xml文档中，节点显示名称的属性名称
        var node_value_name = "id"; //xml文档中，节点值的属性名称

        var LIST_HTML='ol';    //列表是ol还是ul。


        var SELF = this;
        var data_saver = this.attr('data-saver');

        if(data_saver!=''){
            $('#'+data_saver).hide();
        }

        var xml_url = this.attr('data-xml');

        var allowIdEdit= this.attr('data-allow-id-edit') ? true :false;  //whether lock node id editing.

        var lockIdEditStr= allowIdEdit==false ? ' disabled="disabled" ': '';

        var depthLimit = 0;

        SELF.XMLData = {};
        SELF.curValue = null;   //current node value.
        SELF.selectId = 0;
        SELF.selected = null; //point to selected .node
        var LEVEL_WIDTH=2; // width of each node LEVEL.
        var MAX_DEPTH=5;   // max 分类深度
        var MAX_PER_DEPTH=99; //每级分类个数max

        var rootId;     //xml root id
        var rootName;   //xml root name




        /**
         *  iniUI
         */
        var ini = function () {
            $('<div class="tree-container"></div>').appendTo(SELF);

            /*$('<div class="node-editor-board">' +
             '<form class="form-horizontal">' +

             '<div class="form-group">' +
             '   <label class="col-md-2 control-label">节点ID</label>' +
             '   <div class="col-md-10">' +
             '       <input class="form-control" form-input="form-input" title="节点ID" required="" type="text" name="node_id" id="node_id" placeholder="节点ID,请输入纯数字，每2位，表示一个节点深度。" >' +
             '   </div>' +
             '</div>' +

             '<div class="form-group">' +
             '   <label class="col-md-2 control-label">节点标题文字</label>' +
             '   <div class="col-md-10">' +
             '       <input class="form-control" form-input="form-input" title="节点ID" required="" type="text" name="node_name" id="node_name" placeholder="节点标题文字，10个字以内。" >' +
             '   </div>' +
             '</div>' +

             '<div class="form-group">' +
             '   <label class="col-md-2 control-label"></label>' +
             '   <div class="col-md-10">' +
             '       <a  class="btn btn-primary btn-save">更改/添加</a>' +
             '   </div>' +
             '</div>' +

             '</form>'+
             '</div>'
             ).
             appendTo(SELF);*/

            $('<div class="toolbar">' +
                '<a class="btn btn-default btn-add"><i class="glyphicon glyphicon-plus"></i> 添加节点</a> ' +
                '<a class="btn btn-default btn-add-sub"><i class="glyphicon glyphicon-plus-sign"></i> 添加子节点</a> ' +
                '<a class="btn btn-default btn-delete"><i class="glyphicon glyphicon-remove"></i> 删除节点</a> ' +
                '<a class="btn btn-default btn-move-up"><i class="glyphicon glyphicon-arrow-up"></i> 上移</a> ' +
                '<a class="btn btn-default btn-move-down"><i class="glyphicon glyphicon-arrow-down"></i> 下移</a> ' +
                '<button disabled="disabled" class="btn btn-primary btn-save"><i class="glyphicon glyphicon-floppy-disk"></i> 保存更改</button> ' +
                '</div>').appendTo(SELF);

        };

        /**
         * getDepthById
         * 返回一个id是第几级的。1级代表，最大分类。2级代表1级的子分类，以此类推
         * @param idNum
         * @returns {number}
         */
        var getDepthById=function(idNum){
            var modNum;
            var re=1;
            for(var i=1; i<= MAX_DEPTH ; i++){
                modNum= idNum % Math.pow(10,LEVEL_WIDTH*i);
                if(modNum>0){
                    re=MAX_DEPTH-i+1;
                    break;
                }
            }
            return re;
        };//-/

        /**
         * setCurrentNode
         *
         * @param curNode
         */
        var setCurrentNode=function(curNode){
            $(SELF).find('.node').removeClass('selected');
            $(curNode).addClass('selected');
            SELF.selectId = $(curNode).attr('id');
            SELF.selectDepth=1;
            SELF.selected = curNode;
            var curName = $(curNode).text();
            $(SELF).find('.node-editor-board #node_id').val(SELF.selectId);
            $(SELF).find('.node-editor-board #node_name').val(curName);

            //$(curNode).find('.input-node-name').focus();

            var curLi=$(curNode).closest('li');
            if($(curLi).hasClass('closed')){
                $(curLi).removeClass('closed');
                $(curLi).addClass('opened');
            }else if($(curLi).hasClass('opened')){
                $(curLi).removeClass('opened');
                $(curLi).addClass('closed');
            }
        };//-/

        /**
         * saveData
         * save modified xml-data into data-saver by string format.
         */
        var saveData=function(){
            if(data_saver!=''){
                var root=$(SELF).find('.tree-container '+LIST_HTML+':eq(0)');
                var xmlResultStr=readNodeDoc(root);
                xmlResultStr='<?xml version="1.0" encoding="utf-8"?>\n' +
                    '<'+node_root_name+' '+(rootId==null? '' : ' id="'+rootId+'" ' )+(rootName==null? '' : ' name="'+rootName+'" ' )+'  >\n'+
                    xmlResultStr+
                    '</'+node_root_name+'>';
                //console.log(xmlResultStr);
                $('#'+data_saver).val(xmlResultStr);


            }
        };//-/

        /**
         * readNodeDoc
         * read doc of node, and return xml string.
         * @param curNode
         * @returns {string}
         */
        var readNodeDoc=function(curNode){
            var nodeStr='';

            if($(curNode).children('li').length>0){ //has child

                $(curNode).children('li').each(function(i){
                    var curId=$(this).find('.node').attr('id');
                    var curName=$(this).find('.node').attr('name');
                    nodeStr+='<'+node_name+' '+node_value_name+'="'+curId+'" '+node_text_name+'="'+curName+'" ';

                    var curSubList=$(this).find('ul:eq(0)');
                    if(curSubList.length<=0){
                        curSubList=$(this).find('ol:eq(0)');
                    }

                    if(curSubList.length>0){
                        nodeStr+='>\n'+readNodeDoc(curSubList);
                        nodeStr+='</item>\n';
                    }else{
                        nodeStr+='/>\n';
                    }


                });

            }
            return nodeStr;


        };//-/


        /**
         * attachEventHandle
         * attach event handler
         */
        var attachEventHandle = function () {

            //add root:
            $(SELF).delegate('.tree-container .btn-add-root', 'click', null, function (e) {

                $(SELF).find('.tree-container').empty();

                $('<'+LIST_HTML+'>' +
                    '<li>' +
                    '<div class="node" id="100000000" name="节点标题Demo">' +
                    '<input type="text" class="input-node-name" value="节点标题Demo">-' +
                    '<input type="text" class="input-node-id" '+lockIdEditStr+' value="100000000"><em></em>' +
                    '</div>' +
                    '</li>' +
                    '</'+LIST_HTML+'>').appendTo($(SELF).find('.tree-container'));

            });


            //click tree item:
            $(SELF).delegate('.tree-container li .node', 'click', null, function (e) {

                setCurrentNode(this);

            });

            //stop click event when click in input
            $(SELF).delegate('.tree-container li .node input', 'click', null, function (event) {
                event.stopPropagation();
            });


            //stop click event when click in input
            $(SELF).delegate('.tree-container li .node input', 'change', null, function (event) {
                var curNode=$(this).closest('.node');

                $(curNode).attr('id',$(curNode).find('.input-node-id').val() );
                $(curNode).attr('name',$(curNode).find('.input-node-name').val() );

                $(SELF).find('.toolbar .btn-save').removeAttr('disabled');

            });

            //editor btn btn-save
            $(SELF).find('.toolbar .btn-save').click(function (e) {

                saveData();
                $(this).attr('disabled','disabled');
               /* if (SELF.selected) {
                    /!*$(SELF.selected).attr('id', $(SELF).find('.node-editor-board #node_id').val());
                    $(SELF.selected).attr('name', $(SELF).find('.node-editor-board #node_name').val());
                    $(SELF.selected).text($(SELF).find('.node-editor-board #node_name').val());*!/
                    //to do
                }*/

            });

            //delete node:
            $(SELF).find('.toolbar .btn-delete').click(function (e) {

                if (SELF.selected) {

                    $(SELF).find('.toolbar .btn-save').removeAttr('disabled');

                    var curLi = $(SELF.selected).closest('li');
                    $(curLi).remove();
                }
            });


            //move up:
            $(SELF).find('.toolbar .btn-move-up').click(function (e) {
                if (SELF.selected) {

                    $(SELF).find('.toolbar .btn-save').removeAttr('disabled');

                    var curLi = $(SELF.selected).closest('li');
                    var prevLi=$(curLi).prev('li');
                    if(prevLi.length>0){
                        $(curLi).insertBefore(prevLi);

                    }else{
                        alert('无法再往上移动了！');
                    }
                }
            });

            //move up:
            $(SELF).find('.toolbar .btn-move-down').click(function (e) {
                if (SELF.selected) {

                    $(SELF).find('.toolbar .btn-save').removeAttr('disabled');

                    var curLi = $(SELF.selected).closest('li');
                    var prevLi=$(curLi).next('li');
                    if(prevLi.length>0){
                        $(curLi).insertAfter(prevLi);

                    }else{
                        alert('无法再往下移动了！');
                    }
                }
            });

            //add sub node:
            $(SELF).find('.toolbar .btn-add-sub').click(function (e) {
                if (SELF.selected) {

                    $(SELF).find('.toolbar .btn-save').removeAttr('disabled');

                    var curLi = $(SELF.selected).closest('li');

                    //检查是否有子项目
                    var curSubHolder=$(SELF.selected).siblings('ol');
                    if(curSubHolder.length<=0){
                        curSubHolder=$(SELF.selected).siblings('ul');
                    }

                    //当前节点的深度。最左边的，最上级的分类深度为MAX_DEPTH; 最低级别的是1
                    var curDepth=MAX_DEPTH-getDepthById(SELF.selectId)+1;
                    var newId;
                    var newSubLi;

                    console.log('cur sub holder:');
                    console.log(curSubHolder);

                    console.log('current depth:');
                    console.log(curDepth);

                    if(curSubHolder.length<=0){// 没有子项目



                        if(curDepth>1){
                            newId=Number(SELF.selectId)+Math.pow(10,LEVEL_WIDTH*(curDepth-2));
                            curSubHolder= $('<'+LIST_HTML+'><li><div class="node" id="'+newId+'" name="新的子节点"><input type="text" class="input-node-name" value="新的子节点">-<input type="text" class="input-node-id" '+lockIdEditStr+' value="'+newId+'"><em></em></div></li></'+LIST_HTML+'>');
                            $(curSubHolder).insertAfter(SELF.selected);
                            newSubLi=$(curSubHolder).find('li');
                            $(curLi).removeClass('closed');
                            $(curLi).addClass('opened');
                        }else{
                            alert('当前树只支持'+MAX_DEPTH+'级节点，无法创建子节点！');
                            return;
                        }



                    }else{ //已经存在子项目

                        //取得子项目id最高值
                        var allSiblings=$(curSubHolder).children('li');
                        console.log(allSiblings);
                        var idMax=0;
                        allSiblings.each( function(i){

                            var curId=Number($(this).find('.node:eq(0)').attr('id'));
                            if(curId && idMax<curId){
                                idMax=curId;
                            }
                        });

                        //console.log(idMax);

                        //计算子项目新的id
                        newId=idMax+Math.pow(10,LEVEL_WIDTH*(curDepth-2));

                        //添加子项目
                        newSubLi= $('<li><div class="node" id="'+newId+'" name="新的子节点"><input type="text" class="input-node-name" value="新的子节点">-<input type="text" class="input-node-id" '+lockIdEditStr+' value="'+newId+'"><em></em></div></li>');
                        $(newSubLi).appendTo(curSubHolder);
                        $(curLi).removeClass('closed');
                        $(curLi).addClass('opened');

                    }

                    //set current:
                    $(SELF.selected).removeClass('selected');
                    setCurrentNode($(newSubLi).find('.node'));
                   // $(newSubLi).find('.input-node-name').focus();



                }
            });

            //add new node:
            $(SELF).find('.toolbar .btn-add').click(function (e) {
                if (SELF.selected) {

                    $(SELF).find('.toolbar .btn-save').removeAttr('disabled');

                    var curLi = $(SELF.selected).closest('li');

                    var siblingId=0;
                    var siblingLi=$(curLi).prev('li');
                    if(siblingLi.length<=0){
                        siblingLi=$(curLi).next('li');
                    }


                    //取得相邻节点id：
                    if(siblingLi.length>0){
                        siblingId=$(siblingLi).find('.node:eq(0)').attr('id');
                    }else{
                        siblingLi=$(curLi).next('li');
                        if(siblingLi.length>0){
                            siblingId=$(siblingLi).find('.node:eq(0)').attr('id');
                        }
                    }

                    var idDelta=SELF.selectId;  //只有一个节点时，默认取当前id
                    var idMax=SELF.selectId;    //只有一个节点时，默认取当前id

                    if(siblingLi.length>0){//siblings exits:
                        siblingId=$(siblingLi).find('.node:eq(0)').attr('id');
                        idDelta= Math.abs( SELF.selectId- siblingId);
                        //取得当前深度，id的最高值。
                        var allSiblings=$(curLi).siblings('li');
                        allSiblings.each( function(i){

                            var curId=Number($(this).find('.node:eq(0)').attr('id'));
                            if(curId && idMax<curId){
                                idMax=curId;
                            }
                        });

                    }

                    //console.log('idDelta, idMax:');
                    //console.log(idDelta);
                    //console.log(idMax);




                    //当前深度
                    var curDepth=MAX_DEPTH-getDepthById(idDelta)+1;

                    console.log('current depth:');
                    console.log(curDepth);

                    var newId=Number(idMax)+Math.pow(10,LEVEL_WIDTH*(curDepth-1));




                    //console.log('sibling id:'+siblingId);



                    var newLi=$('<li class="">' +
                        '<div class="node" id="'+newId+'" name="新的节点1">' +
                        '<input type="text" class="input-node-name" value="新的节点1">-' +
                        '<input type="text" class="input-node-id" '+lockIdEditStr+' value="'+newId+'"><em></em>' +
                        '</div>' +
                        '</li>');

                    $(newLi).insertAfter($(curLi));

                    $(SELF).find('.node').removeClass('selected');

                    setCurrentNode($(newLi).find('.node'));







                }

            });

        };


        /**
         *
         */
        var addCreateRootUI=function(){


            console.log(SELF);

            $('<a class="btn btn-primary btn-lg btn-add-root" >当前无数据，点击添加基础节点</a>').appendTo($(SELF).find('.tree-container'));



        };//-/

        /**
         * loadXML
         * load xml to SELF.XMLData. and call handleFinished func.         *
         * @param handleFinished {function} : func called when load finished.
         */
        var loadXML = function (handleFinished) {


            $.get(xml_url).success(function (result) {

                SELF.XMLData = result;
                if (handleFinished && typeof handleFinished == 'function') {
                    handleFinished(result);
                }
            }).error(function(result){
                console.log('xml load error');
                addCreateRootUI();

            });
        };//-/




        /**
         * viewTree
         * @param xmlData
         */
        var viewTree = function (xmlData) {
            $(SELF).find('.tree-container').empty();

            console.log($(xmlData));


            var $startXML = $(xmlData).children()[0];

            rootId= $($startXML).attr('id') ;
            rootName=$($startXML).attr('name');



            var uiHtml = readNode($startXML, '', SELF.curValue, 1);
            $(uiHtml).appendTo($(SELF).find('.tree-container'));


        };//-/

        /**
         * readNode
         * @param currentNode
         * @param ui
         * @param curValue
         * @param depth
         * @returns {*}
         */
        var readNode = function (currentNode, ui, curValue, depth) {



            //dg++;

            //console.log(dg);
            //console.log(depthLimit);


            //depth limit:
            if (depthLimit > 0 && depth > depthLimit) {
                return ui;
            }

            //hide attribute:
            if ($(currentNode).attr('hide')) {
                return ui;
            }


            if ($(currentNode).children().length > 0) {

                ui += '<ol>';

                $(currentNode).children(node_name).each(function (i) {

                    var nodeText = $(this).attr(node_text_name);
                    var nodeValue = $(this).attr(node_value_name);

                    if (!$(this).attr('hide')) {//not hide:
                        var classN = '';

                        //class of which has children.
                        if ($(this).children().length > 0) {
                            classN = 'closed';
                        }

                        //check if current.
                        var emClass = '';
                        if (nodeValue == curValue) {
                            classN += ' cur ';
                            emClass = ' class="checked"';

                            $(wrap_obj).find('.input_region').val(nodeText);
                            $('#' + data_saver).attr('label', nodeText);
                        }

                        //build current ui li html start:
                        ui += '<li class="' + classN + '"><div class="node" id="' + nodeValue + '" name="' + nodeText + '"><input type="text" class="input-node-name" value="' + nodeText + '" />-<input type="text" class="input-node-id" '+lockIdEditStr+' value="' + nodeValue + '" /><em' + emClass + '></em></div>';

                        if (!$(this).attr('hide')) {
                            ui = readNode(this, ui, curValue, depth + 1); //递归
                        }
                        ui += '</li>';  // li close
                    }


                });

                ui += '</ol>';  //close ul
            }


            return ui;

        };


        //start:
        ini();
        attachEventHandle();
        loadXML(viewTree);

    };

})(jQuery);

//setup when document.ready
$(document).ready(function(){

    var class_name="tree-editor";

    var ctrlist = $('.'+class_name);


    for(i=0;i<ctrlist.length;i++)
    {

        $("."+class_name+":eq("+i+")").treeEditor();



    }

});
