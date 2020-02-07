//load css files.
j79.loadCSS("/css/treeselector.css");

/**
*  treeNavigator
*  navigation element with tree format and view.
*
*  @container: <div class="tree-navi"    //need this class to locate and indentify the control 
                    data-saver="[id of input element in which selected value will save]"  
				    data-xml="[xml url]"
				    selected-id="[initial selected value of the tree | can be empty]"
 					data-click-func="when click, execute this function by current category id as param".
                ></div>
*  @xml      :     
*              root node name       - items
*              item node name       - item
*              title attrbitue name - name
* 			   id attribute name    - id  
                                      e.g.:  <items>
                                               <item name="clothes" id="10000">
                                                    <item name="coat" id="10100"/>
													<item name="trousers" id="10200"/>
                                               </item>
                                             </items>
				   
*/



(function($){
	
	
	
	
	
	$.fn.treeNavigator=function(settings){
		
		if(this.length<1){return;};
		
		

		// 默认值
		/*settings=$.extend({
			
			xml_url:''
			
		
		
		},settings);*/
		
		
		
		
		var wrap_obj=this;
		
		var data_saver=this.attr('data-saver');
		//var data_saver_text=this.attr('data-saver-text');
		var xml_url=this.attr('data-xml');
		var current_value= typeof this.attr('selected-id')  =='undefined' ? '': this.attr('selected-id');

		//读取深度：
		var depthLimit=typeof this.attr('data-depth-limit')  =='undefined' || parseInt(this.attr('data-depth-limit'))<=0  ? 0: parseInt(this.attr('data-depth-limit'));
		
		var clickFunc=typeof this.attr('data-click-func')  =='undefined' || this.attr('data-click-func') =='' ? '': this.attr('data-click-func');
		
		var node_name="item"; //xml文档中，节点的标签名称
		var node_root_name="items"; //xml文档中，root节点的标签名称
		var node_text_name="name"; //xml文档中，节点显示名称的属性名称
		var node_value_name="id"; //xml文档中，节点值的属性名称
		
		var dg=0;
		
	
		
		
		//显示tree
		/*var readNode=function(currentNode, ui, curValue){
			
			var re=false;
			dg++;

			if($(currentNode).children().length>0){
				var $ul=$('<ul/>');
				$ul.appendTo($(ui));
			}
			$(currentNode).children(node_name).each(function(i){
					
					$ul.appendTo($(ui));		
					
					var $li=$('<li/>');
					
					if($(this).children().length>0){
						
						
						$li.attr("class","closed");	
						
					}
					
					var nodeText=$(this).attr(node_text_name);
					var nodeValue=$(this).attr(node_value_name);
					
					
					var emClass='';
					
					
					
					if( nodeValue==curValue){
						$li.addClass('cur');
						emClass=' class="checked"';
						
						$(wrap_obj).find('.input_region').val(nodeText);
						$('#'+data_saver).attr('label',nodeText);
												
						re=true;
					}
					
					var $li_item=$('<b class="node"><a id="'+nodeValue+'" name="'+nodeValue+'">'+nodeText+'<em'+emClass+'></em></a></b>');
					
					$li_item.appendTo($li);

					if(!$(this).attr('hide')){
						var reSub=readNode(this, $li, curValue); //递归
						$li.appendTo($ul);
					}
					

					
					
					if( reSub==true || re==true){ //与当前值相同,那么设置cur
						
						$li.parents('li.closed').each(function(i){
							$(this).removeClass('closed');	
							$(this).addClass('opened');
						});					
									
						re=true;
											
					}
							
				
			});
			
			return re;
				
		};//---------------------------*/

		var readNode=function(currentNode, ui, curValue, depth){



			//dg++;

			//console.log(dg);
			//console.log(depthLimit);


            //depth limit:
			if(depthLimit>0 && depth> depthLimit){
				return ui;
			}

			//hide attribute:
            if($(currentNode).attr('hide')){
                return ui;
            }


			if($(currentNode).children().length>0){

				ui+='<ul>';

				$(currentNode).children(node_name).each(function(i){

					var nodeText=$(this).attr(node_text_name);
					var nodeValue=$(this).attr(node_value_name);

                    if(!$(this).attr('hide')){//not hide:
                        var classN='';

                        //class of which has children.
                        if($(this).children().length>0){
                            classN='closed';
                        }

                        //check if current.
                        var emClass='';
                        if( nodeValue==curValue){
                            classN+=' cur ';
                            emClass=' class="checked"';

                            $(wrap_obj).find('.input_region').val(nodeText);
                            $('#'+data_saver).attr('label',nodeText);


                        }

                        //build current ui li html start:
                        ui+='<li class="'+classN+'"><b class="node"><a id="'+nodeValue+'" name="'+nodeText+'">'+nodeText+'<em'+emClass+'></em></a></b>';

                        if(!$(this).attr('hide')){
                            ui=readNode(this, ui, curValue, depth+1); //递归
                        }
                        ui+='</li>';  // li close
                    }




				});

				ui+='</ul>';  //close ul
			}




			return ui;

		};

		
		/**========================================================
		*  function: XMLLoaded
		*
		*  读取xml文件成功后调用
		*  根据xml，生成ul+li树结构html，加入到顶容器wrap_id。
		*  并且生成相应点击事件处理函数
		**/
		var XMLLoaded=function(xml){





			//递归读取xml，并创建tree html, 并加入到wrap_id容器中

			$startXML=$(xml).children()[0];
			//readNode($startXML, $(wrap_obj),current_value);

			var menuHtml=readNode($startXML, '',current_value,1);

			$(menuHtml).appendTo(wrap_obj);


			//set current:
			if(current_value){

				var curA=$(wrap_obj).find('A[id='+current_value+']');
				$(curA).find('em').addClass('checked');

				if(curA.length>0){
					var curLi=$(curA).closest('li');
					if($(curLi).find('ul').length>0){
						$(curLi).removeClass('closed');
						$(curLi).addClass('opened');
					}
					$(curLi).parentsUntil( wrap_obj, 'li').removeClass('closed');
					$(curLi).parentsUntil( wrap_obj, 'li').addClass('opened');
				}
			}





			
			
			//创建事件管理：tree展开关闭事件	+单击选中事件		
			//$(wrap_obj).find(".node").unbind('click');	//清除原始click，避免2次调用。



			$(wrap_obj).delegate('.node','click',null,function(){	//单击事件：
				
				
				//清楚所有cur， checked 样式。
				var $treeRoot=$(wrap_obj);		
				$treeRoot.find(".cur").removeClass('cur');
				//$treeRoot.find(".checked").removeClass('checked');
				
				//取得当前选中的id，并给data-saver控件赋值。
				var curID=$(this).find("A").attr("id");
				var curText=$(this).find("A").text();
				
				$('#'+data_saver).val(curID);
				$('#'+data_saver).attr('label',curText);
				$('#'+data_saver).trigger('change');		
				
				
				if(clickFunc!=''){
					eval(clickFunc+'('+curID+')');
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
				//$(this).find('em').addClass('checked');
				
				
			});//--------/tree展开关闭事件	+单击选中事件
			
		
			
			
			
				
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
			
				
			//读取xml，初始化
			$.get(xml_url, function(data){
			  XMLLoaded(data);
			});		
			 
			
		};//-------------------------------------------------
		
		
		var resetView=function(){
			$(this).find(".cur").removeClass('cur');
		};
		
		
		ini();
		
		return this;


		
		
		
	};
	
	
	
})(jQuery);
/**
*  treeNavigatorReset
*  reset tree view and selected value
*/
(function($){

	$.fn.treeNavigatorReset=function(){
		var data_saver=this.attr('data-saver');
		$(this).find('#'+data_saver).val('');
		$(this).find(".cur").removeClass('cur');
		$(this).find(".opened").removeClass('opened').addClass('closed');
		//$(this).find(".checked").removeClass('checked');
	};
})(jQuery);





$(document).ready(function(){	

	var class_name="tree-navi";
		
	var ctrlist = $('.'+class_name);
	
	
	for(i=0;i<ctrlist.length;i++)
	{
		
		$("."+class_name+":eq("+i+")").treeNavigator();	
		
		
			
	}

});



