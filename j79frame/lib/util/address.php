<?php
namespace j79frame\lib\util;

use j79frame\lib\core\j79obj;
use j79frame\lib\util\Lang;

/**
*  Address
*  @author: jin rong (rong.king@foxmail.com)
*  @attribute   
*		
*  @method
*		   getCityIdx       [static]
*		   getCityKey       [static]
*		   getCityName      [static]
*          getAreaIdx       [static]
*          getAreaName      [static]
*          getProvName      [static]
*          getCountryName   [static]
*          getFullAddress   [static]

*	   
*         
**/
class Address extends j79obj
{
	
		
	protected static $_cityLabel=array(
	                                 
										 'yj'=>array('yanji','延吉',     '연길' ,'',''),  
										 'tm'=>array('tumen', '图门',    '도문','',''), 
										 'dh'=>array('dunhua', '敦化',   '돈화','','', '돈하','돈아'),  
										 'hl'=>array('helong', '和龙',   '화룡','','', '하룡'),  
										 'hc'=>array('hunchun','珲春',   '훈춘','',''),  
										 'lj'=>array('longjing','龙井',  '룡정','',''),  
										 'wq'=>array('wangqing','汪清',  '왕청','',''), 
										 'at'=>array('antu', '安图',     '안도','','')						
	
	                                  );
							
	protected static $_cityIdx =array(
	                                 
										 'yj'=>1,  
										 'tm'=>2, 
										 'dh'=>3,  
										 'hl'=>4,  
										 'hc'=>5,  
										 'lj'=>6,  
										 'wq'=>7, 
										 'at'=>8						
	
	                                  );
	
	
	protected static $_provLabel=array(
	                                    
										array( 'jilin', '吉林','길림')  //0- 吉林
									   
									    //... ... curently only support jilin
									   );
	protected static $_countryLabel=array(
	                                    
										array('china','中国',  '중국')  //0- 中国
									   
									    //... ... curently only support china
									   );
	
	/**
	*  getCityList
	*  return city list array  
	*  
	*  @param  {bool} flagUseKey : true - set value as city key; false- set value with city index.
	*  @param  {int} lang        : language of label string.
	*                              default=NULL, defined by \CONFIG::$LANG
	*  @return {array}           : =array(
	*                                     array(
	*                                           'label'=>'延吉',
	*                                           'value'=> 0
	*                                     ),
	*                                     array(
	*                                           'label'=>'图门',
	*                                           'value'=> 1
	*                                     )	
	*
	**/
	public static function getCityList($flagUseKey=false, $lang=NULL){
		
		$result=array();
		
		foreach(static::$_cityLabel as $key => $value){
			
			$reItem=array();
			
			$cityNameArr=$value;			
			$namePos= is_null($lang) ? intval(\CONFIG::$LANG) : intval($lang);
			$cityLabel= intval($namePos)<count($cityNameArr) ? $cityNameArr[intval($namePos)] : $cityNameArr[0];
			if(trim($cityLabel)==''){
				$cityLabel=	$cityNameArr[0];
			}
			$reItem['label']=$cityLabel;
			if($flagUseKey==false){
				$reValue=	array_key_exists($key,static::$_cityIdx)? trim(static::$_cityIdx[$key]):'';
			}else{
				$reValue=	$key;
			}
			$reItem['value']=$reValue;
			array_push($result, $reItem);
			
		}
		return $result;
		
	}//-/
	
	
	/**
	*  getCityIdx
	*  search city name string and return matched city idx.
	*  
	*  e.g.: 'yanji' => 0
	*        'yanji city mudan road' =>0
	*
	*  @param  {string} cityName : city name string. 
	*  @return {int}             : city idx. more list, pls refer to static::$_cityIdx;
	*
	**/
	public static function getCityIdx($cityName){
		$cityKey='';
		$cityKey=static::getCityKey($cityName);
		
		if($cityKey!==false && array_key_exists($cityKey,static::$_cityLabel)){
			
			return 	static::$_cityLabel[$cityKey];
		}
		
		return false;
		
	    	
	}//-/
	
	
	/**
	*  getCityKey
	*  search city name string and return matched city key.
	*  e.g.: 'yanji' => 'yj'
	*        'yanji city mudan road' =>'yj'
	*
	*  @param  {string} cityName : city name string.  
	*  @return {string}          : city key. e.g.:'yj' , 'hc'   .more list, pls refer to static::$_cityIdx;
	**/
	public static function getCityKey($cityName){
		
		foreach( static::$_cityLabel as $key=>$cityNameArr){
			for($j=0; $j<count($cityNameArr); $j++){
				
				$curText=$cityNameArr[$j];
				if(trim($curText)!='' && stripos($cityName, $curText)!==false){				
					
					
					return $key;					
					break;	
				}
			}		
				
		}	
		return false;
		
	    	
	}//-/
	
	
	/**
	*  getCityName
	*  return city name string by city idx or key.
	*  e.g.: getCityName(0,1) => '延吉'
	*        getCityName(0,2) => 'yanji'
	*        getCityName(0,3) => '연길'
	*
	*  @param  {string} cityIdxOrKey : city idx or key.  
	*  @param  {int}    lang         : language of name string.
	*                                  default=NULL, defined by \CONFIG::$LANG
	*                                  e.g.: lang=1 -> '延吉' lang=2 -> 'yanji' 
	*  @return {string/bool}         : city name string. | false -> not found.
	**/
	public static function getCityName($cityIdxOrKey, $lang=NULL){
		
		if(is_numeric($cityIdxOrKey)){//idx

		    
			$cityKey=array_search(intval($cityIdxOrKey),static::$_cityIdx);


			if($cityKey!==false){							
				$cityName=static::_getCityNameByKey(trim($cityKey), $lang);			
				return $cityName!==false ? $cityName : false;
				
			}else{
				return false;	
			}
			
			
		}else{//key
		
			$cityName=static::_getCityNameByKey(trim($cityIdxOrKey), $lang);			
			return $cityName;
			
		}
		
	    	
	}//-/

