<?php
namespace j79frame\lib\db;
use j79frame\lib\core\j79obj;
use j79frame\lib\db\DBConnector;
use j79frame\lib\model\Model;
use j79frame\lib\controller\DataFormat;



/**
 * DBUnitGenerator
 * class
 * 根据对象设计，产生数据库表生成与数据更新php代码，以及前台输入表单UI
 *
 * @package    j79frame\app\model
 * @author     jin.rong <rong.king@foxmail.com>
 *
 */
class DBUnitGenerator extends j79obj
{


    protected $_settings = array();  //main setting data

    protected $_name=''; //current unit name.auto-get from settings.
    protected $_idName='';//current unit id name.
    protected $_mainTable = ''; //main table name, auto-get from settings.

    protected $_attributes =array(); //attributes list array. auto-get from settings.

    protected $_fieldSettings=array(); //main tb field settings.



    protected $_errorCode=0;   // error code transfer inner class.

    protected $_ACTION_TYPES=array('list', 'detail', 'insert', 'update', 'patch','delete');  //all action type string array.

    protected $_IMG_FILENAME_LEN=24; //img filename length when saving into mySql db.

    protected $_settingFilePath="\\j79frame\\app\\settings\\db\\"; // setting file path.

    protected $_settingFileExt="json"; //setting file extension name.


    //type of operation:
    const MODIFY_TYPE_INSERT=1;
    const MODIFY_TYPE_UPDATE=2;
    const MODIFY_TYPE_PATCH=3;
    const MODIFY_TYPE_DELETE=4;






    /**
     * __construct
     * DBUnit constructor.
     * @param null/array/string $settings :  array  - then use $settings as whole setting data for this DBUnit.
     *                                       string - then use $settings as object name, and load from file.
     *
     *
     */
    public function __construct($settings = NULL)
    {

        if (!is_null($settings) && !empty($settings)) {

            //get vars from settings:
            $this->iniSetting($settings);

        }

    }//-/


    /**
     * getSetting
     * get current setting-data.
     * @return array
     */
    public function getSetting(){
        return $this->_settings;
    }//-/



    /**
     * iniSetting
     * get setting-data and initialize vars.
     * @param array $settings: setting-data.
     * @return bool: true-ok; false- error.
     */
    public function iniSetting($settings){


        //verify settings data and set to $this->_settings.
        if(is_string($settings)){//if string, then get setting-data from name.
            //$this->_settings=json_decode($settings,true);
            $this->_loadSetting($settings,false);

        }else if( \GF::isAssoc($settings)){
            $this->_settings = $settings;
        }else{
            return false;
        }

        if(empty($this->_settings)){
           return false;
        }


        //get unit name and table
        $this->_name= \GF::getKeyValue('name', $this->_settings);
        $this->_mainTable=\GF::getKeyValue('table',$this->_settings);

        //get attributes list array
        $this->_attributes=\GF::getKeyValue('attributes', $this->_settings);

        //get id name.
        if(count($this->_attributes)>0){

            foreach($this->_attributes as $attrName=>$attrSet){
                if($attrSet['type']=='ID'){
                    $this->_idName=$attrName;
                    break;
                }
            }
        }

        //get field settings:
        $this->_fieldSettings=$this->_parseFieldSettings($this->_attributes);


    }//-/

    /**
     * generate
     * start generate by $this->_settings data.     *
     * @param $params
     * @return bool: true-success; false-failed.
     */
    public function generate($params=null){



        if(count($this->_settings)>0){

            //clear error
            $this->errClear();


            //create/modify DB table
            if(!$this->_updateTable($params)){
                /*$this->errPush(self::ERROR_FAILED_OPERATION,"update table failed.");
                return false;*/
                return true;
                //return DataFormat::ModelResultFormatter(false,self::ERROR_FAILED_OPERATION,'update table failed.');
            }else{
                return false;
                //return DataFormat::ModelResultFormatter(true,0,'Success-generating tables.');
            }
        }

    }//-/


