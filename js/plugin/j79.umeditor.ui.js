//load css files.
j79.loadCSS("/css/umeditor/css/umeditor.css");


/** ================================================================================================================
**	RichTextEditorControl
**  
**	umeditor的控件化调用
**  created by J79 studio
**  底层使用umeditor
**
**  控件表现形式：
**				<div class="richtext-editor" style="height:200px" data-saver="CTR_detail"  toolbar-set-idx="1" 
**					 name="CTR_detail_editor" id="CTR_detail_editor" title="详细内容"></div>
**
**	说明：
**		class="richtext-editor"   : js根据这个class名来辨认控件。
**		style					  : 主要是height值，div的高度，直接决定编辑器的高度。
**		data-saver				  : data-saver指定id的textarea是编辑器的初始值提供器，同时也是编辑器结果html的存储容器。
**									初始化时，如果data-saver指定的textarea的值不为空，那个把此值赋予editor的初始html值。
**		toolbar-set-idx			  : 编辑器的工具栏组合的编号。现有2个：0---完整工具栏，默认值;   1---精简化，文本编辑为主的工具栏。
**
**  =================================================================================================================
**/


/**RichTextEditorControl 构造函数.
*
* @param {string} wrap_id  控件的容器id，控件会建立在此容器里面。
* @param {string} recorder_id     具体内容保存到这个recorder_id指定的控件的value属性中。一般指向hidden类的input。
*
*
**/
function RichTextEditorControl(wrap_id, recorder_id){
		this.wrap_id=wrap_id;   //tree的容器
		this.recorder_id=recorder_id; //点击选中的节点值，保存到这个recorder_id指定的控件的value属性中。一般指向hidden类的input。
		
		
		
		this.editor_home_url='';//编辑器所在页面目录。
		this.toolbar_set_idx=0; //工具栏配置用的数组idx
		
		this.toolbar_set=new Array();
		
		
		var toolbar1=[
            'source | undo redo | bold italic underline strikethrough | superscript subscript | forecolor backcolor | removeformat |',
            'insertorderedlist insertunorderedlist | selectall cleardoc paragraph | fontfamily fontsize' ,
            '| justifyleft justifycenter justifyright justifyjustify |',
            'link unlink | emotion image video  | map',
            '| horizontal print preview fullscreen', 'drafts', 'formula'
        ];
		this.toolbar_set.push(toolbar1);  //toolbar_set_idx==0 ->完整编辑工具栏
		
		var toolbar1=[
				'undo redo | bold italic underline strikethrough | forecolor backcolor | removeformat |',
				'insertorderedlist insertunorderedlist | paragraph | fontfamily fontsize' ,
				'| justifyleft justifycenter justifyright justifyjustify |',
				'link unlink',
				'| horizontal preview fullscreen'
			];			
		
		this.toolbar_set.push(toolbar1);//toolbar_set_idx==1 ->简单版，文本编辑为主的工具栏
		
		
		
		this.img_uploader_url="php/imageUp.php";
	    //get img upload path:
		this.img_Path='';
	    if($('#'+wrap_id).attr('img-path') && $('#'+wrap_id).attr('img-path')!=''){
			this.img_Path=$('#'+wrap_id).attr('img-path');
			if(this.img_Path.substr(-1,1)!='/'){
				this.img_Path+='/';
			}

		}

				this.img_field='upfile';
		
		this.height=$('#'+wrap_id).height();
		
		
		
		
		this.tip_text='';	// 提示文本	
		
		this.umeditor=null; // umeditor 实例
		
		this.textHtml='';// 已存在的内容html。
		
		
		
}

