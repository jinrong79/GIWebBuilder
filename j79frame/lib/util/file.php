<?php
namespace j79frame\lib\util;
use j79frame\lib\core\j79obj;

/**=================CLASS: ErrorManager
*  file类
*  @author: jin rong (rong.king@foxmail.com)
*  @attribute   

*  @method
*		[static] getRandomFileName 
*  		[static] getName
*       [static] getPath
*       [static] saveFile
*       [static] appendFile
**/
class File extends j79obj
{
	
	/**
	*  getRandomFileName
	*  get random file name using current time+random.
	*  random number size, is defined by arguments[ randWidth ]
	*
	*  @param {string} fileExt : file extesion string. with or without ".", 
	*                            if blank, then output pure file name without extension.
	*                            e.g.: "txt" / ".txt"
	*  @param {int} randWidth  : random number size. default=8, 8 digit in decimal.
	*/
	public static function getRandomFileName($fileExt='', $randWidth=8){
		$t = time();
        $d = explode('-', date("Y-y-m-d-H-i-s"));
        $format = '{yy}{mm}{dd}{hh}{ii}{ss}';
        $format = str_replace("{yyyy}", $d[0], $format);
        $format = str_replace("{yy}", $d[1], $format);
        $format = str_replace("{mm}", $d[2], $format);
        $format = str_replace("{dd}", $d[3], $format);
        $format = str_replace("{hh}", $d[4], $format);
        $format = str_replace("{ii}", $d[5], $format);
        $format = str_replace("{ss}", $d[6], $format);
        $format = str_replace("{time}", $t, $format);
		
		$randNum = rand(1, pow(10,$randWidth));
		
		$fileName=$format .$randNum;
		
		$fileExt=trim($fileExt);
		if($fileExt!=''){
			$fileExt=substr($fileExt,0,1)=='.' ? $fileExt : '.'.$fileExt;
			$fileName.=$fileExt;			
		}
		
		return  $fileName;
			
	}//-/
	
	
	/**
	*  getName
	*  get filename only from full file path or url strings.
	*
	*  @param {string} fileFullPath   : file full path or url.
	*                                   e.g.: "c:\aa\bb\cc.txt" "http://www.aa.com/bb/cc/dd.txt"
	*  @param {bool}   flagWithoutExt : return only pure filename without extension part if it is true.
	*                                   default=false.
	*  @return {stirng}               : file name only. / false - error
	*                                   e.g.: "cc.txt"  "dd.txt" | "cc" "dd"  (flagWithoutExt=true)
	*/
	public static function getName($fileFullPath, $flagWithoutExt=false){

        if(!is_string($fileFullPath)){
            return false;
        }
		
		if(trim($fileFullPath)=='') 
		    return '';
		
		$fileFullPath=static::getAbsPath($fileFullPath);
		$re=pathinfo($fileFullPath);
		
		$fileName=$re['basename'];

        $fileNamePure=$re['filename'];

		
		//$fileName=preg_replace("/(.)+(?=\/|\\\\)(\/|\\\\)/u",'',$fileFullPath);
		if($flagWithoutExt==true){
			$fileName=$fileNamePure;
			//$fileName=preg_replace("/\.(?<=\.)[^\.]+$/",'',$fileName);	
		}

		//去掉尾部"?aaa=111"
		$fileName=preg_replace("/\?[^\?]*$/u",'',$fileName);

		return $fileName;
	}//-/
	
