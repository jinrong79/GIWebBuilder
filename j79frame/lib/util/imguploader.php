<?php
namespace j79frame\lib\util;

use j79frame\lib\util\Uploader;
use j79frame\lib\util\Image;
use j79frame\lib\util\Log;
/**
*  Image
*  主要功能： 图片处理类
*
*  对象属性:
*			inputNameFile      :  file input control name in request.
            inputNameSavePath      :  control name of img save path in request
            inputNameThumbW
            inputNameThumbH
            inputNameCompressW
            inputNameCompressH
	        fileMaxSize        :  allowed file max size in bytes.
*  Method: 
*           upload      -do upload process.
*           setSavePath -set default save path.
*
*/
class  ImgUploader
{
	

	public $inputNameFile='file_field'; //file input control name in request.

	
	public $inputNameSavePath='save_path';                   //control name of img save path in request
	public $inputNameThumbW='thumb_width';
    public $inputNameThumbH='thumb_height';
    public $inputNameCompressW='compress_width';
    public $inputNameCompressH='compress_height';
	public $fileMaxSize=20971520;                   //file max size, default 20m bytes.

    protected $_allowedExt=  array(".png", ".jpg", ".jpeg"); //allowed extention
	protected $_fileNameFormat="{yy}{mm}{dd}{hh}{ii}{ss}{rand:5}"; // img file naming format

	protected $_fileInfo=array(); //file info array. all file info saved in this array after upload

	protected $_fileSavePath='/upload/'; //uploaded file save in here. this value is filled by $_REQUEST[$this->inputNameSavePath].

	protected $_image=NULL; // \j79frame\lib\util\Image object instance, contains current image file.
 
	
	/**
	*  __construct
	*/
	public function __construct(){
		
		$this->_fileInfo=array();
		$this->_fileInfo['state']='';
		$this->_fileInfo['url']='';
		
	}//-/
	
	
    

	/**
	*  setSavePath
	*  set save path.
	*  @param {string} fileSavePath : img file save path not including file name.
	*                                 this path is relative to root, not absolute one.
	*                                 e.g.:  /data/file/img/
	*/
	public function setSavePath($fileSavePath){
		
		//$fileSavePath= substr($fileSavePath,0,1)!='/' && substr($fileSavePath,0,1)!="\\" ? '/'.$fileSavePath: $fileSavePath;

        $fileSavePath=File::formatAbsUrl($fileSavePath);

		
		if(realpath(\CONFIG::$PATH_ROOT.$fileSavePath)!==false){
			//$fileSavePath= substr($fileSavePath,-1,1)!='/' && substr($fileSavePath,-1,1)!="\\" ? $fileSavePath . '/' : $fileSavePath;
			$this->_fileSavePath=$fileSavePath;
			return true;
		}else{
			$p1=File::getAbsPath(\CONFIG::$PATH_ROOT.$fileSavePath);
			if( ! is_dir($p1)){	
			
				$re_md=mkdir($p1,0777,true);			
				if( !$re_md){					
					Log::add( 'error: failed creating uplodad file dir-'.$fileSavePath);  
					return false; 
				}else{
					$this->_fileSavePath=$fileSavePath;
					return true;	
				}
		    }
		    	
		}
	}//-/
	
	
	/**
	*  getSavePath
	*  get save path
	*/
	public function getSavePath(){	
		
		$this->setSavePath($this->_fileSavePath);		
		return $this->_fileSavePath;
	}//-/


    /**
	*  upload
	*  process upload, add thumb image, and do compress.
	*
    *  @return {array}: file info
	*                       = array(
	*                               "state"    =>      //上传状态，上传成功时必须返回"SUCCESS"
	*                               "url"      =>      //返回的地址
	*                               "title"    =>      //新文件名
	*                               "original" =>      //原始文件名
	*                               "type"     =>      //文件类型
	*                               "size"     =>      //文件大小
	*                               "width"    =>      //img width
	*                               "height"   =>      //img height
	*                               "thumb_width"  =>  //thumb img height, if no thumb, then not exist key.
	*                               "thumb_height" =>  //thumb img height
	*                               "thumb_url" =>     //thumb img full url
	*                               "compress" =>      //if yes, means compressed.
	*                       )
	*
	*/
	public function upload(){
		
		
		if($this->_upload()===true){

			$this->_addThumb();
			$this->_compress();
		}
        
		$result=$this->_fileInfo;
		
		if( isset($result['state']) && strtolower($result['state'])=='success'){
			$result['result']=1;
			
		}else{
			$result['result']=0;
			$result['msg']='file uploader failed:'.(isset($result['state'])  ? $result['state']:'fileuploader returned invalid result.');
		}
		
		return $result; 


	}
	

