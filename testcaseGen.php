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
              'label'=>'3DXQ',
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
              'label'=>'3DXQ',
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
              'label'=>'3DXQ',
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
              'label'=>'3DXQ',
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
              'label'=>'3DXQ',
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
              'label'=>'3DXQ',
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
              'label'=>'3DXQ',
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
              'label'=>'3DXQ',
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
              'label'=>'3DXQ',
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
              'label'=>'3DXQ',
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
              'label'=>'3DXQ',
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

      )



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
                if(mt_rand(1,100)<= round($this->bugRate*100)){
                    $this->totalBugAmount++;
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
        echo '<h4 style="text-align: left;">'.$this->terminalName." - ".$this->name.'</h4>'.$this->generate();
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
        'name'=>"应急预案-视频监控",
        "channel"=>"应急预案",
    ),
    array(
        'name'=>"应急预案-安保机器人",
        "channel"=>"应急预案",
    ),
    array(
        'name'=>"应急预案-保安人员",
        "channel"=>"应急预案",
    ),
    array(
        'name'=>"应急预案-应急物资",
        "channel"=>"应急预案",
    ),
    array(
        'name'=>"应急预案-智能终端",
        "channel"=>"应急预案",
    ),







    array(
        'name'=>"用户",
        "terminal"=>'后台',
        "channel"=>"后台",
        "type"=>100,
    ),


);

$curIdx=0;
$totalB=0;
$totalCase=0;
for($i=0;$i<count($setting);$i++){

    $curSet=$setting[$i];
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


