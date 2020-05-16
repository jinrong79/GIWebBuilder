<?php
namespace j79frame\lib\util;
header("Content-Type: text/html; charset=utf-8");
require_once   dirname(__FILE__).DIRECTORY_SEPARATOR."global.config.php";



class testCaseItem{

    public $modelFileUrl='caseModel1.html'; // modelFile url

    public $name=''; // function name
    public $type=0;  // normal   style
                    /* 0- normal
                       1- with thermodynamic
                       99- custom
                    */
    public $channel=''; //channnel name
    public $des=''; //function des
    public $goal=''; //test goal
    public $cond=''; //test condition
    public $bugRate=0; //bug rate;
    public $records=array(); //test record

    public $curIdx=0; //item idx in current

    public $curTextSet=array();// current text set

    public $recordSample='<tr>
      <td width="236" colspan="2" valign="top">##act##</td>
      <td width="236" valign="top">##expect##</td>
      <td width="247" valign="top">##real##</td>
    </tr>';

    public $textSetting=array(
      'type0'=>array(
          array(
              'group'=>1,
              'label'=>'3DDWBT',
              'des'=>'在白天场景中，##name##3D地图定位显示功能',
              'goal'=>'检查在白天场景中##name##3D地图定位是否正常显示',
              'cond'=>'用户登录成功，进入##channel##频道',
              'records'=>array(
                  array(
                    'act'=>'<p>点击左侧##name##图标</p>',
                    'expect'=>'<p>在3D场景中，显示多个带有##name##图标的立体定位标</p>',
                    'resultSuccess'=>'<p>正常！</p>',
                    'resultFail'=>array('<p>显示有重叠现象，有问题！</p>','<p>不显示远处的定位标，有问题！</p>','<p>3D场景中没有任何变化，有问题！</p>'),
                  ),
              )
          ),

          array(
              'group'=>1,
              'label'=>'3DDWHY',
              'des'=>'在黑夜场景中，##name##3D地图定位显示功能',
              'goal'=>'检查在黑夜场景中##name##3D地图定位是否正常显示',
              'cond'=>'用户登录成功，进入##channel##频道，开启设定里面的黑夜',
              'records'=>array(
                  array(
                      'act'=>'<p>点击左侧##name##图标</p>',
                      'expect'=>'<p>在3D场景中，显示多个带有##name##图标的立体定位标</p>',
                      'resultSuccess'=>'<p>正常！</p>',
                      'resultFail'=>array('<p>显示有重叠现象，有问题！</p>','<p>远处的定位标，黑夜中看不清，有问题！</p>','<p>3D场景中没有任何变化，有问题！</p>'),
                  ),
              )
          ),


          array(
              'group'=>1,
              'label'=>'3DDWYT',
              'des'=>'在下雨场景中，##name##3D地图定位显示功能',
              'goal'=>'检查在下雨场景中##name##3D地图定位是否正常显示',
              'cond'=>'用户登录成功，进入##channel##频道，开启设定里面的下雨',
              'records'=>array(
                  array(
                      'act'=>'<p>点击左侧##name##图标</p>',
                      'expect'=>'<p>在3D场景中，显示多个带有##name##图标的立体定位标</p>',
                      'resultSuccess'=>'<p>正常！</p>',
                      'resultFail'=>array('<p>显示有重叠现象，有问题！</p>','<p>远处的定位标，雨中看不清，有问题！</p>','<p>3D场景中没有任何变化，有问题！</p>'),
                  ),
              )
          ),

          array(
              'group'=>2,
              'label'=>'3DDJDW',
              'des'=>'点击##name##定位图标，显示##name##详情页',
              'goal'=>'检查在3D场景中点击##name##3D定位标，能否在正确位置显示详情',
              'cond'=>'用户登录成功，进入##channel##频道，点击左侧##name##图标以后',
              'records'=>array(
                  array(
                      'act'=>'<p>点击左侧##name##图标，点击3D场景中的任意一个定位标</p>',
                      'expect'=>'<p>以鼠标点击点为起始点，弹出信息窗口，显示##name##详情信息</p>',
                      'resultSuccess'=>'<p>正常！</p>',
                      'resultFail'=>array('<p>显示位置不对，有问题！</p>','<p>部分窗口被遮住，有问题！</p>','<p>3D场景中没有任何变化，有问题！</p>'),
                  ),
              )
          ),

      ),

    );


