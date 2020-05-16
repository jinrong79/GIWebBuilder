<?php

/**
 * Class
 * GF
 * global functions
 */
class GF
{

    /**
     * getKeyValue
     * get value from assoc-array data by key-name.
     * key-name support "data.idx" like multi-level key-name.
     * @param string $keyName      : key-name of value to get,support "data.idx" like multi-level key-name.
     * @param array  $data         : data to read from.
     * @param mixed  $defaultValue : if invalid key or no-existing key , then return defaultValue, [NULL- default]
     * @param bool   $flagNoBlank  : flag how to handle blank string like "  ".
     *                               true[default] -- cheat blank string as empty, and return default value.
     *                               false -- cheat blank string as normal string  and return it.
     * @return mixed                 : return value.
     */
    public static function getKeyValue($keyName, $data, $defaultValue=NULL,$flagNoBlank=true){
        if(!is_string($keyName) || trim($keyName)=='' ||  !is_array($data) || count($data)<=0 || !self::isAssoc($data) ){
            return $defaultValue;
        }
        $keyName=trim($keyName);

        //if single level key-name:
        if(stripos($keyName,'.')===false){
            $curData=isset($data[$keyName]) ? $data[$keyName]:NULL;

        }else{//if deep level key-name:

            $keyNameArr=explode('.', $keyName);
            $curData=$data;
            foreach ($keyNameArr as $keyN) {
                if(isset( $curData[$keyN])){
                    $curData=$curData[$keyN];
                }else{
                    //not found key, then set null and break loop:
                    $curData=NULL;
                    break;
                }
            }
        }

        //check if blank string when flagNoBlank==true:
        $curData=$flagNoBlank===true && is_string($curData) && trim($curData)==''  ? NULL: $curData;

        //if null then return defaultValue,else return data:
        return is_null($curData)? $defaultValue:$curData;

    }//-/

    /**
     * getKey
     * alias of getKeyValue
     * @param string $keyName
     * @param array  $data
     * @param mixed  $defaultValue
     * @param bool   $flagNoBlank
     * @return mixed
     */
    public static function getKey($keyName, $data, $defaultValue=NULL,$flagNoBlank=true){
        return self::getKeyValue($keyName, $data, $defaultValue,$flagNoBlank);
    }//-/

    /**
     * getValue
     * alias of getKeyValue
     * @param string $keyName
     * @param array  $data
     * @param mixed  $defaultValue
     * @param bool   $flagNoBlank
     * @return mixed
     */
    public static function getValue($keyName, $data, $defaultValue=NULL,$flagNoBlank=true){
        return self::getKeyValue($keyName, $data, $defaultValue,$flagNoBlank);
    }//-/


    /**
     *  fillKey
     *  [static]
     *  read dataArray, find keyName item, if exist set the value to targetVar.
     *  if not exist  or dataArray is not valid:
     *                if default!=NULL and default!='NULL' ,
     *                   then set default value to targetVar;
     *                if default=='NULL',
     *                   then set NULL to targetVar;
     *                else
     *                   do nothing to targetVar.
     *  otherwise: do nothing.
     *
     *  @param {mix}    targetVar  : target var. Be noticed, it pass by reference.
     *  @param {string} keyName    : array key name
     *  @param {array}  dataArray  : key-array which contain main data.
     *  @param {mix}    default    : default value. if NULL, then not set; if 'NULL' string then, set NULL when default.
     *  @param {bool}   flagNoBlank: true[default] -- cheat blank string as empty, and return default value.
     *
     */
    public static function fillKey(&$targetVar, $keyName, $dataArray, $default=NULL,$flagNoBlank=true){

        //read value and set to targetVar when value not NULL.
        $readValue=self::getKeyValue($keyName,$dataArray,NULL,$flagNoBlank);
        if(!is_null($readValue)){
            $targetVar=$readValue;
            return;
        }

        //set default value if default!=NULL.
        if(!is_null($default)){
            $targetVar=strcasecmp($default,'NULL')!=0? $default: NULL;
        }

    }//-/

