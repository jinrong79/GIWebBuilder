<?php
namespace j79frame\lib\db;
use j79frame\lib\core\j79obj;
use j79frame\lib\db\DBConnector;
use j79frame\lib\util\Log;
use mysqli;

/**
*  DBReader
*  数据库读取类。
*  主要是针对表的读取操作。
*  数据库连接中通过DBConnector::connect()自动获取。	
*
*  @author: jin rong (rong.king@foxmail.com)					
**/

class DBReader extends  j79obj
{
	
	const  DEFAULT_PAGE_VOLUME=20; //每页记录数量默认值。
	
	//public  $dbConnect=null; //mysqli db connect
	
	
	protected $_SQL=''; //当前查询的整体sql，不包括LIMIT 部分。
	
	
	//protected $_escapeWords=array( ';' , ' DROP ', ' UPDATE ', ' DELETE ' , ' CREATE ', ' ALTER ', 'RENAME '); // 从sql语句中删除的危险单词。
	
	protected $_escapeWords=array( ';' ); // 从sql语句中删除的危险单词。
	
	
	protected static $_flagAddLog= true; //whether add log.

    protected $_errorMsg=''; //inner msg for error recording.


    protected $_dbConnect=null; //db connection. when empty, get connection from DBConnect::connect()
 
	
	/**
	*    __construct
	*/
	public function __construct(){

	}//-/

    /**
     * __get
     * get attribute from outside.
     * @param $keyname
     * @return null|string
     */
    public function __get($keyname){
        if(empty($keyname) || !is_string($keyname)) return null;

        switch(trim(strtolower($keyname))){

            case 'error_msg':
                return str_replace("'",'&quot;',$this->_errorMsg);
                break;
            case 'dbconnect':
                return $this->_dbConnect;
                break;
            default:
                return NULL;
        }

    }//-/

    /**
     * __set
     * setter for 'dbConnect'.
     * @param $keyname
     * @param $value
     */
    public function __set($keyname,$value){
        if(empty($keyname) || empty($value)) return;

        switch(trim(strtolower($keyname))) {
            case 'dbconnect':
                $this->_dbConnect=$value;
                break;
        }

    }//-/

    /**
     * _getDBConnect
     * get db connect.
     * if $this->_dbConnect valid, then get it;
     * if not, then get from DBConnect::connet();
     * @return mixed|null
     */
    protected function _getDBConnect(){
        return empty($this->_dbConnect) ? DBConnector::connect() : $this->_dbConnect;
    }//-/
	
	
		
	/**__desturct
	* 关闭数据库。 
	*/
	public function __destruct ()
	{
		//if (! is_null($this->dbConnect)) {
		//	mysqli_close($this->dbConnect);
		//}
		
	}//-/
	
	/**
	*  _addLog
	*  according to $this->_flagAddLog, add log or not.
	*/
	protected static function _addLog($logStr, $valName=''){
		if(self::$_flagAddLog==true){
		    if($valName==''){
			  Log::add($logStr);
			}else{
			  Log::val($valName,$logStr);
			}
				
		}
	}//-/


	/**
	 * multiRead
	 * do multi-line sql read.
	 * @param {string/array}  $sqlArr : sql string array or sql string in which every sql is separated by ';'.
	 */
	public function multiRead($sqlArr){

		if(empty($sqlArr)){
			return false;
		}

		//get params sqlArr
		if(is_string($sqlArr)){
			$sqlArr=explode(';',$sqlArr);
		}
		if(!is_array($sqlArr)){
			return false;
		}

		//get not-empty sqls into sqlList;
		$sqlList=array();
		foreach($sqlArr as $sqlSingle){
			if(!empty(trim($sqlSingle))){
				array_push($sqlList, trim($sqlSingle));
			}
		}

		//get db connection:
		$db= $this->_getDBConnect();
		if(empty($db)){
			return false;
		}

		//get multi-line sql strings.
		$query=implode(';',$sqlList);

		$resultTotal=array();

		// execute multi query
		self::_addLog('multi-query sql', $query);

		if ($db->multi_query($query)) {
			do {
				// store first result set
				if ($result = $db->store_result()) {

					$reData=array();
					while ($row = $result->fetch_assoc()) {
						array_push($reData, $row);
					}
					array_push($resultTotal, $reData);
				}
				$result->free();

			} while ($db->more_results() && $db->next_result());
			return $resultTotal;
		}else{
			self::_addLog('multi-query error', mysqli_errno($db));
			//return mysqli_errno($db);
			return false;
		}

	}//-/
	
