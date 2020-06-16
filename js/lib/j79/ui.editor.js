// JavaScript Document
/**
*  j79UIEditor
*  construct typical editor ui。
*  
*  needs: js/lib/core.list.js
*
*  @param {object} settings : 
                                   ={
									  target        : server target for update and listing, 
													  like: 'AdmProUpdater',
													  
									  xmlUrl        : format xml file url
									  xmlUrlEdit    : format xml file url for edition. 
									  
									  objectName    : main text name for target object.
									                  like: '产品'
									  
									  ui            : ui selector info object. has default values.
									}
*
*/
function j79UIEditor(params, iniData){
    
	 
	 var settings={

		 "flag_return_object": false,   //return detail in object format or json string . true- object; false- json string[default]
		 "ui":{
						"title"          : "body .page-title",
						"form"           : "#product-detail-form",
						"form_container" : ".product-detail-edit",

			  }
		 
	 };
	 
	 settings=$.extend(settings, params);
	 
	 
	console.log(settings);
	 
	
	/*=============  function ini ================ */
	
	
	var ini=function(){
	   
	    //set title
		//var idx=Number(j79.getURLParam('idx'));
		var idx= j79.getURLParam('idx') || '';

		if(idx!=''){//idx exists:
			$(settings.ui.title).html(settings.objectName+'修改 <span class="label label-info">编号: <b>'+idx+'</b></span>');
			j79.viewLoading(settings.ui.form_container);		
			j79.post({
														
					  data          : {         
									   "target" : settings.target,
									   "action" : 'SELECT',
									   "idx"    : idx
									   
									  },
									  
					  title         : "读取"+settings.objectName+"详细信息" ,//alert window title.
					  actionSuccess : function(result){  
													 
										  j79.hideLoading(settings.ui.form_container);
										  if(result){
											  setupForm(result.data,1);													  
										  }												      
						  
									  },
					  actionFailed : function(result){  
													 
										  j79.hideLoading(settings.ui.form_container);
										  if(result && result.error_code){
											  
											  if(result.error_code=2000){
												 alert("出错，无权读取"+settings.objectName+"详细信息！");   
											  }
										  }												      
						  
									  }
					  
										
					 }
			  );
		}else{//idx not exists:
			$(settings.ui.title).html('添加新'+settings.objectName);
			j79.viewLoading(settings.ui.form_container);	
			
			var paramData=null;
			/*var shopId=j79.getURLParam('shop');
			if(shopId && shopId>0){
				paramData={'shop_idx':shopId};
			}*/

			if(iniData){
				paramData=iniData;
			}
			
			
			
			setupForm(paramData,0);
		}	
		
	};//-/
	
	/**
	*  setupForm
	*  @param {obj} formData : form data.
	*  @param {int} mode     : 0- insert ; 1- modify.
	*/
	var setupForm=function(formData, mode){
		
		console.log('form ui editor:mode=>'+mode);
		console.log('form ui editor:xmlUrlEdit=>'+settings.xmlUrlEdit);

		var xmlurl= mode==1 && settings.xmlUrlEdit && settings.xmlUrlEdit!='' ?settings.xmlUrlEdit : settings.xmlUrl;

		console.log('form ui editor:xml selected=>'+xmlurl);

		var flagEdit= mode && mode==1 ? true :false;

        var flagStaticView= j79.getURLParam('view_static')=='1' ? true : false;

		var rndStr= xmlurl.indexOf('?')>0 ? '&rnd='+(Math.random()*1000):'?rnd='+(Math.random()*1000);

		var formSetting={
		
			urlXML: xmlurl+rndStr,
			uiForm: settings.ui.form,
			data  : formData,
            flagView: flagStaticView,
			flagEdit: flagEdit,
			actionFinished: function(){
									   
									   j79.hideLoading(settings.ui.form_container);	
                                       if(flagStaticView==true){

                                           $(settings.ui.form).addClass('form-view');
                                           return true;
                                       }
									   $btnSubmit=$('<div class="form-group"><div class="col-md-10 col-md-offset-2">'+
													'<button class="btn btn-primary btn-lg btn-submit" type="button"><i class="glyphicon glyphicon-ok"></i> 提交</button>'+
													'</div></div>');
									   $btnSubmit.appendTo($(settings.ui.form));

									   
									   $(settings.ui.form).find('.btn-submit').click(function(e) {
											
											var result=true;

											$(settings.ui.form+" [form-input]").each(function(){
											  var singleResult=$(this).validate();
											  if(singleResult==false){
												result=false;		
											  }
											});

											if(result==true){
												
												formSubmit();
											}else{
												alert("信息填写不完整，请检查~");  
											}
											  
									   });									   
									   
									   ini_onBlur_validation();
							}
			
		};

		console.log('form setting before form building:');
		console.log(formSetting);
		
		var formB=new j79FormBuilder(formSetting);
		formB.ini();	
		
	};//-/
	
	/**
	*  formSubmit
	*  提交表单
	*/
	var formSubmit=function(){
		
		var postData={target:settings.target};
	
		if(j79.getURLParam('idx') && j79.getURLParam('idx')!=''){//edit
			postData.action='UPDATE';
			postData.idx=j79.getURLParam('idx');
			
		}else{//add new
			postData.action='CREATE';
		}
		
		//append form data to postData
		var formData={};
		$(settings.ui.form+" [form-input]").each(function(){
			var singleResult=$(this).validate();
			console.log('form item:');
			console.log($(this));


			  if(singleResult==true){

				  
				  if($(this).val()){

					  var valueType=$(this).attr('value-type') || '';


					  if(settings.flag_return_object==true && valueType.toLowerCase()=='json'){//if json type and flag_return_object==true, then return in object format.
						  console.log('this item is json type and flag_return_object=true :');
						  var curData=j79.toJSON($(this).val());
						  if(curData){
							  formData[$(this).attr('id')]= curData;
						  }
					  }else{//else return in json string.
						  formData[$(this).attr('id')]=$(this).val();
					  }


					  /*if(valueType.toLowerCase()=='json'){
						  console.log('this item is json type============');
						  var curData=j79.toJSON($(this).val());
						  if(curData){
							  formData[$(this).attr('id')]= curData;
						  }
					  }else{
						  formData[$(this).attr('id')]=$(this).val();
					  }*/




					  
				  }
				  
			  }
		});	
		postData.data=formData;

		console.log('form data:');
		console.log(formData);
		//view loading layer win:
		var mwsettings={
						title: '提交更新',
						bodyHtml: 
						 '<div class="loading">'+
						 '<p>正在提交更新，请耐心等待...</p>'+
						 '<div class="sk-wave">'+
						 '<div class="sk-rect sk-rect1"></div>'+
						 '<div class="sk-rect sk-rect2"></div>'+
						 '<div class="sk-rect sk-rect3"></div>'+
						 '<div class="sk-rect sk-rect4"></div>'+
						 '<div class="sk-rect sk-rect5"></div>'+
						 '</div></div>',
						btnHtml:'<b></b>'
		};
		j79.viewModal( mwsettings, 'mw1' );
		
		//post data:
		j79.post({
													
				  data          : postData,
								  
				  title         : "更新"+settings.objectName+"详细信息" ,//alert window title.
				  actionSuccess : function(result){  
				  
									  $('#mw1').modal('hide');	 
									 
									  var clickRe=window.confirm(settings.objectName+'信息提交成功，点击关闭本窗口。');
									  if(clickRe==true){
										 window.opener = null;								 
										 window.close()  
									  }
									  //alert(settings.objectName+'信息提交成功，点击关闭本窗口');
									  
																				  
					  
								  },
				  actionFailed : function(result){  
									  console.log(result);			 
									  $('#mw1').modal('hide');
									  alert(settings.objectName+'信息提交过程中，服务器报错,出错代码：'+(result && result.error_code? result.error_code : 'N/A') +'| 提示信息：'+(result && result.msg ? result.msg :'N/A'  ));	  										      
					  
								 },
				  
				  
									
				 }
		  );
		
	};//-/
	
	
	ini();
	
		
	
}