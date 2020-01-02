<?php
namespace j79frame\lib\util;
use j79frame\lib\util\HttpRequest;

/**
*  Lang
*  global page language class
*  @author: jin rong (rong.king@foxmail.com)
*  @attribute   
*		
*  @method
*		   
*	       setLang    [static]
*          getLangIdx [static]
*         
**/
class Lang
{
	
    const DEFAULT_LANG_IDX=1;	
	
	protected static $_LangLabel=array(
	                                 
										 							 
										 'en'=>'English',
										 'cn'=>'简体中文',		
										 'kr'=>'조선말',   
										 'ru'=>'русский',  
										 'jp'=>'日本語'  					
	
	                                  );
							
	protected static $_LangIdx =array(
	                                 
										 
										 'en'=>0,
										 'cn'=>1,   
										 'kr'=>2,  
										 'ru'=>3,  
										 'jp'=>4
									
	                                  );
									  
	
	protected $_dictionary=NULL;                   // dictionary xml
	
	protected $_dicFileName='dictionary.xml'; // dictionary.xml file name.
	
	
	
	/**
	*  __construct
	*/
	public function __construct(){
		
		/* 
		//set current lang idx:
		if(is_null($currentLangId)){
			$this->_langIdx=static::DEFAULT_LANG_IDX;			
		}else{
			$this->_langIdx=intval($currentLangId);
		}
		 */
		//read xml into var;
		$this->_readDic();
	}//-/
	
	/**
	*  _readDic
	*  read dictionary.xml to $this->_dictionary
	*/
	protected function _readDic(){
		
		$path=realpath( \CONFIG::$URL_SETTINGS. '/'.$this->_dicFileName);
		if($path!==false){
			
			$xml=simplexml_load_file($path);
			if($xml){
				$this->_dictionary=$xml;	
			}
		}
	}//-/
	
	
	/**
	*  getLangLabel
	*  get language label text according to langIdx.
	*/
	public static function getLangLabel($langIdx=1){
		if(count( static::$_LangLabel)>$langIdx && $langIdx>=0){
			$curKey=static::getLangKey($langIdx);
			if(array_key_exists($curKey, static::$_LangLabel)){
				return static::$_LangLabel[$curKey];	
			}
		}
		return '';
		
	}//-/
	
	/**
	*  genLangMenu
	*  construct html code for language menu.
	*/
	public static function genLangMenu($curLangIdx=NULL){
		
		if(is_null($curLangIdx)){			
		   $curLangIdx=self::$_LangIdx;
		}
		
        $html='';
        $currentURL=htmlentities($_SERVER['PHP_SELF']);
        $linkLangBase=$currentURL.'?'.HttpRequest::getURLParams('lang');
		
		$i=0;
		foreach(static::$_LangLabel as $key=>$value){
			$strActive= $i==$curLangIdx? 'active' : '';
			$list=str_replace('(#url#)', $linkLangBase.'&amp;lang='.$i,'<li class=" (#active#)"><a href= "(#url#)" target="_top">(#label#)</a></li>');
			$list=str_replace('(#label#)', $value, $list);
			
			$list=str_replace('(#active#)', $strActive, $list);
			$html.=$list;
			
			$i++;
			
		}
		
		return $html;
		
	}//-/

    public static function getLangArray(){
        $result=array();

        foreach (self::$_LangLabel as $langCode=>$langLabel){

            $langIdx=self::getLangIdx($langCode);
            $item=array('code'=>$langCode, 'label'=>$langLabel);
            if($langIdx!==false){

                $result[$langIdx]=$item;

            }
        }
        return $result;

    }//-/
	
	
	/**
	*  getLangIdx
	*  get language idx by language key. for example: getLangIdx('cn') => 0
	*
	*  @param {string} langKey : language key
	*
	*/
	public static function getLangIdx($langKey){
		
		if(array_key_exists(strtolower($langKey),static::$_LangIdx)){
			
			return 	static::$_LangIdx[strtolower($langKey)];
		}
		
		return false;
		
	}//-/
	
	/**
	*  getLangKey
	*  get language key by language idx.
	*  @param {int} langIdx : language idx.
	*  @return {string/bool}: language key like 'cn' in lower case. false -> not found. 
	*/
	public static function getLangKey($langIdx){
		
		$idx=array_search(intval($langIdx), static::$_LangIdx);
		return $idx;
		
	}//-/
	
	
	/**
	*  setLang
	*  set current language idx( in \CONFIG::$APP['lang'] ) by langage idx or key
	*
	*  @param {string/int} langIdxOrKey : language idx or key
	*
	*/
	public static function setLang($langIdxOrKey){
		
		if(is_numeric($langIdxOrKey)){//idx:
			
			if(in_array(intval($langIdxOrKey),static::$_LangIdx)){
				\CONFIG::$APP['lang']=intval($langIdxOrKey);
			}
		}else{//key:
		    if(array_key_exists(strtolower($langIdxOrKey),static::$_LangIdx)){
			    \CONFIG::$APP['lang']=static::$_LangIdx[strtolower($langIdxOrKey)];
		    }
		}
		
		
		
	}//-/
	
	/**
	*  text
	*  get current languge text according to given english text $strEn
	*
	*  @param {string} strEn:  given english text , which must exist in dictionary.xml
	*  @return : return current language text. 
	*            if xml data not exists or not found text in xml,
	*            then return $strEn self back. 
	*/
	public function text($strEn){
		
		/* if(is_null($this->_dictionary)){			
			$this->_readDic();
		} */
		
		if(is_null($this->_dictionary) || intval(\CONFIG::$APP['lang'])==0){
			return $strEn;	
		}
		
		$xml=$this->_dictionary;
		
		$result=$xml->xpath("p[@en='".$strEn."']");
		
		if($result!==false && count($result)>0){
			
			$xmlItem=$result[0];		
			$curLangKey=static::getLangKey(intval(\CONFIG::$APP['lang']));
			
			$curText=$xmlItem->children()->{$curLangKey};
			if(!is_null($curText)){
				return $curText;	
			}
			
		}
		return $strEn;	
		
		
	}//-/
	
		
	
	
	

}//======================/CLASS: ErrorInfo