	/**
	*  getExt
	*  get extension name.
	*  @param  {string} fileName : filename string
	*  @return {string}          : file extension name, without dot.     
	*/
	public static function getExt($fileFullName){
		if(trim($fileFullName)=='') 
		    return '';
			
		$fileFullPath=static::getAbsPath($fileFullName);
		$re=pathinfo($fileFullPath);
		//$fileExt=preg_replace("/^(.)+\./u",'',$fileFullName);
		return $re['extension'];
	}//-/
	
	
	/**
	*  getPath
	*  get path part from full path string.
	*  output path not contain tail "\" or "/"
	*  @param {string} fileFullPath : file full url or path. e.g.: c:\aa\bb\c.txt ; aa/bb/cc/dd.jpg
	*  @return {string}             : file path part without filename.
	*                                 e.g.:  c:\aa\bb  ;  aa/bb/cc
	*                                 [notice] path string not contain its last "/" or "\"
	*/
	public static function getPath($fileFullPath){
		
		if(trim($fileFullPath)=='') 
		    return '';
		
		$fileFullPath=static::getAbsPath($fileFullPath);
		$re=pathinfo($fileFullPath);
		return $re['dirname'];
		//$path=preg_replace("/[^\/\\\\]+$/u",'',$fileFullPath);
		//return preg_replace("/\\\\$|\/$/u",'',$path);
		
		
	}//-/
	
	
	/**
	*  formatAbsUrl
	*  get absolute url which start with "/" and end witout "/";
	*  
	*/
	public static function formatAbsUrl($url){
		
		if(trim($url)=='') 
		    return '';
		
		$absUrl=str_replace('\\', '/', $url);
		$absUrl=substr($absUrl, 0, 1)!='/' ? '/'.$absUrl: $absUrl;
		$absUrl=substr($absUrl, -1, 1)=='/' ? substr($absUrl,0, strlen($absUrl)-1): $absUrl;
		return $absUrl;
			
	}//-/
	
	/**
	*  exists
	*  check whether given pathOrUrl exists or not.
	*
	*  @param  {string} pathOrUrl : path or url string.
	*  @return {bool}             : return exists or not. ( exists as a file or dir, return true)
	*/
	public static function exists($pathOrUrl){
		
		$pathAbs=static::getAbsPath($pathOrUrl);

		if( file_exists($pathAbs)){
		  return true;	
		}
		return false;		
		
	}//-/

	/**
	 * genFullPath
	 * generate full path with url and filename.
	 * @param $urlOrPath
	 * @param $fileName
	 */
	public static function genFullPath($urlOrPath,$fileName){
		//$pathPrefix=self::formatAbsUrl($urlOrPath);
		return static::getAbsPath($urlOrPath.DIRECTORY_SEPARATOR.$fileName);
	}//-/
	
	
	/**
	*  copyFile
	*  copy file with some pre-handling operation.
	*  1) if overwrite: check existance of the target name, if exists, then delete it.
	*  2) if not overwrite: check existance of the target name,  if exists ,return false;
	*  3) check $fromFile, if it exists, if not ,return false;
	*  4) check $toFile, if empty string or not string, then return false;
	*  5) do copy.
	*  6) return result of copy.
	*
	*  @param {string} formFile      : copy original-file path/url.
	*  @param {string} toFile        : copy to-file path/url.
	*  @param {bool}   flagOverwrite : whether overwrite to-file. default=true;
	*  @return {bool}                : true- success ; false - failed.
	*/
	public static function copyFile($fromFile, $toFile, $flagOverwrite=true){
	    //if overwrite then clear file with name of $toFile. 
		
		$fromFile=static::getAbsPath($fromFile);
		$toFile=static::getAbsPath($toFile);	
		
		if( $fromFile==$toFile){
		  return false;	
		}
		
		
		//if toFile exists:
		if(file_exists($toFile) && !is_dir($toFile)){
			
			if($flagOverwrite==true){
				$re=unlink($toFile);
				if($re===false){
				   return false;  
				} 
			}else{
			    return false;	
			}
		  	
		}		
		
		//do copy
		if( is_string($toFile) && trim($toFile)!=''){ 
		  
		   if(!file_exists($fromFile)){ //if fromFile not exists
		   
			  return false;			 
		   }
		
		   $toPath=static::getPath($toFile);
		   if( ! is_dir($toPath)){//判断目录是否存在，没有则建立。				
			   $re_md=mkdir($toPath,0777,true);			
			   if ( !$re_md){
					//echo 'Exception: failed creating log dir';  
					return false;
				 }						  
		   }		   		
		   return copy ($fromFile, $toFile);
		   
		}
		
		return false;	
		
		
	}//-/

	/**
	 * delete
	 * @param $filePathOrUrl
	 *
	 * @return bool|null : true- success; false-failed deleting; NULL- file not exists.
	 */
	public static function delete($filePathOrUrl){

		if(self::exists($filePathOrUrl)){
			$fileAbsPath=static::getAbsPath($filePathOrUrl);
			$reDel=unlink($fileAbsPath);
			return $reDel;
		}
		return NULL;
	}//-/
	