    /**
	*  _upload
    *  upload img, and return file info.	
	*/
	protected function _upload(){
		/* 上传配置 */
		$base64 = "upload";
		$fileSavePath=isset($_REQUEST[$this->inputNameSavePath]) ? trim($_REQUEST[$this->inputNameSavePath]) : '';
		


		if($fileSavePath!=''){	
		
			$this->setSavePath($fileSavePath);				
		}
				
		$fileSavePath=File::formatAbsUrl($this->_fileSavePath).'/';
			

		$config = array(
					"pathFormat" => $fileSavePath.$this->_fileNameFormat,
					"maxSize" => $this->fileMaxSize        
				);
		$config["allowFiles"]=$this->_allowedExt;

		  
		//if( ! array_key_exists($this->inputNameFile, $_REQUEST)){
		//	return false;
		//}
		
		
		if( ! is_dir(\CONFIG::$PATH_ROOT.$fileSavePath)){
			
		    $re_md=mkdir(\CONFIG::$PATH_ROOT.$fileSavePath,0777,true);
		    if( !$re_md){
			   	Log::add( 'Exception: failed creating uplodad file dir-'.$fileSavePath);  
			    return false; 
			}
		}
		
		
		/* 生成上传实例对象并完成上传 */
		$up = new Uploader($this->inputNameFile, $config, $base64);


		

		/**
		 * 得到上传文件所对应的各个参数,数组结构
		 * array(
		 *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
		 *     "url" => "",            //返回的地址
		 *     "title" => "",          //新文件名
		 *     "original" => "",       //原始文件名
		 *     "type" => ""            //文件类型
		 *     "size" => "",           //文件大小
		 * )
		 */

		/* 返回数据 */

		$this->_fileInfo=$up->getFileInfo();
        
		if( strcasecmp($this->_fileInfo['state'],'SUCCESS')==0){
			
			$fullPath=realpath(\CONFIG::$PATH_ROOT.$this->_fileInfo['url']);
			
			$this->_fileInfo['save_path']=$this->_fileSavePath;
					
			if($fullPath!==false){	
				$im=new Image($fullPath);
				$this->_fileInfo['width']=$im->Width;
				$this->_fileInfo['height']=$im->Height;
				$this->_image=$im;
			}
			return true;
		}else{
			return false;
		}

		
	}//-/

    /**
    *  _addThumb
	*  add thumb img
    */
	protected function _addThumb(){

		$im=$this->_image;
		
		$fileSavePath=$this->_fileSavePath;
		@ $ThumbW=$_REQUEST[$this->inputNameThumbW];
		@ $ThumbH=$_REQUEST[$this->inputNameThumbH];
		

		

		$ThumbW= $ThumbW=='' ? 0: intval($ThumbW);
		$ThumbH= $ThumbH=='' ? 0: intval($ThumbH);

		//制作缩略图
		$this->_fileInfo['thumb_url']='';
		if($ThumbW>0 || $ThumbH>0 ){
			
			$this->_fileInfo['thumb_width']=$ThumbW;
			$this->_fileInfo['thumb_height']=$ThumbH;
			
			$originFile=realpath(\CONFIG::$PATH_ROOT.$this->_fileInfo['url']);
			
			
			$fileExt=strtolower(strrchr($originFile, '.'));	
			$newFile=static::getFullName($this->_fileNameFormat, $fileExt);
			
			$newFileFullName=\CONFIG::$PATH_ROOT. $fileSavePath."thumb/".$newFile;
			
			Log::add('img upload:'.$newFileFullName);
			
			$re=$im->Resize($ThumbW, $ThumbH, $newFileFullName);
			if($re==true){		
				$this->_fileInfo['thumb_url']=$fileSavePath."thumb/".$newFile;	
				
			}
		}

	}//-/

    /**
    *  _compress
	*  compress img
    */
    protected function _compress(){
		
		$im=$this->_image;
		//$fileSavePath=$this->_fileSavePath;
		
		 $CompressW=isset($_REQUEST[$this->inputNameCompressW]) ? intval($_REQUEST[$this->inputNameCompressW]) :0;
		 $CompressH=isset($_REQUEST[$this->inputNameCompressH]) ? intval($_REQUEST[$this->inputNameCompressH]) :0;

		/*$CompressW= $CompressW=='' ? 0: intval($CompressW);
		$CompressH= $CompressH=='' ? 0: intval($CompressH);*/



		//压缩图片
		if($CompressW>0 || $CompressH>0 ){
            Log::val('$CompressW:',$CompressW);
            Log::val('$CompressH:',$CompressH);
			$re=$im->ShrinkAndCrop($CompressW, $CompressH);
            Log::val('re shrink:',$re);
			if($re==true){		
				$this->_fileInfo['compress']='yes';	
				$this->_fileInfo['compress_width']=$CompressW;
			    $this->_fileInfo['compress_height']=$CompressH;
				
			}
			
		}


	}//-/


    /**
	*  getFullName
	*  get full name by format and ext.
	*/
	public static function getFullName($pathFormat, $ext)
    {
        //替换日期事件
        $t = time();
        $d = explode('-', date("Y-y-m-d-H-i-s"));
        $format = $pathFormat;
        $format = str_replace("{yyyy}", $d[0], $format);
        $format = str_replace("{yy}", $d[1], $format);
        $format = str_replace("{mm}", $d[2], $format);
        $format = str_replace("{dd}", $d[3], $format);
        $format = str_replace("{hh}", $d[4], $format);
        $format = str_replace("{ii}", $d[5], $format);
        $format = str_replace("{ss}", $d[6], $format);
        $format = str_replace("{time}", $t, $format);

        

        //替换随机字符串
        $randNum = rand(1, 10000000000) . rand(1, 10000000000);
        if (preg_match("/\{rand\:([\d]*)\}/i", $format, $matches)) {
            $format = preg_replace("/\{rand\:[\d]*\}/i", substr($randNum, 0, $matches[1]), $format);
        }        
        return $format . $ext;
    }//-/
	
	
	
	
	
	
	
		
	
	
}//=/