    /*public static function getCityName($cityIdxOrKey, $lang=NULL){

        if(is_numeric($cityIdxOrKey)){//idx


            $cityKey=array_search(intval($cityIdxOrKey),static::$_cityIdx);


            if($cityKey!==false){
                $cityName=static::_getCityNameByKey(trim($cityKey), $lang);
                return $cityName!==false ? $cityName : false;

            }else{
                return false;
            }


        }else{//key

            $cityName=static::_getCityNameByKey(trim($cityIdxOrKey), $lang);
            return $cityName;

        }


    }//-/*/
	
	
	/**
	*  _getCityNameByKey
	*  return city name string by city key.
	*  e.g.: _getCityNameByKey('yj',1) => '延吉'
	*        _getCityNameByKey('yj',2) => 'yanji'
	*        _getCityNameByKey('yj',3) => '연길'
	*
	*  @param  {string} cityKey      : city key like 'yj'.  
	*  @param  {int}    lang         : language of name string.
	*                                  default=NULL, defined by \CONFIG::$LANG
	*  @return {string/bool}         : city name string | false -> not found.
	*/
	protected static function _getCityNameByKey($cityKey, $lang=NULL){			
		if(array_key_exists($cityKey, static::$_cityLabel)){
						
			$cityNameArr=static::$_cityLabel[$cityKey];			
			$namePos= is_null($lang) ? intval(\CONFIG::$LANG) : intval($lang);
			$cityName=intval($namePos)<count($cityNameArr) && trim($cityNameArr[intval($namePos)])!='' ? $cityNameArr[intval($namePos)] : $cityNameArr[0];			
			return $cityName;				
		}else{
			return false;	
		}
		
	}//-/
	
	
	/**
	*  getProvName
	*  return province name string by province idx or key.
	*  e.g.: getProvName(0,1) => '吉林'
	*        getProvName(0,2) => 'jilin'
	*        getProvName(0,3) => '길림'
	*
	*  @param  {string} IdxOrKey     : prov idx or key.  
	*  @param  {int}    lang         : language of name string
	*                                  default=NULL, defined by \CONFIG::$LANG
	*                                  e.g.: lang=1 -> '延吉' lang=2 -> 'yanji' 
	*  @return {string/bool}         : city name string. | false -> not found.
	**/
	public static function getProvName($IdxOrKey, $lang=NULL){
		
		if(is_numeric($IdxOrKey)){//idx
		    $NameArr=static::$_provLabel[0];
			$namePos= is_null($lang) ? intval(\CONFIG::$LANG) : intval($lang);
			return intval($namePos)<count($NameArr) && trim($NameArr[intval($namePos)])!='' ? $NameArr[intval($namePos)] : $NameArr[0];
			
		}else{//key
		
				
			$NameArr=static::$_provLabel[0];
			$namePos= is_null($lang) ? intval(\CONFIG::$LANG) : intval($lang);
			return intval($namePos)<count($NameArr) && trim($NameArr[intval($namePos)])!='' ? $NameArr[intval($namePos)] : $NameArr[0];
			
		}
		
	    	
	}//-/
	
	
	/**
	*  getCountryName
	*  return country name string by country idx or key.
	*  e.g.: getCountryName(0,1) => '中国'
	*        getCountryName(0,2) => 'china'
	*        getCountryName(0,3) => '중국'
	*
	*  @param  {string} IdxOrKey     : country idx or key.  
	*  @param  {int}    lang         : language of name string
	*                                  default=NULL, defined by \CONFIG::$LANG
	*                                  e.g.: lang=1 -> '延吉' lang=2 -> 'yanji' 
	*  @return {string/bool}         : city name string. | false -> not found.
	**/
	public static function getCountryName($IdxOrKey, $lang=NULL){
		
		if(is_numeric($IdxOrKey)){//idx
		    $NameArr=static::$_countryLabel[0];
			$namePos= is_null($lang) ? intval(\CONFIG::$LANG) : intval($lang);
			return intval($namePos)<count($NameArr) && trim($NameArr[intval($namePos)])!='' ? $NameArr[intval($namePos)] : $NameArr[0];
			
		}else{//key
		
				
			$NameArr=static::$_countryLabel[0];
			$namePos= is_null($lang) ? intval(\CONFIG::$LANG) : intval($lang);
			return intval($namePos)<count($NameArr) && trim($NameArr[intval($namePos)])!='' ? $NameArr[intval($namePos)] : $NameArr[0];
			
		}
		
	    	
	}//-/
	
	
	
	
	/**
	*  getAreaIdx
	*  return city area idx according to address and city idx or key.
	*  currently return 0 in any case.
	*  keep for future extensions.
	*
	*  @param  {string} addressDetail : address detail string, for example: '铁南延龙路1245号乐达公寓7号楼'
	*  @param  {int}    cityIdxOrKey  : city idx or key. default=0,yanji
	*  @return {int}                  : address area idx in this city. 
	*/
	public static function getAreaIdx($addressDetail, $cityIdxOrKey=0){
		if(is_numeric($cityIdxOrKey)){//idx
		
		    return 0;
			
		}else{//key
		
			return 0;
		}		
		
	}//-/
	
	
	/**
	*  getAreaName
	*  return area name string by province idx or key.
    *  keep for future extensions.
	*
	*  @param  {string} IdxOrKey     : prov idx or key.  
	*  @param  {int}    lang         : language of name string
	*                                  default=NULL, defined by \CONFIG::$LANG
	*                                  e.g.: lang=1 -> '延吉' lang=2 -> 'yanji' 
	*  @return {string/bool}         : name string. | false -> not found.
	**/
	public static function getAreaName($IdxOrKey, $lang=NULL){
		
		//current empty，keep for future extensions.
		return '';
		
	    	
	}//-/
	
	
	/**
	*  getFullAddress
	*  get full address string by country idx, prov idx, city idx or key, and address detail.
	*/
	public static function getFullAddress($addressDetail, $cityIdxOrKey=0, $areaIdx=0,  $provIdx=0, $countryIdx=0){
		
		$langKey=Lang::getLangKey(\CONFIG::$LANG);
		
		
			
		$langKey=$langKey!==false ? $langKey : 'cn';	
		
		
		
		
		switch($langKey){
		
			case 'cn':  //cn
				
				return static::getCountryName($countryIdx).static::getProvName($provIdx).static::getCityName($cityIdxOrKey).' '.static::getAreaName($areaIdx).$addressDetail;
			
			case 'en':  //english
				
				return $addressDetail.', '.static::getAreaName($areaIdx).static::getCityName($cityIdxOrKey). ', '.static::getProvName($provIdx).', '.static::getCountryName($countryIdx);
			
			case 'kr':  //kr
				
				return static::getCountryName($countryIdx).static::getProvName($provIdx).static::getCityName($cityIdxOrKey).static::getAreaName($areaIdx).$addressDetail;
			
			case 'ru':  //ru
				
				return static::getCountryName($countryIdx).', '.static::getProvName($provIdx).', '.static::getCityName($cityIdxOrKey).static::getAreaName($areaIdx).', '.$addressDetail;
				
			case 'jp':  //jp
				
				return static::getCountryName($countryIdx).static::getProvName($provIdx).static::getCityName($cityIdxOrKey).static::getAreaName($areaIdx).$addressDetail;
			
					
			
		}
			
		
	}//-/
	
	
		
	
	
	

}//======================/CLASS: ErrorInfo

