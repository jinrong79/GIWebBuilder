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

   	  console.log('start form-validation!')

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

	  }else if(typeof($(this).attr('validator-expression'))!='undefined'){//如果有validator-expression，需要检查

			var validExpression=$(this).attr('validator-expression');
			errorMsg = $(this).attr('validator-msg');

			test_result=eval(validExpression);

			if (!test_result) {//tester-expression计算结果为false： 显示tooltip，添加错误class：has-error
				  setError(this, errorMsg);
			} else {//tester-expression计算结果为true， 去掉tooltip和错误class：has-error
				  removeError(this);
			}
			return test_result; //返回验证结果 true/false

		
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




//初始化onBlur验证
function ini_onBlur_validation(){
		
	$('[form-input]').blur(function(){
		$(this).validate();
	});
}//----------/ini_onBlur_validation


