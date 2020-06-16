/**  
** form表单处理通用js
** created by J79 studio  
**/


(function($){
   /**------------method: validate();
   ** 表单验证函数。	
   **---1. 根据required属性存在与否来作为必填项检查.
   **---2. 根据validator属性上的值作为正则表达式来进行验证.  
   **---3. 验证出错时提示文，在validator-msg属性里。	
   **---4. 验证信息框，使用了bootstrap的tooltip控件。
   **
   **举例： <input type="text" required="required" validator="^[A-Za-z]$" validator-msg="请输入一位英文字符" />
   **
   **/
   $.fn.validate = function(){

   	   var test_result;
	   
	   //bind change to check validation
	   var bindChange=function(objInput){
		   
		   $(objInput).unbind('change');
		   $(objInput).change(function(e) {

			   $(objInput).validate();
		   });  
		   
	   };
	   
	   //set error
	   var setError=function(objInput, errorMsg){
		    $(objInput).parent().attr('data-toggle','tooltip');		  
			$(objInput).parent().attr('data-placement','top');		  
			$(objInput).parent().attr('data-original-title',errorMsg);
			$(objInput).parent().tooltip('show');
		    $(objInput).closest('.form-group').removeClass('valid');
			$(objInput).closest('.form-group').addClass('has-error');
		    $(objInput).addClass('has-error');
			bindChange(objInput);
		   
	   };
	   
	   //remove error flag
	   var removeError=function(objInput){
		   $(objInput).closest('.form-group').removeClass('has-error'); 
		   $(objInput).closest('.form-group').addClass('valid');
		   $(objInput).parent().removeAttr('data-toggle'); 
		   $(objInput).parent().removeAttr('data-original-title'); 
		   $(objInput).parent().removeAttr('data-placement');
		   $(objInput).removeClass('has-error');
		   
	   };
	   
	   
	   
	  
	  
	  //test required or not
	  var errorMsg;
	  $attr_req=$(this).attr('required');	  
	  if(typeof($attr_req)!='undefined'){ //如果是必填项： 		  
		  
		  var test_blank= (typeof($(this).val())=='undefined' || $(this).val().toString()=='');
		  /*console.log($(this).attr('id'))
		  console.log('value is blank:'+test_blank);*/
		  
		  //if json type, then do further check if json object is empty.
		  if(test_blank==false && $(this).attr('value-type') && $(this).attr('value-type')=='json'){
			  
			  console.log('value is blank in json test==================');	
			   		  
			
			  var valueJson=$.parseJSON($(this).val().toString()); 
			  
			  console.log(valueJson);
			  
			  if(valueJson){
				 test_blank=true; 
				 for(var attribute in valueJson){
					test_blank=false;
				 }
				  
			  }else{
				 test_blank=true;  
			  }
		  }
		  //console.log('value is blank[retest]:'+test_blank);
		  
		  if(test_blank){ //如果是必填项没有填写，  显示tooltip，添加错误class：has-error
			  errorMsg='必填项目! ';
			  setError(this,errorMsg);
			  
			  return false;
		  }else{//如果必填项目已填写，去掉tooltip和错误class：has-error
			  removeError(this);			  
		  }
		  
	  }else{//如果是选填项目
		  
		  
		  if( typeof($(this).val())=='undefined' || $(this).val().toString()==''){ //如果值为空白，既返回true。
		  
		        removeError(this);		
				return true;  
		  }
	  }
	  
	  //test regExp formula 正则表达式验证
	  if(typeof($(this).attr('validator'))!='undefined') {//如果有validator属性， 取其值为正则表达式。

		  errorMsg = $(this).attr('validator-msg');

		  var regStr = $(this).attr('validator');
		  var reg = new RegExp(regStr);
		  var test_result = reg.test($(this).val());

		  if (!test_result) {//正则表达式验证有误： 显示tooltip，添加错误class：has-error

			  setError(this, errorMsg);

		  } else {//正则表达式验证无误， 去掉tooltip和错误class：has-error

			  removeError(this);

		  }
		  return test_result; //返回验证结果 true/false

	  }else if(typeof($(this).attr('tester-func'))!='undefined'){//如果，有tester-func，需要检查

		  errorMsg = $(this).attr('tester-msg');
		
	  }else{//其他不需要验证和检查
		  
		return true;//如果不需要验证，则返回true  
	  }

	  //测试特别规则




	   
	  
   }//------------/method: validate();
})(jQuery)//-----------------------------------------



$(document).ready(function(){
	$('body').delegate('input[form-input]','blur', null, function(e){
		$(this).validate();
	});
});


/*
//初始化取色器控件
function ini_color_pic(){
	
	$('.ctr-color-pick').colpick({
	  layout:'hex',
	  submit:0,
	  colorScheme:'dark',
	  onChange:function(hsb,hex,rgb,el,bySetColor) {
		  $(el).css('background-color','#'+hex);
		  $(el).css('color','#ffffff');
		  
		  if(!bySetColor) $(el).val(hex);
	  }
	}).keyup(function(){
	$(this).colpickSetColor(this.value);
	});
	
	
	$('.ctr-color-pick').each(function(){
    	$(this).colpickSetColor(this.value);
  	});
	
	
}//----------*/

//初始化onBlur验证
function ini_onBlur_validation(){
		
	$('[form-input]').blur(function(){
		$(this).validate();
	});
}//----------/ini_onBlur_validation

/**
* form_validate
* 表单整体验证。
*
* 注：只对带有form-input属性的表单输入控件进行验证。没有此属性的输入控件，会被忽视。
*
* @param {string} form_selector  表单jquery选择器字符串,如果是id,需要前面带#，例如： form_selector='#form1'
* @return {boolean} true- 验证成功; false-验证失败.
**/
/*
function form_validate(form_selector){
	var result=true;
	$(form_selector+" [form-input]").each(function(){
      var singleResult=$(this).validate();
	  if(singleResult==false){
	  	result=false;
	  }
  	});

	return result;

}//----------/form_validate*/