	/**
	* read
	* @param {array} data=>(                                  
	*                                  'select_from'=> select * from 的目标sql语句，即sql查询中，除去开头select * from和结尾LIMIT部分，剩余的中间sql语句。
	*                                                           比如：select * from tb_test where test_idx>5 order by test_idx DESC LIMIT 0,10 的情况:
	*                                                                    select_from =>'tb_test where test_idx>5 order by test_idx DESC'
	*                                  'fields' => 数组, 读取的字段名称列表。
	*                                                  例如：array ( fieldname1, fieldname2....)
	*                                                  注意：有join的情况，如果fieldname是用作关联的字段，需要写完整名，即包括表明的完整字段名，例如：tb_pro.pro_idx
	*
	*                                  'start' => 第几行开始返回。用于LIMIT。 start 计数从1开始。
	*                                  'read_amount'=>一共返回几行记录。用于LIMIT。
	*                                                 0-全部； 
	*                                                 没有设置时，取DEFAULT_PAGE_VOLUME。
	*                                  'page'=> 返回第几页。有page值，就忽略 start 和read_amount; page计数从1开始。
	*                                  'per_page'=>每页记录数量。  
	*                                )                                
	* @return {array}: 返回结果，格式如下：
	*                                                         'result'        =>{int}   总体结果： 0- 失败； 1-成功
	*                                                         'error_code' =>{int}  出错代码：
	*                                                                                           0:没有错误；  
	*                                                                                          (0, 1000]：提供的参数，错误；
	*                                                                                          (2000, OO):数据库错误, 详情参考_DBRead方法的返回值的说明
	*                                                         'records'      =>{array} 读取的结果数组。
    */
	public function read($data){

        //$this->startTime = microtime(true); 
		//self::_addLog('db read start<');
		self::_addLog('db-read START:'.\CONFIG::GET_SPENT_TIME());
		
		//ini return var
		$arrReturn=array();		
		
		
		//para verify
		if( ! is_array($data) || !array_key_exists('select_from',$data)   ){
		    $arrReturn['result']=0;
			$arrReturn['error_code']=1000;
			return $arrReturn;
		}		
		
		
		
		$strSQL=$data['select_from'];		
		$strSQL=str_ireplace($this->_escapeWords,'', $strSQL);
		
		self::_addLog(  $data,'read param:');
		
		if(array_key_exists('page',$data)){//verify page para to construct LIMIT string
			$page=intval($data['page'])>=1? intval($data['page']):1;
			$pageVolume = array_key_exists('per_page',$data) ?  intval($data['per_page']) :static::DEFAULT_PAGE_VOLUME;
			$pageVolume = $pageVolume>0 ? $pageVolume : static::DEFAULT_PAGE_VOLUME;
			$limitPara1 = ($page-1) * $pageVolume;
			$limitPara2= $pageVolume;
		}else{//verify start to construct LIMIT string
			$limitPara1 = array_key_exists('start',$data) && intval($data['start'])>=1 ?  intval($data['start']) -1 :0;
			$limitPara1 = $limitPara1>=0 ? $limitPara1 : 0;			
			$limitPara2 = array_key_exists('read_amount',$data) && is_numeric($data['read_amount']) && intval($data['read_amount'])>=0 ?  intval($data['read_amount']) : static::DEFAULT_PAGE_VOLUME;			
		}
		
		
		//fields list
		$fields=array_key_exists('fields',$data) && is_array($data['fields']) ? $data['fields']: array();

        $fieldsStr=static::parseFieldList($fields);
		
		//$strSQL='SELECT ' . (count($fields)>0 ?  implode(" , ",$fields) : ' * ' ) . ' FROM ' . $strSQL;
		$strSQL="SELECT $fieldsStr FROM  $strSQL";
		$this->_SQL=$strSQL;
		
		if(intval($limitPara2)>0){
		   $strSQL.=" LIMIT $limitPara1 , $limitPara2 ";
		}
		
		
		//db operation:
		return $this->_DBRead($strSQL);	
		
	}//-/
	
