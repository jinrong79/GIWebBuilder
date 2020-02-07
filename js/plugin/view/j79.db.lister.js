/*
列表插件。

html模板规则:
-列表用模板html，从当前节点的子节点取得，并且删除当前子节点。
-直接替换：[#属性名#]
-js执行后替换: {# js code #}
              不能换行。js code里面可以包含[#XXXX#]， 优先进行[#XXXX#]替换。

------------------------------ */



(function($){
	

	
	/* .dbSelector
	** db_selector初始化
	*/
	$.fn.dbLister=function(settings){
		if (this.length < 1) {
			return;
		}
		var SELF=this;

		var TOTAL_AMOUNT=0;   //db record total amount
		var VIEW_AMOUNT=8;    //list view row amount
		var SERVICE_PARAMS=''; //params for server communication. format like "page=1&shelve=1"

		var AUTO_LOAD=$(SELF).attr('data-no-autoload') ? false : true;

		var PAGER_CONTAINER=$(SELF).attr('data-page-container') || '';

		var CURRENT_PAGE=$(SELF).attr('data-page-current') && parseInt($(SELF).attr('data-page-current'))>0 ? $(SELF).attr('data-page-current'):1 ;

		var LIST_ITEM_HTML=$(SELF).html();  // list item html template. like '<li>[#title#]</li>'

		$(SELF).empty();

		//基本参数取得
		var SERVICE_TARGET=$(SELF).attr('data-target') || '';
		if(SERVICE_TARGET==''){
			return;
		}

		var DATA_POST={
			"page": 1,
			"format": 0,
			"terminal":0,
			"per_page":VIEW_AMOUNT,
		};



		var parseParams=function(){

			SERVICE_TARGET=$(SELF).attr('data-target') || '';
			if(SERVICE_TARGET==''){
				return false;
			}

			SERVICE_PARAMS=$(SELF).attr('data-params') || '';
			VIEW_AMOUNT= $(SELF).attr('data-view-amount') && parseInt($(SELF).attr('data-view-amount'))>0 ? parseInt($(SELF).attr('data-view-amount')) : 8;


			var paramData={};

			//解析params：
			if(SERVICE_PARAMS!=''){

				//console.log('cur params');
				//console.log(SERVICE_PARAMS);

				var params=SERVICE_PARAMS.split('&');
				var curParam, paramItem, paramKey, paramValue;
				for(var i=0; i<params.length; i++){
					curParam=params[i];
					paramItem=curParam.split('=');
					paramKey=paramItem[0];
					paramValue=0;
					if(paramItem.length>1){
						paramValue=paramItem[1];
					}
					paramData[paramKey]=paramValue;
				}

			}

			DATA_POST=$.extend(DATA_POST, paramData);
			DATA_POST.action='SELECT';
			DATA_POST.target=SERVICE_TARGET;
			DATA_POST.per_page=VIEW_AMOUNT;
			DATA_POST.page=CURRENT_PAGE;



			//console.log(DATA_POST);

			return true;

		};//-/

		SELF.setPage=function(pageNo){
			//解析参数
			if(parseInt(pageNo)>0){
				CURRENT_PAGE=parseInt(pageNo);
				parseParams();
				loadData();
			}


		};//-/

		/**
		 * setPage
		 * @param pageNo
		 */
		var setPage=function(pageNo){
			//解析参数
			if(parseInt(pageNo)>0){
				CURRENT_PAGE=parseInt(pageNo);
				parseParams();
				loadData();
			}


		};//-/


		var loadData=function(){


			if(SERVICE_TARGET==''){
				return false;
			}

			DATA_POST.action='SELECT';
			DATA_POST.target=SERVICE_TARGET;
			j79.viewLoading(SELF);
			//communicating with server:
			j79.post(
				{
					title: $(SELF).attr('id'),
					data: DATA_POST,
					actionSuccess: function (result) {  //after success
						j79.hideLoading(SELF);
						if (result) {

							//get total amount
							if (result.total_amount && Number(result.total_amount) >= 1) {
								TOTAL_AMOUNT= Number(result.total_amount);
							}

							$(SELF).empty();

							if (result.data) {

								//view list
								var dbData=result.data;
								var viewAmount=dbData.length< VIEW_AMOUNT ?  dbData.length: VIEW_AMOUNT;
								var htmlLi='';
								var reg, regRe;
								var curRe, curReJs;
								for(var i=0; i<viewAmount;i++){

									htmlLi=j79.setHtml(dbData[i], LIST_ITEM_HTML);


									 reg = new RegExp("\\{#(.+)#\\}", "g");

									 regRe=htmlLi.match(reg);
									if(regRe){
										for(var j=0;j<regRe.length;j++){
											curRe=regRe[j];
											curReJs=curRe.substr(2);
											curReJs=curReJs.substr(0, curReJs.length-2);
											//console.log(curReJs);
											htmlLi=htmlLi.replace(curRe, eval(curReJs));
										}
									}
									$(htmlLi).appendTo(SELF);

								}

								//view pager:
								if(PAGER_CONTAINER!=''){
									$(PAGER_CONTAINER).empty();
									j79.viewPager( Math.ceil(TOTAL_AMOUNT / VIEW_AMOUNT)   , CURRENT_PAGE, PAGER_CONTAINER, SELF );

								}

							}
						}

					},


				});

		};//-/






		
		//绑定 change 事件
		$(this).change(function(){
			parseParams();
			loadData();

		});



		//解析参数
		parseParams();

		if(AUTO_LOAD==true){
			loadData();
		}
				
		

		
		
		
	};
})(jQuery);


/*$(".db-lister").dbLister();
$(".db-lister").change();*/

function INI_DB_LISTER(){
	var class_name="db-lister";
	var ctrlist = $('.'+class_name);

	for(i=0;i<ctrlist.length;i++)
	{
		$("."+class_name+":eq("+i+")").dbLister();
	}
}
INI_DB_LISTER(); //start ini when doc ready.



