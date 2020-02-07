// admin product list

/**
*  j79List
*  list UI handler class.
*
*  @usage:
          1)initialize and view list:
 
            var listSetting={ ... ...}
            var list1=new j79List(listSetting);	
            list1.ini(); 
      
	      2)reload list:
		    //load page 5:
		    list1.read( { page: 5});
			
			//load category=100000000 list:  
			list1.read( { category: 100000000});  
			   
*  data foramt when initialize:params={
 
										uiList        :  list container selector string, like '#proList'
										uiBtn         :  list-modifier container btn-group selector string.
										uiCategory    :  list-category container selector string.
										uiSort        :  list-sort container selector string
										uiPager       :  list-pager container selector string.
										uiSearch      :  list-search container selector string
										target        :  server target when posting data to.
										itemGenerator :  function to view a single line of list: 
										                 function(rowData, idx)
										page          :  current page
										perPage       :  record amount in one page.
										
										div           :  current div idx
										category      :  current category idx
										sort          :  current sort value
										searchValue   :  current seach value
										searchKey     :  current search key.
										category_xml  :  category xml url.
					                  }
*
*  
*/
function j79List(params){
	
	
	
	this.totalAmount=0;
	//this.page=1;	
	
	this.params=new Object(); //list params.

	this.category_xml=params && params.category_xml ? params.category_xml : '';
	
	
	
	this.params.page=j79.getURLParam('page') && parseInt(j79.getURLParam('page'))>0 ? parseInt(j79.getURLParam('page')) :1;
	
	//div:
	if(typeof params.div =='undefined' || params.div==null ){ 
	
		if(j79.getURLParam('div') && parseInt(j79.getURLParam('div'))>0 ){
			this.params.div=j79.getURLParam('div');
			
			
		   
		   /*var divName=j79App.DIVISION[div];
		   $('#titleMain').text(divName+'-商品列表');*/
		
			
		}
	}else{
	   this.params.div=	params.div;
	}
	
	
	var div=this.params.div;		
	
	
	
	//sort
	if(typeof params.sort =='undefined' || params.sort==null ){ 
		if(j79.getURLParam('sort') && j79.getURLParam('sort')!='' ){
			this.params.sort=j79.getURLParam('sort');
		}
	}else{
		this.params.sort=params.sort;
	}
	
	//category	
	if(typeof params.category =='undefined' || params.category==null ){ 
		if(j79.getURLParam('category') && j79.getURLParam('category')!='' ){
			this.params.category=j79.getURLParam('category');
	    }
	}else{
		this.params.category=params.category;
	}
	
	
	//searchValue
	if(typeof params.searchValue =='undefined' || params.searchValue==null ){ 
		if(j79.getURLParam('searchValue') && j79.getURLParam('searchValue')!='' ){		
		  this.params.searchValue=decodeURI( j79.getURLParam('searchValue')) ;
	    }
	}else{
		this.params.searchValue=params.searchValue;
	}
	
	//searchKey	
	if(typeof params.searchKey =='undefined' || params.searchKey==null ){ 
		if(j79.getURLParam('searchKey') && j79.getURLParam('searchKey')!='' ){
			this.params.searchKey=j79.getURLParam('searchKey');
		}
	}else{
		this.params.searchKey=params.searchKey;
	}
	
	
	
	
	this.params.per_page=12;
	
	
	if(params){
	    this.uiList= params.uiList ;
		this.uiBtn=params.uiBtn;
		this.uiCategory=params.uiCategory;
		this.uiSort=params.uiSort;
		this.uiPager=params.uiPager;
		this.uiSearch=params.uiSearch;				
		this.itemGenerator=params.itemGenerator;	
				
		this.params.target=params.target;
		this.params.per_page=params.perPage || 16;
		this.params.page=params.page || 1;
	}
	
	this.disabledList=new Array();
		  
}//-/