	/**
	*  readQ
	*  read quick by simple params
	*  @param {string} selectFromSQL : select from sql.(not include 'select * from')
	*  @param {string} fieldsStr     : fields list string or array of fields. default '*'
	*  @return {array/false}         : false- error in reading db                        
	*                                  Array- result rows in array(include empty array).
	*/
	public function readQ($selectFromSQL, $fieldsStr='*'){

        if(empty($selectFromSQL)){
            return false;
        }
		
		$fieldsStr=static::parseFieldList($fieldsStr);
		
		$strSQL="SELECT  $fieldsStr FROM $selectFromSQL";		
		//db connect varify
        $db=$this->_getDBConnect();
        if(empty($db)) {
			return false;
		}		
		self::_addLog(  $strSQL,'readQ SQL:');
		
		//db operation:			  
		$result=$db->query($strSQL);
		if(mysqli_errno($db)==0 ){//数据库没有出错
		    $data=array();
			
			if(mysqli_num_rows($result)>0){
				
				for($i=0;$i<mysqli_num_rows($result);$i++){
					
					$row=mysqli_fetch_assoc($result);				
					array_push($data, $row);
				}	
				
			}					
			return $data;	
			
		}else{
            $this->_errorMsg='error_no:'.mysqli_errno($db).'|error_msg:'.mysqli_error($db);
			return false; 
		}	
	}

    /**
     * @param array $tbSettings : join table settings. For its format, pls refer to static::generateJoinSQL     *
     * @param  string  $sqlString :  sql string except join and select. includes condition string  and order by and grouping and limit , etc.
     *                                ' where pro_idx>100 order by pro_idx DESC LIMIT 0,100'
     * @return bool/array : false- error;  array- data result list.
     */
	public function readJoin($tbSettings, $sqlString){


        if(empty($tbSettings)){
            $this->_errorMsg='incoming param:tbSettings is empty.';
            return false;
        }

        $sql='';
        $fields=array();

        //generate join sql
        if(static::generateJoinSQL($tbSettings,$sqlString,$sql,$fields)==false){
            $this->_errorMsg='Failed when generating JOIN sql.';
            return false;
        }


        //read db and return result.
        return $this->readQ($sql, $fields);


    }//-/