    /**
     *  getVParams
     *  verify params for vital keys.
     *  if all exists, then return those key data.
     *  else return false, and filled noErrorOrEmptyKeys with empty key name array.
     *  @param array        $data                : param data.
     *  @param array/string $keyNameList         : keyname list string or array.
     *  @param mixed        &$noErrorOrEmptyKeys : will be filled with true if all exists.
     *                                             else will be filled with empty key name array.
     *  @param bool         $flagNoBlank         : true-cheat blank string(like "  ") as empty, return default value.
     *
     *  @return mixed                            : if all exists, then return value of key data in array. else false;
     *
     *  @usage:
     *          list($idx,$uid,$sid)=self::getVParams($params,'idx,user_idx,shop_idx',$noErr);
     *          if($noErr!==true){
     *          	  return $noErr; //return empty key list.
     *          }
     */
    public static function getVParams($data, $keyNameList, &$noErrorOrEmptyKeys,$flagNoBlank=true){


        $keyNameList=is_string($keyNameList) && strlen($keyNameList)>0 ? explode(',',$keyNameList) : $keyNameList;
        if(!is_array($keyNameList)){//error
            return false;
        }

        $result=array();
        $emptyList=array();
        foreach($keyNameList as $keyName){

            if(trim($keyName)!=''){
                $curV= self::getKeyValue($keyName, $data,NULL,$flagNoBlank);
                array_push($result, $curV);
                if(is_null($curV)){
                    array_push($emptyList, $keyName);
                }
            }
        }

        $noErrorOrEmptyKeys=true;
        if(count($emptyList)>0){
            $noErrorOrEmptyKeys=$emptyList;
        }

        return $result;


    }//-/



    /**
     * isOK
     *  check if result is success or not.
     *  @param  {key-array} result : result data in key-array format.
     *  @return {boolean}          : if result['result']==1 then return true, else return false.
     */
    public static function isOK($result){
        if( is_array($result) && array_key_exists('result', $result) && intval($result['result'])==1){
            return true;
        }else{
            return false;
        }
    }//-/
	
	/**
	*  parseInt
	*  parse given strings to integer ( regardless of length of number).
	*  accept thousand-seperator.
	*  e.g.: 12,000 => 12000
	*        -123   => -123
	*        1.3400 => 0
	*        12aab  => 0
	*        ,1100  => 0
	*        00100  => 100
	*
	*  @param  {string} numString : given number string to parse  
	*  @return {string}           : intenger number in string.
	*/
	public static function parseInt($numString){
	       $numString=trim((string)$numString);	   
		   $numString=str_replace(' ','',$numString); 
		   $preChar=substr($numString,0,1);
		   
		   if($preChar==',' || ($preChar=='-' && substr($numString,1,1)==',')){
			  return '0';   
		   }	  
		   
		   $numString=str_replace(',','',$numString);   
		   
		   if($preChar=='-'){
			  $numString=trim(substr($numString,1)); 
		   }else{
			  $preChar='';   
		   }
		    
		   $arrMatch=array();
		   if(preg_match('/^[0-9]+$/',$numString,$arrMatch)>0){			   
			   
			  $re= $arrMatch[0];			  
			  $re=preg_replace('/^0+/','',$re);
			  $re=$re==''? '0':$re;			  		  	  
			  return $preChar.$re;
			  
		   }else{
			  return '0'; 
		   }
		 

	}//-/parseInt
	
	
	/**
	*  parseIdx
	*  read idx param, and try to parse it into idx or idx list.
	*  return int or array or false when failed.
	*
	*  @param {mix}    idx  :  idx to parse, 
	*                          -can be int single number
	*                          -can be array
	*                          -can be string contains numbers seperate by ","
	*  @param {string} sep  :  seperator of idx list string,default=','  
	*  @return {int/array/bool} : false-- failed in parsing , idx is not valid.
	*                             int  -- single idx number
	*                             array-- array of idx, length >0                 
	*/
	public static function parseIdx($idx, $sep=','){
		
		
		if(is_array($idx)){//if idx is array:
		    $result=array();			
			for($i=0;$i<count($idx);$i++){				
				if(intval($idx[$i])>0){
					array_push($result,intval($idx[$i]));
				}
			}
			if(count($result)>1){
				return $result;	
			}else if(count($result)==1){
			    return $result[0];	
			}
			
		}else if(is_numeric($idx) && intval($idx)>=0){//if idx is single number		
			return intval($idx);
			
		}else if(is_string($idx)){
			$arrIdx=explode($sep,(string)$idx);
			if($arrIdx!==false){//if idx contains number seperate by ",": like '1,2,3,4'
			    $result=array();			
				for($i=0;$i<count($arrIdx);$i++){				
					if(is_numeric($arrIdx[$i]) && intval($arrIdx[$i])>=0){
						array_push($result,intval($arrIdx[$i]));
					}
				}
				if(count($result)>1){
					return $result;	
				}else if(count($result)==1){
					return $result[0];	
				}			
			}
		}
		return false;
		
		
	}//-/

