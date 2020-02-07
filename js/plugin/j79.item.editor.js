/**
 itemeditor
*                                      
*              
*/
j79.loadCSS("/css/itemeditor.css");

(function($){
   
   $.fn.itemEditor = function(){
	   
	   var SELF=this;
	   
	
	   var DATA;
	   
	   var DATA_SAVER=$(SELF).attr('data-saver') || '';
	   	   
	   if(DATA_SAVER!=''){
		  $('#'+DATA_SAVER).hide();   
	   }
	   
	   var PLACE_HOLDER=$(SELF).attr('placeholder') || '请填写名称。多项，以逗号分开，比如: 红色,白色';
	   
	   /**
	   *  setData
	   *  set data to data-saver control
	   */
	   var setData=function(){
		   DATA=new Array();
		   $(SELF).find('.list-part .opt-item').each(function(index, element) {
                
				var curLabel=$(this).attr('title');
				if(curLabel){
					DATA[index]={label:curLabel};
					
				}
				
           });
		   console.log('setData=');
		   console.log(DATA);
		   if(DATA_SAVER!=''){
				   
		     $('#'+DATA_SAVER).text( JSON.stringify(DATA) ); 
		   }
			   
		   
	   };//-/setData
	   
	   
	   /**
	   *  readData
	   *  read data from data-saver to current UI.
	   */
	   var readData=function(){
		   var data;
		   
		   if(DATA_SAVER=='' || !$('#'+DATA_SAVER).val()){
			   return false;
		   }
		   console.log($('#'+DATA_SAVER).val());
		   
		   
		   data=j79.toJSON($('#'+DATA_SAVER).val());
		   
		   
		   //console.log(data);
		   if(data==null || data.length<=0){
		       return false;	   
		   }		   
		   
		   DATA=data;		   
		   var cItem,cImg, cLabel;
		   for(var i=0; i<DATA.length; i++){
			  
			  cItem=DATA[i];
			  
			  cImg=cItem.img ? '<img src="'+cItem.img+'" class="img-opt">':'';
			  if( ! cItem.label){
				 cLabel=cItem.toString();
				 cItem=new Object();
				 cItem.label= cLabel;
				 DATA[i]=cItem;
			  }
			  cLabel=cItem.label;
			  //cLabel=cLabel==''? cItem.toString(): cLabel;
			  
			  $('<li class="opt-item" title="'+cLabel+'">'+cImg+'<span>'+cLabel+'</span></li>').appendTo($(SELF).find('.list-part'));
			   
		   }
		   
				   
		   $('#'+DATA_SAVER).text( j79.toJSONString(DATA) ); 
		   
		   
		   
	   };//-/readData
	   
	   
	   
	   
	   var build=function(){
		   $(SELF).children().remove();
		   $('<div class="input-part"><div class="input-group">'+
		     '   <input type="text" class="form-control input-labels" name="ctr_labels" id="ctr_labels" placeholder="'+PLACE_HOLDER+'" />'+
			 '   <span class="input-group-btn"><button class="btn btn-info btn-add" type="button"><i class="glyphicon glyphicon-plus"></i> 添加</button></span>'+
			 '</div></div>').appendTo(SELF);
		   $('<ul class="list-part"></ul>').appendTo(SELF);
		   
		   
		   readData();
		   
		   
		   //attach del btn action
		   $(SELF).find('.list-part').delegate('.btn-del',"click",null,function(e){
				
				$(this).closest('.opt-item').remove();
				setData();
		   });
		   
		   
		   var addItem=function(){
			   if(!$(SELF).find('.input-labels').val()){
				   
				   return false;  
			   }
			   
			   var itemListStr=$(SELF).find('.input-labels').val();
			   $(SELF).find('.input-labels').val('');
			   
			   var itemList;
			   if( itemListStr.indexOf('，')>0 ){
				  itemList=itemListStr.split('，'); 
			   }else{
				  itemList=itemListStr.split(',');    
			   }			   
			   
			   var cImg, cLabel;
			   cImg='';
			   
			   if( typeof(DATA)=='undefined'){
				  DATA=new Array();   
			   }
			   
			   var curDataIdx=DATA.length;
			   
			   for(var i=0; i<itemList.length; i++){
			  
				  cLabel=itemList[i];
				  
				  DATA[curDataIdx+i]={ label:cLabel};
				  
				  
				  //cImg=cItem.img ? '<img src="'+cItem.img+'" class="img-opt">':'';
				  //cLabel=cItem.label? cItem.label :'';
				  
				  $('<li class="opt-item" title="'+cLabel+'">'+cImg+'<span>'+cLabel+'</span><a class="btn-del"><i class="glyphicon glyphicon-remove"></i> 删除</a></li>').appendTo($(SELF).find('.list-part'));
				   
			   }
			   if(DATA_SAVER!=''){
				   
				   $('#'+DATA_SAVER).text( JSON.stringify(DATA) ); 
			   }
			   
		   }
		   
		   
		   $(SELF).find('.input-part').delegate('.input-labels',"keypress",null,function(e){
				  				   
				 if(e.which == 13 ){
					addItem();     
				 }
		    });
		   
		   
		   //attach add btn action
		   $(SELF).find('.input-part').delegate('.btn-add',"click",null,function(e){
			   
			   addItem(); 
			   
		   });
		   
		    
		   
	   };
	   
	  
	   
	   
	   build(SELF);
	   
	   
	   
	     
	   
	  
   }
})(jQuery)//-----------------------------------------


function Setup_ItemEditor(){	

    var Control;
	var data_saver_id;
	
	var treeSel = $('.item-editor');
	
	for(i=0;i<treeSel.length;i++)
	{
		
		//listDir=$(treeSel[i]).hasClass('horizontal')? 1:0;
		$(treeSel[i]).itemEditor();
		
		
		
	}	

}

$(document).ready(function(){	
		
	Setup_ItemEditor();

});