	/**
	*  moveFile
	*  move file. 
	*  @param {string}  fromFile, toFile :  file path or url
	*  @param {bool}    flagOverwrite    :  whether overwrite or not.
	*  
	*  @return {bool}                    :  true - success ; false - failed.
	*/
	public static function moveFile($fromFile, $toFile, $flagOverwrite=true){
	   	
		$re=static::copyFile($fromFile, $toFile, $flagOverwrite);
		if($re===true){
		   $re=unlink(static::getAbsPath($fromFile));	
		}
		return $re;
	}//-/


    /**
     * getURL
     * change all '\' to '/'.
     * @param $strPathOrUrl
     * @return string
     */
    public static function getURL($strPathOrUrl){

        return str_ireplace("\\",'/', $strPathOrUrl);

    }//-/
	
	/**
	*  getAbsPath
	*  format give url/path string to system related format with full path.
	*  if windows:  '/path/1.jpg' => 'c:\wwwroot\path\1.jpg'
	*               '\path\1.jpg' => 'c:\wwwroot\path\1.jpg';
	*               'c:\wwwroot\path\1.jpg'=>'c:\wwwroot\path\1.jpg';
	*  if other system(unix):  '/path/1.jpg' => '/wwwroot/path/1.jpg'
	*                          '/wwwroot/path/1.jpg' => '/wwwroot/path/1.jpg'
	*                          '\wwwroot\path\1.jpg' => '/wwwroot/path/1.jpg';
	*
	*  @param {string}  strPathOrUrl : string of path or url relative to wwwroot dir.
	*                                  '/data/1.jpg'  'c:\wwwroot\data\1.jpg'
     * @return string
	*/
	public static function getAbsPath($strPathOrUrl, $rootPath=''){
		
		
		//get root path, root-path end witout DIRECTORY_SEPARATOR, should be like 'c:\wwwroot'
		if($rootPath==''){
		    $rootPath=\CONFIG::$PATH_ROOT;
		}
		
		
		$strPathOrUrl=trim($strPathOrUrl);
		if(!is_string($strPathOrUrl) || $strPathOrUrl==''){
		  return $rootPath;		  
		}
				
		//check whether contains rootPath at head of strPathOrUrl;
		$re=stripos($strPathOrUrl, $rootPath);			
		if( $re!==false && $re==0){//contains rootPath at head:		  	
			return str_replace(array('/', '\\'),DIRECTORY_SEPARATOR,$strPathOrUrl);			
		}else{//not contain rootPath:
			
			//check whether first char is '/' '\', if not add DIRECTORY_SEPARATOR at front of strPathOrUrl.
			$strF=substr($strPathOrUrl,0,1);
			if($strF!='/' &&  $strF!=DIRECTORY_SEPARATOR){
				$strPathOrUrl=DIRECTORY_SEPARATOR.$strPathOrUrl;
			}	
			
			//rootPath + DIRECTORY_SEPARATOR replaced path string.	
			return $rootPath.str_replace(array('/', '\\'),DIRECTORY_SEPARATOR,$strPathOrUrl);		
			
		}
		
		
	}//-/
	
	
	
	
	