    /**
     * parseIdxArray
     * parseIdx array or string, and return in array
     * @param $idx
     * @param string $sep
     * @return array: if error then return empty array. Still return array when single idx.
     */
    public static function parseIdxArray($idx, $sep=','){
        $re=static::parseIdx($idx, $sep);

        if($re===false){
            return array();
        }
        if(!is_array($re)){
            return array($re);
        }
        return $re;
    }//-/

	
	/**
	*  strCount
	*  count charactors in str. including utf-8.
	*  return {int} : amount of charactors in a string.
	*/
	public static function strCount($str)
    {
        preg_match_all("/./us", $str, $matches);
        return count(current($matches));
    }//-/
	
	
	/**
	*  isInt
	*  check if value is int like value inspite of value type.	
	*  following will return true:
	*  -string of integer with blank at the front or at the back of it. like ' 29 '
    *  -int 
	*
	*  @param {mix} value : testing value.
	*  @return {bool} : true- is integer; false-not
	*/
	public static function isInt($value){

		if(!is_numeric($value) && !is_string($value)){
			return false;
		}
		
		$value=trim(''.$value);
        $valueParsed=self::parseInt($value);

        if(strcasecmp($value,$valueParsed)==0){
            return true;
        }
		return false;
		
	}//-/
	
	/**
	*  isAssoc
	*  判断数组是否为关联数字
	*/
	public static function isAssoc($arr) {  
        return is_array($arr) && array_keys($arr) !== range(0, count($arr) - 1);
    }//-/  
	
	
	/**
	*  isExpired
	*  判断当前是否在timeStart和timeEnd之间， 是，返回true。
	*/
	public static function isExpired($timeStart, $timeEnd){
		
		$timeStart= is_null($timeStart)? strtotime('2000-01-01 00:00:00'):strtotime($timeStart);		
		$timeEnd= is_null($timeEnd)? strtotime('2999-01-01 00:00:00'):strtotime($timeEnd);
		
		if( $timeStart< time() && time()< $timeEnd){
		   return true;	
		}		
		return false;		
		
	}//-/
	
	/**
	*  getIP
	*/
	public static function getIP(){
		$ip='Unkown IP';
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
			return static::isIP($_SERVER['HTTP_CLIENT_IP'])?$_SERVER['HTTP_CLIENT_IP']:$ip;
		}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			return static::isIP($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$ip;
		}else{
			return static::isIP($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:$ip;
		}
	}//-/
	
	/**
	*  isIP
	*/
	public static function isIP($str){
		$ip=explode('.',$str);
		for($i=0;$i<count($ip);$i++){  
			if($ip[$i]>255){  
				return false;  
			}  
		}  
		return preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/',$str);  
	}//-/
	


    /**
     * getJSON
     * get json format data and transfer into Assoc-array( key-array).
     * if string, then json_decode
     * if array, then return data directly
     * if obj, then return  get_object_vars(data).
     * else return null;
     * @param $data
     * @return mixed|null
     */
    public static function getJSON($data){
        if(is_array($data)){
            return $data;
        }else if(is_object($data)){
            return get_object_vars($data);
        }else if(is_string($data) && trim($data)!=''){
            return json_decode($data,true);
        }else{
            return null;
        }
    }//-/