/**
*  j79List methods  
*/	  
j79List.prototype={
	       
		   /**
		   *  validate settings.
		   *  @return {bool} : true -- valid settings. false -- invalid settings.
		   */
		   isValid:function(){
		      if(!this.uiList || !this.itemGenerator || !this.params || !this.params.target){
				  return false;	
			  }else{				 
				  return true;	
			  }   
		   },//-/isValid


	/**
	 *  iniCategory
	 *  category ui create.
	 */
	iniCategory:function(){

		if(this.category_xml!=''){

			var curCatIdStr=this.params.category && this.params.category!='' && this.params.category>0 ? ' selected-id="'+this.params.category+'" ' :'';

			var $element=$( '<div class="all"><a class="btn btn-link btn-view-all"><i class="glyphicon glyphicon-th-large"></i> 显示全部</a></div>'+
					'<div id="list_cat_tree" class="tree-navi" data-saver="curCat" data-xml="'+this.category_xml+'"  '+curCatIdStr+'><input type="hidden" class="input-current-category" id="curCat" name="curCat" value="" /></div>');

			$element.appendTo($(this.uiCategory));
			$(this.uiCategory).find('#list_cat_tree').treeNavigator();
		}


	},//-/iniCategory
		   
		   /**
		   *  ini
		   */
	       ini:function(){



			   var selfObj=this;


			   selfObj.iniCategory();
			   
			   if(!this.isValid()){
			       console.log('list cannot create due to invalid params!');   
				   return false;
			   }
			   
			   //attaching btn action 
			   $(this.uiBtn+' .btn-select-all').click(function(e) {            
				 selfObj.selectAll();
			   });
			   
			   $(this.uiBtn+' .btn-delete').click(function(e) {								   
				 selfObj.deleteMulti();
			   });
			   
			   $(this.uiBtn+' .btn-onshelve').click(function(e) { 			               
				 selfObj.setShelveMulti(1);
			   });
			   
			   $(this.uiBtn+' .btn-offshelve').click(function(e) {				 	            
				 selfObj.setShelveMulti(0);
			   });
			   
			   $(this.uiSort+'  .btn').click(function(e) {	
			       
				  
				   			 	            
				   var sortKey=$(this).attr('sort-key');				  
				   var sortValue=$(this).find('input').val();		   
				
				   
				   if( $(this).hasClass('active')){		
				      
					  if(sortValue=='down'){
						   $(this).find('i').removeClass('glyphicon-arrow-down');
					       $(this).find('i').addClass('glyphicon-arrow-up');
						   $(this).find('input').val('up');
						   sortValue='up';
						   
					  }else{
						   $(this).find('i').removeClass('glyphicon-arrow-up');
					       $(this).find('i').addClass('glyphicon-arrow-down');
						   $(this).find('input').val('down');
						   sortValue='down';
					  }      
					 
					  
				   }
				   //console.log(sortKey+sortValue);
				   
				   
				   selfObj.params.page=1;
				   selfObj.params.sort=sortKey+sortValue;
				   selfObj.read();				   
				   
			   });	
			   
			   
			   
			   	   
			   
			   //btn of category view all
			   $(this.uiCategory+' .btn-view-all').click(function(e) {
				 delete selfObj.params.category;
				 selfObj.params.page=1;            
				 selfObj.read();
				 $(selfObj.uiCategory+' .tree-navi').treeNavigatorReset();
			   });
			   
			   //category change action:
			   $(this.uiCategory+' .input-current-category').unbind('change');
			   $(this.uiCategory+' .input-current-category').change(function(e) {

				   console.log('changed---');
				 
				 var params=new Object();

				 selfObj.params.page=1;
				 selfObj.params.category=$(this).val();
		             //params.category=Number($(this).val());
		         selfObj.read();
				 

				 
			   });
			   
			   //hadler for search
			   var handlerSearch=function(){
				   
				  // alert('search');
				   
				   var searchValue=$(selfObj.uiSearch+'  .input-search-value').val();
				   var searchKey=$(selfObj.uiSearch+'  .input-search-key').val();
				   
				 
				   
				   if( searchValue &&  searchKey){	
				   
				     		   
					  
					  var param=new Object();
					  
					  if($(selfObj.uiCategory+'  #curCat') && $(selfObj.uiCategory+'  #curCat').val() ){
					      var curCat=Number($(selfObj.uiCategory+'  #curCat').val());
						  if(curCat>0){
							  param.category=curCat;
						  } 
						  
						  
					  }
					  selfObj.params.page=1;
					  selfObj.params.searchKey=searchKey;
					  selfObj.params.searchValue=searchValue;
					  
					  selfObj.read(); 
					  //$(selfObj.uiCategory+' .tree-navi').treeNavigatorReset();
				   }
				   
			   }
			   
			   
			   $(this.uiSearch+' .btn-search').click(function(e) {				   
				  handlerSearch(); 			 
				 
			   });
			   
			   $(this.uiSearch+' .input-search-value').keydown(function(e){
				  				   
				 if(e.which == 13 ){
					handlerSearch();     
				 }
			   });
			   //action for search reset							 
			   $(this.uiSearch+' .btn-reset').click(function(e) {
				   
				   $(selfObj.uiSearch+' .input-search-value').val('');
				   
				   delete selfObj.params.searchKey;
				   delete selfObj.params.searchValue;
				   selfObj.params.page=1;
				   selfObj.read(); 
				   //$(selfObj.uiCategory+' .tree-navi').treeNavigatorReset();
				   
			   });
			   
			   
			   
			   
			   
			   
			   //read data from server and display on list container.
			   this.read();  
			    
		   },//-/int
	       
		   /**
		   *  read
		   *  read data from server
		   *  @param {object} params : data for conditioning list.
		   *                           e.g.: params.page=2  -> get page 2 of list.
		   */
		   read:function(){

			   console.log('read---');
			    var selfObj=this;
				
				//check validation
				if(!this.isValid()){
			       console.log('list cannot create due to invalid params!');   
				   return false;
			    }
				
				//set post-data default value
				var dataPost={
					page:1,
					format:0,					
					action: 'SELECT',					
					
				};				
				
				//combine current param values to post data.			
				$.extend(dataPost,this.params);			
				
				//reset UI and view loading
				$(selfObj.uiList).children().remove();
				j79.viewLoading(this.uiList);

			   /*console.log('1111');
			   console.log('222');
			   console.log('333');
			   console.log('444');
			   console.log('555');*/
				
				//communicating with server:	
				j79.post(
				
				
				    {
						title: "读取产品列表", 
						data : dataPost, 
						actionSuccess: function(result){  //after success
							              console.log('get result!----');
										  j79.hideLoading(selfObj.uiList);
										  if(result){ 
											  
											  //get total amount
											  if(result.total_amount && Number(result.total_amount)>=1){
												  selfObj.totalAmount=Number(result.total_amount);
												  
											  }
											  
											  //selfObj.page=selfObj.page;
											  
											  
											  if( result.data){
												  //view list
												  selfObj.view(result.data);
												  selfObj.attachActionHandler();
												  
											  }
										  }
					
					                    },
						actionFailed: function(result){
							                 j79.hideLoading(selfObj.uiList);
											 console.log('load list failed!');
							
						                }
									
					
					
					});
		   }, //-/read
		   
		   /**
		   *  view
		   *  view list to customer
		   *  @param {array of object} proData : product list data get from server result['data']; 
		   */		   
		   view:function(proData){
		   
		        if(!this.isValid()){
			       console.log('list cannot create due to invalid params!');   
				   return false;
			    }
		   
		        $(this.uiList).children().remove();
				
				
				
				//if no result:
				if(proData.length==0){
				  $listItem=$('<div class="list-none"><i class="glyphicon glyphicon-info-sign"></i> 正在建设中... </div>');
				  $listItem.appendTo($(this.uiList));
				  $(this.uiPager).find('.pager-bar').remove();	
				  return;	
				}
					   
				//var shelveTag,shelveBtn,flagShelve;	  
				for(var i=0; i< proData.length;i++){
					
					
					//generate list item element
					$listItem=this.itemGenerator(proData[i], i);
					
					//append new item to list.
					$listItem.appendTo($(this.uiList));
					  
					
				}
				$(this.uiPager).find('.pager-bar').remove();
				
				
				
				j79.viewPager(Math.ceil(this.totalAmount /this.params.per_page), this.params.page, this.uiPager,this);
				$(this.uiPager).find('.total-amount-no').text(this.totalAmount);
				
		   
			   
		   },//-/view
		   
		   /**
		   *  setPage
		   *  set page no
		   */
		   setPage:function(newPage){
			  
			   //console.log(listObj);
			   this.params.page=newPage;
			   this.read();
			   
		   },//-/
		   
		   
		   
		   /**
			*  attachActionHandler
			*  attach action handler to list items.
			*/
			attachActionHandler:function(){
			  
				//attach Delete
				this.attachActionDelete(this.uiList);
				
				//attach shelve related action
				this.attachActionShelve(this.uiList);
				
			
				
				
				
			},//-/attachActionHandler
			
			
			/**
			*  attachActionDelete
			*  attach delete related action
			*/
			attachActionDelete: function(uiList){
				
				var selfObj=this;
				
				//attach Delete
				$(uiList+' li .btn-delete').click(function(e) {
					
					var curIdx=$(this).parent().closest('li').attr('item-id');
					
					selfObj.deleteItem(curIdx, uiList);
					
				});	
			},//-/attachActionDelete
			
			
			
			
			
			/**
			*  attachActionShelve
			*  attach shelve related action
			*/
			attachActionShelve: function(uiList){
				
				var selfObj=this;
				
				//attach onshelve
				$(uiList+' li .btn-onshelve').unbind('click');	
				$(uiList+' li .btn-onshelve').click(function(e) {
					
					   
					
					var curIdx=$(this).parent().closest('li').attr('item-id');
					$(this).btnLoading();	
					//$(this).attr('disabled','disabled');
					selfObj.setShelve(curIdx, 1,uiList);
					
				});	
				
				//attach offshelve
				$(uiList+' li .btn-offshelve').unbind('click');	
				$(uiList+' li .btn-offshelve').click(function(e) {
					
					var curIdx=$(this).parent().closest('li').attr('item-id');
					//$(this).btnLoading();	
					//$(this).attr('disabled','disabled');
					selfObj.setShelve(curIdx, 0, uiList);
					
				});	
			},//-/attachActionShelve
			
			
			/**
			*  deleteItem
			*  delete item by given idx.
			*  idx can be single idx number, or can be idx list seperated by ',' like:'12,13'
			*/
			deleteItem: function(idx, uiList){
			    var selfObj=this;
				
				
				if(j79.isId(idx)){
					
				    var flagContinue=confirm("确认要删除吗？");					
					if(!flagContinue){
						return false;			
					
					}
					
					
					
					var dataPost=new Object();
					dataPost.target=this.params.target;
					dataPost.action='DELETE';
					dataPost.format=0;			
					dataPost.idx=idx;
					
					
					
					//generate disabled btn list:
					disBtns=new Array();
					var arrIdx=idx.split(',');
					for(var i=0;i<arrIdx.length;i++){
						disBtns.push(uiList+' li[item-id='+arrIdx[i]+'] .btn-delete');					
						
					}
					if(arrIdx.length>1){
						disBtns.push(this.uiBtn+' .btn-delete');
					}					
					
					//start to post to server:
					j79.post(
					
					
					 {
						title: "删除产品", 
						data : dataPost, 
						actionSuccess: function(result){  //after success
						                  
										  var arrIdx=idx.split(',');
										  for(var i=0;i<arrIdx.length;i++){
											  console.log(uiList+' li[item-id='+arrIdx[i]+']');						
											  $(uiList+' li[item-id='+arrIdx[i]+']').remove();
										  }
					
					                    },
									
					     disabled: disBtns
					
					});				
					
					
					
					
				}else{
					return false;	
				}
				
				
			},//-/deleteItem
			
			
			
			
			
			/**
			*  setShelve
			*  set item shelve by given idx.
			*  idx can be single idx number, or can be idx list seperated by ',' like:'12,13'
			*  
			*/
			setShelve: function (idx, value,uiList){
				
				var selfObj=this;
			
				
				
				if(j79.isId(idx)){
					
					value=Number(value)==1 ? 1:0;	
					
					var dataPost=new Object();
					dataPost.target=this.params.target;;
					dataPost.action='PATCH';
					dataPost.format=0;			
					dataPost.idx=idx;
					dataPost.shelve=value;
					
					
					//generate disabled btn list:
					disBtns=new Array();
					var btnClass= value==1 ? '.btn-onshelve': '.btn-offshelve';
					var arrIdx=idx.split(',');
					for(var i=0;i<arrIdx.length;i++){
						disBtns.push(uiList+' li[item-id='+arrIdx[i]+'] '+btnClass);			
						
					}
					if(arrIdx.length>1){
						disBtns.push(this.uiBtn+' '+btnClass);
					}
					
							
					
					j79.post(
							  {
								  title: "产品上架/下架", 
								  data : dataPost, 
								  actionSuccess: function(data){//after success						
							
													var arrIdx=idx.split(',');													
																						
													//var btnClass= value==1 ? '.btn-onshelve': '.btn-offshelve';
													var btnClassNew= value==1 ? 'btn btn-default btn-offshelve':'btn btn-success btn-onshelve';							
													var btnLabel=value==1 ? '<i class="glyphicon glyphicon-download"></i> 下架':'<i class="glyphicon glyphicon-upload"></i> 上架';
													
													
													
													for(var i=0;i<arrIdx.length;i++){
														
														
														
														//$(uiList+' li[item-id='+arrIdx[i]+']').find(btnClass).removeAttr('disabled');
														$(uiList+' li[item-id='+arrIdx[i]+']').find(btnClass).html(btnLabel);						
														
														
														$(uiList+' li[item-id='+arrIdx[i]+']').find(btnClass).prop('class',btnClassNew);
														
														
														
														if(value==1){
															$(uiList+' li[item-id='+arrIdx[i]+'] .title').find('span.offshelve').remove();
														}else{
															
															$newFlag=$('<span class="label label-info offshelve">已下架</span>');
															$(uiList+' li[item-id='+arrIdx[i]+'] .title').find('span.offshelve').remove();
															$newFlag.appendTo($(uiList+' li[item-id='+arrIdx[i]+']').find('.title'));
															
															
														}
													}
													selfObj.attachActionShelve(uiList);
												
												}, //actionSuccess/
												
								   /*actionFailed: function(data){
													  var arrIdx=idx.split(',');
													  var btnClass= value==1 ? '.btn-onshelve': '.btn-offshelve';
													  for(var i=0;i<arrIdx.length;i++){						
														  $(uiList+' li[item-id='+arrIdx[i]+']').find(btnClass).removeAttr('disabled');
													  }	
												 },*/
											  
								   disabled: disBtns
							  
							  });
					
					
					
					
					
				
					
					
					
					
				}
				
				
			},//-/setShelve
			
			
			/**
			*  getSelectedIdx
			*  get selected item idx list seperated by ','
			*/
			getSelectedIdx:function (){
				
				var selectedItems=$(this.uiList+" input[name='selectedItems']:checked").closest("li");		
				
				var curItem;
				var idxList='';
				var sep='';
				for(i=0;i<selectedItems.length;i++)
				{
					curItem=selectedItems[i];
					idxList+=sep+parseInt($(curItem).attr('item-id'));
					sep=',';
				}
				return idxList;
				
			},//-/getSelectedIdx
			
			
			/**
			*  selectAll
			*
			*/
			selectAll: function (){
				
			   if(  $(this.uiBtn+' .btn-select-all').hasClass('active')){
					$(this.uiList+" input:checkbox").each(function () {
						   $(this).prop('checked',false);
					});
				   $(this.uiBtn+' .btn-select-all').removeClass('active');
			   }else{
				   $(this.uiList+" input:checkbox").each(function () {
						   $(this).prop('checked',true);
					});
				   $(this.uiBtn+' .btn-select-all').addClass('active');
			   }
			},//-/
			
			
			/**
			*  deleteMulti
			*  delete selected items.
			*/
			deleteMulti: function (){
				
				
				
				var idxList=this.getSelectedIdx();
				if(idxList!=''){	    
					this.deleteItem(idxList, this.uiList);
						
				}else{
					$(this.uiBtn+' .btn-delete').btnReset();
					alert('请先选择要操作的项（勾选产品图左侧的框）！');	
				}
				
			},//-/deleteMulti
			
			/**
			*  setShelveMulti
			*  delete selected items.
			*/
			setShelveMulti:function (shelveStatus){
				
				var idxList=this.getSelectedIdx();
				if(idxList!=''){	    
					this.setShelve(idxList,shelveStatus,this.uiList);
						
				}else{
					var btnClass= shelveStatus==1 ? '.btn-onshelve': '.btn-offshelve';
					$(this.uiBtn+' '+btnClass).btnReset();
					alert('请先选择要操作的项（勾选产品图左侧的框）！');	
				}
				
			},//-/setShelveMulti
		   
		   
};//=/j79List.prototype