    /**
     * _updateTable
     * @param $params
     * @return bool
     */
    protected function _updateTable($params=null){

        $dbc=DBConnector::connect();

        //check table existence:
        $table=$this->_mainTable;
        $sql="select TABLE_NAME from INFORMATION_SCHEMA.TABLES where TABLE_NAME='$table';";
        $result = $dbc->query($sql);

        if ($result) {

            $actionType='create';
            $sqlCode='';
            if($result->num_rows>0){//table exists:
                $actionType='alter';
                $sqlCode=$this->_generateDBAlterSQL();
            }else{//not exists:
                $sqlCode=$this->_generateDBCreateSQL();
            }


            self::log("db modification sql", $sqlCode);

            /*echo "sql code:$sqlCode";
            echo "----";*/
            /*return array(
              'data'=>$sqlCode
            );*/

            //return $sqlCode;


            $result->close();


            if(empty($sqlCode)){
                $this->errPush(self::WARNING_NO_OPERATION,"Warning: No operation for table altering.");
                return true;
            }

            //update table in db.
            $resultUpdateTable=$dbc->query($sqlCode);


            if(!$resultUpdateTable){
                $this->errPush(self::ERROR_FAILED_DB_OPERATION,"failed table [$actionType] operation in db.");
                return false;
            }

            return true;


        }else{

            $this->errPush(self::ERROR_FAILED_DB_OPERATION+1,"db read failed when checking table existance.");
            return false;
        }

    }//-/

    /**
     * _createTable
     * create table by settings.
     * @param null $tbName : if not provided, then use $this->_mainTable
     * @param null $fieldSettings: if not provided, then use $this->_fieldSettings.
     * @param null $params: detail params for potential usage.
     * @return bool : true- success; false -error, detail error info, can refer to self::errPop
     */
    protected function _createTable($tbName=null,$fieldSettings=null,$params=null){


        $sql=$this->_generateDBCreateSQL($tbName,$fieldSettings,$params);

        if($sql===false){
            $this->errPush(self::ERROR_FAILED_OPERATION,"Failed generating table-creation mysql script.");
            return false;
        }

        $dbc=DBConnector::connect();
        if(is_null($dbc)){
            $this->errPush(self::ERROR_FAILED_DB_OPERATION,"Failed opening DB.");
            return false;
        }

        //update table in db.
        $resultUpdateTable=$dbc->query($sql);

        if(!$resultUpdateTable){
            $this->errPush(self::ERROR_FAILED_DB_OPERATION,"failed table creation operation in db.");
        }

        return $resultUpdateTable;

    }//-/

    /**
     * _generateDBCreateSQL
     * generate db creation mysql script string.
     * @param $params
     * @return string: mysql scripting for creating table.
     */
    protected function _generateDBCreateSQL($tbName=null,$fieldSettings=null,$params=null){

        $tbName=empty(trim($tbName)) ? $this->_mainTable: $tbName;

        $fieldSettings=empty($fieldSettings)? $this->_fieldSettings: $fieldSettings;


        $result="CREATE TABLE IF NOT EXISTS `$tbName` (".PHP_EOL;


        $uniqKey='';
        $indexKey='';
        $primKey='';
        $itemSql='';

        foreach ($fieldSettings as $dbName=>$fieldSet){


            list($curItemSql,$curKeyStr,$curKeyType)=static::getFieldSQL($dbName, $fieldSet);
            if(!empty($curItemSql)){
                $itemSql.=$curItemSql.",".PHP_EOL;

                if(!empty($curKeyStr)){

                    switch(strtoupper($curKeyType)){
                        case 'PRI':
                            $primKey=$curKeyStr;
                            break;
                        case 'UNI':
                            $uniqKey.=','.PHP_EOL.$curKeyStr;
                            break;
                        case 'MUL':
                            $indexKey.=','.PHP_EOL.$curKeyStr;
                            break;
                    }
                }
                /*if(!empty($curUniStr)){
                    $uniqKey.=','.PHP_EOL.$curUniStr;
                }
                if(!empty($curIndexStr)){
                    $indexKey.=','.PHP_EOL.$curIndexStr;
                }*/
            }

        }


        if(empty($primKey)){
            $this->errPush(self::ERROR_INVALID_PARAM,"No primary key settings.");
            return false;
        }

        $result.=$itemSql.$primKey.$uniqKey.$indexKey.PHP_EOL;
        $result.=") ENGINE=InnoDB  DEFAULT CHARSET=utf8";
        return $result;



    }//-/


