<?php
namespace j79frame\lib\doc;

/**
 * Class testCaseBuilder
 * build test case doc in html format.
 */
class testCaseBuilder{

    public $totalBugRate=0.12;

    //style for html view
    public $styleCode= '<style>
  table{border-collapse:collapse}
  table td{font-size:9.0pt;mso-bidi-font-size:12.0pt;
  font-family:宋体;mso-ascii-font-family:"Times New Roman";mso-hansi-font-family:
  "Times New Roman";color:black; padding:1pt 4pt;}
    table td>p{ padding:1pt 0;}
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

        echo $this->styleCode;

        for($i=0;$i<count($this->itemList);$i++){

            $curSet=$this->itemList[$i];
            $curSet['bugRate']=$totalBugRate;
            $curSet['startIdx']=$curIdx;

            $t=new testCaseItem($curSet);
            $t->view();
            $curIdx=$t->curIdx;
            $totalB+=$t->totalBugAmount;
            $totalCase+=$t->totalAmount;
        }
        echo '<hr/>';
        echo "<p>bug total:$totalB</p>";
        echo "<p>case total:$totalCase</p>";
    }//-/

    /**
     * generate
     * generate text and return string.
     * @return string
     */
    public function generate(){
        $resultStr='';
        $curIdx=0;
        $totalB=0;
        $totalCase=0;
        $totalBugRate=$this->totalBugRate;
        for($i=0;$i<count($this->itemList);$i++){

            $curSet=$this->itemList[$i];
            $curSet['bugRate']=$totalBugRate;
            $curSet['startIdx']=$curIdx;

            $t=new testCaseItem($curSet);
            $resultStr.=$t->generate();
            $curIdx=$t->curIdx;
            $totalB+=$t->totalBugAmount;
            $totalCase+=$t->totalAmount;
        }


        return $resultStr;


    }//-/



}//==/class:testCaseBuilder