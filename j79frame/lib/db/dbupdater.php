<?php
namespace j79frame\lib\db;
use  j79frame\lib\db\DBConnector;
use  j79frame\lib\util\Log;
use mysqli;




/**
*  DBUpdater
*  数据库更新类
*  主要是针对表的添加/更新/删除等操作。
*  数据库连接中通过DBConnector::connect()自动获取。
*  注：table名，table中的字段定义，table的key字段名称等，为静态变量。设置一次，可以在多个对象里使用，
*  通过setTable方法，或者初始化方法来重置这些设定。
*
*
*	@author: jin rong (rong.king@foxmail.com)
**/

class DBUpdater
{


	public $flagUseOperator=true; //flag to use operator or not in update.

	protected  $_tableName='';//{string} table name
	protected  $_keyField=''; //{string} primary key field name

	/*$_fieldsDef
	 *field definition setting:
	 * e.g.:
	 * _fieldsDef=array(
                            'table'=>table name,
                            'primary_key'=> primary key field name,
                            'fields'=>array(
                                            array(
                                                   'field'=>name of field,
                                                   'type'=>type string of this field, full string from mysql,e.g.: 'int(10) unsigned',
                                                   'key'=>PRI/UNI/empty string , indicate primary key / unique key /normal key
                                             ),
	                                        ... ...

                             )
                         )
    */
	protected  $_fieldsDef=null;


    protected $_dbConnect=null; //db connection. when empty, get connection from DBConnect::connect()

    protected $_errorMsg='';   //error msg.
	
	
	
	protected static $_flagAddLog= true; //whether add log.
	
	/**
	*  _addLog
	*  according to $this->_flagAddLog, add log or not.
	*/
	protected static function _addLog($logStr){
		if(self::$_flagAddLog==true){
		    Log::add($logStr);
		}
	}//-/
	


