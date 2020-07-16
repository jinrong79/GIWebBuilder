//load css files.
j79.loadCSS("/css/dbselector.css");

/**
*dbSelector
*/



(function($){
	
	/* .clear
	** 重置db_selector,值清空。
	*/
	$.fn.clear=function(settings){
		var wrap_obj=this;
		var data_saver=this.attr('data-saver');
		wrap_obj.find('.input_region').val('');
		$('#'+data_saver).val('');
		wrap_obj.find('.view-board').empty();			
	};
	
	/* .dbSelector
	** db_selector初始化
	*/
	$.fn.dbSelector=function(settings){
		if (this.length < 1) {
			return;
		}
		var SELF=this;

		var VIEW_STATIC=$(SELF).hasClass('view-static') ? true : false;  //控件为静态显示状态
		
		var dbData=[];
		
		var field_title=this.attr('field-title');
		var field_idx=this.attr('field-idx');
		var url_addnew=this.attr('url-addnew');
		
		var wrap_obj=this;
		
		var ctrDisabled=this.attr('disabled') ? true : false;
		
		if(ctrDisabled){
			$(this).addClass('disabled');
		}
		
		
		var data_saver=this.attr('data-saver');
	
		var db_url=this.attr('db-url');
		
		var text_name=this.attr('text-name');
		
		var category_value; //categroy value if set
		var category_saver; //id of input which has categroy value in its value.
		
		category_value= typeof( $(wrap_obj).attr('db-category'))== "undefined" ? '' : $(wrap_obj).attr('db-category') ; //get value of category
		category_saver= typeof( $(wrap_obj).attr('db-category-saver'))== "undefined" ? '' : $(wrap_obj).attr('db-category-saver'); //get value of category saver
		
		
		var ui=$(
		'<div class="value-view">'+
        '    <input name="" id=""  class="input_region" type="text" placeholder="-请选择'+text_name+'-" title="点击选择'+text_name+'" readonly="readonly" value="" />'+       
        '</div>'+
    	'<div class="selector">'+ 
		      
        '<div class="view-board"></div>'+
		'<div class="row view-summary"><div class="col-md-3 keywoard-view"></div><div class="col-md-9 pager-area"></div></div>'+ 
		''+ 
		//'<div class="search-area"><input type="text"  class="keyword" name="keyword" id="keyword" value="" /><a id="btnSearch" class="btn btn-primary">查找</a><a id="btnSearchAll" class="btn btn-default">显示全部</a><span class="status"></span></div>'+
		//'<div class="tip-board"><p>请根据关键字搜索'+text_name+'，然后点击相应结果来选择'+text_name+'...</p><p>如果结果中没有您要的'+text_name+'，请点击 <a class="btn btn-sm btn-default" href="'+url_addnew+'" target="_blank">添加新的'+text_name+'</a></p></div>'+
		//'<div class="addnew-board"><input style="width:60%;" name="new_provider_name" id="new_provider_name" placeholder="请输入完整的'+text_name+'..." /><a id="btnAdd" class="btn btn-default">添加新的'+text_name+'</a></div>'+       
    	'<a  class="bu_close">关闭</a>'+
		'</div>');
		
		ui.appendTo(wrap_obj);

		
		
		var box_obj=$(wrap_obj).find('.selector');
		
		var viewer_obj=$(wrap_obj).find('.value-view');
		
		
		
		//var city_json;
		var text_tip="-请选择-";
		
		
		
		
		var current_value='';
		
		
		
		current_value=$('#'+data_saver).val();
		
		
		
		var node_name="category"; //xml文档中，节点的标签名称
		var node_root_name="category"; //xml文档中，root节点的标签名称
		var node_text_name="name"; //xml文档中，节点显示名称的属性名称
		var node_value_name="id"; //xml文档中，节点值的属性名称
		
		
		//get category value: if has categroy-saver, then get value of it; if not, then get category-value attribute.
		var get_category=function(){
			
	
			
			category_value= typeof( $(wrap_obj).attr('db-category'))== "undefined" ? '' : $(wrap_obj).attr('db-category') ; //categroy value if set
			category_saver= typeof( $(wrap_obj).attr('db-category-saver'))== "undefined" ? '' : $(wrap_obj).attr('db-category-saver'); //id of input which has categroy value in its value.
									
			var categoryValue='';
			if(category_saver!=''){
				categoryValue=$('#'+category_saver).val();
			}else{
				categoryValue=category_value;
			}		
	
			
			return categoryValue;
		};
		
		
		
		
		//submit_value:选择结束后，生成相应的值，并存入data-saver中。隐藏选择面板。
		var submit_value=function(){
				
				
				$('#'+data_saver).val(cur_idx);			
				
				$(box_obj).hide();
		};//-----------------/
		
		
		var handleLoaded=function(iniValue){
			if(iniValue && parseInt(iniValue)!=0){
			   wrap_obj.find('.input_region').val(wrap_obj.find('.view-board li A:eq(0)').text()); 	
			}
			//wrap_obj.find('.input_region').val(wrap_obj.find('.view-board li A:eq(0)').text());
			
			wrap_obj.find('.view-board li').click(function(){
				wrap_obj.find('.input_region').val($(this).find('A').text());
				$('#'+data_saver).val($(this).find('A').attr('idx'));
				
				wrap_obj.find('.view-board li').removeClass("selected");
				
				$(this).addClass("selected");
				//box_obj.hide();
			});
		};//-/
		
		/**
		*  setPage
		*
		*/
		var pageHandler={};
		pageHandler.setPage=function(pageNo){
				
			    startLoad(null,false, pageNo);
			
		};//--/
		
		
		/**
		*  startLoad
		*  start load data.
		*  @param {string/int} iniValue : current idx when control initialized.
		*                                 used when form is in edit mode or preset item to user.
		*  @param {bool}       loadAll  : true -- load all data. false- just search by keyword
		*  @param {int}        pageNo   : load page number.
		*/		
		var startLoad=function(iniValue, loadAll,pageNo){
			
			wrap_obj.find('.view-board').empty();
			wrap_obj.find('.view-board').append('<span class="loading"></span>');
			
			
			
			
			var postData={};
			
			
			
			var keyValue=wrap_obj.find('.keyword').val();
			if(keyValue!='' && !loadAll){
				postData['searchValue']=keyValue;
			}
			
			if(iniValue && iniValue!='' && parseInt(iniValue)!=0){
				
				/*postData['searchValue']=iniValue;
				postData['searchKey']=field_idx;*/

				postData[field_idx]=iniValue;

			}
			
			pageNo=pageNo || 1;
			var per_page=20;
			
			postData['per_page']=per_page;
			postData['page']=pageNo;
			
			//console.log('db-selector search:');
			console.log(postData);

			let dataT=new dataTransporter();

			dataT.dataGet({
				"url":db_url,
				"requestType":"GET",
				"data": postData,
				"success":function(data){



					wrap_obj.find('.view-board .loading').remove();

					console.log("Data: " + data );

					var jsonData=data;

					if(typeof  jsonData =='object'){

					}else{
						return;
					}

					//try to parse result into json format



						wrap_obj.dbData=jsonData.data;
						wrap_obj.total_amount=jsonData.data.length;

						if(wrap_obj.dbData  && wrap_obj.dbData.length>0){
							var itemData, $itemLi;

							var titleArr=field_title.split('|');

							for(var i=0; i<wrap_obj.dbData.length ; i++){
								itemData=wrap_obj.dbData[i];

								var titleText='';
								var sep='';
								var sepT='';
								for(var j=0; j<titleArr.length;j++){
									titleText+=sep+itemData[titleArr[j]]+sepT;
									sep=' (';
									sepT=' )';
								}

								$itemLi=$('<li><a idx="'+itemData[field_idx]+'">'+titleText+'</a></li>');
								$itemLi.appendTo(wrap_obj.find('.view-board'));
							}

							//view pager
							$(ui).find('.pager-bar').remove();
							j79.viewPager(Math.ceil(jsonData.total_amount /per_page), pageNo, $(ui).find('.pager-area'),pageHandler);
							$(ui).find('.total-amount-no').text(jsonData.total_amount);

							var searchInfo='';
							if(postData && postData['searchValue']){
								searchInfo='当前关键字:<b>'+postData['searchValue']+'</b> ';
							}
							//wrap_obj.find('.view-summary').html('<p>'+searchInfo+'总共 <b>'+wrap_obj.total_amount+'</b> 条记录，当前显示最多20条。</p>');

							wrap_obj.find('.view-summary .keywoard-view').html('<p>'+searchInfo+'</p>');

						}

						handleLoaded(iniValue);









				},
				"failed":function(code,msg,xmlHR){



				}
			});

			
			/*$.post( db_url , postData, function(data,status){
				          
						  wrap_obj.find('.view-board .loading').remove();
						
						  console.log("Data: " + data + "\nStatus: " + status);	
						  
						  var jsonData;
						  //try to parse result into json format 
						  try{
						       jsonData = JSON.parse(data);
							   
						  }catch(err){//result format is not json
							  						
							 
							   alert('读取数据发生错误，返回错误格式！');
							   
							   return;
							 
						  }
						  
						  if(jsonData.result && jsonData.result==1){
							  
							   wrap_obj.dbData=jsonData.data;
							   wrap_obj.total_amount=jsonData.total_amount;
							   
							   if(wrap_obj.dbData  && wrap_obj.dbData.length>0){
								  var itemData, $itemLi;
								  
								  var titleArr=field_title.split('|');					  
								  
								  for(var i=0; i<wrap_obj.dbData.length ; i++){
								       itemData=wrap_obj.dbData[i];
									   
									   var titleText='';
									   var sep='';
									   var sepT='';
									   for(var j=0; j<titleArr.length;j++){
										   titleText+=sep+itemData[titleArr[j]]+sepT;
										   sep=' (';
										   sepT=' )';
									   }
									   
									   $itemLi=$('<li><a idx="'+itemData[field_idx]+'">'+titleText+'</a></li>');
									   $itemLi.appendTo(wrap_obj.find('.view-board'));
								  }
								  
								  //view pager
								  $(ui).find('.pager-bar').remove();			
								  j79.viewPager(Math.ceil(jsonData.total_amount /per_page), pageNo, $(ui).find('.pager-area'),pageHandler);
								  $(ui).find('.total-amount-no').text(jsonData.total_amount);
								  
								  var searchInfo='';
								  if(postData && postData['searchValue']){
									  searchInfo='当前关键字:<b>'+postData['searchValue']+'</b> ';
								  }
								  //wrap_obj.find('.view-summary').html('<p>'+searchInfo+'总共 <b>'+wrap_obj.total_amount+'</b> 条记录，当前显示最多20条。</p>');
								  
								  wrap_obj.find('.view-summary .keywoard-view').html('<p>'+searchInfo+'</p>');
								     
							   }
							   
							   handleLoaded(iniValue);
							   
							  
							  
						  }else{
							   alert(uiTitle+'读取数据出错！信息:'+jsonData.error_code); 
						  }
						  
						    
			})
			.error(function(data, status,e) {//error when communicating with server
					 
							
						 
					wrap_obj.find('.view-board .loading').remove();	 
						
					 alert('<p>'+uiTitle+'读取数据时，连接服务器出错，请稍后再试！</p>');
						
						
							
			});*/
			
			
			
			
		};//-/
		
		
		
		
		//ini: 初始化
		var ini=function(){
			
		
			
			wrap_obj.find('#btnAdd').click(function(){

				   if(VIEW_STATIC){
					   return;
				   }
			    
					wrap_obj.find('.view-board').empty();
					wrap_obj.find('.view-board').append('<span class="loading"></span>');
			
					wrap_obj.find('.view-board').load(db_url, {'new_name':wrap_obj.find('#new_provider_name').val(), 'category':get_category()} , handleLoaded);			
				
				
			});
			
			
			//鼠标进入城市框，显示城市选择器
			viewer_obj.find('.input_region').click(function(){
				if(VIEW_STATIC){
					return;
				}
				if(ctrDisabled==true) return;
				if( box_obj.css('display')=='none'){
				   box_obj.css('z-index',9999);							
				   box_obj.show();
				   
				}else{
					box_obj.css('z-index',1);			
				  	box_obj.hide();
				}
					
				
				
			});
			
			wrap_obj.find('.bu_close').click(function(){
				
				box_obj.hide();
			});
			
			
			
			wrap_obj.find('#btnSearch').click(function(){
				startLoad();
			});
			
			wrap_obj.find('#btnSearchAll').click(function(){
				startLoad(null,true);
			});
			
			wrap_obj.find('.keyword').keypress(function(){
				
				if(event.keycode==13){
					startLoad();							
					
				}
			});
			
			
				
			 
			
		};//-------------------------------------------------
		
		
		
		
		ini();
		
		
		
		startLoad(current_value);
				
		

		
		
		
	};
})(jQuery);


function DBSelector_Setup(){
	
	var class_name="db-selector";		
	var ctrlist = $('.'+class_name);	
	
	for(i=0;i<ctrlist.length;i++)
	{
			
		$("."+class_name+":eq("+i+")").dbSelector();
		//$("."+class_name+":eq("+i+")").clear();		
		
		
			
	}
	
}


$(document).ready(function(){	
		
	DBSelector_Setup();

});