    /**
     * formatPath
     * return standard path string. dir is sperated by DIRECTORY_SEPARATOR constant
     * @param  string pathString : path or url string.
     * @return string          : return path string.
     */
    public static function formatPath($pathString)
    {
        return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $pathString);

    }//-/


    /**
     * get First alphabet of PinYin of cnStr.
     * e.g.:
     *      zh2PYFirst("中国");  => ZG
     *      zh2PYFirst("中国",true);  => Z
     * @param string $cnStr                    : chinese string in UTF-8
     * @param bool $flagGetFirstCharctorOnly   : get only one character's first alphabet when true. [default]-false
     * @return string : frist alphabet of PinYin of cnStr in upper-case.
     */
    public static function zh2PYFirst($cnStr, $flagGetFirstCharctorOnly=false){


        $result = '' ;
        $cnStr = iconv("UTF-8", "GB2312", $cnStr);
        $loopNo=$flagGetFirstCharctorOnly ? 1: strlen($cnStr);
        if($loopNo<=0){
            return '';
        }

        for ($i=0; $i<$loopNo; $i++) {
            $p = ord(substr($cnStr,$i,1));

            if ($p>160) {
                $q = ord(substr($cnStr,++$i,1));
                $p = $p*256 + $q - 65536;
            }

            $result .= substr(static::zhNo2PY($p),0,1);
        }
        return strtoupper( $result);
    }//-/




    /**
     * zhNo2PY
     * get PinYin by chinese ascII number.
     * e.g:
     *     when "中" ,get asc = -10544, then put this number to current function:
     *     zhNo2PY(-10544)  => 'zhong'
     * @param $num
     * @param string $blank : return $blank when unknown
     * @return int|string
     */
    public static function zhNo2PY($num, $blank = ''){

        $pylist = array(
            'a'=>-20319,
            'ai'=>-20317,
            'an'=>-20304,
            'ang'=>-20295,
            'ao'=>-20292,
            'ba'=>-20283,
            'bai'=>-20265,
            'ban'=>-20257,
            'bang'=>-20242,
            'bao'=>-20230,
            'bei'=>-20051,
            'ben'=>-20036,
            'beng'=>-20032,
            'bi'=>-20026,
            'bian'=>-20002,
            'biao'=>-19990,
            'bie'=>-19986,
            'bin'=>-19982,
            'bing'=>-19976,
            'bo'=>-19805,
            'bu'=>-19784,
            'ca'=>-19775,
            'cai'=>-19774,
            'can'=>-19763,
            'cang'=>-19756,
            'cao'=>-19751,
            'ce'=>-19746,
            'ceng'=>-19741,
            'cha'=>-19739,
            'chai'=>-19728,
            'chan'=>-19725,
            'chang'=>-19715,
            'chao'=>-19540,
            'che'=>-19531,
            'chen'=>-19525,
            'cheng'=>-19515,
            'chi'=>-19500,
            'chong'=>-19484,
            'chou'=>-19479,
            'chu'=>-19467,
            'chuai'=>-19289,
            'chuan'=>-19288,
            'chuang'=>-19281,
            'chui'=>-19275,
            'chun'=>-19270,
            'chuo'=>-19263,
            'ci'=>-19261,
            'cong'=>-19249,
            'cou'=>-19243,
            'cu'=>-19242,
            'cuan'=>-19238,
            'cui'=>-19235,
            'cun'=>-19227,
            'cuo'=>-19224,
            'da'=>-19218,
            'dai'=>-19212,
            'dan'=>-19038,
            'dang'=>-19023,
            'dao'=>-19018,
            'de'=>-19006,
            'deng'=>-19003,
            'di'=>-18996,
            'dian'=>-18977,
            'diao'=>-18961,
            'die'=>-18952,
            'ding'=>-18783,
            'diu'=>-18774,
            'dong'=>-18773,
            'dou'=>-18763,
            'du'=>-18756,
            'duan'=>-18741,
            'dui'=>-18735,
            'dun'=>-18731,
            'duo'=>-18722,
            'e'=>-18710,
            'en'=>-18697,
            'er'=>-18696,
            'fa'=>-18526,
            'fan'=>-18518,
            'fang'=>-18501,
            'fei'=>-18490,
            'fen'=>-18478,
            'feng'=>-18463,
            'fo'=>-18448,
            'fou'=>-18447,
            'fu'=>-18446,
            'ga'=>-18239,
            'gai'=>-18237,
            'gan'=>-18231,
            'gang'=>-18220,
            'gao'=>-18211,
            'ge'=>-18201,
            'gei'=>-18184,
            'gen'=>-18183,
            'geng'=>-18181,
            'gong'=>-18012,
            'gou'=>-17997,
            'gu'=>-17988,
            'gua'=>-17970,
            'guai'=>-17964,
            'guan'=>-17961,
            'guang'=>-17950,
            'gui'=>-17947,
            'gun'=>-17931,
            'guo'=>-17928,
            'ha'=>-17922,
            'hai'=>-17759,
            'han'=>-17752,
            'hang'=>-17733,
            'hao'=>-17730,
            'he'=>-17721,
            'hei'=>-17703,
            'hen'=>-17701,
            'heng'=>-17697,
            'hong'=>-17692,
            'hou'=>-17683,
            'hu'=>-17676,
            'hua'=>-17496,
            'huai'=>-17487,
            'huan'=>-17482,
            'huang'=>-17468,
            'hui'=>-17454,
            'hun'=>-17433,
            'huo'=>-17427,
            'ji'=>-17417,
            'jia'=>-17202,
            'jian'=>-17185,
            'jiang'=>-16983,
            'jiao'=>-16970,
            'jie'=>-16942,
            'jin'=>-16915,
            'jing'=>-16733,
            'jiong'=>-16708,
            'jiu'=>-16706,
            'ju'=>-16689,
            'juan'=>-16664,
            'jue'=>-16657,
            'jun'=>-16647,
            'ka'=>-16474,
            'kai'=>-16470,
            'kan'=>-16465,
            'kang'=>-16459,
            'kao'=>-16452,
            'ke'=>-16448,
            'ken'=>-16433,
            'keng'=>-16429,
            'kong'=>-16427,
            'kou'=>-16423,
            'ku'=>-16419,
            'kua'=>-16412,
            'kuai'=>-16407,
            'kuan'=>-16403,
            'kuang'=>-16401,
            'kui'=>-16393,
            'kun'=>-16220,
            'kuo'=>-16216,
            'la'=>-16212,
            'lai'=>-16205,
            'lan'=>-16202,
            'lang'=>-16187,
            'lao'=>-16180,
            'le'=>-16171,
            'lei'=>-16169,
            'leng'=>-16158,
            'li'=>-16155,
            'lia'=>-15959,
            'lian'=>-15958,
            'liang'=>-15944,
            'liao'=>-15933,
            'lie'=>-15920,
            'lin'=>-15915,
            'ling'=>-15903,
            'liu'=>-15889,
            'long'=>-15878,
            'lou'=>-15707,
            'lu'=>-15701,
            'lv'=>-15681,
            'luan'=>-15667,
            'lue'=>-15661,
            'lun'=>-15659,
            'luo'=>-15652,
            'ma'=>-15640,
            'mai'=>-15631,
            'man'=>-15625,
            'mang'=>-15454,
            'mao'=>-15448,
            'me'=>-15436,
            'mei'=>-15435,
            'men'=>-15419,
            'meng'=>-15416,
            'mi'=>-15408,
            'mian'=>-15394,
            'miao'=>-15385,
            'mie'=>-15377,
            'min'=>-15375,
            'ming'=>-15369,
            'miu'=>-15363,
            'mo'=>-15362,
            'mou'=>-15183,
            'mu'=>-15180,
            'na'=>-15165,
            'nai'=>-15158,
            'nan'=>-15153,
            'nang'=>-15150,
            'nao'=>-15149,
            'ne'=>-15144,
            'nei'=>-15143,
            'nen'=>-15141,
            'neng'=>-15140,
            'ni'=>-15139,
            'nian'=>-15128,
            'niang'=>-15121,
            'niao'=>-15119,
            'nie'=>-15117,
            'nin'=>-15110,
            'ning'=>-15109,
            'niu'=>-14941,
            'nong'=>-14937,
            'nu'=>-14933,
            'nv'=>-14930,
            'nuan'=>-14929,
            'nue'=>-14928,
            'nuo'=>-14926,
            'o'=>-14922,
            'ou'=>-14921,
            'pa'=>-14914,
            'pai'=>-14908,
            'pan'=>-14902,
            'pang'=>-14894,
            'pao'=>-14889,
            'pei'=>-14882,
            'pen'=>-14873,
            'peng'=>-14871,
            'pi'=>-14857,
            'pian'=>-14678,
            'piao'=>-14674,
            'pie'=>-14670,
            'pin'=>-14668,
            'ping'=>-14663,
            'po'=>-14654,
            'pu'=>-14645,
            'qi'=>-14630,
            'qia'=>-14594,
            'qian'=>-14429,
            'qiang'=>-14407,
            'qiao'=>-14399,
            'qie'=>-14384,
            'qin'=>-14379,
            'qing'=>-14368,
            'qiong'=>-14355,
            'qiu'=>-14353,
            'qu'=>-14345,
            'quan'=>-14170,
            'que'=>-14159,
            'qun'=>-14151,
            'ran'=>-14149,
            'rang'=>-14145,
            'rao'=>-14140,
            're'=>-14137,
            'ren'=>-14135,
            'reng'=>-14125,
            'ri'=>-14123,
            'rong'=>-14122,
            'rou'=>-14112,
            'ru'=>-14109,
            'ruan'=>-14099,
            'rui'=>-14097,
            'run'=>-14094,
            'ruo'=>-14092,
            'sa'=>-14090,
            'sai'=>-14087,
            'san'=>-14083,
            'sang'=>-13917,
            'sao'=>-13914,
            'se'=>-13910,
            'sen'=>-13907,
            'seng'=>-13906,
            'sha'=>-13905,
            'shai'=>-13896,
            'shan'=>-13894,
            'shang'=>-13878,
            'shao'=>-13870,
            'she'=>-13859,
            'shen'=>-13847,
            'sheng'=>-13831,
            'shi'=>-13658,
            'shou'=>-13611,
            'shu'=>-13601,
            'shua'=>-13406,
            'shuai'=>-13404,
            'shuan'=>-13400,
            'shuang'=>-13398,
            'shui'=>-13395,
            'shun'=>-13391,
            'shuo'=>-13387,
            'si'=>-13383,
            'song'=>-13367,
            'sou'=>-13359,
            'su'=>-13356,
            'suan'=>-13343,
            'sui'=>-13340,
            'sun'=>-13329,
            'suo'=>-13326,
            'ta'=>-13318,
            'tai'=>-13147,
            'tan'=>-13138,
            'tang'=>-13120,
            'tao'=>-13107,
            'te'=>-13096,
            'teng'=>-13095,
            'ti'=>-13091,
            'tian'=>-13076,
            'tiao'=>-13068,
            'tie'=>-13063,
            'ting'=>-13060,
            'tong'=>-12888,
            'tou'=>-12875,
            'tu'=>-12871,
            'tuan'=>-12860,
            'tui'=>-12858,
            'tun'=>-12852,
            'tuo'=>-12849,
            'wa'=>-12838,
            'wai'=>-12831,
            'wan'=>-12829,
            'wang'=>-12812,
            'wei'=>-12802,
            'wen'=>-12607,
            'weng'=>-12597,
            'wo'=>-12594,
            'wu'=>-12585,
            'xi'=>-12556,
            'xia'=>-12359,
            'xian'=>-12346,
            'xiang'=>-12320,
            'xiao'=>-12300,
            'xie'=>-12120,
            'xin'=>-12099,
            'xing'=>-12089,
            'xiong'=>-12074,
            'xiu'=>-12067,
            'xu'=>-12058,
            'xuan'=>-12039,
            'xue'=>-11867,
            'xun'=>-11861,
            'ya'=>-11847,
            'yan'=>-11831,
            'yang'=>-11798,
            'yao'=>-11781,
            'ye'=>-11604,
            'yi'=>-11589,
            'yin'=>-11536,
            'ying'=>-11358,
            'yo'=>-11340,
            'yong'=>-11339,
            'you'=>-11324,
            'yu'=>-11303,
            'yuan'=>-11097,
            'yue'=>-11077,
            'yun'=>-11067,
            'za'=>-11055,
            'zai'=>-11052,
            'zan'=>-11045,
            'zang'=>-11041,
            'zao'=>-11038,
            'ze'=>-11024,
            'zei'=>-11020,
            'zen'=>-11019,
            'zeng'=>-11018,
            'zha'=>-11014,
            'zhai'=>-10838,
            'zhan'=>-10832,
            'zhang'=>-10815,
            'zhao'=>-10800,
            'zhe'=>-10790,
            'zhen'=>-10780,
            'zheng'=>-10764,
            'zhi'=>-10587,
            'zhong'=>-10544,
            'zhou'=>-10533,
            'zhu'=>-10519,
            'zhua'=>-10331,
            'zhuai'=>-10329,
            'zhuan'=>-10328,
            'zhuang'=>-10322,
            'zhui'=>-10315,
            'zhun'=>-10309,
            'zhuo'=>-10307,
            'zi'=>-10296,
            'zong'=>-10281,
            'zou'=>-10274,
            'zu'=>-10270,
            'zuan'=>-10262,
            'zui'=>-10260,
            'zun'=>-10256,
            'zuo'=>-10254
        );

        if($num>0 && $num<160 ) {
            return chr($num);
        } elseif ($num<-20319||$num>-10247) {
            return $blank;
        } else {
            foreach ($pylist as $py => $code) {
                if($code > $num) break;
                $result = $py;
            }
            return $result;
        }
    }//-/




	
	
	
	
	
	
	
	
}//=/

