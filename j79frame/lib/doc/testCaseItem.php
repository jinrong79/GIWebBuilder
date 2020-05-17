<?php
namespace j79frame\lib\doc;
use j79frame\lib\util\File;


/**
 * Class testCaseItem
 * single test case item class
 */
class testCaseItem{

    public $modelFileUrl='/caseModel1.html'; // modelFile url

    public $name=''; // function name
    public $type=0;  // normal   style
    /* 0- normal
       1- with thermodynamic
       99- custom
    */
    public $channel=''; //channnel name
    public $terminalName='终端'; //terminal name, default ="终端"
    public $des=''; //function des
    public $goal=''; //test goal
    public $cond=''; //test condition
    public $bugRate=0; //bug rate;
    public $records=array(); //test record

    public $curIdx=0; //item idx in current

    public $totalBugAmount=0; //current item total bug amount
    public $totalAmount=0; //current item total test case amount

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
                'label'=>'3DDW',
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
                'label'=>'3DDW',
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
                'label'=>'3DDW',
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
                'group'=>1,
                'label'=>'3DDW',
                'des'=>'在下雪场景中，##name##3D地图定位显示功能',
                'goal'=>'检查在下雪场景中##name##3D地图定位是否正常显示',
                'cond'=>'用户登录成功，进入##channel##频道，开启设定里面的下雪',
                'records'=>array(
                    array(
                        'act'=>'<p>点击左侧##name##图标</p>',
                        'expect'=>'<p>在3D场景中，显示多个带有##name##图标的立体定位标</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>显示有重叠现象，有问题！</p>','<p>远处的定位标，雪中看不清，有问题！</p>','<p>3D场景中没有任何变化，有问题！</p>'),
                    ),
                )
            ),

            array(
                'group'=>2,
                'label'=>'3DDJ',
                'des'=>'点击##name##定位图标，显示##name##详情页',
                'goal'=>'检查在3D场景中点击##name##3D定位标，能否在正确位置显示详情',
                'cond'=>'用户登录成功，进入##channel##频道，点击左侧##name##图标以后',
                'records'=>array(
                    array(
                        'act'=>'<p>点击3D场景中的任意一个##name##定位标</p>',
                        'expect'=>'<p>以鼠标点击点为起始点，弹出信息窗口，显示##name##详情信息</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>显示位置不对，有问题！</p>','<p>部分窗口被遮住，有问题！</p>','<p>3D场景中没有任何变化，有问题！</p>'),
                    ),

                )
            ),

            array(
                'group'=>2,
                'label'=>'3DDJ',
                'des'=>'点击新的##name##定位图标，会关闭已打开详情页窗口，再显示新的##name##详情页',
                'goal'=>'检查在3D场景中点击##name##3D定位标能否正确关闭已打开的详情页',
                'cond'=>'用户登录成功，进入##channel##频道，点击左侧##name##图标以后，点击一个，打开详情页',
                'records'=>array(
                    array(
                        'act'=>'<p>点击3D场景中的其他##name##定位标</p>',
                        'expect'=>'<p>关闭原来的详情页窗口，显示新的##name##详情信息窗口</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>原窗口关闭，但是新窗口位置还在原位置，有问题！</p>','<p>原有窗口不关闭，有问题！</p>'),
                    ),
                )
            ),


            array(
                'group'=>2,
                'label'=>'3DDJ',
                'des'=>'点击靠右侧的##name##定位图标，正确显示##name##详情页',
                'goal'=>'检查在3D场景中点击靠右侧的##name##3D定位标，能否在正确位置显示详情',
                'cond'=>'用户登录成功，进入##channel##频道，点击左侧##name##图标以后',
                'records'=>array(
                    array(
                        'act'=>'<p>点击3D场景中的靠近右侧的一个##name##定位标</p>',
                        'expect'=>'<p>以鼠标点击点为起始点，弹出信息窗口，显示##name##详情信息，但是信息窗口右侧不会越出屏幕</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>显示太靠右侧，部分窗口看不见，有问题！</p>','<p>部分窗口被遮住，有问题！</p>'),
                    ),
                )
            ),

            array(
                'group'=>2,
                'label'=>'3DDJ',
                'des'=>'点击靠底部的##name##定位图标，正确显示##name##详情页',
                'goal'=>'检查在3D场景中点击靠底部的##name##3D定位标，能否在正确位置显示详情',
                'cond'=>'用户登录成功，进入##channel##频道，点击左侧##name##图标以后',
                'records'=>array(
                    array(
                        'act'=>'<p>点击3D场景中的靠近底部的一个##name##定位标</p>',
                        'expect'=>'<p>以鼠标点击点为起始点，弹出信息窗口，显示##name##详情信息，但是信息窗口底部不会越出屏幕</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>显示位置不对，太靠下，部分窗口看不到，有问题！</p>','<p>部分窗口被遮住，有问题！</p>'),
                    ),
                )
            ),

            array(
                'group'=>2,
                'label'=>'3DDJ',
                'des'=>'弹出窗位置与常住信息窗重叠的##name##定位图标，点击后，##name##详情页显示在常驻信息窗之上',
                'goal'=>'检查弹出窗位置与常住信息窗重叠的##name##定位图标，能否在正确位置显示详情，不被遮挡',
                'cond'=>'用户登录成功，进入##channel##频道，点击左侧##name##图标以后',
                'records'=>array(
                    array(
                        'act'=>'<p>点击弹出窗位置与常住信息窗重叠的一个##name##定位标</p>',
                        'expect'=>'<p>以鼠标点击点为起始点，弹出信息窗口，并且不会被常驻信息窗遮住</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>部分窗口被遮住，有问题！</p>','<p>没有窗口弹出，有问题！</p>','<p>3D场景中没有任何变化，有问题！</p>'),
                    ),
                )
            ),


            array(
                'group'=>2,
                'label'=>'3DDJ',
                'des'=>'在群选模式下，点击##name##定位图标，不显示##name##详情页',
                'goal'=>'检查在多选模式下，点击##name##3D定位标，能否正常屏蔽详情显示',
                'cond'=>'用户登录成功，进入##channel##频道，点击左侧##name##图标，切换群选模式',
                'records'=>array(
                    array(
                        'act'=>'<p>点击3D场景中的任意一个##name##定位标</p>',
                        'expect'=>'<p>没有弹出##name##信息窗</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>显示##name##详情页，有问题！</p>'),
                    ),

                )
            ),


            array(
                'group'=>3,
                'label'=>'XQ',
                'des'=>'##name##详情页中,显示用户手册指明的完整内容',
                'goal'=>'检查##name##详情页中,是否显示完整内容',
                'cond'=>'用户登录成功，进入##channel##频道，点击左侧##name##图标以后，点击任意3D定位标，弹出详情窗口。',
                'records'=>array(
                    array(
                        'act'=>'<p>点击任意##name##3D定位标，弹出详情窗口，详细查看</p>',
                        'expect'=>'<p>显示用户手册指明的完整内容</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>缺少标题，有问题！</p>','<p>标题正常，主内容区域空白，获取不到数据，有问题！</p>','<p>整个窗口空白，有问题！</p>'),
                    ),
                )
            ),

            array(
                'group'=>3,
                'label'=>'XQ',
                'des'=>'##name##详情页中,显示的内容位置要正常',
                'goal'=>'检查##name##详情页中,内容的显示位置是否正常',
                'cond'=>'用户登录成功，进入##channel##频道，点击左侧##name##图标以后，点击任意3D定位标，弹出详情窗口。',
                'records'=>array(
                    array(
                        'act'=>'<p>点击任意##name##3D定位标，弹出详情窗口，查看信息显示位置</p>',
                        'expect'=>'<p>信息都在正确位置显示，信息对齐</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>标题与标题栏不对齐，有问题！</p>','<p>主内容区域与标题，不对齐，有问题！</p>'),
                    ),
                )
            ),

            array(
                'group'=>3,
                'label'=>'XQ',
                'des'=>'##name##详情页中,显示的内容全部在窗口区域，没有溢出',
                'goal'=>'检查##name##详情页中,内容是否溢出窗口',
                'cond'=>'用户登录成功，进入##channel##频道，点击左侧##name##图标以后，点击任意3D定位标，弹出详情窗口。',
                'records'=>array(
                    array(
                        'act'=>'<p>点击任意##name##3D定位标，弹出详情窗口，查看信息</p>',
                        'expect'=>'<p>信息都在窗口内部，没有溢出窗口现象</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>标题太长，溢出窗口，有问题！</p>','<p>主内容区域溢出窗口，有问题！</p>'),
                    ),
                )
            ),

            array(
                'group'=>3,
                'label'=>'XQ',
                'des'=>'##name##详情页中,显示的内容格式要正常',
                'goal'=>'检查##name##详情页中,检查内容的显示格式是否正确',
                'cond'=>'用户登录成功，进入##channel##频道，点击左侧##name##图标以后，点击任意3D定位标，弹出详情窗口。',
                'records'=>array(
                    array(
                        'act'=>'<p>点击任意##name##3D定位标，弹出详情窗口，查看信息的格式</p>',
                        'expect'=>'<p>信息的格式与用户手册相符，不出现负值或0等非法值</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>标题中有非法字符，有问题！</p>','<p>主内容区域有非法字符，有问题！</p>','<p>内容空白，有问题！</p>'),
                    ),
                )
            ),

            array(
                'group'=>4,
                'label'=>'XQ',
                'des'=>'##name##详情页，可以拖动',
                'goal'=>'检查##name##详情页窗口是否可以拖动',
                'cond'=>'用户登录成功，进入##channel##频道，点击左侧##name##图标以后，点击任意3D定位标，弹出详情窗口。',
                'records'=>array(
                    array(
                        'act'=>'<p>鼠标按住##name##详情窗口顶部，进行拖动</p>',
                        'expect'=>'<p>详情窗口可以跟着鼠标移动，形成拖动效果</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>拖动过程会中断，有问题！</p>','<p>拖动时看不到窗口，有问题！</p>','<p>没有反应，有问题！</p>'),
                    ),
                )
            ),

            array(
                'group'=>4,
                'label'=>'XQ',
                'des'=>'##name##详情页，可以点击关闭',
                'goal'=>'检查##name##详情页窗口是否可以点击关闭',
                'cond'=>'用户登录成功，进入##channel##频道，点击左侧##name##图标以后，点击任意3D定位标，弹出详情窗口。',
                'records'=>array(
                    array(
                        'act'=>'<p>点击##name##详情页窗口右上角关闭按钮</p>',
                        'expect'=>'<p>##name##详情窗口关闭</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>点击有反应，但是不关闭，有问题！</p>','<p>无法点击，没有反应，有问题！</p>'),
                    ),
                )
            ),

            array(
                'group'=>4,
                'label'=>'XQ',
                'des'=>'##name##详情页，拖动顺滑',
                'goal'=>'检查##name##详情页窗口拖动是否顺滑',
                'cond'=>'用户登录成功，进入##channel##频道，点击左侧##name##图标以后，点击任意3D定位标，弹出详情窗口。',
                'records'=>array(
                    array(
                        'act'=>'<p>鼠标按住##name##详情窗口顶部，进行拖动</p>',
                        'expect'=>'<p>##name##详情窗口拖动顺滑，不发生卡顿</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>拖动有延迟现象，有问题！</p>','<p>拖动过程会脱位，发生中断，有问题！</p>'),
                    ),
                )
            ),


        ), //type0

        //type1: 热力图
        'type1'=>array(

            array(

                'group'=>1,
                'label'=>'3DRLXS',
                'des'=>'在3D场景中，##name##显示热力图',
                'goal'=>'检查在3D场景中##name##热力图是否正常显示',
                'cond'=>'用户登录成功，进入##channel##频道',
                'records'=>array(
                    array(
                        'act'=>'<p>点击左侧##name##图标</p>',
                        'expect'=>'<p>在3D场景中，显示热力图</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>没有显示热力图，有定位标，有问题！</p>','<p>3D场景中没有任何变化，有问题！</p>'),
                    ),
                )
            ),

            array(

                'group'=>1,
                'label'=>'3DRLXS',
                'des'=>'在3D场景中，##name##显示热力图时，其他模型为冰面模式',
                'goal'=>'检查在3D场景中显示##name##热力图，其他场景模型是否为冰面模式。',
                'cond'=>'用户登录成功，进入##channel##频道',
                'records'=>array(
                    array(
                        'act'=>'<p>点击左侧##name##图标</p>',
                        'expect'=>'<p>在3D场景中，显示热力图时，其他场景模型换为冰面模式</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>只有建筑是冰面，地面和道路没有冰面效果，有问题！</p>','<p>只有地面和道路是冰面，建筑没有冰面效果，有问题！</p>','<p>没有显示冰面效果，有问题！</p>'),
                    ),
                )
            ),

            array(

                'group'=>1,
                'label'=>'3DRLXS',
                'des'=>'在3D场景中，##name##显示热力图时，显示定位标',
                'goal'=>'检查在3D场景中显示##name##热力图，是否显示定位标。',
                'cond'=>'用户登录成功，进入##channel##频道',
                'records'=>array(
                    array(
                        'act'=>'<p>点击左侧##name##图标</p>',
                        'expect'=>'<p>在3D场景中，显示热力图时，还有定位标显示</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>没有定位标，有问题！</p>'),
                    ),
                )
            ),
            array(
                'group'=>2,
                'label'=>'3DDJ',
                'des'=>'点击##name##定位图标，显示##name##详情页',
                'goal'=>'检查在3D场景中点击##name##3D定位标，能否在正确位置显示详情',
                'cond'=>'用户登录成功，进入##channel##频道，点击左侧##name##图标以后',
                'records'=>array(
                    array(
                        'act'=>'<p>点击3D场景中的任意一个##name##定位标</p>',
                        'expect'=>'<p>以鼠标点击点为起始点，弹出信息窗口，显示##name##详情信息</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>显示位置不对，有问题！</p>','<p>部分窗口被遮住，有问题！</p>','<p>3D场景中没有任何变化，有问题！</p>'),
                    ),
                    array(
                        'act'=>'<p>点击3D场景中的靠近右侧的一个##name##定位标</p>',
                        'expect'=>'<p>以鼠标点击点为起始点，弹出信息窗口，显示##name##详情信息，但是信息窗口右侧不会越出屏幕</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>显示位置不对，有问题！</p>','<p>部分窗口被遮住，有问题！</p>','<p>3D场景中没有任何变化，有问题！</p>'),
                    ),
                )
            ),

            array(
                'group'=>2,
                'label'=>'3DDJ',
                'des'=>'点击新的##name##定位图标，会关闭已打开详情页窗口，再显示新的##name##详情页',
                'goal'=>'检查在3D场景中点击##name##3D定位标能否正确关闭已打开的详情页',
                'cond'=>'用户登录成功，进入##channel##频道，点击左侧##name##图标以后，点击一个，打开详情页',
                'records'=>array(
                    array(
                        'act'=>'<p>点击3D场景中的其他##name##定位标</p>',
                        'expect'=>'<p>关闭原来的详情页窗口，显示新的##name##详情信息窗口</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>原窗口关闭，但是新窗口位置还在原位置，有问题！</p>','<p>原有窗口不关闭，有问题！</p>'),
                    ),
                )
            ),


            array(
                'group'=>2,
                'label'=>'3DDJ',
                'des'=>'点击靠右侧的##name##定位图标，正确显示##name##详情页',
                'goal'=>'检查在3D场景中点击靠右侧的##name##3D定位标，能否在正确位置显示详情',
                'cond'=>'用户登录成功，进入##channel##频道，点击左侧##name##图标以后',
                'records'=>array(
                    array(
                        'act'=>'<p>点击3D场景中的靠近右侧的一个##name##定位标</p>',
                        'expect'=>'<p>以鼠标点击点为起始点，弹出信息窗口，显示##name##详情信息，但是信息窗口右侧不会越出屏幕</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>显示太靠右侧，部分窗口看不见，有问题！</p>','<p>部分窗口被遮住，有问题！</p>'),
                    ),
                )
            ),

            array(
                'group'=>2,
                'label'=>'3DDJ',
                'des'=>'点击靠底部的##name##定位图标，正确显示##name##详情页',
                'goal'=>'检查在3D场景中点击靠底部的##name##3D定位标，能否在正确位置显示详情',
                'cond'=>'用户登录成功，进入##channel##频道，点击左侧##name##图标以后',
                'records'=>array(
                    array(
                        'act'=>'<p>点击3D场景中的靠近底部的一个##name##定位标</p>',
                        'expect'=>'<p>以鼠标点击点为起始点，弹出信息窗口，显示##name##详情信息，但是信息窗口底部不会越出屏幕</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>显示位置不对，太靠下，部分窗口看不到，有问题！</p>','<p>部分窗口被遮住，有问题！</p>'),
                    ),
                )
            ),

            array(
                'group'=>2,
                'label'=>'3DDJ',
                'des'=>'弹出窗位置与常住信息窗重叠的##name##定位图标，点击后，##name##详情页显示在常驻信息窗之上',
                'goal'=>'检查弹出窗位置与常住信息窗重叠的##name##定位图标，能否在正确位置显示详情，不被遮挡',
                'cond'=>'用户登录成功，进入##channel##频道，点击左侧##name##图标以后',
                'records'=>array(
                    array(
                        'act'=>'<p>点击弹出窗位置与常住信息窗重叠的一个##name##定位标</p>',
                        'expect'=>'<p>以鼠标点击点为起始点，弹出信息窗口，并且不会被常驻信息窗遮住</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>部分窗口被遮住，有问题！</p>','<p>没有窗口弹出，有问题！</p>','<p>3D场景中没有任何变化，有问题！</p>'),
                    ),
                )
            ),


            array(
                'group'=>2,
                'label'=>'3DDJ',
                'des'=>'在群选模式下，点击##name##定位图标，不显示##name##详情页',
                'goal'=>'检查在多选模式下，点击##name##3D定位标，能否正常屏蔽详情显示',
                'cond'=>'用户登录成功，进入##channel##频道，点击左侧##name##图标，切换群选模式',
                'records'=>array(
                    array(
                        'act'=>'<p>点击3D场景中的任意一个##name##定位标</p>',
                        'expect'=>'<p>没有弹出##name##信息窗</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>显示##name##详情页，有问题！</p>'),
                    ),

                )
            ),


            array(
                'group'=>3,
                'label'=>'XQ',
                'des'=>'##name##详情页中,显示用户手册指明的完整内容',
                'goal'=>'检查##name##详情页中,是否显示完整内容',
                'cond'=>'用户登录成功，进入##channel##频道，点击左侧##name##图标以后，点击任意3D定位标，弹出详情窗口。',
                'records'=>array(
                    array(
                        'act'=>'<p>点击任意##name##3D定位标，弹出详情窗口，详细查看</p>',
                        'expect'=>'<p>显示用户手册指明的完整内容</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>缺少标题，有问题！</p>','<p>标题正常，主内容区域空白，获取不到数据，有问题！</p>','<p>整个窗口空白，有问题！</p>'),
                    ),
                )
            ),

            array(
                'group'=>3,
                'label'=>'XQ',
                'des'=>'##name##详情页中,显示的内容位置要正常',
                'goal'=>'检查##name##详情页中,内容的显示位置是否正常',
                'cond'=>'用户登录成功，进入##channel##频道，点击左侧##name##图标以后，点击任意3D定位标，弹出详情窗口。',
                'records'=>array(
                    array(
                        'act'=>'<p>点击任意##name##3D定位标，弹出详情窗口，查看信息显示位置</p>',
                        'expect'=>'<p>信息都在正确位置显示，信息对齐</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>标题与标题栏不对齐，有问题！</p>','<p>主内容区域与标题，不对齐，有问题！</p>'),
                    ),
                )
            ),

            array(
                'group'=>3,
                'label'=>'XQ',
                'des'=>'##name##详情页中,显示的内容全部在窗口区域，没有溢出',
                'goal'=>'检查##name##详情页中,内容是否溢出窗口',
                'cond'=>'用户登录成功，进入##channel##频道，点击左侧##name##图标以后，点击任意3D定位标，弹出详情窗口。',
                'records'=>array(
                    array(
                        'act'=>'<p>点击任意##name##3D定位标，弹出详情窗口，查看信息</p>',
                        'expect'=>'<p>信息都在窗口内部，没有溢出窗口现象</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>标题太长，溢出窗口，有问题！</p>','<p>主内容区域溢出窗口，有问题！</p>'),
                    ),
                )
            ),

            array(
                'group'=>3,
                'label'=>'XQ',
                'des'=>'##name##详情页中,显示的内容格式要正常',
                'goal'=>'检查##name##详情页中,检查内容的显示格式是否正确',
                'cond'=>'用户登录成功，进入##channel##频道，点击左侧##name##图标以后，点击任意3D定位标，弹出详情窗口。',
                'records'=>array(
                    array(
                        'act'=>'<p>点击任意##name##3D定位标，弹出详情窗口，查看信息的格式</p>',
                        'expect'=>'<p>信息的格式与用户手册相符，不出现负值或0等非法值</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>标题中有非法字符，有问题！</p>','<p>主内容区域有非法字符，有问题！</p>','<p>内容空白，有问题！</p>'),
                    ),
                )
            ),


        ),//type1

        //type2： login
        'type2'=>array(
            array(
                'group'=>1,
                'label'=>'XS',
                'des'=>'##name##UI显示正常',
                'goal'=>'检查##name##UI是否正常',
                'cond'=>'未登录状态下，进入系统,出现##name##窗口',
                'records'=>array(
                    array(
                        'act'=>'<p>进入系统</p>',
                        'expect'=>'<p>##name##UI显示正常,提示信息也正常</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>没有提示信息,无法区分哪些是密码输入框，哪些是账号输入，有问题</p>'),
                    ),
                )
            ),

            array(
                'group'=>2,
                'label'=>'SR',
                'des'=>'##name##UI所有输入正常',
                'goal'=>'检查##name##UI是否所有输入正常',
                'cond'=>'未登录状态下，进入系统，出现##name##窗口',
                'records'=>array(
                    array(
                        'act'=>'<p>进入系统</p>',
                        'expect'=>'<p>##name##UI输入正常</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>密码输入框，显示明文，有问题</p>','<p>账号框无法输入，没有反应，有问题</p>'),
                    ),
                )
            ),

            array(
                'group'=>2,
                'label'=>'SR',
                'des'=>'##name##UI输入出错，正确提醒',
                'goal'=>'检查##name##UI输入出错是否正确提醒',
                'cond'=>'未登录状态下，进入系统，出现##name##窗口',
                'records'=>array(
                    array(
                        'act'=>'<p>进入系统，出现##name##窗口，输入错误账号密码</p>',
                        'expect'=>'<p>##name##UI正确提醒错误信息。</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>没有提醒，只是无法登录，有问题</p>','<p>没有提醒，直接进入系统，有问题</p>'),
                    ),
                )
            ),

            array(
                'group'=>2,
                'label'=>'SR',
                'des'=>'##name##UI输入正确，进入系统',
                'goal'=>'检查##name##UI输入正确时，是否正确关闭登录框，进入系统',
                'cond'=>'未登录状态下，进入系统，出现##name##窗口',
                'records'=>array(
                    array(
                        'act'=>'<p>进入系统，出现##name##窗口，输入正确账号密码</p>',
                        'expect'=>'<p>##name##UI正确关闭窗口，进入系统。</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>进入系统，但是登录框没有消失，有问题</p>','<p>没有反应，有问题</p>'),
                    ),
                )
            ),

            array(
                'group'=>2,
                'label'=>'TC',
                'des'=>'##name##，可以正确退出',
                'goal'=>'检查点击退出登录，能否正确退出系统，回到登录窗口状态',
                'cond'=>'进入系统，登录成功',
                'records'=>array(
                    array(
                        'act'=>'<p>进入系统，登录成功后，点击退出登录</p>',
                        'expect'=>'<p>##name##正确退出，并且返回登录窗口。</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>退出系统后，没有回到登录窗口。有问题</p>','<p>没有反应，有问题</p>'),
                    ),
                )
            ),









        ),//-/type2

        //type3  : setting
        'type3'=>array(

            array(
                'group'=>1,
                'label'=>'XSTB',
                'des'=>'##channel##-##name##正常显示同步状态',
                'goal'=>'检查##channel##-##name##,是否正常显示同步状态',
                'cond'=>'点击右上角##channel##',
                'records'=>array(
                    array(
                        'act'=>'<p>点击右上角##channel##，查看##name##同步选项显示状态</p>',
                        'expect'=>'<p>##channel##-##name##同步状态显示正常</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>与当前同步状态不符合，有问题</p>'),
                    ),
                )
            ),

            array(
                'group'=>2,
                'label'=>'QH',
                'des'=>'##channel##-##name##正常切换效果',
                'goal'=>'检查##channel##-##name##,是否能正确切换效果',
                'cond'=>'点击右上角##channel##',
                'records'=>array(
                    array(
                        'act'=>'<p>点击右上角##channel##，打开窗口，点击切换##name##</p>',
                        'expect'=>'<p>##channel##-##name##切换，在3D场景中正确提现</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>与当前想过不符合，有问题</p>','<p>3D场景没变化，有问题</p>'),
                    ),
                )
            ),

            array(
                'group'=>2,
                'label'=>'QH',
                'des'=>'##channel##-##name##手动切换效果时，同步自动关闭',
                'goal'=>'检查##channel##-##name##,手动切换效果时，是否自动关闭同步',
                'cond'=>'点击右上角##channel##',
                'records'=>array(
                    array(
                        'act'=>'<p>点击右上角##channel##，打开窗口，点击切换##name##</p>',
                        'expect'=>'<p>##channel##-##name##切换时，自动关闭同步</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>没有关闭同步，有问题</p>'),
                    ),
                )
            ),

            array(
                'group'=>2,
                'label'=>'QH',
                'des'=>'##channel##-##name##点击打开同步，自动切换效果为同步获取的##name##',
                'goal'=>'检查##channel##-##name##,点击打开同步时，是否自动切换回当前同步获取的##name##',
                'cond'=>'点击右上角##channel##',
                'records'=>array(
                    array(
                        'act'=>'<p>点击右上角##channel##，打开窗口，先点击手动切换为其他##name##，再点击打开##name##自动同步</p>',
                        'expect'=>'<p>自动切换回当前同步获取的##name##</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>没有同步，还停留在原来的##name##，有问题</p>'),
                    ),
                )
            ),


        ),//type3

        //type4:   search
        'type4'=>array(
            array(
                'group'=>1,
                'label'=>'XSJG',
                'des'=>'##name##显示结果列表',
                'goal'=>'##name##,是否正常显示结果列表',
                'cond'=>'在##channel##右上角，查询',
                'records'=>array(
                    array(
                        'act'=>'<p>在##channel##右上角，输入编号，查询</p>',
                        'expect'=>'<p>##name##结果列表显示正常</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>没有结果列表，也没有提示无结果。有问题</p>'),
                    ),

                )
            ),
            array(
                'group'=>1,
                'label'=>'DJJG',
                'des'=>'##name##点击结果列表，在3D场景中显示定位标，并且移动到结果位置',
                'goal'=>'##name##结果列表，点击后,是否正常显示3D场景中的定位标和视角移动过去',
                'cond'=>'在##channel##右上角，查询',
                'records'=>array(
                    array(
                        'act'=>'<p>点击##name##结果列表中的一项</p>',
                        'expect'=>'<p>在3D场景中显示相应定位标，并且视角移动到其位置上</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>没有显示3D定位标。有问题</p>','<p>视角没有移动过去。有问题</p>'),
                    ),

                )
            )
        ),//type4


        //type5: 视频预案
        'type5'=>array(
            array(
                'group'=>1,
                'label'=>'LB',
                'des'=>'系统的右侧，显示##name##列表',
                'goal'=>'##name##列表，是否正常显示',
                'cond'=>'系统登录后，进入##channel##',
                'records'=>array(
                    array(
                        'act'=>'<p>系统登录后，进入##channel##频道</p>',
                        'expect'=>'<p>系统的右侧，正确显示##name##列表</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>右侧没有列表显示，有问题</p>'),
                    ),

                )
            ),
            array(
                'group'=>2,
                'label'=>'XQ',
                'des'=>'点击##name##列表中的一项，顺序显示视频监控的画面',
                'goal'=>'##name##列表，点击是否正常显示详情',
                'cond'=>'系统登录后，进入##channel##',
                'records'=>array(
                    array(
                        'act'=>'<p>系统登录后，进入##channel##频道，点击##name##列表中的一项</p>',
                        'expect'=>'<p>系统的右侧，正确显示##name##的视频监控，并且顺序切换。3D场景中的定位标也在顺序变化当前颜色。</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>右侧当前视频没有显示，有问题</p>','<p>3D场景中的定位标，没有显示当前状态，有问题</p>','<p>3D场景中的定位标，没有顺序切换，有问题</p>'),
                    ),

                )
            ),

            array(
                'group'=>3,
                'label'=>'TJ',
                'des'=>'##name##列表上，有添加按钮，点击后切换添加新##name##的UI',
                'goal'=>'##name##列表，是否正常显示添加按钮，点击后，是否正常切换添加UI',
                'cond'=>'系统登录后，进入##channel##',
                'records'=>array(
                    array(
                        'act'=>'<p>系统登录后，进入##channel##频道，点击##name##列表上侧的添加按钮</p>',
                        'expect'=>'<p>切换到添加##name##的UI</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>没有显示添加按钮，有问题</p>','<p>没有切换添加UI，有问题</p>'),
                    ),

                )
            ),
            array(
                'group'=>3,
                'label'=>'TJ',
                'des'=>'添加新##name##时，在添加UI，输入新##name##名称，多选监控，点击保存，正常添加新的##name##',
                'goal'=>'##name##添加UI，是否正常运行',
                'cond'=>'系统登录后，进入##channel##，点击添加',
                'records'=>array(
                    array(
                        'act'=>'<p>系统登录后，进入##channel##频道，点击##name##列表上侧的添加按钮，在添加UI上输入视频预案的名称，多选3D场景中的视频监控定位标，最后点击保存</p>',
                        'expect'=>'<p>正确添加##name##，并且切换回列表UI</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>无法多选视频监控，有问题</p>','<p>选择的视频监控个数不符合，有问题</p>','<p>点击保存后，没有切换回列表UI，有问题</p>'),
                    ),

                )
            ),

            array(
                'group'=>4,
                'label'=>'SC',
                'des'=>'点击##name##列表中的一项，点击删除，弹出框询问确认删除，确认后删除当前选中项',
                'goal'=>'##name##，是否能够正确删除选中项',
                'cond'=>'系统登录后，进入##channel##，',
                'records'=>array(
                    array(
                        'act'=>'<p>点击任一个##name##的删除按钮</p>',
                        'expect'=>'<p>正确提示删除确认，点击确认后，正确删除##name##的选中项</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>没有删除确认提醒，有问题</p>','<p>删除后没有效果，还在，有问题</p>'),
                    ),

                )
            ),


        ),
        //-/type5: 视频预案


        //type6: 3D场景导航
        'type6'=>array(
            array(
                'group'=>1,
                'label'=>'YD',
                'des'=>'键盘W，平行于地面向前移动',
                'goal'=>'##name##时，按住键盘W是否能够正确前进',
                'cond'=>'系统登录',
                'records'=>array(
                    array(
                        'act'=>'<p>按住键盘W</p>',
                        'expect'=>'<p>平行于地面向前前进</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>没有平行于地面移动，有问题</p>'),
                    ),

                )
            ),

            array(
                'group'=>1,
                'label'=>'YD',
                'des'=>'键盘S，平行于地面往后移动',
                'goal'=>'##name##时，按住键盘S是否能够正确后退',
                'cond'=>'系统登录',
                'records'=>array(
                    array(
                        'act'=>'<p>按住键盘S</p>',
                        'expect'=>'<p>平行于地面往后移动</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>没有平行于地面移动，有问题</p>'),
                    ),

                )
            ),

            array(
                'group'=>1,
                'label'=>'YD',
                'des'=>'键盘A，平行于地面往左移动',
                'goal'=>'##name##时，按住键盘A是否能够正确往左移动',
                'cond'=>'系统登录',
                'records'=>array(
                    array(
                        'act'=>'<p>按住键盘A</p>',
                        'expect'=>'<p>平行于地面往左移动</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>没有平行于地面移动，有问题</p>'),
                    ),

                )
            ),
            array(
                'group'=>1,
                'label'=>'YD',
                'des'=>'键盘D，平行于地面往右移动',
                'goal'=>'##name##时，按住键盘D是否能够正确往右移动',
                'cond'=>'系统登录',
                'records'=>array(
                    array(
                        'act'=>'<p>按住键盘D</p>',
                        'expect'=>'<p>平行于地面往右移动</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>没有平行于地面移动，有问题</p>'),
                    ),

                )
            ),

            array(
                'group'=>2,
                'label'=>'SJ',
                'des'=>'键盘Q，垂直提升高度',
                'goal'=>'##name##时，按住键盘Q是否能够正确垂直提升高度',
                'cond'=>'系统登录',
                'records'=>array(
                    array(
                        'act'=>'<p>按住键盘Q</p>',
                        'expect'=>'<p>垂直提升高度</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>没有垂直与地面移动，有问题</p>'),
                    ),

                )
            ),

            array(
                'group'=>2,
                'label'=>'SJ',
                'des'=>'键盘E，垂直降低高度',
                'goal'=>'##name##时，按住键盘E是否能够正确垂直降低高度',
                'cond'=>'系统登录',
                'records'=>array(
                    array(
                        'act'=>'<p>按住键盘E</p>',
                        'expect'=>'<p>垂直降低高度</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>没有垂直与地面移动，有问题</p>'),
                    ),

                )
            ),


            array(
                'group'=>3,
                'label'=>'FX',
                'des'=>'通过鼠标，可以转换视角方向',
                'goal'=>'##name##时，是否能够通过鼠标转换视角方向',
                'cond'=>'系统登录',
                'records'=>array(
                    array(
                        'act'=>'<p>使用鼠标各向移动</p>',
                        'expect'=>'<p>根据鼠标移动，转换视角方向</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>没有转换视角方向，有问题</p>'),
                    ),

                )
            ),

            array(
                'group'=>3,
                'label'=>'FX',
                'des'=>'移动中，通过鼠标，可以转换方向',
                'goal'=>'##name##时，移动中，是否能够通过鼠标转换方向',
                'cond'=>'系统登录',
                'records'=>array(
                    array(
                        'act'=>'<p>按住键盘移动中，使用鼠标控制方向</p>',
                        'expect'=>'<p>根据鼠标，换移动方向</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>没有根据换向，有问题</p>','<p>换向与鼠标移动反向，有问题</p>'),
                    ),

                )
            ),


        ),//-/type6





        /*--- backend ----*/

        //type100 -> backend
        'type100'=>array(

            array(
                'group'=>1,
                'label'=>'LB',
                'des'=>'后台-##name##列表正常显示',
                'goal'=>'检查##name##列表,是否正常显示',
                'cond'=>'管理员后台登录成功。',
                'records'=>array(
                    array(
                        'act'=>'<p>点击导航区域中的##name##频道，查看##name##列表</p>',
                        'expect'=>'<p>##name##列表显示正常，所有字段都显示正常</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>列表字段有缺失，有问题</p>','<p>列表空白，有问题！</p>'),
                    ),
                )
            ),
            array(
                'group'=>1,
                'label'=>'LB',
                'des'=>'后台-##name##列表有翻页功能',
                'goal'=>'检查##name##列表是否翻页正常',
                'cond'=>'管理员后台登录成功。',
                'records'=>array(
                    array(
                        'act'=>'<p>点击导航区域中的##name##频道，考察##name##列表的翻页功能</p>',
                        'expect'=>'<p>##name##列表显示翻页按钮，点击正常翻页</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>翻页点击后，页数不准，有问题</p>','<p>翻页点击后，没有反应，有问题</p>','<p>没有翻页，有问题！</p>'),
                    ),
                )
            ),
            array(
                'group'=>1,
                'label'=>'LB',
                'des'=>'后台-##name##列表的相关操作UI：添加，删除，修改',
                'goal'=>'检查##name##列表相关操作UI是否正常',
                'cond'=>'管理员后台登录成功。',
                'records'=>array(
                    array(
                        'act'=>'<p>点击导航区域中的##name##频道，查看##name##列表相关操作UI</p>',
                        'expect'=>'<p>##name##列表相关操作UI：添加，删除，修改等正常显示与互动</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>没有添加按钮，有问题！</p>','<p>没有删除按钮，有问题！</p>','<p>没有修改按钮，有问题！</p>'),
                    ),
                )
            ),
            array(
                'group'=>1,
                'label'=>'LB',
                'des'=>'后台-##name##列表的相关操作-删除,有确认过程，防止误删',
                'goal'=>'检查##name##列表的相关操作-删除,是否有确认过程',
                'cond'=>'管理员后台登录成功。',
                'records'=>array(
                    array(
                        'act'=>'<p>点击导航区域中的##name##频道，显示##name##列表，点击删除</p>',
                        'expect'=>'<p>弹出对话框，确认是否确认删除。</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>弹出对话框，点击取消时，关闭不了，有问题！</p>','<p>点击取消，还是删除了，有问题！</p>','<p>没有对话框，直接删除了，有问题！</p>'),
                    ),
                )
            ),

            array(
                'group'=>1,
                'label'=>'LB',
                'des'=>'后台-##name##列表的相关操作-群体删除',
                'goal'=>'检查##name##列表的相关操作-群体删除,是否正常',
                'cond'=>'管理员后台登录成功。',
                'records'=>array(
                    array(
                        'act'=>'<p>点击导航区域中的##name##频道，显示##name##列表，点选多个项目，点击群体删除</p>',
                        'expect'=>'<p>弹出对话框，确认是否确认删除；确认时，正确删除多个##name##记录。</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>点击取消，还是删除了，有问题！</p>','<p>只删除第一项，有问题！</p>','<p>没有对话框，直接删除了，有问题！</p>','<p>没有删除，无效，有问题！</p>'),
                    ),
                )
            ),

        ),//type100


        //type101 -> backend-list only
        'type101'=>array(
            array(
                'group'=>1,
                'label'=>'LB',
                'des'=>'后台-##name##列表正常显示',
                'goal'=>'检查##name##列表,是否正常显示',
                'cond'=>'管理员后台登录成功。',
                'records'=>array(
                    array(
                        'act'=>'<p>点击导航区域中的##name##频道，查看##name##列表</p>',
                        'expect'=>'<p>##name##列表显示正常，所有字段都显示正常</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>列表字段有缺失，有问题</p>','<p>列表空白，有问题！</p>'),
                    ),
                )
            ),
            array(
                'group'=>1,
                'label'=>'LB',
                'des'=>'后台-##name##列表有翻页功能',
                'goal'=>'检查##name##列表是否翻页正常',
                'cond'=>'管理员后台登录成功。',
                'records'=>array(
                    array(
                        'act'=>'<p>点击导航区域中的##name##频道，考察##name##列表的翻页功能</p>',
                        'expect'=>'<p>##name##列表显示翻页按钮，点击正常翻页</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>翻页点击后，页数不准，有问题</p>','<p>翻页点击后，没有反应，有问题</p>','<p>没有翻页，有问题！</p>'),
                    ),
                )
            ),
            array(
                'group'=>1,
                'label'=>'LB',
                'des'=>'后台-##name##列表的相关操作UI：删除',
                'goal'=>'检查##name##列表相关操作UI是否正常',
                'cond'=>'管理员后台登录成功。',
                'records'=>array(
                    array(
                        'act'=>'<p>点击导航区域中的##name##频道，查看##name##列表相关操作UI</p>',
                        'expect'=>'<p>##name##列表相关操作UI：删除正常显示与互动</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>没有添加按钮，有问题！</p>','<p>没有删除按钮，有问题！</p>','<p>没有修改按钮，有问题！</p>'),
                    ),
                )
            ),
            array(
                'group'=>1,
                'label'=>'LB',
                'des'=>'后台-##name##列表的相关操作-删除,有确认过程，防止误删',
                'goal'=>'检查##name##列表的相关操作-删除,是否有确认过程',
                'cond'=>'管理员后台登录成功。',
                'records'=>array(
                    array(
                        'act'=>'<p>点击导航区域中的##name##频道，显示##name##列表，点击删除</p>',
                        'expect'=>'<p>弹出对话框，确认是否确认删除。</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>弹出对话框，点击取消时，关闭不了，有问题！</p>','<p>点击取消，还是删除了，有问题！</p>','<p>没有对话框，直接删除了，有问题！</p>'),
                    ),
                )
            ),

            array(
                'group'=>1,
                'label'=>'LB',
                'des'=>'后台-##name##列表的相关操作-群体删除',
                'goal'=>'检查##name##列表的相关操作-群体删除,是否正常',
                'cond'=>'管理员后台登录成功。',
                'records'=>array(
                    array(
                        'act'=>'<p>点击导航区域中的##name##频道，显示##name##列表，点选多个项目，点击群体删除</p>',
                        'expect'=>'<p>弹出对话框，确认是否确认删除；确认时，正确删除多个##name##记录。</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>点击取消，还是删除了，有问题！</p>','<p>只删除第一项，有问题！</p>','<p>没有对话框，直接删除了，有问题！</p>','<p>没有删除，无效，有问题！</p>'),
                    ),
                )
            ),

        ),//type101

        //type102,backend setting:
        'type102'=>array(
            array(
                'group'=>1,
                'label'=>'XS',
                'des'=>'后台-##name##页面显示正常',
                'goal'=>'检查##name##页面是否正确显示',
                'cond'=>'管理员后台登录成功。',
                'records'=>array(
                    array(
                        'act'=>'<p>点击导航区域中的##name##频道，显示##name##页面</p>',
                        'expect'=>'<p>所有##name##项目正确显示</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>缺少项目，有问题</p>','<p>页面出现空白，有问题！</p>'),
                    ),
                )
            ),
            array(
                'group'=>1,
                'label'=>'QH',
                'des'=>'后台-##name##选项切换正常',
                'goal'=>'检查##name##选项是否正确切换',
                'cond'=>'管理员后台登录成功。',
                'records'=>array(
                    array(
                        'act'=>'<p>点击导航区域中的##name##频道，显示##name##页面，点击其中的选项来切换</p>',
                        'expect'=>'<p>所有##name##项目切换交互正确</p>',
                        'resultSuccess'=>'<p>正常！</p>',
                        'resultFail'=>array('<p>交互没有反应，有问题</p>','<p>切换不准确，有问题！</p>'),
                    ),
                )
            ),
        ),//-/type102

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

            $this->terminalName=\GF::getKey("terminal",$params,"终端");

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
            $this->totalAmount++;
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
                $curRecordBlank=$curRecord;

                //make bug
                if(mt_rand(1,100)<= round($this->bugRate*100)){
                    $this->totalBugAmount++;
                    $curErrorRecList=\GF::getKey("resultFail",$recordItem);
                    $curRecord=str_replace("##real##",$curErrorRecList[mt_rand(0,count($curErrorRecList)-1)], $curRecordBlank);

                    if(mt_rand(1,5)<= 2){
                        $this->totalBugAmount++;
                        $curRecord.=str_replace("##real##",$curErrorRecList[mt_rand(0,count($curErrorRecList)-1)], $curRecordBlank);
                    }
                    if(mt_rand(1,5)<= 2){
                        $this->totalBugAmount++;
                        $curRecord.=str_replace("##real##",$curErrorRecList[mt_rand(0,count($curErrorRecList)-1)], $curRecordBlank);
                    }

                    $curRecord.=str_replace("##real##",\GF::getKey("resultSuccess",$recordItem), $curRecordBlank);

                }else{
                    $curRecord=str_replace("##real##",\GF::getKey("resultSuccess",$recordItem), $curRecord);
                }
                $recordResult.=$curRecord;
            }

            //do replace
            $curResult=str_replace('##id##',\GF::zh2PYFirst($this->terminalName).'_'.\GF::zh2PYFirst($this->name).'_'.\GF::getKey("label",$this->curTextSet[$i]).'_'.$this->curIdx,$curResult);
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
        echo '<h5 style="text-align: left;">'.$this->terminalName." - ".$this->name.'</h5>'.$this->generate();
    }//-/

}//==/class:testCaseItem