    /**
     * _loadSetting
     * load setting from file.
     * @param string $objectName: object name, like "product".
     * @param bool $flagInitializeVar: true[default]- after loading setting-data, do var initialization.
     *                                 false - after loading, do nothing.
     * @return bool
     */
    protected function _loadSetting($objectName, $flagInitializeVar=true){

        $filename=$_SERVER['DOCUMENT_ROOT'].$this->_settingFilePath."$objectName.$this->_settingFileExt";
        $contents='';
        try{
            $errLvl=error_reporting();
            error_reporting(0);
            $handle = fopen($filename, "r");
            error_reporting($errLvl);
            if($handle!==false){
                $contents = fread($handle, filesize ($filename));
            }else{
                $this->errPush(self::ERROR_FAILED_FILE_OPERATION, 'Failed opening setting file.');
                return false;
            }
        }catch(Exception $e){
            $this->errPush(self::ERROR_FAILED_FILE_OPERATION, 'Failed loading setting files by name'.$objectName);
            return false;
        }

        //var_dump($contents);

        $settings=json_decode($contents,true);

        if(empty($settings)){
            $this->errPush(self::ERROR_INVALID_DATA, 'Empty setting data from file.');
            return false;
        }

        $this->_settings=$settings;


        if($flagInitializeVar){
            $this->iniSetting($settings);
        }
        return true;

    }//-/





    /**
     * _generateDBAlterSQL
     * generate db altering mysql script string.
     * @param $params
     * @return string: db-table altering sql script string.
     */
    protected function _generateDBAlterSQL($params=null){


        //get existing table fields setting:----------------
        $curFSet=$this->_parseFieldSettings();

        //var_dump($curFSet);

        //read db and get fields setting:-----------
        $dbFSet=$this->_readDBFieldSettings($this->_mainTable);

        //echo '<hr/>-------------------------------';

        //var_dump($dbFSet);

        $sql=static::cmpFieldSettings($dbFSet,$curFSet,$this->_mainTable);

       /* $sql=array(
            'cur'=>$curFSet,
            'db'=>$dbFSet
        );*/

        return $sql;


    }//-/