	/**
    *  __construct
	*  initialize table name and key field.
    */
	public function __construct($tbName='', $keyField=''){
        self::_addLog('db-write START:'.\CONFIG::GET_SPENT_TIME());
		if(trim($tbName)!=''){
		    $this->setTable($tbName);
		}
		if(trim($keyField)!=''){
		    $this->setKeyField($keyField);
		}

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


	/**
	*  setTable
    *  set table name and initialize related vars.
    */
	public function setTable($tbName){

		if(is_string($tbName) && strlen($tbName)>0 && strcasecmp($tbName, $this->_tableName)!=0){
		    $this->_tableName=$tbName;
			$this->_fieldsDef=null;
			$this->_keyField='';
		}


	}//-/
	
	
	/**
	*  getTable
    *  get table name.
    */
	public function getTable(){
		return $this->_tableName;

	}//-/


	/**
	* setKeyField
    * 设置key field的字段名. 如果没有设置，也可以通过_getFieldsDef方法自动获得。
    * @param {string} keyField:  key field name
    *
    */
	public function setKeyField($keyField){
		if(  is_string($keyField) && strlen($keyField)>0 && strcasecmp($keyField, $this->_keyField)!=0  ){
			$this->_keyField=$keyField;
		}
	}//-/



	/**
	*  getKeyField
    *  返回key field的字段名
    *
    *  @return {string/bool}:
    *                                   string : key field name
    *                                   false  :  key field name cannot read
    */
	public function getKeyField(){
		if( ! is_string($this->_keyField) || strlen($this->_keyField)<=0 ){
			//$this->_fieldsDef=$this->_getFieldsDef();
			$this->_getFieldsDef();
		}
		if(is_string($this->_keyField) && strlen($this->_keyField)>0){
			return $this->_keyField;
		}else{
		    return false;
		}
	}//-/
	
	/**
	*  getFieldsDef
	*  return field definition info arrays.
	*/
	public function getFieldsDef(){
	   if( !is_array($this->_fieldsDef) || count($this->_fieldsDef)<=0){
		   
		   $this->_getFieldsDef();
	   }
	   if( !is_array($this->_fieldsDef) || count($this->_fieldsDef)<=0){
		   return NULL;
	   }else{
		   return $this->_fieldsDef;   
	   }
	}//-/
	
	/**
	* setFieldsDef
	* set fields def from outside.
	* @param {key-array} data: fields definition data.
	*                          =array(
	*                                 'field name1'=>array(
	*                                                      'type'=>'varchar',
	*                                                      'key'=>'UNI'
	*                                                      ),
	*                                 'field name2'=>array(
	*                                                      'type'=>'varchar',
	*                                                      'key'=>'UNI'
	*                                                      ),
	*                                 ... ...
	*                           )
	* @return {bool}: true- set success; false- failed.
	*/
	public function setFieldsDef($data){
	    if(is_array($data) && count($data)>0 ){
			
			$this->_fieldsDef=$data;
			return true;
		}
		return false;
		
	}//-/


	/**
    * insert
    * insert data into db, can do on duplicate update operatons.
    *
    * @param data {array} 数据，格式如下=array(
    *                                        'content'   具体内容的数组
    *                                                     =array(
    *                                                            'fieldname1'=> array(
    *                                                                    'value'=> field value,
    *                                                                    'type' => field type string
    *                                                                    'key'  => UNI/PRI/empty. flag for field.
    *                                                                   ),
    *                                                            'fieldname2'=> array(
    *                                                                    'value'=> field value,
    *                                                                    'type  => field type string
    *                                                                    'key'  => UNI/PRI/empty. flag for field.
    *                                                                   ),
    *                                                            ... ...
    *                                                       )
    *
    *
    *                                                       其中，key有PRI/UNI/空 ；PRI 主键； UNI 不可重复字段； 空 其他类型字段。
    *                                                       field, value 必填；  type和key，可选，如果没有，则自动读取数据库表的字段类型定义，予以判断。
    *                                                       数字类字段，支持 '+1' '-1'等自增减。例如： "fieldA"=> "+1" : 数则等于 fieldA=fieldA+1形式。
    *                                        'behavior' =>{array}  : 插入操作的行为规则，只在insert时起作用
    *										                         比如：
    *										                         "on_duplicate"=>"update" 表示，如果有重复的unique field，则update此记录。
    *										                         "on_duplicate"=>"abort" 表示，如果有重复的unique field，则放弃insert （ 默认值）。
    *
    *                                        'condition'=>{array}  : 更新条件相关的关联数组，只在update时起作用。
    *										                         比如：
    *										                         "key"=>"12345" ：只更新关键字段keyField等于12345
    *										                         "SQL"=>"user_level >1": 只更新　user_level 大于1的记录。
    *                                       )
    *
    *  @return {array}: 返回结果，格式如=array(
    *                                       'result'        =>{int}   总体结果： 0- 失败； 1-成功
    *                                       'error_code' =>{int}  出错代码：
    *                                                                   0:没有错误；
    *                                                                   (0, 1000]：提供的参数，错误；
    *                                                                   (1000,2000]：字段名字不存在，或者字段类型定义无法读取;table没有指定1001；
    *                                                                   (2000, OO):数据库错误, 详情参考_DBUpdate方法的返回值的说明
    *
    *                                       'idx'            =>{int}   插入的新记录idx。失败的时候，没有此字段。
    *                                                                  如果操作结果成功(result=1)，但此idx=0，意味着：insert on duplicate update的时候，所有数据都相同，对数据库没有进行任何修改。
    *                                       'affected_amount' =>{int}  更新/删除数据库时，更新/删除的记录总数。
    *                                      )
    */
	public function insert($data){
		
		self::_addLog('insert param:==================');
		self::_addLog($data);

		return $this->_update($data,'insert');

	}//-/

	/**
    *  update
    *  @param data {array}     :  格式与insert相同
    *  @return {array}         :  格式与insert方法的返回值相同。
    */
	public function update($data){
		
		//Log::add($data);

		return $this->_update($data,'update');

	}//-/

	/**
	*  delete
    *  @param data {array}     :  格式与insert相同
    *  @return {array}         :  格式与insert方法的返回值相同。
    */
	public function delete($data){

		return $this->_update($data,'delete');

	}//-/
	
	/**
	*  sql
	*  run sql string directly.
	*  return {key-array} typical db return format 
	*/
	public function sql($sqlStr){
		
		$reError=array('result'=>0);
		
		if(empty(trim($sqlStr))){
			$reError['error_code']=1000;
			$reError['msg']='sql string is empty.';
			return $reError;
		}
		
		$sqlStr=trim($sqlStr);
		
		if( strcasecmp(substr($sqlStr,0,6), 'select')==0){
			$reError['error_code']=999;
			$reError['msg']='can not run select sql string, only allow insert/update/delete';
			return $reError;
		}
		
		$actionType='';
		if( strcasecmp(substr($sqlStr,0,6), 'insert')==0){
			$actionType='insert';
		}
		if( strcasecmp(substr($sqlStr,0,6), 'update')==0){
			$actionType='update';
		}
		if( strcasecmp(substr($sqlStr,0,6), 'delete')==0){
			$actionType='delete';
		}
		
		if($actionType==''){
			$reError['error_code']=800;
			$reError['msg']='sql string is invalid, cannot get action type.';
			return $reError;
		}
		
		
		return $this->_DBUpdate($sqlStr, $actionType);
		
		
	}//-/
	
	
	/**
	*  deleteByIdx
    *  @param  {int/array/string} idx :  int  ->idx to delete. 
	*                                    array->contains list of idx to delete.
	*                                    string->list of idx seperated by ",": e.g.: '1,2,3'
    *  @return {array}                :  格式与insert方法的返回值相同。
    */
	public function deleteByIdx($idx){
		
		$arrReturn=array();
		
		//build matching string according to params (idx)
		$matchStr='';
		
		$curIdx=self::parseIdx($idx);
		
		
		if(is_array($curIdx)){//if idx is array:
		
			$matchStr=implode(',',$curIdx);
			if( trim($matchStr)!=''){
			  $matchStr=' IN (' . $matchStr. ')';
			}else{
				
				return static::_returnFormatter(false,1000,'Error: invalid params when deleting by idx!');
								
				
			}
			
		}else if(is_numeric($curIdx) && intval($curIdx)>0){//if idx is single number
		
			$matchStr=' = ' . intval($idx);
			
		}else{//idx invalid:
			
			
			return static::_returnFormatter(false,1000,'Error: invalid params when deleting by idx!');
			
			
		}
		
		//db connect varify
        $db= $this->_getDBConnect();
        if(empty($db)){
			
			return static::_returnFormatter(false,2001,'Error: db connect is invalid!');
			
			
		}
		
		//db operation:
		$strSQL='DELETE FROM '.$this->getTable().' WHERE '. $this->getKeyField().$matchStr;
		
		self::_addLog('deleteByIdx sql:'.$strSQL);
		
		$db->query($strSQL);
		
		if(mysqli_errno($db)==0 ){//数据库没有出错

			
			$arrReturn['affected_amount']=mysqli_affected_rows($db);
			self::_addLog('db-write [delete by idx] end:'.\CONFIG::GET_SPENT_TIME());			
			return static::_returnFormatter($arrReturn);

		}else{

			 //数据库读取出错，返回错误号（mysqli_errno错误号+2000)
			 
			 return static::_returnFormatter(false,2000+mysqli_errno($db),'Error: failed db operation when deleting by idx!');			 
			
		}
		
		
		

	}//-/
	
	
	
	

	/**
    *  updateCount
    *  @param {string} fieldName    : 更新的字段名
	*  @param {string} increaseNum  : 增加量，必须带符号，+/- 。例如 'number'=>'+1'
	*  @param {string} conditionSQL : 条件限制SQL语句，不带where关键字。
    *  @return {array}              : same as method:update
    */
	public function updateCount($fieldName,$increaseNumStr, $conditionSQL){
		//ini return var
		$arrReturn=array();
		//verify table name.
		if(!is_string($this->_tableName)  || strlen($this->_tableName)<=0){//if table not set, error exit.
			
			
			return static::_returnFormatter(false,1001,'Error: table name is empty!');	
		}
		
		
		//$patternValue='/^[\+,\-,\*,\/]\d+[\.]?\d*$/';
		
		//check param validity:
		if(trim($fieldName)==''  || trim($increaseNumStr)=='' || trim($conditionSQL)==''){
			//format match "+ - * /" with number,
			
			
			return static::_returnFormatter(false,1000,'Error: params is empty!');	
		}
		
		//check increaseNumStr validity:
		if( !static::isOperator($increaseNumStr) && !is_numeric($increaseNumStr)){
			
			
			return static::_returnFormatter(false,999,'Error: invalid increaseNum param!');			
		}
		
		
		$increaseNumStr=!static::isOperator($increaseNumStr) ? '+'.$increaseNumStr : $increaseNumStr;

		
		
		//$arrCut=array(',' , ';' , 'DROP ', 'UPDATE ', 'DELETE ' , 'CREATE ', 'ALTER ', 'RENAME ');
		//$conditionSQL=str_ireplace($arrCut,'', $conditionSQL);
		$strSQL="UPDATE ". $this->_tableName . " SET $fieldName = $fieldName $increaseNumStr WHERE " . $conditionSQL;
		return $this->_DBUpdate($strSQL, 'update');

	}//-/
	
	
	
	
	
	/**
    *  updateField
	*  update single field value
    *  @param {string} fieldName           : 更新的字段名
	*  @param {string} value               : field value. no-adding slashes.
	*  @param {string} conditionSQL        : 条件限制SQL语句，不带where关键字。
	*  @param {string} type                : value type name string, like 'varchar', 'int'. default 'int'
	*  @param {bool}   flagDisableOperator : disable operator like value or not, default=false.
    *  @return {array}                     : same as method:update
    */
	public function updateField($fieldName, $value, $conditionSQL, $type='int',$flagDisableOperator=false, $flagUseBitOperator=false){
		
		//verify table name.
		if(!is_string($this->_tableName)  || strlen($this->_tableName)<=0){//if table not set, error exit.
			
			return static::_returnFormatter(false,1001,'Error: table name is empty!');	
		}
		
		//check param validity:
		if(trim($fieldName)==''  || trim($value)=='' || trim($conditionSQL)=='' || trim($type)==''  ){
			
			return static::_returnFormatter(false,1000,'Error: some params is missing params(field name:'.$fieldName.';value:'.$value.';condtionSQL:'.$conditionSQL.';type:'.$type);
		}		
		
		$value=$this->_getWrappedValue($value,$fieldName,$type, !$flagDisableOperator, $flagUseBitOperator);
		
		
		//$arrCut=array(',' , ';' , 'DROP ', 'UPDATE ', 'DELETE ' , 'CREATE ', 'ALTER ', 'RENAME ');
		//$conditionSQL=str_ireplace($arrCut,'', $conditionSQL);
		$strSQL="UPDATE ". $this->_tableName . " SET $fieldName = $value WHERE " . $conditionSQL;

		return static::_returnFormatter( $this->_DBUpdate($strSQL, 'update'));

	}//-/
	
	/**
	*  updateFieldByIdx
	*  update single field by idx.
	*  @param {int/string} idx : target idx or idx list seperated by ',', something like: '12,13'
	*  @param others pls refer to method[updateField]
	*  @return {key-array}     : typical model return format. same as method:update
	*/
	public function updateFieldByIdx($idx, $fieldName, $value, $type='int'){
		
		$idxSql=\GF::parseIdxToSQL($idx);
		
		if(empty($idxSql)){
			return static::_returnFormatter(false,1000,'Error: invalid params:idx!');		
		}
	    $strCon=$this->_keyField.$idxSql;	

		
		
		
		return static::_returnFormatter( $this->updateField($fieldName, $value, $strCon, $type));
		
	}//-/

	/**
	 * multiUpdate
	 * do multi update.
	 * @param $sqlArr: sql array.
     * @return bool: true-success; false-failed
	 */
	public function multiUpdate($sqlArr){

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
		echo $query;
		echo '<hr/>';
		if ($db->multi_query($query)) {
			do {
				// store first result set
				$result = $db->store_result();
				echo '-----------';
				var_dump($result);
				if ($result) {

					$reData=array();
					$reData['affected_amount']=0;
					if($db->affected_rows()>0) {
						$reData['idx'] = $db->insert_id();
						$reData['affected_amount']=$db->affected_rows();
					}
					array_push($resultTotal, $reData);
				}
				if(!is_bool( $result)){
					$result->free();
				}


			} while ($db->more_results() && $db->next_result());
			return $resultTotal;
		}else{

			echo mysqli_errno($db);
			return false;
		}

	}//-/
	
	

	/**
	*  _update
    *  数据库表的更新/插入/删除操作的具体操作函数。
    *  @param   array  $data       : 格式与insert相同
    *  @param   string $updateMode : 操作的类型：insert/update/delete
    *  @return  array             : 格式与insert方法的返回值相同。
    */
	protected function _update($data, $updateMode='insert'){		
		
		//self::_addLog($data);

		//verify table name.
		if(!is_string($this->_tableName)  || strlen($this->_tableName)<=0){//if table not set, error exit.
			
			
			return static::_returnFormatter(false,1001,'Error: tableName is not valid!');	
		}

		//para(data) verify:
		//  1- content
		if( !array_key_exists('content',$data) || !is_array($data['content']) || count($data['content'])<=0 ){
			if(strcasecmp($updateMode,'delete')!=0){//if insert or update, content should not be empty, error return.   
				
				return static::_returnFormatter(false,1000,'content should not be empty for update/insert!');
				
			}
			$data['content']=array();
		}
		
		
		
		$content=$data['content'];
		
		//Log::val('content', $content);
		
		//  2- behavior
		if(array_key_exists('behavior',$data) && is_array($data['behavior'])){
		    $behavior=$data['behavior'];
		}else{
			$behavior=array('on_duplicate'=>'abort');
		}
		
		//  3- condition		
		if(array_key_exists('condition',$data) && is_array($data['condition'])){
		    $condition=$data['condition'];
		}else{
			$condition=null;
		}
        $strCondition=trim($this->_getConditionString($condition));//get condition string.
		if( ($strCondition===false || $strCondition=='' ) && 
		    (strcasecmp($updateMode,'delete')==0 || strcasecmp($updateMode,'update')==0)){			
			
			return static::_returnFormatter(false,999,'Error: condition string invalid, can not update/delete without condition!');
				
		}
			
		//construct field list and value list strings
		if(strcasecmp($updateMode,'delete')!=0){//if insert or update, then get field and value list string for SQL

			$reList=$this->_getSQLSetList($content,$updateMode);		
			
			if( ! is_array($reList)){//if empty list, then error exit.
				
				return static::_returnFormatter(false,4000+$reList,'Error: failed consturcting sql value list, code:'.$reList);
				
				
			}
			
			$strFieldList=$reList['field_list'];
			$strValueList=$reList['value_list'];
			$strUpdateList=$reList['update_list'];		

		}

		//build SQL string
		if( strcasecmp($updateMode,'insert')==0){//insert operation SQL
			$strSQL='INSERT INTO ' .$this->_tableName. ' (' . $strFieldList . ') VALUES ' . $strValueList ;

			//process for on duplicate settings:		
			if(array_key_exists('on_duplicate',$behavior) && strcasecmp($behavior['on_duplicate'],'update')==0){				
				$strSQL .='  ON DUPLICATE KEY UPDATE ' . $strUpdateList;
			}
		}elseif(strcasecmp($updateMode,'update')==0){//update operation SQL

			$strSQL='UPDATE  ' .$this->_tableName. ' SET  ' . $strUpdateList. ' WHERE ' . $strCondition;		
            

		}elseif(strcasecmp($updateMode,'delete')==0){//delete operation SQL
		    
			$strSQL = 'DELETE FROM '.$this->_tableName.'  WHERE  '.$strCondition;			

		}else{//operation string is not valid:
			
		   
		   
		   return static::_returnFormatter(false,998,'Error: operation mode is not valid for updater!');
		}

		//start DB update

		return static::_returnFormatter($this->_DBUpdate($strSQL, $updateMode));

	}

	/**
	*  _DBUpdate
    *  实际对DB进行操作。
    *  @param string $strSQL        :  sql 语句，已经通过前期拼接完成的sql
    *  @param string $updateMode    :  更新的类型 insert/update/delete;
    *  @return array: =array(
	*                          'result'           =>{int}   总体结果： 0- 失败； 1-成功
    *                          'error_code'       =>{int}  出错代码：
    *                                                 0:没有错误；
    *                                                 (2000, OO):数据库错误, 举例：
	*                                                            2001: db connection failed.
	*                                                            3062: key duplicated.  
	*                                                            (3062-2000=1062 -> 
	*                                                             mysqli error code for "key duplicated".)
	*                           'idx'             =>{int} 插入的新记录idx。失败的时候，没有此字段。
	*                                               如果操作结果成功(result=1)，但此idx=0，意味着：
	*                                               insert on duplicate update的时候，所有数据都相同，对数据库没有进行任何修改。
	*                           'affected_amount' =>{int}  更新/删除数据库时，更新/删除的记录总数。
	*                          )
    *                                                                                 
    *                                                                                               
    *                                                           
    */
	protected function _DBUpdate($strSQL, $updateMode='insert'){
		
		

		//db connect varify
        $db= $this->_getDBConnect();
        if(empty($db)){
			
			
			
			$arrReturn['result']=0;
			$arrReturn['error_code']=2001;
			return $arrReturn;
		}

		//echo $strSQL;
		self::_addLog('update SQL:'.$strSQL);
		$db->query($strSQL);
		if(mysqli_errno($db)==0 ){//数据库没有出错

			$arrReturn['result']=1;
			$arrReturn['error_code']=0;
            self::_addLog('db-write end:'.\CONFIG::GET_SPENT_TIME());			
			if(strcasecmp($updateMode,'insert')==0){//if insert, then get newly inserted idx.
				  if(mysqli_affected_rows($db)>0){
					  $arrReturn['idx']=mysqli_insert_id($db);
				  }else{
					  $arrReturn['idx']=0;//没有一个记录变化：insert duplicate on key udpate的时候，所有数值都相同，则不会更新任何记录。
				  }
			}else{//if update/delete , then get affected row amount.
				 $arrReturn['affected_amount']=mysqli_affected_rows($db);
			}
			return $arrReturn; //return successfully.

		}else{

			 //数据库读取出错，返回错误号（mysqli_errno错误号+2000)
			$arrReturn['result']=0;
			$arrReturn['error_code']=2000+mysqli_errno($db);
			$errDetail='';
			switch(mysqli_errno($db)){
				
				case 1062:
				          $errDetail='Duplicate key!';
						  break;
				case 1406:
				          $errDetail='Data too long!';
						  break;
				
				
			}		
			
			
			$arrReturn['msg']='DB operation error.'.$errDetail;
			return $arrReturn;
		}

	}//-/

	/**
	*  _getConditionString
    *  根据condition数组内容，构建条件语句sql
    *  @param {array} condition: 格式与insert方法的data参数的data['condition']值相同。
    *
    *  @return {string/bool}:
    *                                    string : 返回条件语句SQL，例如：'fieldA<1'  或者 'tb_idx=123'
    *                                     false :  出错。
    */
	protected function _getConditionString($condition){
		
		

		//para verify
		if( ! is_array($condition) ){
			return false;
		}

		$strCondition='';
		$strConditionSep='';

		if(array_key_exists('key',$condition)){//condition 参数带有 key 值： where key_idx=XXX 形式
			$keyFieldValue=$condition['key'];
			$keyFieldName=$this->getKeyField();
			if($keyFieldName===false){// key field can not retrieve, then exit with error
				return false;
			}
			if(is_array($keyFieldValue)){//如果key的值等于数组，即 key_idx IN (100,300,312)类型的条件。
				$keyValueListSep='';
				$keyValueList='';
				for($vIndex=0; $vIndex<count($keyFieldValue); $vIndex++){
					$curValue=$keyFieldValue[$vIndex];
					$curValue=$this->_getWrappedValue($curValue, $keyFieldName);
					if($curValue===false){
					   return false;
					}
					$keyValueList .=$keyValueListSep . $curValue;
					$keyValueListSep=' , ';
				}
				$strCondition .=$strConditionSep . " $keyFieldName IN ($keyValueList) ";


			}else{//如果key的值不是数组:
				$keyFieldValue=$this->_getWrappedValue($keyFieldValue, $keyFieldName);
				if($keyFieldValue===false){
					return false;
				}
				$strCondition .=$strConditionSep . " $keyFieldName=$keyFieldValue ";
			}
			$strConditionSep=' AND ';

		}
		if(array_key_exists('SQL',$condition)){//condition 参数带有 SQL 值： where SQL 形式
			$strConditionSQL=$condition['SQL'];
			//安全性过滤字符
			//$arrCut=array( ';' , 'DROP ', 'UPDATE ', 'DELETE ' , 'CREATE ', 'ALTER ', 'RENAME ');
			//$strConditionSQL=str_ireplace($arrCut,'', $strConditionSQL);
			$strCondition .=$strConditionSep . $strConditionSQL;
			$strConditionSep=' AND ';

		}

		return $strCondition;


	}//-/
	
	
	/**
	*  _getSQLSetList
	*  get sql list string and update string considering the multi value situation.
	*  @param {key-array} contentArr : db update content array
	*  @param {string}    updateMode : 'insert' or 'update';
	*  @return {array/int}:
	*                      int(1000): 提供的数组为空
	*                      int(999) : 提供的数组，数据有问题，value值为数组，但是每个value的数组大小不一。
	*                      int(2000): 字段的类型，获取不到
	*                      int(1999): 字段的key类型（PRI/UNI/空字符串）值，获取不到
	*                      array    : 产生的键值对数组，每项的值为单个。
	*                               (
	*                                'field_list'  => 'fieldname1, fieldname2',
	*                                'value_list'  => '(value1, value2)',
	*                                'update_list' => 'fieldname1=value1, fieldname2=value2'
	*                                )
    *                      array    : 产生的键值对数组，每项的值为数组。
	*                               (
	*                                'field_list'  => 'fieldname1, fieldname2',
	*                                'value_list'  => '(value11, value21),(value12,value22)',
	*                                'update_list' => 'fieldname1=value1, fieldname2=value2',
	*                                'value_array' => array('(value11, value21)' , '(value12,value22)')
	*                                )
	*                       int(1000): error, empty array of contentArr
	*                       int(1001): error, 每条数据中value数组与否不符合
	*                       int(1002): error, 每条数据中value数组个数不符合
	*                       int(1003): error, key field与否，无法判断
	*                       int(1004): error, value无法生成sql字符串，type无法找到。
	* 
	*/
	public function _getSQLSetList($contentArr, $updateMode='insert'){
		//validate contentArr
		if(!is_array($contentArr) || count($contentArr)<=0){
		  return 1000;	
		}		
		
		$len=count($contentArr);
		$valueListArr=NULL;
		$valueListStr='';
		$valueIsArray=false;
		$valueArrLen=0;	
		$strFieldList='';	
		$ValueListUpdateStr='';	
		
		
		//get first element to test:
		foreach ( $contentArr as $key => $curItem){		    
			$strVal=is_array($curItem) && array_key_exists('value', $curItem) ? $curItem['value']:'';
			break;				
		}		
		
		$strVal=is_array($strVal) && count($strVal)==1 ? $strVal[0] : $strVal;//如果是长度等于1的数组
		$strVal=is_array($strVal) && count($strVal)<=0 ? 'null' : $strVal; //如果是长度等于0的数组，设置为'null'
		if(is_array($strVal)){//如果value是个长度大于一的数组:
		    $valueIsArray=true;						
			$valueArrLen=count($strVal);
			
			$valueListArr=array();
			for($j=0;$j<$valueArrLen;$j++){
				array_push($valueListArr, '(');
			}
							
		}else{
		    $valueListStr='(';
			
		}
		
		$sepField='';
		$sepUpdate='';
		$curEnableOperator=false;
		
		$i=0;
		foreach( $contentArr as $curFieldName => $curItem){	
		
		    
			$curType=array_key_exists('type',$curItem) ? $curItem['type']:'';
			$curEnableOperator=isset($curItem['disable_operator']) && $curItem['disable_operator']==true ? false : true;

            $curBitOperator=isset($curItem['bit_operator']) && $curItem['bit_operator']==true ? true : false;



            $strVal=$curItem['value'];


			$strVal=is_array($strVal) && count($strVal)==1 ? trim($strVal[0]) : $strVal;//如果是长度等于1的数组
			$strVal=is_array($strVal) && count($strVal)<=0 ? 'null' : $strVal; //如果是长度等于0的数组，设置为'null'
						
			if( is_array($strVal)!=$valueIsArray){//数组与否不符合，返回false
				return 1001;		
			}
			if($valueIsArray==true && count($strVal)!=$valueArrLen){//数组个数不符合：返回false
				return 1002;		
			}
			
			$strFieldList.=$sepField.$curFieldName;
			
			
			if(strcasecmp($updateMode,'insert')==0){//if insert
			
				$curKey=array_key_exists('key',$curItem) ? $curItem['key']:'';
				$reUnique=$this->_isUniqueField($curKey, $curFieldName);
				if( is_null($reUnique)){//if can not get key value , then error-return.	
				    //echo 'key not found:'.$curFieldName;					
					return 1003;
				}
				if($reUnique===false){//if not unique then add to update item list.
				    
					//check if value is "+1" type.
				    if($valueIsArray==true){
					   	$curValueStr=$strVal[0];
					}else{
						$curValueStr=$strVal;
					}
					if( static::isNumericType($curType) && static::isOperator($curValueStr)){
					    
						$updateValueStr=$this->_getWrappedValue($curValueStr,$curFieldName, $curType,$curEnableOperator,$curBitOperator);
					}else{
						$updateValueStr='VALUES(' . $curFieldName.')';
					}
					
					
					$ValueListUpdateStr .=$sepUpdate . $curFieldName . '=' . $updateValueStr;
					$sepUpdate=' , ';
				}	
				
			}
			
			
			if($valueIsArray==true){//VALUE IS ARRAY:
			    //$sep='';
				for($j=0;$j<$valueArrLen; $j++){
				    //echo $strVal[$j].'<br/>';
					//echo $curFieldName.'<br/>';
				   	$curValue=$this->_getWrappedValue($strVal[$j],$curFieldName,$curType,$curEnableOperator,$curBitOperator);
					
					//echo 'curvalue='.$curValue.'<br/>';
					
					if($curValue===false){//if can not get field type to wrap value, then error-return		
					   return 1004;
					}
										
					$valueListArr[$j].=$sepField.$curValue;
					//$sep=',';
					
					if($j==0 && strcasecmp($updateMode,'update')==0){//if update: update value list只取数组中第一个值来更新。数据库更新只使用value数组中的第一个元素，其他的会忽视。					
						
					    $ValueListUpdateStr .=$sepUpdate . $curFieldName . '=' . $curValue;			
						$sepUpdate=' , ';
					    	
					}									
					
					
					if($i==	$len-1){ //last item, then add closure ')'
						$valueListArr[$j].=')';
					}
					
				}
				
				
			}else{//VALUE  IS NOT ARRAY:
				$curValue=$this->_getWrappedValue($strVal,$curFieldName, $curType,$curEnableOperator,$curBitOperator);
				if($curValue===false){//if can not get field type, then error-return		
				   return 1004;
				}	
				
			   	$valueListStr .=$sepField.$curValue;
				//$sep=',';
				if($i==	$len-1){ //last item, then add closure ')'
					$valueListStr.=')';					
				}
				
				if(strcasecmp($updateMode,'update')==0){//if update:				
				    $ValueListUpdateStr .=$sepUpdate . $curFieldName . '=' . $curValue;
					$sepUpdate=' , ';	
				}
				
				
			}
			
			$sepField=',';
			
			$i++;
			
			
		}//-foreach
		
		$result=array();
		$result['field_list']=$strFieldList;
		$result['update_list']=$ValueListUpdateStr;
		
		if($valueIsArray==true){
			
			$valueListStr=implode(',',$valueListArr);			
		    $result['value_list']=$valueListStr;
			$result['value_array']=$valueListArr;   
			
			
		}else{			
			$result['value_list']=$valueListStr;		    
		}
		
		return $result;
		
	}//-/_getSQLSetList
	





	/**
	*  _getWrappedValue
    *
    *  根据字段的数据类型，产生用于sql数据库读写的value的字符串值。
    *  主要是针对字符串和日期型变量，addslashes转义（防注入功能）， 然后在左右两侧，添加单引号。
    *  如果是int类型，intval来转换；如果是其他数字类型，floatval来转换。
    *  如果是数字类型，同时flagNumOperator=true, 那么，支持'+1'之类的自增减操作：
	*  value=>'+1' ; fieldName=> 'comment_amount'  type=>'int'  : return  'comment_amount+1'
    *  比如：   value=123 ; type='varchar'  => return :  '123'
    *          value=56'78;  type='varchar' => return :  '56\'78'
    *          when type is numberic, support "+ - * /" with number, e.g.: '+1'  or '*5'
    *
    *  @param {string} value           : 欲处理的值的字符串
	*  @param {string} fieldName       : 字段的名称，默认为空。type为空时，必填，根据
	*                                    fieldName，在$this->_fieldsDef(字段定义数组)中，
	*                                    查找对应的字段数据类型type值。
    *  @param {string} type            : 字段类型名称字符串，默认为空，此时，需要读取fieldName参数，
	*                                    根据$this->_fieldsDef(字段定义数组)，
	*                                    找到相应的字段的数据类型。类型判断是靠type值内是否
	*                                    含有int等数字类型名称来判断。结果只有：是数字，不是数字，两种。
	*                                    type的值和mysql的字段定义的值相容。
	*                                    比如：type='bigint(10) UNSIGNED'是允许的，等于'int'
	*  @param {bool}   flagNumOperator : true- support "+ - * /" ; [default]
	*                                    false- ingnore operators.
	*
    *  @return {string/bool}
    *                                  false  : error
    *                                  string : wrapped value string.
    */
	protected  function _getWrappedValue( $value, $fieldName='', $type='', $flagNumOperator=true, $flagBitOperator=false){
        
		//$flagNumOperator=$this->flagUseOperator;
		//Log::val('get wrap value value',$value);
		//Log::val('get wrap value field',$fieldName);
		//Log::val('get wrap value flagNumOperator',($flagNumOperator==true ? 'true':'false'));

		$curTypeStr=trim($type);

		if($curTypeStr=='' && $fieldName!=''){//if type string is not given, then read $this->_fieldsDef.

			//if _fieldsDef empty, then get def by calling getTableDefArray();
			if(! is_array($this->_fieldsDef) || count($this->_fieldsDef)<=0){
				    $this->_getFieldsDef();
					
			}
			if(is_array($this->_fieldsDef) && count($this->_fieldsDef)>0){
				
				
				if(array_key_exists($fieldName,$this->_fieldsDef) && is_array($this->_fieldsDef[$fieldName]) && array_key_exists('type',$this->_fieldsDef[$fieldName])){					
					$curTypeStr=$this->_fieldsDef[$fieldName]['type'];				   	
				}else{
					$curTypeStr='';
				}	
				
				
				
			}

		}

		if($curTypeStr=='')
		    return false;


		return static::getWrappedValue($value,$curTypeStr,$fieldName,$flagNumOperator,$flagBitOperator);



	}//-/
	
	/**
	*  getWrappedValue 
	*  [static]
	*  get wrapped value string by given type.
	*  when type is not number, then addslashes and add ' outside.
	*                           e.g.: value=123 ; type='varchar'  => return :  '123'
	*  when type is number:     if integer then user intval to transfer value, 
	*                           else use floatval to transefer.
	*                           support "+ - * /" with number, e.g.: '+1'  or '*5'
	*                           e.g.: value=>'+1' ; fieldName=> 'comment_amount'  type=>'int'  
	*                           return: comment_amount+1
	*                
    *  
    *
    *  @param {string} value     : value to transfer
    *  @param {string} type      : type of value, default 'int', 
	*                              can use mysql type definition string directly
	*                              e.g.: 比如：type='bigint(10) UNSIGNED'
	*  @param {string} fieldName : field name used in '+1' like value to 
	*                              construct result like 'tb_count+1' result.
	*                              default is empty string.
	*  @param {bool}   flagNumOp : true- support "+ - * /"[default] ; 
	*                              false- ingnore operators.
    *
    *  @return {string/bool}     :
    *                              false   : error
    *                              string : wrapped value string.
	*/
	public static function getWrappedValue($value, $type, $fieldName='', $flagNumOp=true, $flagBitOp=false){
		
	
		$type=trim($type)==''? 'int' : strtolower(trim($type));//if not set type then use as integer.
		
		//if value is null or 'null','NULL', then return string null (widthout ' around it )		
		if(is_null($value) or strtolower(trim($value))=='null'){
		   return 'null';	
		}
			
		//match pattern to identify numberic value.		
		
		if( static::isNumericType($type)){// if numberic value    
			$value=trim((string)$value);		    

            //Log::val('getWrap flag Bit',$flagBitOp );

            if($flagBitOp) {//if bit operation, then set "field=field | value " value.
                if( intval($value)<0){ // <0 , delete bit data
                    return $fieldName . ' ^ ' . abs(intval($value));
                }else if(intval($value)>0){// >0, add bit data.
                    return $fieldName . ' | ' . intval($value);
                }else{ //equal 0
                    return '0';
                }
            }else if($flagNumOp==true && static::isOperator($value)){//if has "+ - * /" at front and operation is enable.
                $preChar=substr($value,0,1);
                $pureNum=\GF::parseInt(substr($value,1));
                return $fieldName . $preChar . $pureNum;
            }

            //else return normal number value.
			if(stripos($type, 'int')!==false){				
				return \GF::parseInt($value);
			}else{
				return floatval($value);
			}

		}else{//else if string type of value, then addslashes, and add ' around the value.
			$value=addslashes($value);
			return "'" . $value . "'";
		}
			
	}//-/
	
	
	/**
	*  isNumericType
	*  check if numeric type by typename.
	*  e.g.:  'unsigned int' => true;  'double' => true; 'varchar'=> false;
	*  @param  {string} typeName : type name string, like 'unsigned int', same as mySQL data type name.
	*  @return {bool} : true- is value of "+1" type; false- not.
	*
	*/
	public static function isNumericType($typeName){
		
		if(// if numberic value
		    stripos($typeName,'int')!==false  ||
			stripos($typeName,'float')!==false  ||
			stripos($typeName,'double')!==false  ||
			stripos($typeName,'decimal')!==false  ||
			stripos($typeName,'numberic')!==false  ||
			stripos($typeName,'real')!==false  			
		
		   ){
			  return true;   
		 }
		return false;
	}//-/
	
	/**
	*  isOperator
	*  check value if the type of "+1" value.
	*  @return {bool} : true- is value of "+1" type; false- not.
	*/
	public static function isOperator($value){
		$value=trim((string)$value);
		$preChar=substr($value,0,1);
		if(in_array( $preChar, array('+','-','/','*'))){
			$pureNum=\GF::parseInt(substr($value,1));
			if($pureNum>0){
			  return true;	
			}
		}		
		return false;
	}//-/
	

	
	/**
	*  setFieldValue
	*  [static]
	*  set field value into field info array.
	*  
	*  @pram {string}  fieldName     : name of field to set.
	*  @pram {string}  fieldValue    : current value to set.
	*  @param {array}  fieldListArray: array of field info list
	*                                 =array(
	*           
	*			                             'fieldname'=>    //field name as key name.
	*                                                 array(
	*			                                  
	*			                                        'type'  => type string of this field, 
	*			                                                   can set full string from mysql,
	*                                                              e.g.: 'int(10) unsigned',
	*			                                        'key'   => PRI/UNI/empty string , 
    *			                                                   indicates primary key / 
	*                                                              unique key /normal key
	*			                                        'value' => field value
	*			                                      ), 
	*			                             ... ...
    *			                            )
	*  
	*  @return {bool}:   false -- error ; true -- success                          
	*/
	public static function setFieldValue(&$fieldListArray, $fieldName, $fieldValue ){
		
		if(!is_array( $fieldListArray) || count($fieldListArray)<=0){
			return false;	
		}
		
		if(!array_key_exists($fieldName, $fieldListArray)){//if field not exists, then return false;			
		    return false;	
		}
		$fieldListArray[$fieldName]['value']=$fieldValue;
		return true;
		
	}//-/


	/**_isUniqueField
    * 	判断是否为unique字段
    *  如果，第一个参数strKey不为空，根据strKey来判断是否为unique字段：  'UNI' => 是 ；'PRI'=>是；   其他 ,不是。
    *  如果，第一个参数strKey为空，那根据第二个参数fieldName，在$this->_fieldsDef中，查找key值。
    *  @param {string} strKey      :  key值， 'UNI' => 是 ；'PRI'=>是；   其他 ,不是。默认值为空，此时，根据fieldName从$this->_fieldsDef中，查找key值
    *  @param {string} fieldName :  字段名称，如果strKey不为空，忽略此项。默认值为空。
    *
    *  @return {int/bool}  null       :  出错，无法取得key值。
    *                                 true      :  是unique
    *                                 false     :  不是unique
    */
	public function _isUniqueField($strKeyValue='', $fieldName=''){

		$keyString='';
        $strKeyValue=trim((string)$strKeyValue); 
		$fieldName=trim((string)$fieldName);
		if($fieldName=='' && !isset($strKeyValue[2])){
		  return NULL;	
		}
		if(is_string($strKeyValue) && isset($strKeyValue[2])){
			$keyString=$strKeyValue;
		}else{
			//if _fieldsDef empty, then get def by calling _getFieldsDef();			
			if(! is_array($this->_fieldsDef) || count($this->_fieldsDef)<=0){
				    
					$this->_getFieldsDef();
					
			}
			
			
			if(is_array($this->_fieldsDef) && count($this->_fieldsDef)>0){
				
				if(array_key_exists($fieldName,$this->_fieldsDef)){
					
					$keyString= array_key_exists('key', $this->_fieldsDef[$fieldName])?  $this->_fieldsDef[$fieldName]['key']:'';
				   	
				}else{
					return null;
				}				
				
			}else{
				return null;
			}

		}

		//match pattern to identify unique type.
		
		if( stripos( $keyString,'PRI')!==false || stripos( $keyString,'UNI')!==false ){// if unique type
			return true;
		}else{//else if  not unique, return false;
			return false;
		}

	}//-/



	/**_getFieldsDef
    * 取得数据库中指定表的字段定义。
    *
	*  @param {string} tbName : table name, default empty, then get from $this->_tableName
    *  @return mix:
    *						false:   出错
    *                      array:  字段定义的数组,每个元素为一个关联数组, =array(
    *                             '字段名称1'=> (
    *                                     
    *                                            'type'=>字段类型，完整mysql字符串，比如'int(10) unsigned',
    *                                            'key'=>主键/唯一键表示PRI/UNI , 一般类型为空
    *                                            ),
    *                             '字段名称2'=> (
    *                                     
    *                                            'type'=>字段类型，完整mysql字符串，比如'int(10) unsigned',
    *                                            'key'=>主键/唯一键表示PRI/UNI , 一般类型为空
    *                                            ),
	*                              ... ...
    */
	protected function _getFieldsDef($tbName=''){

		//db connect varify
		$db=DBConnector::connect();
		if($db===false) return false;

		//table name varify: _table
		$tbName=trim((string)$tbName);
		if(strlen($tbName)<=0){
		   $tbName=$this->_tableName;
		}
		if(strlen($tbName)<=0){
			return false;
		}
		

		//return var ini
		$defInfoList=array();

		//read column definiation
	   $query='show columns from '.$tbName;
       $result= $db->query($query);
	   if(mysqli_errno($db)!=0 || mysqli_num_rows($result)<=0){
		   return false;
	   }
	   for($i=0;$i<mysqli_num_rows($result);$i++){
		   $row=mysqli_fetch_assoc($result);



		   if( stripos((string)$row['Key'], 'PRI')!==false){//设置主键
			 	$this->_keyField=(string)$row['Field'];
		   }
		   
		   $defInfoList[ $row['Field']]=array(
		                                     'type'=> (string)$row['Type'],
											 'key' => (string)$row['Key'],
                                             'extra'=>(string)$row['Extra']
		                                     );

		   //array_push($re, $arrContent);

	   }
	   if(count($defInfoList)>0){
		  $this->_fieldsDef=$defInfoList; 
	   }
	   
	   return $defInfoList;


	}//-/


	/**
	*  parseIdx
	*  read idx param, and try to parse it into idx or idx list.
	*  return int or array or false when failed.
	*
	*  @param {mix}  idx  :  idx to parse, 
	*                        -can be int single number
	*                        -can be array
	*                        -can be string contains numbers seperate by ","
	*  @return {int/array/bool} : false-- failed in parsing , idx is not valid.
	*                             int  -- single idx number
	*                             array-- array of idx, length >0                 
	*/
	public static function parseIdx($idx){
		
		
		
		if(is_array($idx)){//if idx is array:
		    $result=array();			
			for($i=0;$i<count($idx);$i++){				
				if(is_numeric($idx[$i])){
					array_push($result,intval($idx[$i]));
				}
			}
			if(count($result)>0){
				return $result;	
			}			
			
		}else if(is_numeric($idx) && intval($idx)>0){//if idx is single number		
			return intval($idx);
			
		}else{
			$arrIdx=explode(',',$idx);
			if($arrIdx!==false){//if idx contains number seperate by ",": like '1,2,3,4'
			    $result=array();			
				for($i=0;$i<count($arrIdx);$i++){				
					if(is_numeric($arrIdx[$i])){
						array_push($result,intval($arrIdx[$i]));
					}
				}
				if(count($result)>0){
					return $result;	
				}			
			}
		}
		return false;
		
		
	}//-/
	
	
	/**
	*  addFieldItem
	*  add field name, type, value ,key-type to arrayContent.
	*  always add as none-duplicated item. 
	*  if exists item with same field name, 
	*  then delete old one  and set current data.	
	*  after done,$arrayContent will be ready for dbupdater. it will look like:
	*                           =array(
	*                                  'pro_name'=>array(
	*                                                     'type' => 'varchar',
	*                                                     'value'=> 'DELL U2401f Monitor',
	*                                                     'key'  => ''
	*                                                   ),
	*                                  'pro_code'=>array(
	*                                                     'type' => 'varchar',
	*                                                     'value'=> 'U2710f',
	*                                                     'key'  => 'UNI',	
	*                                                   ),
	*                                  ... ...
	*                                 )
	*                                        
	* 
	*
	*  @param {array}   arrayContent : array to contain all field data. 
	*                                  NOTICE: its pass by reference. its original data will be changed. 
	*  @param {string}        field  : field name
	*  @param {string/array}  value  : field value: if NULL, then do not add 'value' key
	*  @param {string}        type   : type of field, 'int' /'varchar'. 
	*                                  only differ into 2 types: number/string
	*  @param {string}        key    : field key type=PRI/UNI/empty -> primarykey / unique key/ normal key
	*  @param {bool}  flagDisableOperator : whether disable operator like value or not.
	*                                       default=false, enable operator like  value, e.g.: '+2'
	*  
	*  @return {bool} : false --failed;  true -- success.
	*/
	public static function addFieldItem(&$arrayContent, $field, $value, $type,$key='',$flagDisableOperator=false, $flagBitOperation=false){
	  return static::_addFieldItem($arrayContent, $field, $value, $type,$key,false,$flagDisableOperator,$flagBitOperation);
	}//-/addFieldItem
	
	
	/**
	*  addFieldItemMulti
	*  add field name, type, value ,key-type to arrayContent.
	*  if already exists item with same field-name, then add value into array.	
	*  e.g.: 	
	*       original $arr has fieldA with value=1, 
	*       addFieldItem($arr,'fieldA', 2,'int','',true) =>  
	*       then $arr data: fieldA will have value =array(1,2)
	*  after done,$arrayContent will be ready for dbupdater. it will look like:
	*                           =array(
	*                                  'pro_name'=>array(
	*                                                     'type' => 'varchar',
	*                                                     'value'=> array('proAAA', 'proBBB')
	*                                                     'key'  => ''
	*                                                   ),
	*                                  'pro_code'=>array(
	*                                                     'type' => 'varchar',
	*                                                     'value'=> array('U2710f','T2700f'),
	*                                                     'key'  => 'UNI',	
	*                                                   ),
	*                                  ... ...
	*                                 )
	*                                        
	* 
	*
	*  @param {array}   arrayContent : array to contain all field data. 
	*                                  NOTICE: its pass by reference. its original data will be changed. 
	*  @param {string}        field  : field name
	*  @param {string/array}  value  : field value: if NULL, then do not add 'value' key
	*  @param {string}        type   : type of field, 'int' /'varchar'. 
	*                                  only differ into 2 types: number/string
	*  @param {string}        key    : field key type=PRI/UNI/empty -> primarykey / unique key/ normal key
	*  @param {bool}  flagDisableOperator : whether disable operator like value or not.
	*                                       default=false, enable operator like  value, e.g.: '+2'
	*  
	*  @return {bool} : false --failed;  true -- success.
	*/
	public static function addFieldItemMulti(&$arrayContent, $field, $value, $type,$key='',$flagDisableOperator=false){
	  return static::_addFieldItem($arrayContent, $field, $value, $type,$key,true,$flagDisableOperator);	
	}//-/addFieldItemMulti
	
	
	/**
	*  _addFieldItem
	*  add field name, type, value ,key-type to arrayContent.
	*  if $flagDupAsArray=true, add value of same field name as array.
	*  e.g.: 	
	*       original $arr has fieldA with value=1, 
	*       addFieldItem($arr,'fieldA', 2,'int','',true) =>  
	*       then $arr data: fieldA will have value =array(1,2)
	*          
	*
	*  after done,$arrayContent will be ready for dbupdater. it will look like:
	*                           =array(
	*                                  'pro_name'=>array(
	*                                                     'type' => 'varchar',
	*                                                     'value'=> 'DELL U2401f Monitor',
	*                                                     'key'  => ''
	*                                                   ),
	*                                  'pro_code'=>array(
	*                                                     'type' => 'varchar',
	*                                                     'value'=> 'U2710f',
	*                                                     'key'  => 'UNI',	*
	*                                                   ),
	*                                  ... ...
	*                                 )
	*                                        
	* 
	*
	*  @param {array}       arrayContent : array to contain all field data.
	*                                      NOTICE: its pass by reference. its original data will be changed.
	*  @param string        field        : field name
	*  @param string/array  value        : field value: if NULL, then do not add 'value' key
	*  @param string        type         : type of field, 'int' /'varchar'.
	*                                       only differ into 2 types: number/string
	*  @param string        key          : field key type=PRI/UNI/empty -> primarykey / unique key/ normal key
	*  @param bool          flagDupAsArray      : add duplicate field data as array or not. default=false;
	*                                             if this flag=false, when add duplicate field,
	*                                             it will delete old field value.
	*  @param bool          flagDisableOperator : whether disable operator-like value or not.
	*                                             default=false, enable operator-like  value, e.g.: '+2'
	*  
	*  @return bool : false --failed;  true -- success.
	*/
	protected static function _addFieldItem(&$arrayContent, $field, $value, $type,$key='',       
	                                     $flagDupAsArray=false, $flagDisableOperator=false, $flagBitOperation=false){

		//Log::val('flag dis operator',( $flagDisableOperator? 1:0));
		//Log::val('value', $value);
		//Log::val('field', $field);

		//if value is null, then exit.
		if(is_null($value)){
		  return false;	
		}
		
		//format key & type var
		$key=trim($key);
		$key=stripos($key,'PRI')!==false ? 'PRI':$key;
		$key=stripos($key,'UNI')!==false ? 'UNI':$key;
		
		$type=strtolower($type);
		
		
		if(is_array($arrayContent) && array_key_exists($field,$arrayContent)){//field already exists:
		
		    if($flagDupAsArray==false){//不允许重复插入，则删除旧值，重新添加新值。
			    //unset old data
			    unset($arrayContent[$field]);			  
				//add new.		   
			    $arrayContent[$field]=array(
											 'value'=>$value,
											 'type' =>strtolower($type),
											 'key'  =>$key,
											 'disable_operator'=>$flagDisableOperator,
                                             'bit_operator'=>$flagBitOperation
											 );
			    
			   
			}else{//允许重复插入，值加入为数组			   
			  
				  
				if(array_key_exists('value',$arrayContent[$field])){//exist value in this field:
					
					if(is_array($arrayContent[$field]['value'])){//if value is array:
						if(is_array($value)){							
							$arrayContent[$field]['value']=array_merge($arrayContent[$field]['value'], $value);
						}else{
						    array_push($arrayContent[$field]['value'], $value);
						}
						
					}else{//if value is not array:
					    $oldValue=$arrayContent[$field]['value'];
						
					    if(is_array($value)){
							$tmpArr=array($oldValue);
							$arrayContent[$field]['value']=array_merge($tmpArr, $value);
							
						}else{						
						    $arrayContent[$field]['value']=array($oldValue, $value);
						}
					}				
					
				}else{//if value does not exist in this field:
				   $arrayContent[$field]['value']=(string)$value; 
					
				}	
				
			}
		
		
		
		}else{//field not exists:
			
			//add new.		   
			$arrayContent[$field]=array(
										 'value'=>$value,
										 'type' =>strtolower($type),
										 'key'  =>$key,
										 'disable_operator'=>$flagDisableOperator,
                                         'bit_operator'=>$flagBitOperation
										);
			
		}


		//Log::val('addField result',$arrayContent);
		return true;
		
		
		
	}//-/


    /**
     * _returnFormatter
     * return formatter for dbupdater.
     * @param mixed $result : true  - successful; false - failed;
     *                        assoc - success width detail returning data.
     *                        when assoc, check if it has 'result' key,
     *                                    if it has then use it as result .
     *                                    if not has 'result', consider as success, give 'result'=1.
     * @param int $errorCode
     * @param string $msg
     * @return array
     */
	protected static function _returnFormatter($result, $errorCode=0, $msg=''){
		
		$re=array();
		
		if(! is_array($result)){
			
			   $re['result']    = $result===true ? 1 : 0;
			   $re['error_code']= $result===true ? 0 : intval($errorCode);
			   $re['msg']=$msg;  			
		}else{			   
			   
			   $result['result']=isset($result['result']) ? intval($result['result']):1 ;
			   $result['error_code']=isset($result['error_code']) ? intval($result['error_code']) :0;
		       $re=$result;	
		}
		return $re;
		
	}//-/
	
	
	
	/**
	*  __destruct
    *  close db
    */
	public function __destruct ()
	{
		//if (! is_null($this->dbConnect)) {
		//	mysqli_close($this->dbConnect);
		//}

	}//-/
	



}//=/DB
