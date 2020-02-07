/** =======================================================================================================================================
**  文件上传控件的UI处理类ImgUploaderControl
**  created by J79 studio
**  底层采用WebUploader.
**
**  控件表现形式：
**				<div class="img-uploader" data-saver="CTR_vimg_files" data-multi="data-multi"  
**					thumbnail-upload="yes" thumbnail-width="200" thumbnail-data-saver="CTR_vimg_files_thumb"
**					data-compress-width="1920" data-compress-height="1200"	data-compress-limit-size="10000"  data-compress-crop="yes"				
**					id="CTR_vimg" name="CTR_vimg" title="用于项目详细页面的大图,可上传多张.建议1920*1080像素。"></div>
**
**	说明：
**		class="img-uploader"  	  : js根据这个class名来辨认控件。
**		data-saver				  : data-saver指定id的textarea是控件的初始值提供器，同时也是控件操作结果的存储容器。
**									图片上传后,以<img src="XXXX/xxx.jpg" fileid="KKK"><img ...>形式存储于这个data-saver容器上。
**		data-multi				  : 是否允许多图上传。存在即true。
**		thumbnail-upload		  : 指定是否额外上传缩略图，只要存在这个属性就是true。
**		thumbnail-width			  : 缩略图限宽，不存在这个属性，即不限制宽度。
**		thumbnail-height		  : 缩略图限高，不存在这个属性，即不限制高度。以上2个中，必须存在一个。
**		thumbnail-data-saver	  : 缩略图的存储容器(textarea)的id。
**									注：双击页面上的展示图来删除已上传的图片时，不会影响到这个容器的值。
**									后台必须先拿到data-saver值，并根据data-saver中的图片列表，
**                                  每个图片根据fileid值，在thumbnail-data-saver中选取依然有效的缩略图项。
**		data-compress-width		  : 上传图片时，需要压缩的宽度限制，不存在这个属性，即不限宽。
**		data-compress-height	  : 上传图片时，需要压缩的高度限制，不存在这个属性，即不限高。以上两个都不存在，就是不压缩，原始大小上传。
**									注：thumbnail上传和压缩上传，只能选择一个，不能2种同时进行。要么上传原图，同时上传缩略图；
**                                  要么，直接上传压缩过的图。
**		data-compress-limit-size  : 压缩时的文件大小最小限制，不存在属性，等于0，就是忽视文件大小，根据上面2个属性来压缩。
**		data-compress-crop		  : 压缩时，是否裁减，不存在属性，等于false，存在即true。
**      file-max-count            : 上传图片的总个数限制。默认值10个。
**  ======================================================================================================================================
**/



/**ImgUploaderControl 构造函数.
*
* @param {string} wrap_id  图片上传控件容器id， 图片上传控件会建立在此容器里面。
* @param {string} file_id  文件上传input控件的id，用于服务器端脚本接受file数据。
* @param {string} url_server  服务器上接受文件上传的php脚本地址,可写相对路径.
* @param {string} url_swf     上传用swf文件的url地址,可写相对路径.
* @param {string} recorder_id     把已上传文件的列表信息，存储到recorder_id表示的容器的value属性里面。
* @param {boolean} multiple    是否开启同时选择多个文件的能力
* @param {string} tip_text     默认显示的提示信息文本
**/
function ImgUploaderControl(wrap_id,file_id,url_server,url_swf,recorder_id, multiple,tip_text){
		this.wrap_id=wrap_id;
		this.file_id=file_id;
		this.url_server=url_server;
		this.url_swf=url_swf;
		this.recorder_id=recorder_id;
		
		this.multiple=multiple;
		this.tip_text=tip_text; //默认显示的提示信息文本		
		this.uploader=null;  //WebUploader对象
 		this.fileCount=0; 
		this.fileSize=0;  //上传文件总的大小
		this.thumbnailWidth=50; //展示用图宽度。这个图是控件里面显示的缩略图，不是上传用的缩略图。
		this.thumbnailHeight=50;//展示用图高度		
		this.state = 'pedding';  //当前状态：ready:准备上传 ，confirm: 成功上传,有可能部分失败 ，其他。		
        this.percentages = {};  // 所有文件的进度信息，key为file id
		this.compress_width=0; //超过1920宽度， 就要压缩成1920后再上传。 
		this.compress_height=0; //超过1200高度， 就要压缩成1200后再上传。  compress_width 或者compress_height 两个都是0，即不压缩。
		this.compress_limit_size=1024*1024;// 压缩时，1m以下的文件，不压缩， 不管大小。
		this.compress_crop=false; // 压缩时，是否裁剪

	    this.fileMaxCount=$('#'+wrap_id).attr('file-max-count') && parseInt($('#'+wrap_id).attr('file-max-count'))>=1 ? parseInt($('#'+wrap_id).attr('file-max-count')): 10;

		
		
		this.thumbnailUp_width=0; //上传后制作缩略图宽度限制，设置0，就等于是忽略了宽度方面的限制。 
		this.thumbnailUp_height=0; //上传后制作缩略图高度限制，设置0，就等于是忽略了高度方面的限制。以上两个值都等于0，不生成缩略图。
		//缩略图的存储位置为save_path+"\thumb\"目录下。
		
		
		
		
		this.uploaded_list='';// 已上传的图片列表'<img src="AAAA" fileid="file_1"><img src="BBBB" fileid="file_2">...'形式
		
		this.save_path='upload/';// 图片上传后存储目的地path， 例如：  "product/vimg/"
		
		
		
}

