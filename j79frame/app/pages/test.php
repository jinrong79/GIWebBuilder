<?php
//include config files.
require_once  $_SERVER['DOCUMENT_ROOT']."/global.config.php"; //优先引入全局设置



//$df=new \j79frame\app\model\DeliveryFee();
//$re=$df->calculateByAddress($fullCode,0,$proList, 222401);

//$tb=new \j79frame\app\model\db\TbBankCard();

//$re=json_encode($tb->fieldSetting);

$filename=$_SERVER['DOCUMENT_ROOT']."\\j79frame\\app\\settings\\db\\testdb.json";
$contents='';
try{
    $errLvl=error_reporting();
    error_reporting(0);
    $handle = fopen($filename, "r");
    error_reporting($errLvl);
    if($handle!==false){
        $contents = fread($handle, filesize ($filename));
    }
}catch(Exception $e){
    echo 'error when load file';
}

//var_dump($contents);

$settings=json_decode($contents,true);

$ob=new \j79frame\lib\model\DBUnitGenerator($settings);

$re=$ob->generate();

echo json_encode($re);

/*if(!\GFunc::isOK($re)){
    $err=$ob->errPop();
    echo json_encode($err);
}else{
    echo json_encode($re);
}*/

/*$err=$ob->errPop();

var_dump($err);*/

//echo $ob->generateDBAlterScript();

//echo $ob->generateDBCreateScript();
//var_dump($ob->createTable());
/*
$a1=array(
    'aa'=>'11',
    'bb'=>'22',
    'cc'=>'33',
    'dd'=>array('ab'=>1)
);
$a2=array(
    'aa'=>'11',

    'cc'=>'33',
    'bb'=>'22',
    'dd'=>array('ab'=>2)
);

var_dump($a1==$a2);*/

//$result=$ob->generateFieldSettings(null);
//$result=$ob->readFieldSettings('tb_product');
//echo json_encode($result);



//var_dump($result);

/*echo '<hr/>------------------------------<br/>'.PHP_EOL;

$result=$ob->readColumnDef('tb_product');

if($result===false){

}


var_dump($result);

/*var_dump(json_encode($result));*/
//echo json_encode($result);

//var_dump($ob->errPop());

?>