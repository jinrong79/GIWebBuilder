<?php
namespace j79frame\lib\doc;

use j79frame\lib\util\File;

/**
 * Class testCaseBuilder
 * build test case doc in html format.
 */
class testCaseBuilder{

    public $totalBugRate=0.12;

    public $bugResultList=''; //bug list string.

    public $bugRetestResultList=''; //

    public $flagSaveFile=false; //save final html when view.

    public $titleFrontNoPrefix="2.5.";

    //style for html view
    public $styleCode= '<style>
  table{border-collapse:collapse}
  table td{font-size:10.5pt;mso-bidi-font-size:12.0pt;
  font-family:宋体;mso-ascii-font-family:"Times New Roman";mso-hansi-font-family:
  "Times New Roman";color:black; padding:6pt 4.0pt 6pt 4.0pt}
    table td>p{ padding:5pt 0;line-height:150%; vertical-align: center;}
    table td>p>span{ line-height: 150%; display: inline-block;vertical-align: center; }
    h3{font-size:12pt; font-family: "宋体"}
    
</style>';


    // case item list settings:
    public $itemList=array(

        array(
            'name'=>"3D导航",
            "channel"=>"系统",
            "type"=>6,
        ),

        array(
            'name'=>"登录",
            "channel"=>"系统",
            "type"=>2,
        ),

        array(
            'name'=>"智能路灯",
            "channel"=>"智慧市政",
        ),
        array(
            'name'=>"智能楼宇",
            "channel"=>"智慧市政",
        ),
        array(
            'name'=>"公租房",
            "channel"=>"智慧市政",
        ),
        array(
            'name'=>"5GWIFI",
            "channel"=>"智慧市政",
            "type"=>1
        ),
        array(
            'name'=>"水质监测",
            "channel"=>"智慧市政",
        ),
        array(
            'name'=>"智能井盖",
            "channel"=>"智慧市政",
        ),
        array(
            'name'=>"智能烟感",
            "channel"=>"智慧市政",
        ),
        array(
            'name'=>"人口大数据热力图",
            "channel"=>"智慧市政",
            "type"=>1
        ),
        array(
            'name'=>"交通信号灯",
            "channel"=>"智慧交通",
        ),
        array(
            'name'=>"路边停车",
            "channel"=>"智慧交通",
        ),
        array(
            'name'=>"智能公交车站",
            "channel"=>"智慧交通",
        ),
        array(
            'name'=>"无人摆渡车",
            "channel"=>"智慧交通",
        ),
        array(
            'name'=>"交通热力图",
            "channel"=>"智慧交通",
            "type"=>1
        ),
        array(
            'name'=>"视频监控",
            "channel"=>"智慧安保",
        ),
        array(
            'name'=>"安保机器人",
            "channel"=>"智慧安保",
        ),
        array(
            'name'=>"保安人员",
            "channel"=>"智慧安保",
        ),
        array(
            'name'=>"应急物资",
            "channel"=>"智慧安保",
        ),
        array(
            'name'=>"智能终端",
            "channel"=>"智慧安保",
        ),
        array(
            'name'=>"警情热力图",
            "channel"=>"智慧安保",
            "type"=>1
        ),

        array(
            "phase"=>2,
            'name'=>"应急预案_视频监控",
            "channel"=>"应急预案",
        ),
        array(
            'name'=>"应急预案_安保机器人",
            "channel"=>"应急预案",
        ),
        array(
            'name'=>"应急预案_保安人员",
            "channel"=>"应急预案",
        ),
        array(
            'name'=>"应急预案_应急物资",
            "channel"=>"应急预案",
        ),
        array(
            'name'=>"应急预案_智能终端",
            "channel"=>"应急预案",
        ),
        array(
            'name'=>"视频预案",
            "channel"=>"视频预案",
            "type"=>5
        ),

        array(
            'name'=>"时间效果",
            "channel"=>"系统设定",
            "type"=>3
        ),
        array(
            'name'=>"天气效果",
            "channel"=>"系统设定",
            "type"=>3
        ),
        array(
            'name'=>"季节效果",
            "channel"=>"系统设定",
            "type"=>3
        ),
        array(
            'name'=>"楼宇亮化效果",
            "channel"=>"系统设定",
            "type"=>3
        ),
        array(
            'name'=>"搜索",
            "channel"=>"系统设定",
            "type"=>4
        ),




        array(
            "phase"=>3,
            'name'=>"管理员登录",
            "terminal"=>'后台',
            "channel"=>"后台",
            "type"=>2,
        ),

        array(
            'name'=>"用户",
            "terminal"=>'后台',
            "channel"=>"后台",
            "type"=>100,
        ),
        array(
            'name'=>"角色",
            "terminal"=>'后台',
            "channel"=>"后台",
            "type"=>100,
        ),
        array(
            'name'=>"权限",
            "terminal"=>'后台',
            "channel"=>"后台",
            "type"=>100,
        ),
        array(
            'name'=>"日志",
            "terminal"=>'后台',
            "channel"=>"后台",
            "type"=>101,
        ),
        array(
            'name'=>"系统设置",
            "terminal"=>'后台',
            "channel"=>"后台",
            "type"=>102,
        ),



    );
    /*e.g.:
        $setting=array(

            array(
                "name"=>"3D导航",     // item name: must fill
                "channel"=>"系统",    // channel name: must fill
                "type"=>6,           //default=0;
                                     // 0- normal test case, case structure setting from 'type0'
                                     // other number - test case others.
                "terminal"=>"终端";   // case apply to terminal: default="终端"

            ),

            array(
              'name'=>"智能路灯",
              "channel"=>"智慧市政",
            ),

        );

     */
    /**
     * view
     * view text in html
     */
    public function view(){
        $curIdx=0;
        $totalB=0;
        $totalCase=0;
        $totalBugRate=$this->totalBugRate;


        $this->bugResultList='';
        $sumList='<table><tbody>';

        $curPhase=1;

        $itemHTML='';

        $retestList=array(
          'round1'=>array(),
          'round12'=>array(),
          'round13'=>array()

        );

        $retestAmount=0;

        $bugModifiedRec='<table><tbody>';

        echo '<a href="#bugModifiedList" target="_top">bug modified list</a>';

        $tmpTxt='<h3><span lang="EN-US" style="font-size:12.0pt;mso-bidi-font-size:
20.0pt;font-family:宋体">'.$this->titleFrontNoPrefix.($curPhase).'</span> 第1轮测试</h3>';
        echo $tmpTxt;

        $itemHTML.=$tmpTxt;

        $itemNo=1;

        $turnAmount=0;

        for($i=0;$i<count($this->itemList);$i++){

            $curSet=$this->itemList[$i];
            $curSet['bugRate']=$totalBugRate;
            $curSet['startIdx']=$curIdx;

            $itemPhase=\GF::getKey("phase",$curSet,1);

            //var_dump($curSet);


            if($itemPhase>$curPhase){



                //$this->bugRetestResultList='';


                $tmpTxt="<p>本轮测试，一共 $turnAmount 条测试用例。</p>";


                $tmpTxt.='<h3><span lang="EN-US" style="font-size:12.0pt;mso-bidi-font-size:
20.0pt;font-family:宋体">'.$this->titleFrontNoPrefix.($itemPhase).'</span> 第'.$itemPhase.'轮测试</h3>';

                $itemHTML.=$tmpTxt;

                echo $tmpTxt;

                $turnAmount=0;

                // array_push($retestList['round'.$curPhase],$this->bugRetestResultList);

                $returnTestListPrefix='<h3><span lang="EN-US" style="font-size:12.0pt;mso-bidi-font-size:
20.0pt;font-family:宋体">对前'.($itemPhase-1).'轮bug的回归测试 (共 '.$retestAmount.' 条)</h3>';
                $returnTestListPrefix.=$this->bugRetestResultList;

                $itemHTML.=$returnTestListPrefix;
                echo $returnTestListPrefix;

                $itemHTML.=$returnTestListPrefix;

                //$retestAmount=0;
                $curPhase=$itemPhase;
                $itemNo=1;


            }


            $curSet['phase']=$curPhase;

            $t=new testCaseItem($curSet);
            $t->titlePrefix='<a name="_Toc'.(42005496+$i).'"><span lang="EN-US" style="font-size:12.0pt;mso-bidi-font-size:
20.0pt;font-family:宋体">'.$this->titleFrontNoPrefix.($curPhase).'.'.($itemNo).'</span></a> ';
            $itemHTML.=$t->view();
            $retestAmount+=$t->bugRetestAmount;
            $itemNo++;
            $curIdx=$t->curIdx;
            $totalB+=$t->totalBugAmount;
            $totalCase+=$t->totalAmount;

            $turnAmount+=$t->totalAmount;

            $bugModifiedRec.=$t->bugModifiedRec;
            if($t->flagBug){
                $this->bugResultList.=$t->bugResult;
                $this->bugRetestResultList.=$t->bugRetestItem;
            }

            $sumList.="<tr><td>".$t->channel."-".$t->name."</td><td>".$t->totalAmount."</td></tr>";

            //echo $sumList;



        }

        $curPhase++;

        $tmpTxt="<p>本轮测试，一共 $turnAmount 条测试用例。</p>";
        $tmpTxt.= '<h3><span lang="EN-US" style="font-size:12.0pt;mso-bidi-font-size:
20.0pt;font-family:宋体">'.$this->titleFrontNoPrefix.($curPhase).'</span> 第'.($curPhase).'轮测试</h3>';
        echo $tmpTxt;
        $itemHTML.=$tmpTxt;

        // array_push($retestList['round'.$curPhase],$this->bugRetestResultList);

        $returnTestListPrefix='<h3><span lang="EN-US" style="font-size:12.0pt;mso-bidi-font-size:
20.0pt;font-family:宋体">对前'.($curPhase-1).'轮bug的回归测试 (共 '.$retestAmount.' 条)</h3>';
        $returnTestListPrefix.=$this->bugRetestResultList;
        echo $returnTestListPrefix;
        $itemHTML.=$returnTestListPrefix;

        $itemHTML.=$returnTestListPrefix;


        $sumList.='</tbody></table>';

        $bugModifiedRec.='</tbody></table>';


        $finalHTML=$this->styleCode;
        $finalHTML.=$itemHTML;
        $finalHTML.='<hr/>'.PHP_EOL;
        $finalHTML.="<p>bug total:$totalB</p>".PHP_EOL;
        $finalHTML.="<p>case total:$totalCase</p>".PHP_EOL;
        $finalHTML.='<hr/>'.PHP_EOL;
        $finalHTML.="<H2>SUM LIST</H2>".PHP_EOL;
        $finalHTML.=$sumList;
        $finalHTML.='<hr/>'.PHP_EOL;
        $finalHTML.="<H2><a id='bugModifiedList'></a>BUG MODIFIED RESULT LIST</H2>".PHP_EOL;
        $finalHTML.='<hr/>'.PHP_EOL;
        $finalHTML.= $bugModifiedRec.PHP_EOL;

        //$finalHTML.= $this->bugResultList.PHP_EOL;

        /*$finalHTML.='<hr/>'.PHP_EOL;
        $finalHTML.="<H2>BUG RETEST RESULT LIST</H2>".PHP_EOL;
        $finalHTML.='<hr/>'.PHP_EOL;
        $finalHTML.= $this->bugRetestResultList.PHP_EOL;*/



        echo $finalHTML;
        if($this->flagSaveFile){
            $fn=File::getRandomFileName('html');
            echo $fn;
            File::saveFile($fn, $finalHTML);


        }

        /*echo '<hr/>';
        echo "<p>bug total:$totalB</p>";
        echo "<p>case total:$totalCase</p>";
        echo '<hr/>';
        echo "<H2>BUG RESULT LIST</H2>";
        echo '<hr/>';
        echo $this->bugResultList;*/

    }//-/

    /**
     * generate
     * generate text and return string.
     * @return string
     */
    public function generate(){

        $bugResultList='';

        $resultStr='';
        $curIdx=0;
        $totalB=0;
        $totalCase=0;
        $totalBugRate=$this->totalBugRate;
        $this->bugResultList='';

        $curPhase=0;


        for($i=0;$i<count($this->itemList);$i++){

            $curSet=$this->itemList[$i];
            $curSet['bugRate']=$totalBugRate;
            $curSet['startIdx']=$curIdx;

            $t=new testCaseItem($curSet);
            $curResult=$t->generate();
            $resultStr.=$curResult;
            $curIdx=$t->curIdx;
            $totalB+=$t->totalBugAmount;
            $totalCase+=$t->totalAmount;
            if($t->flagBug){
                $this->bugResultList.=$curResult;
                $this->bugRetestResultList.=$t->bugRetestItem;
            }
        }

        //last phase:



        return $resultStr;


    }//-/



}//==/class:testCaseBuilder