	/**
	*  renameFiles
	*  rename files by pattern
	*  
	*  @param {array}  oldFileNames : array of file full path and name string
	*  @param {array}  newFileNames : to save array of new file name. pass by refer.
	*  @param {string} pattern      : indicate how to rename the file.
	*                                 (#name#) -> original file name without ext.
	*                                 (#path#) -> original path with "\" at tail
	*                                 (#ext#)  -> original file ext without '.'
	*                                 (#idx#)  -> idx number which autoincreased 
	*                                             starting with 0.   
	*  @param {string} fileBasePath : file path root, default=''.
	*                                 if not empty, 
	*                                 file full path= fileBasePath+filename from oldFileNames.             
	*/
	public static function renameFiles($oldFileNames, &$newFileNames, $pattern='(#path#)temp_(#name#).(#ext#)', $basePath=''){
		//params validation:
		if(!is_array($oldFileNames) || count($oldFileNames)<=0 || trim($pattern)==''){
			return false;
		}
		
		$rootPath='';
		$result=true;
		
		$newFileNames=array();
		
		for($i=0;$i<count($oldFileNames);$i++){
			
			$curFile=$oldFileNames[$i];
			
			$fileName=static::getName($curFile,true);
			
			if(trim($fileName)==''){
			  continue;	
			}
				
			$filePath=static::getPath($curFile)."\\";
			$fileExt=static::getExt($curFile);
			
			if($basePath!=''){
			   $rootPath=substr($basePath,-1,1)=="\\" || substr($basePath,-1,1)=="/"? 	substr($basePath,0,strlen($basePath)-1):$basePath;
			   
			   $filePath=substr($filePath,0,1)!="\\" && substr($filePath,0,1)!="/"?  "/".$filePath : $filePath;
			   $curFile=substr($curFile,0,1)!="\\" && substr($curFile,0,1)!="/"?  "/".$curFile : $curFile;
			}
			
			
			$newFullName=$pattern;
			$newFullName=str_replace('(#path#)',$filePath,$newFullName);
			$newFullName=str_replace('(#name#)',$fileName,$newFullName);
			$newFullName=str_replace('(#ext#)',$fileExt,$newFullName);
			$newFullName=str_replace('(#idx#)',(string)$i,$newFullName);
			if($basePath!=''){
			   $newFullName=substr($newFullName,0,1)!="\\" && substr($newFullName,0,1)!="/"?  "/".$newFullName : $newFullName;	  
			}
			
			
			if(file_exists($rootPath.$curFile)){
				
			
			   $re=rename( static::getAbsPath($rootPath.$curFile), static::getAbsPath($rootPath.$newFullName));	
			      
			}else{
			   $re=false;	
			}
			
			array_push($newFileNames, $re==true? $newFullName: $curFile);			
			$result=$result && $re;
			
		}
		return $result;
		
		
		
	}//-/
	
	
	/**
	*  saveFile
	*  can overwrite or append. default, over-write.
	*  @param {string}  fileFullPath  : file full path and filename. if no path provided, then use \CONFIG::$PATH_ROOT
	*  @param {string}  content       : content to save  
	*  @param {bool}    flagAppend    : flag to decide append or not. 
	*                                   false[default]-overwrite; true - append
     * @return {bool} : true -OK ; false- error.
	*/
	public static function saveFile($fileFullPath, $content, $flagAppend=false){
		$fileFullPath=trim($fileFullPath);
		if($fileFullPath==''){
			return false;	
		}
        $fileFullPath=static::getAbsPath($fileFullPath);
		$filePath=static::getPath($fileFullPath);
		if($filePath==''){
			$filePath=\CONFIG::$PATH_ROOT;
			$fileFullPath=$filePath.'/'.static::getFileName($fileFullPath);	
		}
		//if dir not exist, then make dir.
		if( ! is_dir($filePath)){//判断目录是否存在，没有则建立。				
		   $re_md=mkdir($filePath,0777,true);			
		   if ( !$re_md){
				//echo 'Exception: failed creating dir';
                Log::val("file save failed",'Exception: failed creating dir' );
				return false;
		   }				 
						
		}
		
		
		try{
			
			file_put_contents($fileFullPath, $content, ($flagAppend==true ? FILE_APPEND:LOCK_EX));	
		}catch(Exception $e){
			//echo "Exception ".$e->getCode().": ".$e->getMessage()." in ".$e->getFile()." on line ".$e->getLine()."<br/>";
            Log::val("file save failed","Exception ".$e->getCode().": ".$e->getMessage()." in ".$e->getFile()." on line ".$e->getLine() );
			return false;	
		}			
		return true;	
		
	}//-/
	
	/**
	*  appendFile
	*  append content to the file
	*/
	public static function appendFile($fileFullPath, $content){
					
		return static::saveFile($fileFullPath,$content,true);	
		
	}//-/

    /**
     * readFile
     * read file and return as string.
     * if error,then return false
     * @param $urlOrPath : file url or path relative to site root.
     * @return bool|string: false- error; string - file content string.
     */
    public static function readFile($urlOrPath){
        $filePath=self::getAbsPath($urlOrPath);



        if(!file_exists($filePath)) {
           return false;
        }

        $errLvl = error_reporting();
        error_reporting(0);
        $handle = fopen($filePath, "r");
        error_reporting($errLvl);
        if ($handle === false) {
            return false;
        }
        $templateStr='';
        if(filesize($filePath)>0){

            $templateStr = fread($handle, filesize($filePath));
        }
        return $templateStr;
    }//-/
	
}//=/