/**
* 定义RichTextEditorControl的类方法
**/
RichTextEditorControl.prototype={
	
	/**
	**  类方法: createUI
	**  -建立UI html元素并添加到容器中.
	**  -创建相应事件处理函数
	**/
	createUI:function(){
		
		//内部引用对象的属性。
		var wrap_id=this.wrap_id;
		var recorder_id=this.recorder_id;
		var editor_home_url=this.editor_home_url;
		var toolbar_set_idx=this.toolbar_set_idx;
		var maxSize=$('#'+this.wrap_id).attr('img-max-size') && parseInt($('#'+this.wrap_id).attr('img-max-size'))>0 ? parseInt($('#'+this.wrap_id).attr('img-max-size')) :0;

		var img_Path=editor_home_url+'php/';
		var img_uploader_url=this.img_uploader_url;
		if(this.img_Path && this.img_Path!=''){
			img_Path='';

			img_uploader_url+='?save_path='+this.img_Path+(maxSize>0? '&max_size='+maxSize:'');
		}

		var img_field=this.img_field;		
		var toolbar_set=new Array();		
		toolbar_set=this.toolbar_set;		
		var textHtml=this.textHtml;
	
		
		//隐藏data-saver
		$("#"+recorder_id).hide();
			
		
		//建立选项对象：
		window.UMEDITOR_CONFIG = {

			//为编辑器实例添加一个路径，这个不能被注释
			UMEDITOR_HOME_URL : editor_home_url
	
			//图片上传配置区
			,imageUrl:editor_home_url+img_uploader_url             //图片上传提交地址
			,imagePath:img_Path                  //图片修正地址，引用了fixedImagePath,如有特殊需求，可自行配置
			,imageFieldName:img_field                   //图片数据的key,若此处修改，需要在后台对应文件修改对应参数
	
	
			//工具栏上的所有的功能按钮和下拉框，可以在new编辑器的实例时选择自己需要的从新定义
			,toolbar:toolbar_set[toolbar_set_idx]
	
			//语言配置项,默认是zh-cn。有需要的话也可以使用如下这样的方式来自动多语言切换，当然，前提条件是lang文件夹下存在对应的语言文件：
			//lang值也可以通过自动获取 (navigator.language||navigator.browserLanguage ||navigator.userLanguage).toLowerCase()
			//,lang:"zh-cn"
			//,langPath:URL +"lang/"
	
			//ie下的链接自动监测
			//,autourldetectinie:false
	
			//主题配置项,默认是default。有需要的话也可以使用如下这样的方式来自动多主题切换，当然，前提条件是themes文件夹下存在对应的主题文件：
			//现有如下皮肤:default
			//,theme:'default'
			//,themePath:URL +"themes/"
	
	
	
			//针对getAllHtml方法，会在对应的head标签中增加该编码设置。
			//,charset:"utf-8"
	
			//常用配置项目
			//,isShow : true    //默认显示编辑器
	
			//,initialContent:'欢迎使用UMEDITOR!'    //初始化编辑器的内容,也可以通过textarea/script给值，看官网例子
	
			//,initialFrameWidth:500 //初始化编辑器宽度,默认500
			//,initialFrameHeight:500  //初始化编辑器高度,默认500
	
			//,autoClearinitialContent:true //是否自动清除编辑器初始内容，注意：如果focus属性设置为true,这个也为真，那么编辑器一上来就会触发导致初始化的内容看不到了
	
			//,textarea:'editorValue' // 提交表单时，服务器获取编辑器提交内容的所用的参数，多实例时可以给容器name属性，会将name给定的值最为每个实例的键值，不用每次实例化的时候都设置这个值
	
			//,focus:false //初始化时，是否让编辑器获得焦点true或false
	
			//,autoClearEmptyNode : true //getContent时，是否删除空的inlineElement节点（包括嵌套的情况）
	
			//,fullscreen : false //是否开启初始化时即全屏，默认关闭
	
			//,readonly : false //编辑器初始化结束后,编辑区域是否是只读的，默认是false
	
			//,zIndex : 900     //编辑器层级的基数,默认是900
	
			//如果自定义，最好给p标签如下的行高，要不输入中文时，会有跳动感
			//注意这里添加的样式，最好放在.edui-editor-body .edui-body-container这两个的下边，防止跟页面上css冲突
			//,initialStyle:'.edui-editor-body .edui-body-container p{line-height:1em}'
	
			//,autoSyncData:true //自动同步编辑器要提交的数据
	
			//,emotionLocalization:false //是否开启表情本地化，默认关闭。若要开启请确保emotion文件夹下包含官网提供的images表情文件夹
	
			//,allHtmlEnabled:false //提交到后台的数据是否包含整个html字符串
	
			//fontfamily
			//字体设置
	//        ,'fontfamily':[
	//              { name: 'songti', val: '宋体,SimSun'},
	//          ]
	
			//fontsize
			//字号
			//,'fontsize':[10, 11, 12, 14, 16, 18, 20, 24, 36]
	
			//paragraph
			//段落格式 值留空时支持多语言自动识别，若配置，则以配置值为准
			//,'paragraph':{'p':'', 'h1':'', 'h2':'', 'h3':'', 'h4':'', 'h5':'', 'h6':''}
	
			//undo
			//可以最多回退的次数,默认20
			//,maxUndoCount:20
			//当输入的字符数超过该值时，保存一次现场
			//,maxInputCount:1
	
			//imageScaleEnabled
			// 是否允许点击文件拖拽改变大小,默认true
			//,imageScaleEnabled:true
	
			//dropFileEnabled
			// 是否允许拖放图片到编辑区域，上传并插入,默认true
			//,dropFileEnabled:true
	
			//pasteImageEnabled
			// 是否允许粘贴QQ截屏，上传并插入,默认true
			//,pasteImageEnabled:true
	
			//autoHeightEnabled
			// 是否自动长高,默认true
			//,autoHeightEnabled:true
	
			//autoFloatEnabled
			//是否保持toolbar的位置不动,默认true
			//,autoFloatEnabled:true
	
			//浮动时工具栏距离浏览器顶部的高度，用于某些具有固定头部的页面
			//,topOffset:30
	
			//填写过滤规则
			//,filterRules: {}
		};
		
		
		
		//创建umeditor实例		
		this.umeditor = UM.getEditor(wrap_id);
		var umeditor=this.umeditor;
		
		
		
		
		this.umeditor.setHeight(this.height);  //调整编辑器的高度
		
		
		
		
		umeditor.addListener('contentChange',function(){//内容有变化，即时存入data-saver指定的控件的value和plain属性里面。
	  
        				
			
			$('#'+recorder_id).val(  umeditor.getContent());
			$('#'+recorder_id).attr('plain', umeditor.getContentTxt()); 
			
    	});
		
		umeditor.addListener('ready',function(){//编辑器初始化完毕后：
	  
        				
			if(textHtml!=''){ //如果存在textHtml值，那么插入。
			
	 			umeditor.setContent( textHtml);
	
			}
			
    	});
		
			
		 
	}//--------------/类方法: createUI

}//------------------/类方法结束