    /**
     * generateJoinSQL
     * 根据Join表设定生成用于数据库读取的select_from 字符串和字段名数组。
     * @param array $tbSettings : join table settings.
     *               =array(
     *
     *                       //table 1, main table
     *                       array(
     *                            'name'=>'tb_pro',                //  table name,
     *                                                             //  can use sql: (select pro_idx from tb_product where pro_idx>100) as tbPro
     *                            'fields'=>'pro_idx,pro_price',   //  fields to get from final result. do NOT need table name prefix. can use array.     *
     *                       ),
     *
     *                       //table 2, join table
     *                       array(
     *                            'name'=>'tb_pro_detail',         //  table name,
     *                                                             //  can use sql: (select pro_idx from tb_product where pro_idx>100) as tbPro
     *                            'fields'=>'pro_idx,pro_price',   //  fields to get from final result. do NOT need table name prefix. can use array.
     *                            'on'=>'pro_idx',                 //  on field of this table. not need table name prefix.
     *                            'on_prev'=>'tb_pro.pro_idx',     //  on field of left join talbe, must include table name prefix.
     *                            'type'=>'left'                   //  'left' : left join [default]; 'inner', 'right'
     *                       ),
     *
     *                       //table 3, join table
     *                       array(
     *                            'name'=>'tb_pro_ws',               //  table name,
     *                                                               //  can use sql: (select pro_idx from tb_product where pro_idx>100) as tbPro
     *                            'fields'=>'pro_idx,pro_price_ws',  //  fields to get from final result. do NOT need table name prefix. can use array.
     *                            'on'=>'pro_idx',             //  on field of this table. not need table name prefix.
     *                            'on_prev'=>'tb_pro.pro_idx',       //  on field of left join talbe, must include table name prefix.
     *                            'type'=>'left'                   //  'left' : left join [default]; 'inner', 'right'
     *                       ),
     *
     *
     *                     )
     * @param  string  $sqlString  :  sql string except join and select. includes condition string  and order by and grouping and limit , etc.
     *                                ' where pro_idx>100 order by pro_idx DESC LIMIT 0,100'
     * @param string $resultSQL    : result sql (select_from sql, not entire sql.)
     * @param array  $resultFields : result fields list array.
     * @return bool : false- error;  true-ok, detail data get by $resultSQL and $resultFields
     */
    public static function generateJoinSQL($tbSettings, $sqlString, &$resultSQL, &$resultFields){

        if(empty($tbSettings)){

            return false;
        }

        $sql='';

        $fields=array();

        //loop to build join string.
        foreach ($tbSettings as $tbItemSet){

            $tbName=\GF::getValue('name', $tbItemSet);
            $tbFields=\GF::getValue('fields', $tbItemSet);
            $onRight=\GF::getValue('on', $tbItemSet);
            $onLeft=\GF::getValue('on_prev', $tbItemSet);
            $joinType=\GF::getValue('type', $tbItemSet,'LEFT');

            if(empty($tbName) || empty($tbFields)){
                continue;
            }

            //get table name pure.
            $tbNameStrList=explode(' ',trim($tbName));
            $tbNamePure=$tbNameStrList[ count($tbNameStrList)-1];

            //$joinStr= (!empty($onRight) && !empty($onLeft)) || strtoupper($joinType)=='FULL' ?  " $joinType JOIN " : "";
            $joinStr='';
            if(!empty($onRight) && !empty($onLeft)){
                $joinStr=" $joinType JOIN ";
            }else{
                if(strcasecmp($joinType,'FULL')==0){
                    $joinStr=" JOIN ";
                }
            }

            $joinOn=!empty($onRight) && !empty($onLeft) ? " ON  $onLeft=$tbNamePure.$onRight " :"";
            $sql.=" $joinStr $tbName $joinOn ";


            //get fields list, and add table name prefix.
            $curfieldList=array();
            if(is_string($tbFields)){
                $curfieldList=explode(',',trim($tbFields));
            }elseif(is_array($tbFields)){
                $curfieldList=$tbFields;
            }



            if(!empty($curfieldList)){
                foreach ($curfieldList as $curField){
                    $curField=trim($curField);
                    if(strpos($curField,' ')===false){
                        $curField="$tbNamePure.$curField";
                    }
                    array_push($fields, $curField);
                }
            }

        }

        //prepare sqlString: add table name for ambiguous fields.
        $matchRe=preg_match_all('/[a-zA-Z]+[a-zA-Z0-9\_]*(?![a-zA-Z0-9\_])/u',$sqlString, $sqlBlocks);
        if($matchRe!==false || $matchRe>0){



            //get sql fields name list, exclude sql keyword.
            $sqlFields=array();
            $sqlKeyword=array('select','where','and','or','order','by','desc','asc','limit','having');
            foreach ($sqlBlocks[0] as $sqlWord){
                if(in_array(strtolower($sqlWord), $sqlKeyword)){
                    continue;
                }
                array_push($sqlFields, $sqlWord);
            }


            //loop to replace field_name by table_name.field_name
            foreach ($sqlFields as $sqlFieldName){

                foreach ($fields as $fieldName){
                    if( stripos($fieldName, ".$sqlFieldName")>0){
                        $sqlString=preg_replace('/(?<=[^a-zA-Z0-9\.]{1})' . preg_quote($sqlFieldName) . '(?![a-zA-Z0-9\_])/u',$fieldName,$sqlString,1);
                        break;
                    }
                }
            }



        }

        $sql.= ' '.$sqlString;


        $resultSQL=$sql;
        $resultFields=$fields;


        return true;




    }//-/
	