    /**
     * cmpFieldSettings
     * compare field settings and return modifying sql strings.
     * @param $fSetOrigin
     * @param $fSetCurrent
     * @param null $tbName
     * @return bool|mixed|string
     */
    public static function cmpFieldSettings($fSetOrigin, $fSetCurrent, $tbName){

        if(empty($fSetOrigin) || empty($fSetCurrent) || empty($tbName)){
            //$this->errPush(self::ERROR_INVALID_PARAM, "Empty field setting params.");
            return false;
        }




        //drop fields
        $dropItems='';

        //add fields:
        $addItems='';

        //change fields:
        $changeItems='';

        //previous field, '' when start.
        $prevF='';

        //add key.
        $addKey='';

        //drop key.
        $dropKey='';

        foreach($fSetCurrent as $fName => $fSet){

            //ADD:
            if( !isset($fSetOrigin[$fName])){
                list($fSetString,$keyStr,$keyType)=static::getFieldSQL($fName,$fSet);

                $addItems.=($addItems==''? '':','.PHP_EOL)."ADD COLUMN $fSetString ".($prevF==''? 'FIRST':"AFTER `$prevF`");
                if(!empty($keyStr)){
                    $addKey.=($addKey==''? '':','.PHP_EOL)."ADD $keyStr" ;
                }

            }else{
                //CHANGE:

                //PRI, UNI,MUL key changed:
                $oriKey=\GF::getKeyValue('key',$fSetOrigin[$fName],'');
                $newKey=\GF::getKeyValue('key',$fSet,'');
                if($oriKey!=$newKey){

                    if($oriKey!=''){
                        if($oriKey=='PRI'){
                            $dropKey.=($dropKey==''? '':','.PHP_EOL)."DROP PRIMARY KEY";
                        }else{
                            $dropKey.=($dropKey==''? '':','.PHP_EOL)."DROP INDEX `$fName".($oriKey=='UNI'?'_UNIQUE':'_INDEX')."`" ;
                        }
                    }

                    if($newKey!=''){
                        if($oriKey=='PRI'){
                            $addKey.=($addKey==''? '':','.PHP_EOL)."ADD PRIMARY KEY (`$fName`)";
                        }else{
                            $addKey.=($addKey==''? '':','.PHP_EOL)."ADD INDEX `$fName".($newKey=='UNI'?'_UNIQUE':'_INDEX')."` (`$fName`)" ;
                        }

                    }

                    $fSetOrigin[$fName]['key']='';
                    $fSet['key']='';


                }


                //setting detail changed:
                if($fSetOrigin[$fName]!=$fSet){

                    list($fSetString,$keyStr,$keyType)=static::getFieldSQL($fName,$fSet);
                    $changeItems.=($changeItems==''? '':','.PHP_EOL)."CHANGE COLUMN `$fName` $fSetString";

                }

            }

            $prevF=$fName;
        }

        foreach($fSetOrigin as $fName => $fSet){
            if( !isset($fSetCurrent[$fName])){
                $dropItems.=($dropItems==''? '':','.PHP_EOL)."DROP COLUMN `$fName`";
            }
        }

        //$result="ALTER TABLE `$tbName` ".PHP_EOL;
        $result='[#HEAD#]';
        $result.=($dropItems!=''? ','.PHP_EOL:'').$dropItems;
        $result.=($changeItems!=''? ','.PHP_EOL:'').$changeItems;
        $result.=($addItems!=''? ','.PHP_EOL:'').$addItems;
        $result.=($addKey!=''? ','.PHP_EOL:'').$addKey;
        $result.=($dropKey!=''? ','.PHP_EOL:'').$dropKey;

        $result=str_ireplace('[#HEAD#],','',$result);
        $result=str_ireplace('[#HEAD#]','',$result);



        $result=empty(trim($result)) ? '': "ALTER TABLE `$tbName` ".$result;


        return $result;


    }//-/




