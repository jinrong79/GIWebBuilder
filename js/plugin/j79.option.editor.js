/**
*  optionEditor
*
*  根据设置的产品选项，产生相应SKU库存输入项目。
*  用户输入后，产生一个对象，把此对象转换成json字符串存入Data-saver中。
*  对象的格式：
*            object['opt'+optionvalue]=输入数值。
*            举例用来输入库存：  data['opt102]=99 表明，option=102时的库存值为99。
*                                                  其中option=102，指，选项1选择了2，选项2选择1的情况。
*
*  UI上需要的设置参数：
*                 option-list   : 必须。选项输入框的id列表，逗号分开。比如'input_opt_color,input_opt_size'
*                 data-saver    : 用户的输入值，存入的textarea控件id。
*                 target-label  : 当前操作的数据的名称，比如'库存'
*                 btn-open-label: 打开输入框面板的按钮标题
*                 default-value : 每个选项输入框中的默认的值，不存在默认为空字符串.
*                                      
*              
*/
j79.loadCSS("/css/opteditor.css");

(function($){
   
   $.fn.optionEditor = function(){
	   
	   var SELF=this;
	   var DATA=null;
	   
	   var DEFAULT_VALUE=$(SELF).attr('default-value') || '';
	   
	   var BUILT_ITEM_AMOUNT=0; //accounter for item generator
	       
	   //option input control selecotr list. include '#' at front of control name.
	   var optionInputList=new Array(); 
	   
	   var EDIT_TARGET_LABEL=$(SELF).attr('target-label') || '数据';
	   var BTN_OPEN_LABEL=$(SELF).attr('btn-open-label') || '打开/关闭 '+EDIT_TARGET_LABEL+'编辑器';
	   var BTN_RESET_LABEL='重设';
	   
	   var DATA_SAVER=$(SELF).attr('data-saver') || '';
	   if(DATA_SAVER!=''){
		  $('#'+DATA_SAVER).hide();   
	   }
	   
	   /**
	   *  saveData
	   *  read data from data-saver and save it to global var DATA
	   *
	   */
	   var saveData=function(){
		   
		   if(DATA_SAVER){
			  
			   var strData= $('#'+DATA_SAVER).val() ? $('#'+DATA_SAVER).val():'';   
			   
			   
			   var data=j79.toJSON($('#'+DATA_SAVER).val());
			   
			  
			   if(data!=null){
				   //console.log('get json:');
				    //console.log(data);
				  DATA=data;   
			   }
				
			  
			   
		   }
		   
	   }//-/saveData
	   
	   
	   
	   /**
	   *  build
	   *  read option-list attr, to get option input control list.
	   *  then, call view editor .
	   *  
	   *                  
	   */
	   var build=function(container){
		   var optListStr=$(container).attr('option-list') || '';
	   
		   if(optListStr==''){
			  return false;   
		   }
		   
		   var optCtrListTmp=optListStr.split(',');
		   optionInputList=new Array();
		   for(var i=0; i<optCtrListTmp.length;i++){
			 // if( $('#'+optCtrListTmp[i]).length>0 && $('#'+optCtrListTmp[i]).val() && $('#'+optCtrListTmp[i]).val()!=''){
			      if( $('#'+optCtrListTmp[i]).length>0){
				  optionInputList.push('#'+optCtrListTmp[i]);
				  
			  }
				 
		   }
		   
		   if(optionInputList.length>0){
			   
			  buildEditor(container,optionInputList);
			   
		   }else{
			  buildStatic(container);   
		   }
		   
	   };//-/build
	   
	   
	   /**
	   *  buildStatic
	   *
	   */
	   var buildStatic=function(container){
		   
		   $(container).find('.input-board').remove();
		   $('<div class="input-board"></div>').appendTo(container);
		   $('<form class="form-horizontal form-opt-data"></form>').appendTo($(container).find('.input-board'));
		   var curValue=DATA && typeof(DATA['opt_0_0_0'])!='undefined' ? DATA['opt_0_0_0'] :DEFAULT_VALUE;			
		   
		   $('<div class="form-group">'+
				   '  <label class="col-md-3 control-label">'+EDIT_TARGET_LABEL+':</label>'+
				   '  <div class="col-md-9">'+
				   '     <input class="form-control" type="text" name="optDataInput" opt-idx="1" value="'+curValue+'" />'+
				   '  </div>'+
				   '</div>').appendTo($(container).find('.input-board .form-opt-data')); 
	   };//-/buildStatic
	   
	   
	   
	   /**
	   *  buildEditor
	   *
	   */
	   var buildEditor=function(container, optInputList){
		   
		   $(container).find('.input-board').remove();
		   $('<div class="input-board"></div>').appendTo(container);
		   $('<form class="form-horizontal form-opt-data"></form>').appendTo($(container).find('.input-board'));
		   var i;
		   var total=0;
		   
		   //$('<div class="form-group"></div>').appendTo($(container).find('.form-opt-data'));
		   
		   BUILT_ITEM_AMOUNT=0;
		   
		   var inputHtml= buildCol('','',0,optInputList, '');
		   
		   $(inputHtml).appendTo($(container).find('.form-opt-data'));
		   
		   
		   $(container).find('.form-opt-data').delegate('.form-group','mouseenter',null,function(e){
			  
			  $(this).css('background-color','#FF9'); 
			   
		   });
		   $(container).find('.form-opt-data').delegate('.form-group','mouseleave',null,function(e){
			  
			  $(this).css('background-color','transparent');
			   
		   });
		   
		   //for(i=0;i<optInputList.length;i++){
		  
		  /* i=0
		   var optCtrValue=$(optInputList[i]).val();
		   var optList;
		   if(typeof(optCtrValue)=='undefined' || optCtrValue==''){
			  optList=new Array(); 
		   }else{
			   if( optCtrValue.indexOf('，')>0 && optCtrValue.indexOf(',')<0){
				  optList=optCtrValue.split('，'); 
			   }else{
				  optList=optCtrValue.split(',');    
			   }
		   }
		   */
		  
		   
		   /*if(i==0){
			 var colNum=Math.floor(12/optList.length);
			 
			 for(var j=0; j<optList.length; j++){
				 
				 var curCol=$('<div class="col-md-'+colNum+'"></div>');
			     $(curCol).appendTo( $(container).find('.form-opt-data'));
				 optStaticLabel=optList[j];
				 var optLevel=1;
				 if(optInputList.length>1){
				    buildCol(optStaticLabel,j+1,optLevel,optInputList,curCol);
				 }else{
					$('<div class="form-group">'+
				   '  <label class="col-sm-6 control-label">[ '+optStaticLabel+' ]'+EDIT_TARGET_LABEL+':</label>'+
				   '  <div class="col-sm-6">'+
				   '     <input class="form-control" type="text" name="optDataInput" opt-idx="'+(j+1)+'" value="'+DEFAULT_VALUE+'" />'+
				   '  </div>'+
				   '</div>').appendTo(curCol); 
				 }
				 
			 }
			 
			 
			 
		   }*/
		      
		   //}
		   
		   
		   
		   
	   };//-/buildEditor
	   
	   
	   /**
	   *  buildCol
	   *  递归方式显示编辑项目
	   */
	   var buildCol=function(staticLabel, staticIdx, optLvl, optCtrList,  htmlString){
		   
		   
		   var buildItem=function(curStaticLabel,curStaticIdx){
			    
				var htmlItem;
			   
			    curStaticLabel=curStaticLabel==''? '': '[ '+curStaticLabel+' ]';
				
				
				//console.log('BUILT_ITEM_AMOUNT'+BUILT_ITEM_AMOUNT);
				//console.log(curStaticIdx);
				
				var curValue=DATA && typeof(DATA['opt_'+curStaticIdx])!='undefined' ? DATA['opt_'+curStaticIdx] :DEFAULT_VALUE;
				
					
				
				htmlItem='<div class="form-group"><label class="col-md-4 control-label">'+curStaticLabel+' '+EDIT_TARGET_LABEL+':</label>'+
				 '  <div class="col-md-6">'+
			     '     <input class="form-control" type="text" name="optDataInput" opt-idx="opt_'+curStaticIdx+'" value="'+curValue+'" />'+					   
				 '</div></div>';
				
			   /*$(wrapHead+'  <label class="col-md-2 control-label">'+curStaticLabel+' '+EDIT_TARGET_LABEL+':</label>'+
				 '  <div class="col-md-2">'+
			     '     <input class="form-control" type="text" name="optDataInput" opt-idx="opt_'+curStaticIdx+'" value="'+DEFAULT_VALUE+'" />'+					   
				 '</div>'+wrapTail).appendTo(container);*/
			   BUILT_ITEM_AMOUNT++;
			   return htmlItem;
			   
		   }//-buildItem
		   
		   
		   
		   var optCtrValue=$(optCtrList[optLvl]).val();
		   var optValueList;
		   if(typeof(optCtrValue)=='undefined' || optCtrValue=='' || optCtrValue.length<3){//empty data
			  optValueList=new Array();
			   
			   cur_staticIdx= staticIdx==''? '0' : '0_'+staticIdx;			  		 
			   cur_staticLabel=staticLabel;
			   if(optLvl<optCtrList.length-1){				 
					  htmlString=buildCol(cur_staticLabel,cur_staticIdx,optLvl+1,optCtrList,htmlString);
					 
			   }else{//last level			         
					 htmlString+=buildItem(cur_staticLabel,cur_staticIdx);					
					 
			   }
			   
			  
		   }else{//not empty, then try parse JSON.
			   
			   var optValueList=new Array();
			   var optValueData=j79.toJSON(optCtrValue);
			   
			   
			   
			   if(optValueData==null || optValueData.length<=0){//JSON data is invalid or empty
				   
				   cur_staticIdx= staticIdx==''? '0' : '0_'+staticIdx;			  		 
				   cur_staticLabel=staticLabel;
				   if(optLvl<optCtrList.length-1){				 
						  htmlString=buildCol(cur_staticLabel,cur_staticIdx,optLvl+1,optCtrList,htmlString);
						 
				   }else{//last level			         
						 htmlString+=buildItem(cur_staticLabel,cur_staticIdx);					
						 
				   }
			   
				   	   
			   }else{//JSON data is valid.	   
			   
			   
				   var cItem,cImg, cLabel;
				   
				   for(var i=0; i<optValueData.length; i++){
					  
					  cItem=optValueData[i];
					  
					  if(cItem){
						  cLabel=cItem.label?  cItem.label : cItem.toString();
						  optValueList.push(cLabel);  
					  }
					  
					 
					   
				   }
				   var cur_staticIdx,cur_staticLabel;
				   for(var i=0; i<optValueList.length;i++){
				   
					 //cur_staticIdx=staticIdx+(i+1)*Math.pow(10,optLvl*2);
					 
					 cur_staticIdx= staticIdx==''? (i+1) : (i+1)+'_'+staticIdx;				 
					 cur_staticLabel=staticLabel!='' ?staticLabel+' - '+optValueList[i]: optValueList[i];
					 
					 //console.log('cur idx='+cur_staticIdx);
					 //console.log('cur label='+cur_staticLabel);
					 
					 if(optLvl<optCtrList.length-1){				 
						 htmlString=buildCol(cur_staticLabel,cur_staticIdx,optLvl+1,optCtrList,htmlString);
						 
					 }else{//last level
					 
						 htmlString+=buildItem(cur_staticLabel,cur_staticIdx);
						 
						
						
						 
					 }
				   }//for
			   }
			   
			   
			   /*
			   if( optCtrValue.indexOf('，')>0 && optCtrValue.indexOf(',')<0){
				  optValueList=optCtrValue.split('，'); 
			   }else{
				  optValueList=optCtrValue.split(',');    
			   }*/
			   
			   
			   
			   
			   
			   
			   
		   }
		   
		   return htmlString;
		  
		   
		   
		   
		   
		   
		   
		   
	   };//-/buildCol
	   
	   
	   /**
	   *  setDataSaver
	   *  save input data to data saver.
	   */
	   var setDataSaver=function(){
		   
		   if(DATA_SAVER!=''){
			  /* var data=new Object();
			  $(SELF).find('.input-board input').each(function(index, element) {
				  var curIdx=$(this).attr('opt-idx') || '';
				  if(curIdx!=''){
					  data[curIdx]=$(this).val();
				  }
			  }); */
			  
			  var data=new Array();
			  $(SELF).find('.input-board input').each(function(index, element) {
				  var curIdx=$(this).attr('opt-idx') || '';
				  
				  if(curIdx!=''){
					  var curItem={};
					  curItem[curIdx]=$(this).val();
					  data.push(curItem);
					  //data[curIdx]=$(this).val();
				  }
			  }); 
			  
			  $('#'+DATA_SAVER).text(JSON.stringify(data));
				
			}
		   
	   };//-/setDataSaver
	   
	   
	  
	   $('<div class="btn-part"><a class="btn btn-info btn-open-option-editor">'+BTN_OPEN_LABEL+'</a></div>').appendTo(SELF);
	   
	   $('<a class="btn btn-default btn-reset-editor">'+BTN_RESET_LABEL+'</a>').appendTo($(SELF).find('.btn-part'));
	   
	   //attach action to btn-open
	   $(SELF).find('.btn-open-option-editor').click(function(e) {
           
		   saveData();
		   build(SELF);
		   $(SELF).find('.input-board').show(400);
		   //if(DATA_SAVER!=''){
		   //  $('#'+DATA_SAVER).text('');   
		  // }
		   //$(SELF).find('.input-board').toggle(500);		    
       });
	   
	   //attach action to btn-reset
	   $(SELF).find('.btn-reset-editor').click(function(e) {
		   saveData();
           build(SELF);
		   $(SELF).find('.input-board').show(400);
		   if(DATA_SAVER!=''){
			 $('#'+DATA_SAVER).text('');   
		   }
		   setDataSaver();
       });
	  
	   //attach action to input chage.
	   $(SELF).delegate('.input-board input','change',null,function(e){
		  	  
		  setDataSaver();	
		  saveData();		   
	   });
	   saveData(); 
	   build(SELF);
	   
	   
	     
	   
	  
   }
})(jQuery)//-----------------------------------------


function Setup_OptionEditor(){	

    var Control;
	var data_saver_id;
	
	var treeSel = $('.option-editor');
	
	for(i=0;i<treeSel.length;i++)
	{
		
		//listDir=$(treeSel[i]).hasClass('horizontal')? 1:0;
		$(treeSel[i]).optionEditor();
		
		
		
	}	

}

$(document).ready(function(){	
		
	Setup_OptionEditor();

});