    public function __construct($params)
    {
        $this->ini($params);
    }//-/

    /**
     * ini
     * @param $params
     */
    public function ini($params){
        if($params){

            $this->modelFileUrl=\GF::getKey("modelFileUrl",$params,$this->modelFileUrl);

            $this->name=\GF::getKey("name",$params);
            $this->channel=\GF::getKey("channel",$params);
            $this->type=\GF::getKey("type",$params,0);

            $this->bugRate=\GF::getKey("bugRate",$params,0);

            $this->curIdx=\GF::getKey("startIdx",$params,0);

            //if custom type,then load text from params.
            if($this->type==99){

                /*$this->des=\GF::getKey("des",$params);
                $this->goal=\GF::getKey("goal",$params);
                $this->cond=\GF::getKey("cond",$params);
                $this->records=\GF::getKey("records",$params,array());*/

                $this->curTextSet=\GF::getKey("text",$params);

            }else{
                $this->_getCurSet();
            }


        }
    }//-/

    protected function _getCurSet(){
        $curTypeName='type'.$this->type;
        $curText=\GF::getKey($curTypeName,$this->textSetting);
        if(!$curText){
            return;
        }
        $this->curTextSet=$curText;
    }//-/

    public function generate(){
        $result='';

        $f=new File();
        $itemHtmlSample=$f->readFile($this->modelFileUrl);







        for($i=0;$i<count($this->curTextSet);$i++){
            $this->curIdx++;
            $curResult=$itemHtmlSample;

            $des=\GF::getKey("des",$this->curTextSet[$i]);
            $goal=\GF::getKey("goal",$this->curTextSet[$i]);
            $cond=\GF::getKey("cond",$this->curTextSet[$i]);

            $records=\GF::getKey("records",$this->curTextSet[$i]);

            $recordResult='';

            foreach($records as $key=>$recordItem){
                $curRecord=$this->recordSample;
                $curRecord=str_replace("##act##",\GF::getKey("act",$recordItem), $curRecord);
                $curRecord=str_replace("##expect##",\GF::getKey("expect",$recordItem), $curRecord);
                if(mt_rand(1,100)<= round($this->bugRate*100)){
                    $curErrorRecList=\GF::getKey("resultFail",$recordItem);
                    $curRecord=str_replace("##real##",$curErrorRecList[mt_rand(0,count($curErrorRecList)-1)], $curRecord);

                }else{
                    $curRecord=str_replace("##real##",\GF::getKey("resultSuccess",$recordItem), $curRecord);
                }
                $recordResult.=$curRecord;
            }

            //do replace
            $curResult=str_replace('##id##',\GF::zh2PYFirst($this->name).'_'.\GF::getKey("label",$this->curTextSet[$i]).'_'.$this->curIdx,$curResult);
            $curResult=str_replace('##des##',$des,$curResult);
            $curResult=str_replace('##goal##',$goal,$curResult);
            $curResult=str_replace('##cond##',$cond,$curResult);
            $curResult=str_replace('<!--records-->',$recordResult,$curResult);
            $curResult=str_replace('##name##',$this->name,$curResult);
            $curResult=str_replace('##channel##',$this->channel,$curResult);



            $result.=$curResult;

        }


        return $result;
    }//-/

    public function view(){
        echo '<h4>'.$this->name.'</h4>'.$this->generate();
    }//-/

}//==/class

//view style
echo '<style>
  table{border-collapse:collapse}
  table td{font-size:9.0pt;mso-bidi-font-size:12.0pt;
  font-family:宋体;mso-ascii-font-family:"Times New Roman";mso-hansi-font-family:
  "Times New Roman";color:black; padding:1pt 4pt;}
    table td>p{ padding:1pt 0;}
</style>';


$totalBugRate=0.15;
$setting=array(
    array(
      'name'=>"智能路灯",
      "channel"=>"智慧市政",
    ),
    array(
        'name'=>"智能楼宇",
        "channel"=>"智慧市政",
    ),

);

$curIdx=0;
for($i=0;$i<count($setting);$i++){

    $curSet=$setting[$i];
    $curSet['bugRate']=$totalBugRate;
    $curSet['startIdx']=$curIdx;

    $t=new testCaseItem($curSet);
    $t->view();
    $curIdx=$t->curIdx;
}