    /**
     * getFieldSQL
     * get field setting sql string.
     * @param $fieldName
     * @param $fieldSet
     * @return array :
     *                 0 -> field creation sql string, like:  `pro_title` varchar(20) NOT NULL
     *                 1 -> key sql string, like: PRIMARY KEY (`pro_idx`)
     *                                            UNIQUE KEY `pro_idx_UNIQUE`  (`pro_idx`)
     *                 2 -> key type string: PRI, UNI, MUL;
     */
    public static function getFieldSQL($fieldName,$fieldSet){


        list($valueType,$size,$unsigned,$key,$default,$notNull,$extra,$numWidth,$decimalWidth)=\GF::getVParams($fieldSet,'valueType,size,unsigned,key,default,notNull,extra,numWidth,decimalWidth',$noErr);

        $unsignedStr='';

        switch(strtolower( $valueType)){
            case "tinyint":
            case "smallint":
            case "int":
            case "mediumint":
            case "bigint":

                $valueTypeSize=array(
                    'tinyint'=>3,
                    'smallint'=>5,
                    'mediumint'=>7,
                    'int'=>10,
                    'bigint'=>18
                );

                $valueType=" $valueType($valueTypeSize[$valueType]) ";

                if($unsigned==1){
                    $unsignedStr="UNSIGNED";
                }
                /*echo "$fieldName -> ============= ";
                var_dump($default);*/

                if(is_null($default)){

                    if($notNull==1 || $notNull==true){

                        $default="'0'";
                        if(strcasecmp($extra,'AUTO_INCREMENT')==0){

                            $default=null;
                        }
                    }else{

                        $default="NULL";

                    }
                }



                break;
            case "float":
            case "double":
            case "decimal":

                if(is_numeric($numWidth) && is_numeric($decimalWidth)){
                    $valueType="$valueType($numWidth,$decimalWidth)";
                }

                if($unsigned==1){
                    $unsignedStr="UNSIGNED";
                }

                if(is_null($default)){
                    if($notNull==1 || $notNull==true){
                        $default="'0'";
                        if(strcasecmp($extra,'AUTO_INCREMENT')==0){
                            $default=null;
                        }
                    }else{
                        $default="NULL";
                    }
                }


                break;


            case "varchar":
                $valueType="$valueType($size)";

                if(!is_null($default)){
                    $default="'$default'";
                }else{
                    if(!($notNull==1 || $notNull==true)){
                        $default="NULL";
                        if(strcasecmp($extra,'AUTO_INCREMENT')==0){
                            $default=null;
                        }
                    }
                }

                break;
            case "time":
                $valueType="datetime";

                if(is_null($default)){
                    if(!($notNull==1 || $notNull==true)){
                        $default="NULL";
                    }
                }

                break;

        }

        $notNull=$notNull==1 || $notNull==true? "NOT NULL" : "";

        $defaultStr='';
        if(!is_null($default)){
            $defaultStr='DEFAULT '.(strcasecmp($default,'NULL')!=0 ? "'$default'":'NULL');
        }



        $extra=!empty($extra)? strtoupper($extra):'';

        $itemSql="`$fieldName` $valueType $unsignedStr $notNull $defaultStr $extra";
        $keySql='';



        if(strcasecmp($key,'PRI')==0){
            $keySql="PRIMARY KEY (`$fieldName`)";
            $keySql.=",UNIQUE KEY `".$fieldName."_UNIQUE` (`$fieldName`)";

        }elseif(strcasecmp($key,'UNI')==0){
            $keySql="UNIQUE KEY `".$fieldName."_UNIQUE` (`$fieldName`)";
        }elseif(strcasecmp($key,'MUL')==0){
            $keySql="KEY `".$fieldName."_INDEX` (`$fieldName`)";
        }

        return array($itemSql,$keySql,$key);

    }//


