
//load css files.
j79.loadCSS("/css/j79.tree.link.selector.css");

/*
选择树状结构的节点。
------------------------------ */



(function($){
	
	
	
	
	
	$.fn.treeLinkSelector=function(settings){
		if (this.length < 1) {
			return;
		}


		
		var SELF=this;

		var SELF_ID=this.attr('id')?  this.attr('id') : '';

		if(SELF_ID==''){
			SELF_ID='treeLinkSelector'+(Math.round( Math.random()*1000000));
			this.attr('id' , SELF_ID);
		}
		
		var DATA_SAVER=this.attr('data-saver') ? this.attr('data-saver'):'';

		if(DATA_SAVER!=''){
			$('#'+DATA_SAVER).hide();
		}


		var DATA_SAVE_PATH=this.attr('data-save-path') ? this.attr('data-save-path') : '' ;

		var XML_URL=this.attr('data-xml')? this.attr('data-xml') : '';

		var LINK_URL=this.attr('link-url') || '/com.php?target=pro_list&category=[#idx#]';

		var STR_PLACEHOLDER=$(SELF).attr('title') || '';

		var DEFAULT_MAX_COUNT=3;
		//MAX BANNER COUNT,DEFAULT =3
		var MAX_COUNT = $(SELF).attr('data-max-count') && parseInt($(SELF).attr('data-max-count'))>0 ? parseInt($(SELF).attr('data-max-count')) : DEFAULT_MAX_COUNT;


		
		var UI=$(
			'<div class="description">'+STR_PLACEHOLDER+'</div>'+
			'<div class="list-part"><h3><span class="float-r"><a  class="btn btn-sm btn-primary btn-add"><i class="glyphicon glyphicon-plus"></i> 添加</a></span>列表</h3><ol class="item-list"></ol> </div>'+
		'<div class="edit-panel">' +
				'<h3>添加</h3>'+
		'    <div class="tree-selector" data-expend-mode="1" title="" data-xml="'+XML_URL+'" data-saver="'+SELF_ID+'_cat_id" id="'+SELF_ID+'_treeSelector" name="categorySelector'+SELF_ID+'"></div>'+
		'    <div class="row input-item"><div class="col-md-4">分类id：</div><div class="col-md-8"><input disabled type="text" id="'+SELF_ID+'_cat_id" /></div></div>'+
		'    <div class="row input-item"><div class="col-md-4">分类显示名：</div><div class="col-md-8"><input type="text" id="'+SELF_ID+'_cat_label" value="" /></div></div>'+
		'    <div class="row input-item"><div class="col-md-4">分类链接：</div><div class="col-md-8"><input type="text" id="'+SELF_ID+'_cat_url" value="" /></div></div>'+

		'   <div class="operation"><a  class="btn btn-sm btn-primary btn-add"><i class="glyphicon glyphicon-backward"></i> 添加到列表</a><a  class="btn btn-sm btn-default btn-close">关闭</a></div>'+
		'</div>'+
		'<div class="clear"></div>'

		);



		var HTML_LI_MENU= '<div class="li-menu">' +

			'<a class="btn btn-del" ><i class="glyphicon glyphicon-remove"></i> 删除</a><br/>' +
			'<a class="btn btn-up" ><i class="glyphicon glyphicon-chevron-up"></i> 上移</a> ' +
			'<a class="btn btn-down" >下移 <i class="glyphicon glyphicon-chevron-down"></i></a> ' +
			'</div>';

		var HTML_LI='<li url="[#url#]" title="[#title#]" idx="[#idx#]">'+
			HTML_LI_MENU+
			'<div class="title-part"><p>标题：<b>[#title#]</b></p><p>链接：<a href="[#url#]" target="_blank">[#url#]</a></p></div><div class="clear"></div> </li>';

		var UI_LIST='.item-list';
		var UI_EDITOR='.edit-panel';


		

		if(DATA_SAVER!=''){
			current_value=$.trim($('#'+DATA_SAVER).val());
		}





		var saveData=function(){


			var result = [];


			var imgItem;
			$(SELF).find(UI_LIST+' li').each(function (idx) {

				var dataId=$(this).attr('idx');
				var dataLabel=$(this).attr('title');
				var dataUrl=$(this).attr('url');

				if (dataId && dataLabel && dataUrl ) {
					imgItem={
						"idx":dataId,
						"title": dataLabel,
						"url": dataUrl
					};
					result.push(imgItem);
				}


			});


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
		};


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

						var dataIdx=imgItem.idx || '';
						var dataTitle=imgItem.title || '';
						var dataUrl=imgItem.url || '';

						if( dataTitle &&  dataUrl ){

							$( j79.setHtml(imgItem, HTML_LI)).appendTo($(SELF).find(UI_LIST));

						}

					}

				}


			}


		};//-/

		var build=function(){
			UI.appendTo(SELF);
			$(SELF).find('.tree-selector').treeSelector();

		};


		/**
		 * attachEvent
		 * bind event handler by delegate:
		 */
		var attachEvent=function(){

			//tree-selected:
			$(SELF).delegate(UI_EDITOR+' #'+SELF_ID+'_cat_id', 'change', null, function (event) {
				$(SELF).find(UI_EDITOR+' #'+SELF_ID+'_cat_label').val($(SELF).find(UI_EDITOR+' #'+SELF_ID+'_cat_id').attr('label'));

				var dataId=$(SELF).find(UI_EDITOR+' #'+SELF_ID+'_cat_id').val() || '';

				var dataUrl= j79.setHtml({"idx":dataId}, LINK_URL);
				$(SELF).find(UI_EDITOR+' #'+SELF_ID+'_cat_url').val(dataUrl);
			});

			//list: li clicked, set active, set edit.
			$(SELF).delegate(UI_LIST+'  li', 'click', null, function (e) {
				$(this).siblings().removeClass('active');
				$(this).addClass('active');


			});


			//list:open add new editor:
			$(SELF).delegate(' .list-part .btn-add', 'click', null, function (e) {


				if( $(SELF).find(UI_LIST+' li').length>=MAX_COUNT ){
					alert('上限已达，不能再添加了。');
					return ;
				}

				$(SELF).find(UI_EDITOR+' #'+SELF_ID+'_cat_id').val('');
				$(SELF).find(UI_EDITOR+' #'+SELF_ID+'_cat_label').val('');


				$(SELF).find(UI_EDITOR).show();
			});


			//editor: add new item
			$(SELF).delegate(UI_EDITOR+' .operation .btn-add', 'click', null, function (e) {


				if( $(SELF).find(UI_LIST+' li').length>=MAX_COUNT ){
					alert('上限已达，不能再添加了。');
					return ;
				}


				var dataLabel=$(SELF).find(UI_EDITOR+' #'+SELF_ID+'_cat_label').val() || '';
				var dataId=$(SELF).find(UI_EDITOR+' #'+SELF_ID+'_cat_id').val() || '';
				var dataUrl=$(SELF).find(UI_EDITOR+' #'+SELF_ID+'_cat_url').val() || '';


				if( dataId &&  dataLabel && dataUrl ){

					/*var dataUrl= j79.setHtml({"idx":dataId}, LINK_URL);*/

					var curData={
						"idx":dataId,
						"title": dataLabel,
						"url": dataUrl
					};




					$( j79.setHtml(curData, HTML_LI)).appendTo($(SELF).find(UI_LIST));
					saveData();

					$(SELF).find(UI_EDITOR+' #'+SELF_ID+'_cat_label').val('');
					$(SELF).find(UI_EDITOR+' #'+SELF_ID+'_cat_id').val('');
					$(SELF).find(UI_EDITOR+' #'+SELF_ID+'_cat_url').val('');




				}else{
					alert('请填写完整信息后，再点击添加按钮！');

				}
			});


			//list operation: delete
			$(SELF).delegate(UI_LIST+' .btn-del', 'click', null, function (e) {

				$(this).closest('li').remove();
				saveData();
			});

			//list operation: up
			$(SELF).delegate(UI_LIST+' .btn-up', 'click', null, function (e) {
				var li = $(this).closest('li');
				var preLi=$(li).prev('li');
				if(preLi.length>0){
					$(li).insertBefore(preLi);
					saveData();
				}
			});

			//list operation: down
			$(SELF).delegate(UI_LIST+' .btn-down', 'click', null, function (e) {
				var li = $(this).closest('li');
				var nextLi=$(li).next('li');
				if(nextLi.length>0){
					$(li).insertAfter(nextLi);
					saveData();
				}
			});

			//editor: close window
			$(SELF).delegate(UI_EDITOR+' .operation .btn-close', 'click', null, function (e) {

				$(SELF).find(UI_EDITOR).hide();
			});



			/*











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
			});*/
		};

		
		//ini: 初始化
		var ini=function(){
			build();
			attachEvent();
			readData();
		};//-------------------------------------------------

		
		ini();


		
		
		
	};
})(jQuery);



$(document).ready(function(){

	var class_name="tree-link-selector";

	var ctrlist = $('.'+class_name);


	for(i=0;i<ctrlist.length;i++)
	{

		$("."+class_name+":eq("+i+")").treeLinkSelector();



	}
	
});