/**
* 定义ImgUploaderControl的类方法
**/
ImgUploaderControl.prototype={
	
	/**
	**  类方法: createUI
	**  -建立UI html元素并添加到容器中.
	**  -创建WebUploader的实例，并定义其event的相应函数
	**/
	createUI:function(){
	
		//重新定义一些变量，便于下面引用对象的属性值。
		
		
		
		
		var recorder_id=this.recorder_id;
		var wrap_id=this.wrap_id;
		
		var UI=this;
		
		
		var save_path=this.save_path;
		
		
		var thumb_save_path=this.save_path+'thumb/';
		
		//if iniData not undefined, then set it to $(recorder_id).value.
		//if(iniData && $('#'+this.recorder_id)){
		//   	$('#'+this.recorder_id).text(iniData);
		//	//$('#'+this.recorder_id).text($('#'+this.recorder_id).val());			
		//}
		
		var originalValue='';//original value in recorder_id 	
		if($('#'+this.recorder_id) && $('#'+this.recorder_id).val()!=''){
			originalValue=$('#'+this.recorder_id).val();		   	
		}
		
		//隐藏data-saver	/thumbnail-data-saver	
		$('#'+this.recorder_id).hide();
		
		//clear content.
		$('#'+this.wrap_id).empty();
		
		
		//创建html, 并加入到wrap_id容器中
		var $ui=$(
		'<div class="ImgUploaderControl">'+
		//'<input type="hidden" id="'+this.wrap_id+'_files"   name="'+this.wrap_id+'_files"  value="" >'+
		
		'  <div id="'+this.wrap_id+'_queueList" class="queueList">'+
		'    <div id="dndArea" class="placeholder">'+
		'       <div id="'+this.wrap_id+'_filePicker">选择图片</div>'+
		'       <p>'+this.tip_text+'</p>'+
		'    </div>'+
		'    <div class="filelist"><h5>本地图片:</h5></div>'+		
		'  </div>'+
		'  <div class="statusBar" style="display:none;">'+
        '    <div class="progress" style="display: none;">'+
        '      <span class="text">%</span>'+
        '      <span class="percentage" style="width: 0%;"></span>'+
        '    </div>'+
		'    <div class="info"></div>'+
		'    <div class="btns">'+
		'       <div id="filePickerMore"></div>'+
		'       <div class="uploadBtn">开始上传</div>'+
		'    </div>'+       
        '  </div>'+
		' <div class="uploaded"><h5>已上传列表 <em>(可以鼠标拖动改变顺序) </em>'+
		//' <span><a class="btn btn-primary btn-reset-all">全部重置</a></span>'+
		'</h5><div id="uploadedList" class="list drag-sortable horizontal"></div></div>'+
		'</div>'
		);		
		$ui.appendTo($('#'+this.wrap_id));
		
		
		//read initial data from recorder, and load value to controls
		if($('#'+recorder_id).val()!=''){
			
	  		//$('#'+this.wrap_id).find('#uploadedList').append( $('#'+recorder_id).val());
			
			this.loadValue($('#'+recorder_id).val());
	  		
			
		}
		
		
		//action for reset-all
		$('#'+this.wrap_id+' .uploaded .btn-reset-all').click(function(e) {
            if($('#'+UI.recorder_id)){
			    $('#'+UI.recorder_id).text(originalValue);		   	
		    }
			UI.setState('ready');
			UI.createUI();
			
			
			
			
        });
		
		
		//dragSort插件初始化		
		if($.fn.dragSort){
			
			//alert('has dragSort!!!');
			
		   $('#'+this.wrap_id).find('.drag-sortable').dragSort('img');
		   $('#'+this.wrap_id).find('.list').change(function(e) {
                UI.setValue();
           });
		   	
		}
		
		
		//img-list handler menu	--------------------	
		
		//  btn-del
		$('#'+this.wrap_id).find('.list').delegate('.btn-del','click',null,function(e){
			if( typeof($(this).attr('data-idx'))!='undefined'){
				var curIdx=Number($(this).attr('data-idx'));
				$('#'+UI.wrap_id+' .list img:eq('+curIdx+')').remove();
				$('#'+UI.wrap_id+' .list').find('.list-item-menu').remove();
				$('#'+UI.wrap_id+' .list').trigger('change');	
				//UI.setValue();
			}
			return false;
			
		});
		//btn-view
		$('#'+this.wrap_id).find('.list').delegate('.btn-view-original','click',null,function(e){
			if( typeof($(this).attr('data-idx'))!='undefined'){
				var curIdx=Number($(this).attr('data-idx'));
				var originURL=$('#'+UI.wrap_id+' .list img:eq('+curIdx+')').attr('realsrc');
				window.open(originURL,'原始大小图片');


				//UI.setValue();
			}
			return false;

		});
		
		//  btn-setcover
		/*
		$('#'+this.wrap_id).find('.list').delegate('.btn-setcover','click',null,function(e){
			if( typeof($(this).attr('data-idx'))!='undefined'){
				var curIdx=Number($(this).attr('data-idx'));
				$('#'+UI.wrap_id+' .list img').removeClass('cover-img');
				$('#'+UI.wrap_id+' .list img:eq('+curIdx+')').addClass('cover-img');			
				$('#'+UI.wrap_id+' .list').trigger('change');	
				//UI.setValue();
			}
			return false;
			
		});	
		*/	
		
		//  mouse-over: view menu
		$('#'+this.wrap_id).find('.list').delegate('img',"mouseover",null,function(e){
			 //alert('mouse entered!');
			 $('#'+UI.wrap_id+' .list').find('.list-item-menu').remove();
			 
			 var imgIdx=$(this).index();
			 var curItemX=Math.round($(this).position().left)+5;
			$('<div class="list-item-menu" style="top:5px;left:'+curItemX+'px;">'+
			  '<a class="btn-del" data-idx="'+imgIdx+'" title="删除当前项"><i class="glyphicon glyphicon-remove"></i> 删除</a>'+
				'<a class="btn-view-original" data-idx="'+imgIdx+'" title="查看原始图片"><i class="glyphicon glyphicon-zoom-in"></i> 查看</a>'+
			  //'<a class="btn-setcover" data-idx="'+imgIdx+'" title="把当前项设置为封面图"><i class="glyphicon glyphicon-blackboard"></i> 设为封面图</a>'+
			  '</div>')
			  .appendTo($('#'+UI.wrap_id+' .list'));
			
			 
			 
		});
		//  mouse-leave: remove menu 
		$('#'+this.wrap_id).delegate('.list','mouseleave',null,function(e){
			 $('#'+UI.wrap_id+' .list').find('.list-item-menu').remove();	
			  
			 
		});
		//--/img-list handler menu	--------------------	 
		 
		
		//创建compress相关参数，准备传递给webuploader。	
		
		
		if(this.compress_crop==true){
			var para_compress={				
				
				// 是否允许裁剪。
				crop: true
			};
			
		}else{
			var para_compress=false;
		}
		/*if(this.compress_width==0 && this.compress_height==0){
			para_compress=false;
		}else{
			
			
			para_compress={
				width:(this.compress_width==0 ? 9999:this.compress_width),
				height:(this.compress_height==0 ? 9999:this.compress_height),				
				quality: 90,
			
				// 是否允许放大，如果想要生成小图的时候不失真，此选项应该设置为false.
				allowMagnify: false,
			
				// 是否允许裁剪。
				crop: this.compress_crop,
			
				// 是否保留头部meta信息。
				preserveHeaders: true,
			
				// 如果发现压缩后文件大小比原来还大，则使用原来图片
				// 此属性可能会影响图片自动纠正功能
				noCompressIfLarger: false,
			
				// 单位字节，如果图片大小小于此值，不会采用压缩。
				compressSize: this.compress_limit_size
			
			};
			
		}*/
		
		var para_formData ={
					     
				compress_width:this.compress_width,
				compress_height:this.compress_height,
				thumb_width:this.thumbnailUp_width,
				thumb_height:this.thumbnailUp_height,
				save_path:save_path
		 };
		
		
		//创建WebUploader对象
		this.uploader=WebUploader.create({
			container: '#'+this.wrap_id+'_filePicker',
			pick: {
				id: '#'+this.wrap_id+'_filePicker',
				label: '点击选择图片',
				multiple: this.multiple
			},
			//dnd: '#'+this.wrap_id+'_queueList',
			
			accept: {
				title: 'Images',
				extensions: 'jpg,jpeg,png',
				mimeTypes: 'image/*'
			},        
			swf: this.url_swf,
			//disableGlobalDnd: true,
			chunked: false,
			threads:1,
			server:this.url_server,
			fileVal:this.file_id,
			formData:para_formData,
			compress:para_compress,			
			fileNumLimit: this.fileMaxCount,
			fileSizeLimit: 100 * 1024 * 1024,    // 100 M
			fileSingleSizeLimit: 20 * 1024 * 1024    // 20 M
			
    	});
		
		//给WebUploader实例对象添加一个属性UI， 此属性等于ImgUploaderControl的实例对象。
		this.uploader.UI=this;
		this.uploader.CurrentFileIdx=0;
		
		
		//添加按钮"继续添加"
		if(this.multiple){
		  this.uploader.addButton({
			  id: '#'+this.wrap_id+' #filePickerMore',
			  label: '继续添加'
		  });
		}
		
		
		
		//uploader对象的方法：当选择了一个文件后。
		this.uploader.onFileQueued = function( file ) {
			
			
			this.UI.fileCount++;
			this.UI.fileSize += file.size;
			
			$placeHolder = $('#'+this.UI.wrap_id).find('.placeholder');
		
			if ( this.UI.fileCount === 1 ) {
				$placeHolder.addClass( 'element-invisible' );
				$statusBar = $('#'+this.UI.wrap_id).find('.statusBar'),
				$statusBar.show();
			}
	
			this.UI.addFile( file );
			this.UI.setState( 'ready' );
			this.UI.updateTotalProgress();
			
			
			
			
			
       };
	   
	   //uploader对象的方法：从队列中删除一个文件
	   this.uploader.onFileDequeued = function( file ) {
		   this.UI.fileCount--;
		   this.UI.fileSize -= file.size;
	
		   if ( !this.UI.fileCount ) {
				this.UI.setState( 'pedding' );
		   }
	
		   this.UI.removeFile( file );
		   this.UI.updateTotalProgress();
		   
		  
			

       };
	   
	   //uploader对象的方法: 设置上传百分比
	   this.uploader.onUploadProgress = function( file, percentage ) {
			var $li = $('#'+file.id),
				$percent = $li.find('.progress span');
	
			$percent.css( 'width', percentage * 100 + '%' );
			this.UI.percentages[ file.id ][ 1 ] = percentage;
			this.UI.updateTotalProgress();
       };
	   
	   //uploader对象的方法: 出错时
	   this.uploader.onError = function( code ) {
        	alert( 'Error: ' + code );
       };
	   
	   //uploader对象的方法： 处理返回值
	   this.uploader.on( 'uploadAccept', function( file, response ) {
    	  
		 
		  
	
		  
		  if(response.state=='SUCCESS')
		  {
			
			//添加返回的文件名，到已上传列表中。
			$view_url=response.url;
			if(response.thumb_url!=''){
				$view_url=response.thumb_url;
			}
			
			if(typeof(response.thumb_width)!='undefined'){
			   thumbStr=	' thumb-width="'+response.thumb_width+'" thumb-height="'+response.thumb_height+'" thumb="'+response.thumb_url+'" '; 
			}else{
				thumbStr='';	
			}
			
			if(typeof(response.compress)!='undefined'){
			   compressStr=	' compress-width="'+response.compress_width+'" compress-height="'+response.compress_height+'"  '; 
			}else{
				compressStr='';	
			}
			
			
			$('#'+this.UI.wrap_id+' #uploadedList').append('<img src="'+$view_url+'" realsrc="'+response.url+'" size="'+response.size+'" origin-width="'+response.width+'" origin-height="'+response.height+'" '+thumbStr+' '+compressStr+' />');
			
			UI.setValue();
			
			
			/*
			//已上传列表的删除功能函数设定
			$('#'+this.UI.wrap_id+' #uploadedList img').dblclick(function () {
     			$(this).remove();
				UI.setValue();
				//$('#'+UI.recorder_id).val($('#'+UI.wrap_id+' #uploadedList').html());
				
			});
			$('#'+this.UI.wrap_id+' #uploadedList img').click(function () {
				$(this).siblings().removeClass('cover-img');
     			$(this).addClass('cover-img');
				UI.setValue();
				//$('#'+UI.recorder_id).val($('#'+UI.wrap_id+' #uploadedList').html());
				
			});
			
			*/
			
						
		    return true;
		  }else{
			alert("error! response state is not success, state= "+response.state);
			//alert(JSON.stringify(response));
			
			//$('#'+UI.recorder_id).val(response._raw);
			return false;  
		  }
		  ////if ( hasError ) {
		  //	return false;
		  //}
		});	//---/处理返回值
	   
	   
	   //uploader对象其他状态
	   this.uploader.on( 'all', function( type ) {
        var stats;
        switch( type ) {
            case 'uploadFinished':
				
                this.UI.setState( 'confirm' );
				
				
				
				//把已上传文件列表，记录到recorder_id指定的input控件上
				//$('#'+this.UI.recorder_id).val($('#'+this.UI.wrap_id+' #uploadedList').html());	
				
				this.UI.setValue();
				
                break;

            case 'startUpload':
                this.UI.setState( 'uploading' );
                break;

            case 'stopUpload':
                this.UI.setState( 'paused' );				
                break;

        }
       });
	   
	   
	   
	  
	   
	   
	   //上传按钮
		var $uploadBtn=$ui.find('.uploadBtn');
		
		
		//点击上传按钮时的处理函数：
		$uploadBtn.on('click', function() {
			
			if ( $(this).hasClass( 'disabled' ) ) {
				
				return false;			}
	        
			if ( UI.state === 'ready' ) {		
					
				UI.uploader.upload();
				
			} else if ( UI.state === 'paused' ) {
				
				UI.uploader.upload();
				
			} else if ( this.state === 'uploading' ) {
				
				UI.uploader.stop();
			}
    	});
		
		//提示信息部分。
		var $info=$ui.find('.info');
		
		//提示信息栏目中，点击重新上传和忽略按钮的处理函数
		$info.on( 'click', '.retry', function() {
        	UI.uploader.retry();
    	} );

    	$info.on( 'click', '.ignore', function() {
        	alert( '继续操作，忽视错误上传。' );
    	} );
		
		
		
		
		
	   
	   
	   //初始化按钮，并设置状态
	   $uploadBtn.addClass( 'state-' + this.state );
       this.updateTotalProgress();
	   
       
	   
		
		
		
		
	}, //--/类方法: createUI
	
	
	/**
	*  loadValue
	*  load value to this control.
	*  @param {string} value : string of img data in jason format
	*                          value.img   = array( img full url string array);
	*                          value.thumb = array( thumb-img full url string array );
	*/
	loadValue: function(value){		
		
		
		var imgData = j79.toJSON(value);
		// JSON.parse
		var $imgItem;
		var classStr='';
		if(imgData){	
		  
		  //alert('imgData.length:'+imgData.length);
		  
		  if(imgData.length>0){
			 
		    for(var i=0; i<imgData.length; i++){
			  classStr= imgData[i].cover && imgData[i].cover==1 ? ' class="cover-img" ':'';
			  $imgItem=$('<img realsrc="'+imgData[i].url+'" src="'+(imgData[i].thumb ? imgData[i].thumb:imgData[i].url)+'" '+classStr+'/>');
			  $listUI=$('#'+this.wrap_id).find('.list');
			  $imgItem.appendTo( $listUI);
			}
		  }else{
			 
			 if( imgData.url){
				 
				 classStr= imgData.cover && imgData.cover==1 ? ' class="cover-img" ':'';
				 
				 var imgStr='<img '+classStr+' realsrc="'+imgData.url+'" src="';
				 
				 if(imgData.thumb){
				     imgStr=imgStr+imgData.thumb+'" />';
				 }else{
					 imgStr=imgStr+imgData.url+'" />';
				 }
				$(imgStr).appendTo( $('#'+this.wrap_id).find('.list')); 
			 }
			   
		  }
		}
	},//-/
	
	/**
	*  setValue
	*  set value to data-saver
	*/
	setValue: function(){
		
		var imgData=[];
		
		//console.log('value setted==========');

		
		$('#'+this.wrap_id+' .list').find('img').each(function(i) {
            
			var imgItem={};
			imgItem.url=$(this).attr('realsrc');
			imgItem.thumb=$(this).attr('src');
			if($(this).hasClass('cover-img')){
				imgItem.cover=1;
			}
			
			imgData.push(imgItem);
			    
				
			
			
        });
		
				
		//console.log(JSON.stringify(imgData));		
		//console.log('set to=#'+this.recorder_id);
		
		$('#'+this.recorder_id).text(JSON.stringify(imgData));
		//$('#'+this.recorder_id).text($('#'+this.recorder_id).val());
		$('#'+this.recorder_id).trigger('change');
		
		
		
		
	},//-/
	
	
	/**
	** 类方法: addFile
	** 当有文件添加进来时执行，负责view的创建
	**/
	
	addFile:function(file){
		
		var UI=this;
		
		var $li = $( '<li id="' + file.id + '">' +
                '<p class="title">' + file.name + '</p>' +
                '<p class="imgWrap"></p>'+
                '<p class="progress"><span></span></p>' +
                '</li>' ),

         $btns = $('<div class="file-panel">' +
                '<span class="cancel">删除</span>' +
                '</div>').appendTo( $li ),
				
         $prgress = $li.find('p.progress span'),
         
		 $wrap = $li.find( 'p.imgWrap' ),
         
		 $info = $('<p class="error"></p>'),

         showError = function( code ) {
                switch( code ) {
                    case 'exceed_size':
                        text = '文件大小超出';
                        break;

                    case 'interrupt':
                        text = '上传暂停';
                        break;

                    default:
                        text = '上传失败，请重试';
                        break;
                }

                $info.text( text ).appendTo( $li );
        };

        if ( file.getStatus() === 'invalid' ) {
            showError( file.statusText );
        } else {
            // @todo lazyload
            $wrap.text( '预览中' );
            this.uploader.makeThumb( file, function( error, src ) {
                if ( error ) {
                    $wrap.text( '不能预览' );
                    return;
                }

                var img = $('<img src="'+src+'">');
                $wrap.empty().append( img );
            }, this.thumbnailWidth, this.thumbnailHeight );

            this.percentages[ file.id ] = [ file.size, 0 ];
            file.rotation = 0;
        }

        file.on('statuschange', function( cur, prev ) {
            if ( prev === 'progress' ) {
                $prgress.hide().width(0);
            } else if ( prev === 'queued' ) {
                $li.off( 'mouseenter mouseleave' );
                $btns.remove();
            }

            // 成功
            if ( cur === 'error' || cur === 'invalid' ) {
                console.log( file.statusText );
                showError( file.statusText );
                UI.percentages[ file.id ][ 1 ] = 1;
            } else if ( cur === 'interrupt' ) {
                showError( 'interrupt' );
            } else if ( cur === 'queued' ) {
                UI.percentages[ file.id ][ 1 ] = 0;
            } else if ( cur === 'progress' ) {
                $info.remove();
                $prgress.css('display', 'block');
            } else if ( cur === 'complete' ) {
                $li.append( '<span class="success"></span>' );
            }

            $li.removeClass( 'state-' + prev ).addClass( 'state-' + cur );
        });

        $li.on( 'mouseenter', function() {
            $btns.stop().animate({height: 30});
        });

        $li.on( 'mouseleave', function() {
            $btns.stop().animate({height: 0});
        });

        $btns.on( 'click', 'span', function() {
			
			
            var index = $(this).index(),
                deg;

            switch ( index ) {
                case 0:
                    UI.uploader.removeFile( file );
                    return;

                
            }        


        });
		var $queue = $('#'+this.wrap_id).find('.filelist');		
        $li.appendTo( $queue );
		
	},// ---/类方法: addFile
	

	
	/**
	** 类方法: removeFile
	** 负责UI上的销毁文件项目
	**/
	
    removeFile:function( file ) {
        var $li = $('#'+file.id);

        delete this.percentages[ file.id ];
        this.updateTotalProgress();
        $li.off().find('.file-panel').off().end().remove();
    },//---/ 类方法: removeFile
	
	
	
	
	/**
	** 类方法: updateTotalProgress
	** 更新所有进度
	**/
	updateTotalProgress:function () {
		var  $statusBar = $('#'+this.wrap_id).find('.statusBar');
		var  $progress = $statusBar.find('.progress');
        var loaded = 0,
            total = 0,
            spans = $progress.children(),
            percent;

        $.each( this.percentages, function( k, v ) {
            total += v[ 0 ];
            loaded += v[ 0 ] * v[ 1 ];
        } );

        percent = total ? loaded / total : 0;

        spans.eq( 0 ).text( Math.round( percent * 100 ) + '%' );
        spans.eq( 1 ).css( 'width', Math.round( percent * 100 ) + '%' );
        this.updateStatus();
    }, //---/ 类方法: updateTotalProgress
	
	
	
	/**
	** 类方法: updateStatus
	** 更新上传信息状态
	**/
	updateStatus:function () {
        var text = '', stats;

        if ( this.state === 'ready' ) {
            text = '选中' + this.fileCount + '张图片，共' +
                    WebUploader.formatSize( this.fileSize ) + '。';
        } else if ( this.state === 'confirm' ) {
            stats = this.uploader.getStats();
            if ( stats.uploadFailNum ) {
                text = '已成功上传' + stats.successNum+ '张照片至服务器上，'+
                    stats.uploadFailNum + '张照片上传失败，<a class="retry" href="#">重新上传</a>失败图片或<a class="ignore" href="#">忽略</a>'
            }

        } else {
            stats = this.uploader.getStats();
            text = '共' + this.fileCount + '张（' +
                    WebUploader.formatSize( this.fileSize )  +
                    '），已上传' + stats.successNum + '张';

            if ( stats.uploadFailNum ) {
                text += '，失败' + stats.uploadFailNum + '张';
            }
        }
		
		var $infoBox=$('#'+this.wrap_id).find('.info');

        $infoBox.html( text );
    },//---/ 类方法: updateStatus
	
	
	/**
	** 类方法: setState
	** 设置控件状态
	**/
	setState:function ( val ) {
        var file, stats;

        if ( val === this.state ) {
            return;
        }
		var $uploadBtn=$('#'+this.wrap_id).find('.uploadBtn');
		var $placeHolder=$('#'+this.wrap_id).find('.placeholder');
		var $queue=$('#'+this.wrap_id).find('.filelist');
		var $statusBar = $('#'+this.wrap_id).find('.statusBar');
		var $progress = $statusBar.find('.progress');
		
        $uploadBtn.removeClass( 'state-' + this.state );
        $uploadBtn.addClass( 'state-' + val );
        this.state = val;

        switch ( this.state ) {
            case 'pedding':
                $placeHolder.removeClass( 'element-invisible' );
                $queue.parent().removeClass('filled');
                $queue.hide();
                $statusBar.addClass( 'element-invisible' );
                this.uploader.refresh();
                break;

            case 'ready':
                $placeHolder.addClass( 'element-invisible' );
                $( '#filePickerMore' ).removeClass( 'element-invisible');
                $queue.parent().addClass('filled');
                $queue.show();
                $statusBar.removeClass('element-invisible');
                this.uploader.refresh();
                break;

            case 'uploading':
				$('#'+this.wrap_id).find('#filePickerMore').addClass( 'element-invisible' );
                $progress.show();
                $uploadBtn.text( '暂停上传' );
                break;

            case 'paused':
                $progress.show();
                $uploadBtn.text( '继续上传' );
                break;

            case 'confirm':
                $progress.hide();
                $uploadBtn.text( '开始上传' ).addClass( 'disabled' );

                stats = this.uploader.getStats();
                if ( stats.successNum && !stats.uploadFailNum ) {
                    this.setState( 'finish' );
                    return;
                }
                break;
            case 'finish':
                stats = this.uploader.getStats();
				
                if ( stats.successNum ) {
                    //alert( '上传成功' );
                } else {
                    // 没有成功的图片，重设
                    this.state = 'done';
                    location.reload();
                }
                break;
        }

        this.updateStatus();
    },//--/setState
	
	
	
	
	
	
	
	
	
	
	
	
};