    /**
     * _parseFieldSettings
     * generate table column definition data from settings( attributes of total object setting)
     * @param null $settings : settings data. if null then get from $this->_attributes.
     * @return array : return [DB-Setting-Data format].
     *                 detail description of format, refer to [readColumnDef] function.
     */
    protected function _parseFieldSettings($settings=NULL){

        if(empty($settings)){
            $settings=$this->_attributes;
        }

        $result=array();

        foreach ($settings as $attrName=>$attrSet){

            $dbName=\GF::getKeyValue('dbName',$attrSet,$this->_mainTable.'_'.$attrName);
            $type=\GF::getKeyValue('type',$attrSet);
            if(empty($dbName) || empty($type) || (strcasecmp($type,"VALUE")!==0 && strcasecmp($type,"ID")!==0 && strcasecmp($type,"REFERENCE")!==0)){
                continue;
            }

            $generator='';

            list($valueType,$size,$unsigned,$required,$default,$unique,$index)=\GF::getVParams($attrSet,'valueType,size,unsigned,required,default,unique,index',$noErr);


            $fItem=array();
            if(strcasecmp($type,'ID')==0){
                $fItem['key']='PRI';
                $generator=\GF::getKeyValue('generator',$attrSet);
                if( strcasecmp($generator,'auto_increment')==0){
                    $fItem['extra']='auto_increment';
                }
                $required="1";
                $unsigned="1";


            }elseif($unique==1){
                $fItem['key']='UNI';
            }elseif($index==1){
                $fItem['key']='MUL';
            }



            switch(strtolower( $valueType)){

                case 'string':

                    $valueType= 'varchar';

                    if($required!=1 && is_null($default)){
                        $fItem['default']=NULL;
                    }elseif(!is_null($default)){
                        $fItem['default']=$default;
                    }

                    $fItem['size']=$size;

                    break;

                case 'text':



                    $valueTypeNames=array('tinytext','text','mediumtext','longtext');

                    $valueType= 'text';

                    if($size>pow(2,24)-1){
                        $valueType=$valueTypeNames[3];
                    }elseif($size>pow(2,16)-1){
                        $valueType=$valueTypeNames[2];
                    }elseif($size>pow(2,8)-1){
                        $valueType=$valueTypeNames[2];
                    }


                    break;

                case 'int':

                    if($unsigned==1){
                        $fItem['unsigned']=1;
                    }

                    $valueTypeNames=array('tinyint','smallint','mediumint','int','bigint');

                    //size -> byte size in db storing. size=1,2,3,4,8 bytes.
                    if($size<8 && $size>0){
                        $valueType=$valueTypeNames[$size-1];
                    }elseif($size>=8){
                        $valueType=$valueTypeNames[4];
                    }else{
                        $valueType=$valueTypeNames[3];
                    }



                    if(is_numeric($default)){
                        $fItem['default']=$default;
                    }elseif($required==1){
                        if( strcasecmp($generator,'auto_increment')!==0){
                            $fItem['default']=0;
                        }

                    }else{
                        $fItem['default']=NULL;
                    }




                    break;

                case 'float':
                case 'double':
                case 'decimal':

                    if($unsigned==1){
                        $fItem['unsigned']=1;
                    }

                    if(is_numeric($default)){
                        $fItem['default']=$default;
                    }elseif($required==1){
                        $fItem['default']=0;
                    }else{
                        $fItem['default']=NULL;
                    }

                    $numWidth=\GF::getKeyValue('numWidth',$attrSet);
                    if(is_numeric($numWidth)){
                        $fItem['numWidth']=$numWidth;
                    }

                    $decimalWidth=\GF::getKeyValue('decimalWidth',$attrSet);
                    if(is_numeric($decimalWidth)){
                        $fItem['decimalWidth']=$decimalWidth;
                    }

                    break;


                case 'time':

                    $valueType='time';
                    if(!empty($default)){
                        $fItem['default']=$default;
                    }elseif($required!=1){
                        $fItem['default']=NULL;
                    }

                    break;

                case 'image':

                    $valueType= "varchar";
                    $fItem['size']=$this->_IMG_FILENAME_LEN*$size;
                    if(!empty($default)){
                        $fItem['default']=$default;
                    }

                    break;

            }

            $fItem['valueType']=$valueType;
            if($required==1 || $required===true){
                $fItem['notNull']=1;
            }


            //array_push($result,$fItem);
            $result[$dbName]=$fItem;

        }

        //var_dump($result);


        return $result;

    }//-/