/**==============================
** 页面载入后，自动初始化RichTextEditor_Setup控件。
**/
$(document).ready(function(){	
		
	RichTextEditor_Setup();		
 	
});





/**==============================
* 用于初始化页面上的所有RichTextEditor_Setup控件
*
**/
function RichTextEditor_Setup(){
	
	var Control;
	var data_saver_id;
	
	var treeSel = $('.richtext-editor');
	
	for(i=0;i<treeSel.length;i++)
	{
		
		data_saver_id= typeof( $(".richtext-editor:eq("+i+")").attr('data-saver'))=='undefined'  ? $(".richtext-editor:eq("+i+")").attr('id')+'_saver' :$(".richtext-editor:eq("+i+")").attr('data-saver');		
		
		toolbar_set_idx=typeof( $(".richtext-editor:eq("+i+")").attr('toolbar-set-idx'))=='undefined'  ? 0 : $(".richtext-editor:eq("+i+")").attr('toolbar-set-idx');	
		
		textHtml=$('#'+data_saver_id).val();
		
		Control=new RichTextEditorControl($(".richtext-editor:eq("+i+")").attr('id'),  data_saver_id );
		Control.toolbar_set_idx=toolbar_set_idx;
		Control.editor_home_url='/js/plugin/umeditor/';
		Control.textHtml=textHtml;
		
		Control.createUI();
		
	}
}