	/**
	*  parseFieldList
	*  @param {string/array} fields : field name list(sep by ',') string or name array.
	*                                 '*' /empty string/null to return '*';
	*  @return {string}  : fields name list sep by ',' in string format. 
	*                      '*' when input is '*'/empty string/null
	*/
	public static function parseFieldList($fields){
		
		if(is_string($fields)){				
			$fields=explode(',',trim($fields));				
		}
		if(!is_array($fields)){
			return '*';
		}	
		
		$re='';
		$s='';
		foreach($fields as $itemStr){
			
			$itemStr=trim(str_replace(',','',$itemStr));
            if(!empty($itemStr)){ //TODO: 详细的正则表达过滤
            //if(preg_match('/^\s*[a-zA-Z_]+[a-zA-Z_0-9\.\(\)\*\s]*$/',$itemStr)>0 ){

				$re.=$s.$itemStr;
				$s=',';
			}			
		}		
		
		if($re=='' || $re=='*'){
			$re='*';
		}
		
		return $re;
		
	}//-/
	
	/**
	*  readLine
	*  read single line of record data.
	*  @param {array/string/null}  fields        : array of field name or fields list string.
	*  @param {string} selectFromSQL : select from sql.(not include 'select fields from')
	*
	*  @return {key-array/bool}      : return single record. 
	*                                  if no record, return empty array.
	*                                  =array(
	*                                         'key_name'=>'value',
	*                                         'key_name'=>'value',
	*                                         ... ...
	*                                         )
	*                                  false- error, failed. 
	*/
	public function readLine($fields, $selectFromSQL){
		
		//para verify
		if( trim($selectFromSQL)==''   ){		    
			return false;
		}
		
		//build sql string		
		$strSQL=$selectFromSQL;		
		$strSQL=str_ireplace($this->_escapeWords,'', $strSQL);		
		
		//fields list
		$fieldList=static::parseFieldList($fields);
		
		$strSQL="SELECT  $fieldList FROM " . $strSQL ;
		
		$strSQL.=" LIMIT 0,1 ";
		//db connect varify
        $db=$this->_getDBConnect();
        if(empty($db)) {
			return false;
		}
		
		self::_addLog(  $strSQL,'readline SQL:');
		
		//db operation:			  
		$result=$db->query($strSQL);
		if(mysqli_errno($db)==0 ){//数据库没有出错
		    $data=array();
			if(mysqli_num_rows($result)>0){					
					$data=mysqli_fetch_assoc($result);					
			}			
			return $data;	
			
		}else{
            $this->_errorMsg='error_no:'.mysqli_errno($db).'|error_msg:'.mysqli_error($db);
			return false; 
		}	
		
		
	}//-/
	
	
	
	/**
	*  readValue
	*  读取指定列的第一个记录
	*  @param {string} fieldName     : field name
	*                                  注意：有join的情况，
	*                                  如果field是用作关联的字段，需要写完整名，即包括表明的完整字段名，
	*                                  例如：tb_pro.pro_idx
	*
	*  @param {string} selectFromSQL : sql string after "select XXX from"
	*                                  e.g.: 'tb_user where user_idx=100'    *                       
	*  @return {mix}                 : 返回结果
	*                                  bool(false): error when read db.
	*                                  string/int : value of read field.
	*                                  NULL       : zero record found in db, no error. 
	*/	
	public function readValue($fieldName, $selectFromSQL ){
		
		
		//para verify
		if( trim($fieldName)=='' || trim($selectFromSQL)==''   ){		    
			return false;
		}
		
		//build sql string		
		$strSQL=$selectFromSQL;		
		$strSQL=str_ireplace($this->_escapeWords,'', $strSQL);		
		$strSQL="SELECT  $fieldName FROM " . $strSQL ;
		
		$strSQL.=" LIMIT 0,1 ";
		//db connect varify
		$db=$this->_getDBConnect();
		if(empty($db)) {
			return false;
		}
		
		//db operation:
		//echo $strSQL;	
        
        self::_addLog($strSQL,'readValue SQL');			

		
		$result=$db->query($strSQL);
		if(mysqli_errno($db)==0 ){//数据库没有出错
		    $fieldValue=NULL;
			if(mysqli_num_rows($result)>0){					
					$row=mysqli_fetch_assoc($result);
					$rowFieldName= preg_replace('/(\S+\s+)|(\s*)/','',$fieldName);
					$rowFieldName= preg_replace('/^\S+\./','',$rowFieldName);								
					$fieldValue=array_key_exists($rowFieldName, $row) ? $row[$rowFieldName] : NULL;				
			}			
			return $fieldValue;	
			
		}else{
		 
			return false; 
		}	
		
		
	}//-/