//======================================================================================





/**函数：初始化页面上的控件:
** 在页面上所有class=img-uploader的容器里，添加图片上传控件。
**/
function WebUploader_Setup(){
	
	var UPLOADER_SERVER_FILE_ID='file_field';
	
	//var UPLOADER_SERVER_PHP='/inc/action_upload.php';
	var UPLOADER_SERVER_PHP='/com.php?target=ImgUploadSrv&action=UPDATE&format=0';
	var UPLOADER_SWF_PATH='/js/webuploader/Uploader.swf';
	
	//var UPLOADER_SERVER_PHP='/admin/action_upload.php';	
	//var UPLOADER_SWF_PATH='/script/webuploader/Uploader.swf';
	
	
	var uploaderControl;
	var data_saver_id;
	var thumbnail_saver_id;
	var flagMulti;	
	var tip_text;
	var compress_w;
	var compress_h;
	var compress_limit_size;
	
	var flagUploadThumbnail, thumbnailUp_width, thumbnailUp_height;
	
	var class_name="img-uploader";
	
	var imgUp = $('.'+class_name);
	
	for(i=0;i<imgUp.length;i++)
	{
			
			
			data_saver_id= typeof( $("."+class_name+":eq("+i+")").attr('data-saver'))=='undefined'  ? $("."+class_name+":eq("+i+")").attr('id')+'_saver' :$("."+class_name+":eq("+i+")").attr('data-saver');
			
			save_path=typeof( $("."+class_name+":eq("+i+")").attr('save-path'))=='undefined'  ? '/upload/' :$("."+class_name+":eq("+i+")").attr('save-path');
			
			
			
			
			
			
			
			
			flagMulti= typeof( $("."+class_name+":eq("+i+")").attr('data-multi'))=='undefined'  ? false:true;
			
			
					
			thumbnailUp_width=typeof( $("."+class_name+":eq("+i+")").attr('thumbnail-width'))=='undefined'  ? 0: $("."+class_name+":eq("+i+")").attr('thumbnail-width');
			thumbnailUp_height=typeof( $("."+class_name+":eq("+i+")").attr('thumbnail-height'))=='undefined'  ? 0: $("."+class_name+":eq("+i+")").attr('thumbnail-height');
			
			
			
			
			
			compress_w= typeof( $("."+class_name+":eq("+i+")").attr('data-compress-width'))=='undefined'  ? 0: $("."+class_name+":eq("+i+")").attr('data-compress-width');
			compress_h= typeof( $("."+class_name+":eq("+i+")").attr('data-compress-height'))=='undefined'  ? 0: $("."+class_name+":eq("+i+")").attr('data-compress-height');
			compress_limit_size= typeof( $("."+class_name+":eq("+i+")").attr('data-compress-limit-size'))=='undefined'  ? 0: $("."+class_name+":eq("+i+")").attr('data-compress-limit-size');
			
			compress_crop= typeof( $("."+class_name+":eq("+i+")").attr('data-compress-crop'))=='undefined'  ? false: true;
			
					
		
			
			tip_text=$("."+class_name+":eq("+i+")").attr('title');
			
			uploaderControl=new ImgUploaderControl($("."+class_name+":eq("+i+")").attr('id'),UPLOADER_SERVER_FILE_ID,UPLOADER_SERVER_PHP,UPLOADER_SWF_PATH,data_saver_id, flagMulti,tip_text);
			uploaderControl.compress_width=compress_w;
			uploaderControl.compress_height=compress_h;
			uploaderControl.compress_limit_size=compress_limit_size;
			
			uploaderControl.compress_crop=compress_crop;
			
	
			
					
			uploaderControl.thumbnailUp_width=thumbnailUp_width;
			uploaderControl.thumbnailUp_height=thumbnailUp_height;			
		
			uploaderControl.save_path=save_path;
			
			
			
	   		uploaderControl.createUI(); 
			
	}
	
	
}//----------/函数：初始化页面上的控件。



//load css files.
j79.loadCSS("/css/webuploader.css");



$(document).ready(function(){	
		
	WebUploader_Setup();

});