    /**
     * _readDBFieldSettings
     * read table column definition info in assoc-array format.
     * @param $tbName
     * @return array|bool : return [DB-Setting-Data format].
     *                      false - when error, detail error in model error info
     *                      assoc-array - element format:
     *                      'DB field name'=>array(
     *                                            'valueType'=> int, bigint,float,double,decimal, varchar, text, time ....
     *                                            'notNull'=> 1- yes, not null; 0- can be null.
     *                                            'key'=> PRI(primary key), UNI(unique key), MUL(index key)
     *                                            'size'=> when varchar, string length.
     *                                            'default'=> default value.
     *                                            'unsigned'=> when int or float, unsigned value or not. (1- yes, 0-not unsigned)
     *                                            'extra'=> auto_increment
     *                                            'numWidth'=> when float(M,D), numWidth=M
     *                                            'decimalWidth'=> when float(M,D), decimalWidth=D
     *                                      )
     *
     *
     */
    protected function _readDBFieldSettings($tbName){

        //db connect varify
        $db=DBConnector::connect();
        if($db===false){
            self::errPush(self::ERROR_FAILED_DB_OPERATION,"Failed getting db connect");
            return false;
        }

        //defInfoList
        $defInfoList=array();

        //read column definiation
        $query="show columns from $tbName";
        $result= $db->query($query);
        if(mysqli_errno($db)!=0 || mysqli_num_rows($result)<=0){
            $this->errPush(Model::ERROR_FAILED_DB_OPERATION,'Failed reading columns from '.$tbName);
            return false;
        }
        for($i=0;$i<mysqli_num_rows($result);$i++){
            $row=mysqli_fetch_assoc($result);

            //read valueType and valueSize

            $valueType=(string)$row['Type'];
            $defaultValue=$row['Default'];

            //read key type:
            $keyType=(string)$row['Key'];
            //read extra:
            $extraValue=(string)$row['Extra'];

            $valueUnsigned=NULL;

            if( stripos($valueType, 'int')!==false){//if int:

                if(stripos($valueType,'unsigned')!==false){
                    $valueUnsigned=1;
                }

                $valueTypeNames=array('tinyint','smallint','mediumint','int','bigint');
                foreach($valueTypeNames as $nameId=>$nameStr){
                    if(preg_match('/^'.$nameStr.'.*/', $valueType)>0 ){
                        $valueType=$nameStr;
                        $valueSize=NULL; //$nameId<4 ? $nameId+1 : 8;
                        break;
                    }
                }

                if((string)$row['Null']=='NO' && is_null($defaultValue) && strcasecmp($extraValue,'auto_increment')!==0){
                    $defaultValue='0';
                }





            }elseif(stripos($valueType, 'float')!==false || stripos($valueType, 'double')!==false || stripos($valueType, 'decimal')!==false){


                $valueWidthStr=str_ireplace(array('float','double','decimal','(',')'),'',$valueType);
                $valueType=trim(str_ireplace(array($valueWidthStr,'(',')'),'',$valueType));
                if(!empty($valueWidthStr)){
                    $valueWidthArr=explode(",",$valueWidthStr);
                    if($valueWidthArr!==false && !empty($valueWidthArr[0]) && is_numeric($valueWidthArr[0]) ){
                        $numWidth=$valueWidthArr[0];
                        $decimalWidth=2;
                        if(count($valueWidthArr)>1 && is_numeric($valueWidthArr[1])){
                            $decimalWidth=$valueWidthArr[1];
                        }
                    }
                }








            }elseif( stripos($valueType, 'char')!==false){//if string:
                $valueSize=str_ireplace(array('var','char','(',')'),'',$valueType);
                $valueType=stripos($valueType, 'varchar')!==false ? 'varchar':'char';
            }elseif( stripos($valueType, 'text')!==false){//if text:

                $valueSize=NULL;


            }elseif( stripos($valueType, 'time')!==false){//if time:
                $valueSize=NULL;
                $valueType='time';
            }else{
                continue;
            }




            $fItem=array(
                'valueType'=> $valueType,
            );

            if((string)$row['Null']=='NO'){
                $fItem['notNull']=1;
            }


            if(!is_null($defaultValue)){
                $fItem['default']=$defaultValue;
            }elseif((string)$row['Null']!='NO' && stripos($valueType, 'text')===false){
                $fItem['default']=null;
            }


            if(!empty($keyType)){
                $fItem['key']=$keyType;
            }

            if(!empty($valueSize)){
                $fItem['size']=$valueSize;
            }

            if(!empty($valueUnsigned)){
                $fItem['unsigned']=$valueUnsigned;
            }

            if(!empty($extraValue)){
                $fItem['extra']=$extraValue;
            }
            if(!empty($numWidth)){
                $fItem['numWidth']=$numWidth;
            }
            if(!empty($decimalWidth)){
                $fItem['decimalWidth']=$decimalWidth;
            }

            $defInfoList[ $row['Field']]=$fItem;


        }
        return $defInfoList;
    }//-/



















}//=/