	/**
	*  readAmount
	*  获取一个查询的记录总数。
	*  @param {array} strSQL : 查询的整体SQL，注意，是查询的sql，不是求总数的sql。
	*                          例如： 'SQL'=> 'select * from tb1 where idx>1'
	*                          注意：有join的情况，不能写select * from，
	*                          需要写索引字段的完整名，即包括表名称加点的完整字段名，
	*                          例如：select tb1.idx from ....
	*                                        )
 	*  @return {mix}         : 返回结果
	*                          bool(false): error when read db.
	*                          int        : total amount of sql records.
	*                       
	*                         
	*/
	public function readAmount($strSQL){
		
		
		//para verify
		if(trim($strSQL)==''){		   
			return false;
		}
		
			
		$strSQL=str_ireplace($this->_escapeWords , '' , $strSQL);		
		$strSQL="select count(*)  as TOTAL_AMOUNT  from ( " . $strSQL . " ) tbResult" . rand(100,10000);

        Log::val('readAmount sql:',$strSQL);
		
		//db connect varify
		$db=$this->_getDBConnect();
		if(empty($db)) {
			$arrReturn['result']=0;
			$arrReturn['error_code']=2001;
			return $arrReturn;
		}
		
		//db operation:
		//echo $strSQL;						  
		$result=$db->query($strSQL);
		if(mysqli_errno($db)==0 ){//数据库没有出错
		    $totalAmount=0;
			if(mysqli_num_rows($result)>0){
				$row=mysqli_fetch_assoc($result);											
				$totalAmount= $row['TOTAL_AMOUNT'];				
			}
			
			
			return $totalAmount;	
			
		}else{//数据库读取出错		 
			 
			return false;
		}		
	}//-/
	
	
	
	/**_DBRead
	* @param {array} strSQL :  拼接好的sql语句。	                            
	* @return {array}: 返回结果，格式如下：
	*                                                         'result'        =>{int}   总体结果： 0- 失败； 1-成功
	*                                                         'error_code' =>{int}  出错代码：
	*                                                                                           0:没有错误；  
	*                                                                                          (0, 1000]：提供的参数，错误；                                                                                         
	*                                                                                          (2000, OO):数据库错误, 详情参考_DBRead方法的返回值的说明
	*                                                         'records'      =>{array} 读取到的记录组成的数组。没有出错，记录为0时，返回空数组。出错时不存在此键值。
	*/
	protected function _DBRead($strSQL){
		
		$arrReturn=array();
		//db connect varify
		$db=$this->_getDBConnect();
		if(empty($db)) {
			$arrReturn['result']=0;
			$arrReturn['error_code']=2001;
			return $arrReturn;
		}
		
		//db operation:
		//echo $strSQL . '<hr/>';	
		self::_addLog($strSQL,'_DBRead SQL');				  
		$result=$db->query($strSQL);
		if(mysqli_errno($db)==0 ){//数据库没有出错
		
		    $records=array();		
			
			if(mysqli_num_rows($result)>0){
				
				for($i=0;$i<mysqli_num_rows($result);$i++){
					
					$row=mysqli_fetch_assoc($result);				
					array_push($records, $row);
				}	
				
			}
			$arrReturn['records']=$records;
			$arrReturn['result']=1;
			$arrReturn['error_code']=0;				
			
			//self::_addLog('db read END>');
			//$this->stopTime = microtime(true); 
			self::_addLog('db-read end:'.\CONFIG::GET_SPENT_TIME());
			
			
			return $arrReturn; //return successfully.
		
		}else{
		 
			 //数据库读取出错，返回错误号（mysqli_errno错误号+2000)
            $this->_errorMsg='error_no:'.mysqli_errno($db).'|error_msg:'.mysqli_error($db);
			$arrReturn['result']=0;
			$arrReturn['error_code']=2000+mysqli_errno($db);
			$arrReturn['msg']='read db error!';
			return $arrReturn;	 
		}		
		
	}//-/
	
	
	

	
	
}//=/DB

