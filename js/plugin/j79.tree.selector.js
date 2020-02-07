
//load css files.
j79.loadCSS("/css/treeselector.css");

/*
选择树状结构的节点。
------------------------------ */



(function($){
	
	
	
	
	
	$.fn.treeSelector=function(settings){
		if (this.length < 1) {
			return;
		}
		// 默认值
		/*settings=$.extend({
			
			xml_url:''
			
		
		
		},settings);*/
		
		
		var defaultSet={
		   viewDepth:0,	   //0- no limit; XXX>0 : limit to XXX depth view.
		};
		
		settings=$.extend( defaultSet, settings);
		
		var wrap_obj=this;
		
		var DATA_SAVER=this.attr('data-saver') ? this.attr('data-saver'):'';
		//var DATA_SAVER_text=this.attr('data-saver-text');

		var DATA_SAVE_PATH=this.attr('data-save-path') ? this.attr('data-save-path') : '' ;


		var EXPEND_MODE=this.attr('data-expend-mode') && parseInt(this.attr('data-expend-mode'))==1 ? 1:0; //1- 只打开当前的树，其他的树关闭。 0- 默认值，不关闭其他已经打开的树。


		var xml_url=this.attr('data-xml');

		var flagAllOpened=this.attr('all-opened') ? true : false;

		var flagViewOnly=this.hasClass('view-static') ? true : false;


		
		var ctrDisabled=this.attr('disabled') ? true : false;
		
		if(ctrDisabled){
			$(this).addClass('disabled');
		}

		var linkEditor=this.attr('editor-link') ? '<a class="link-tree-editor" href="'+this.attr('editor-link')+'" target="_blank"><i class="glyphicon glyphicon-edit"></i> 编辑当前分类树</a>' : '';


		
		
		settings.viewDepth=this.attr('view-depth') ? parseInt(this.attr('view-depth')) : 0;	 
		    
		
		var ui=$(
		'<div class="value-view">'+
        '    <input name="" id="" class="input_region" type="text" placeholder="-请选择分类-" title="点击选择" readonly="readonly" value="" />'+       
        '</div>'+
    	'<div class="selector">'+       
        '<div class="tree-board"><div class="clear"></div></div>'+
		linkEditor+
		'<a  class="bu_refresh">刷新</a>'+
    	'<a  class="bu_close">关闭</a>'+

		'</div>');
		
		ui.appendTo(wrap_obj);
		 


		
		
		var box_obj=$(wrap_obj).find('.selector');
		
		var viewer_obj=$(wrap_obj).find('.value-view');
		
		
		
		
		
		//var city_json;
		var text_tip="-请选择-";
		
		
		
		
		var current_value='';

		if(DATA_SAVER!=''){
			current_value=$.trim($('#'+DATA_SAVER).val());
		}

		
		var node_name="item"; //xml文档中，节点的标签名称
		var node_root_name="items"; //xml文档中，root节点的标签名称
		var node_text_name="name"; //xml文档中，节点显示名称的属性名称
		var node_value_name="id"; //xml文档中，节点值的属性名称

		var $DATA_XML_ROOT; //root xml
		
		
		
		
		
		
		//submit_value:选择结束后，生成相应的值，并存入data-saver中。
		var submit_value=function(){
				
				
				//$('#'+DATA_SAVER).val(cur_idx);
				
				//取得当前选中的id，并给data-saver控件赋值。
				
				
				
		
				
				//$(box_obj).hide();
		};//-----------------/
		
		
		//显示tree
		var readNode=function(currentNode, ui, curValue, depth){
			
			var re=false;
			if($(currentNode).children().length>0){
				var $ul=$('<ul/>');
				$ul.appendTo($(ui));
			}
			$(currentNode).children(node_name).each(function(i){
					
					$ul.appendTo($(ui));		
					
					var $li=$('<li/>');
					
					if($(this).children().length>0 ){
						

						$li.attr("class", (flagAllOpened==false? "closed":"opened"));
						
					}
					
					var nodeText=$(this).attr(node_text_name);
					var nodeValue=$(this).attr(node_value_name);
					
					
					var emClass='';
					
					
					
					
					
					if( nodeValue==curValue){
						$li.addClass('cur');
						emClass=' class="checked"';
						
						$(wrap_obj).find('.input_region').val(nodeText);
						$('#'+DATA_SAVER).attr('label',nodeText);
												
						re=true;
					}
					
					var $li_item=$('<b class="node"><a id="'+nodeValue+'" name="'+nodeValue+'">'+nodeText+'<em'+emClass+'></em></a></b>');
					
					$li_item.appendTo($li);
					
					if(settings.viewDepth!=0 && depth>=	settings.viewDepth+1 ){
					    return;	
					}
					
					var reSub=readNode(this, $li, curValue,depth+1); //递归
					$li.appendTo($ul);
					
					
					if( reSub==true || re==true){ //与当前值相同,那么设置cur
						
						$li.parents('li.closed').each(function(i){
							$(this).removeClass('closed');	
							$(this).addClass('opened');
						});					
									
						re=true;
											
					}
							
				
			});
			
			return re;
				
		};//---------------------------


		var saveData=function(curId, curLabel){

			if (DATA_SAVER != '') {

				/*if (DATA_SAVE_PATH) {
					var dataObj = j79.toJSON($('#' + DATA_SAVER).val());
					if (!dataObj) {
						dataObj = {}
					}
					dataObj[DATA_SAVE_PATH] = result;
					result = dataObj;
					$('#' + DATA_SAVER).val(j79.toJSONString(result));

				}else{
					$('#'+DATA_SAVER).val(curId);

				}*/
				$('#'+DATA_SAVER).val(curId);
				$('#'+DATA_SAVER).attr('label',curLabel);
				$('#'+DATA_SAVER).trigger('change');
			}
		};

		/**
		 * readData
		 */
		var readData=function(){
			if(DATA_SAVER!=''){
				current_value=$.trim($('#'+DATA_SAVER).val());
				$(wrap_obj).find('.input_region').val('');
				$(wrap_obj).find('.tree-board').empty();
				readNode($DATA_XML_ROOT, $(wrap_obj).find('.tree-board'),current_value,1);
			}

		};

		/**========================================================
		*  function: XMLLoaded
		*
		*  读取xml文件成功后调用
		*  根据xml，生成ul+li树结构html，加入到顶容器wrap_id。
		*  并且生成相应点击事件处理函数
		**/
		var XMLLoaded=function(xml){



			$(wrap_obj).find('.tree-board').empty();
			
			//递归读取xml，并创建tree html, 并加入到wrap_id容器中
			
			$startXML=$(xml).children()[0];
			$DATA_XML_ROOT=$startXML;
			readNode($startXML, $(wrap_obj).find('.tree-board'),current_value,1);		
			
			
			
			//创建事件管理：tree展开关闭事件	+单击选中事件

			$(wrap_obj).delegate('.node','click',null,function(e){
				//清楚所有cur， checked 样式。
				var $treeRoot=$(wrap_obj).find('.tree-board');
				$treeRoot.find(".cur").removeClass('cur');
				$treeRoot.find(".checked").removeClass('checked');

				//取得当前选中的id，并给data-saver控件赋值。
				var curID=$(this).find("A").attr("id");
				var curText=$(this).find("A").text();

				/*$('#'+DATA_SAVER).val(curID);
				 $('#'+DATA_SAVER).attr('label',curText);
				 $('#'+DATA_SAVER).trigger('change');*/

				saveData(curID,curText);

				$(wrap_obj).find('.input_region').val(curText);

				//$(wrap_obj).find('.selector').hide();


				//如果EXPEND_MODE==1， 只打开当前的节点，其他的自动关闭。
				if(EXPEND_MODE==1){
					$(wrap_obj).find('li.opened').each(function(){

						if( $(this).find('A[id='+curID+']').length<=0 ){
							$(this).removeClass('opened');
							$(this).addClass('closed');
						}
					});
				}

				//tree的收展，设置当前项class：cur
				$(this).parent().each( function(){

					if($(this).hasClass('closed')){
						$(this).removeClass('closed');
						$(this).addClass('opened');
					}else if($(this).hasClass('opened')){
						$(this).removeClass('opened');
						$(this).addClass('closed');
					}

					$(this).addClass('cur');

				});



				//设置选中class:checked
				$(this).find('em').addClass('checked');
			});

		
			
			
			
				
		}; //----------------/函数：读取xml文件成功后调用
		
		/**==========================================
		*  function: XMLLoadError
		*
		*  读取xml文件出错后调用
		**/
		var XMLLoadError=function(xml){
			
			alert("xml读取出错！！");
				
		};//----------------/函数：读取xml文件出错后调
		
		
		//ini: 初始化
		var ini=function(){
			
		
			
			//显示列表
			viewer_obj.find('.input_region').click(function(){	
			   if(ctrDisabled==false && flagViewOnly==false){
				box_obj.css('z-index',9999);							
				box_obj.show();
				}
			});


			
			//关闭列表
			wrap_obj.find('.bu_close').click(function(){
				
				box_obj.hide();
			});
			
			//具体地址输入后，把值更新到data-saver-address里
			//viewer_obj.find('.input_address').change(function(){					
			//	$('#'+DATA_SAVER).val($(this).val());
			//});
			
			
			//读取xml，初始化
			xml_url= xml_url!=''? xml_url+'?rndno='+Math.random():'';
			$.get(xml_url, function(data){
			  XMLLoaded(data);
			}).error(function(result){
				console.log('xml load error');


			});
			
		};//-------------------------------------------------

		$(wrap_obj).find('.bu_refresh').click(function(e){
			ini();
		});

		$(this).change(function(e){
			readData();
		});
		
		
		ini();


		
		
		
	};
})(jQuery);





$(document).ready(function(){

	var class_name="tree-selector";

	var ctrlist = $('.'+class_name);


	for(i=0;i<ctrlist.length;i++)
	{

		$("."+class_name+":eq("+i+")").treeSelector();



	}
	
});