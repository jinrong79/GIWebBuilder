<?php
use j79frame\lib\doc\testCaseBuilder;
header("Content-Type: text/html; charset=utf-8");
require_once   dirname(__FILE__).DIRECTORY_SEPARATOR."global.config.php";

$setting1=array(

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

$tcb=new testCaseBuilder();
$tcb->totalBugRate=0.08;
$tcb->flagSaveFile=true;
$tcb->itemList=$setting1;
$tcb->view();

