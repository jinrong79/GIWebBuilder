<?php
namespace j79frame\lib\util;

use j79frame\lib\util\XML;
use \SimpleXMLElement as SimpleXMLElement;

/**
*  XMLTranslator
*  @author: jin rong (rong.king@foxmail.com)
*  @method:
*  		  (static) translateNode  : translate node path string to xml structure.
*
*/
class XMLTranslator extends XML
{
	
	//------------construct
	public function __construct(){
		
		//set default value
	
		
	}//-----------/construct	
	
	/**
	*  translateNode
	*  根据node_path内容，对XML进行赋值。
	*  如果节点不存在，就创建，并赋值。
	*  @param   {simplexml} $xml  要赋值的对象xml
	*  @param {string} $node_path  节点解析字符串，/和@来表示节点层级和属性，类似xpath
	*  @param {string} $data    要赋的值。
	*  @param {string} $value_type  值的类型， 'xml'-- 是xml格式；其他---纯字符串；默认值为空，即纯字符串。
	*
	*  @return $xml
	*  例：
	*   $xml ====> <item></item>
	*
	*   translateNode($xml,"node1/node2","A") ==> <item><node1><node2>A</node2></node1></item>
	*   translateNode($xml,"node1@node2","A") ==>         <item><node1 node2="A"></node1></item>
	*   translateNode($xml,"node1","A") ==>               <item><node1>A</node1></item>
	*   translateNode($xml,"@node1","A") ==>              <item node1="A"></item>
	*   translateNode($xml,"/node1","A") ==>              <item><node1>A</node1></item>
	*   translateNode($xml,"node1","<P>A</P>",'xml') ==>     <item><node1><P>A</P></node1></item>
	*   translateNode($xml,"node1","<P>A</P>") ==>        <item><node1>&ltP&gtA&lt/P&gt</node1></item>
	*
	**/

	public  static function translateNode($xml, $node_path, $data,$value_type='' ){
		
		$re='';
		
		$attrName='';
		
		$sepLoc=strpos($node_path,'/');
		
		if($sepLoc ===false){// 没有发现分割符"/", 说明已经到最底层了。
			$curNode=$node_path;
			$subNodeStr='';
			//分析@
			$sepAttrLoc=strpos($curNode,'@');
			if($sepAttrLoc!==false){  //如果有@， 进行拆分
			
				
				if($sepAttrLoc==0){//如果一开头就是@
					$attrName= substr($curNode,1);					
					$curNode='';
					
					static::setAttrValue($xml,$attrName,$data);
					
					/*if($xml->attributes()->{$attrName}){       //已存在@属性
						$xml->attributes()->{$attrName}=$data; //@属性赋值
					}else{                                                 //不存在@属性
						$xml->addAttribute(	$attrName, $data); //添加@属性，并赋值
					}*/
				}
					
			}			
			
					
		}else{// 发现分割符"/", 说明还没有到最底层，需要继续拆分。
		
			if($sepLoc>0){//多级标签, 开头没有"/"
			
				//取得curNode ， 剩余节点字符串
				$curNode=substr($node_path,0,$sepLoc);				
				$subNodeStr=substr($node_path,$sepLoc+1);
				
				//检查剩余节点字符串是否为@开头
				$atLoc=strpos($subNodeStr,'@');
				if($atLoc !=false && $atLoc==0){// @在开头， 说明已经是最底层，带有@的情况
					$attrName= substr($subNodeStr,1);
					$subNodeStr='';
					
				}
				
				
				
			}else{//多级标签, 开头有"/"
				$curNode='';
				$subNodeStr=substr($node_path,1);
			}
		
			
		
			
						
		}		
		
		
		
		if($curNode!=='' && $subNodeStr==''){     //是最底层
			if($attrName!=''){       //有@属性 
				if($xml->{$curNode}){      //已存在curNode
				
				
					static::setAttrValue($xml->{$curNode}[0],$attrName,$data);//@属性，并赋值
				
					/*if($xml->{$curNode}->attributes()->{$attrName}){       //已存在@属性
						$xml->{$curNode}->attributes()->{$attrName}=$data; //@属性赋值
					}else{                                                 //不存在@属性
						$xml->{$curNode}->addAttribute(	$attrName, $data); //添加@属性，并赋值
					}*/
					
				}else{        //不存在curNode
					$xml->addChild(	$curNode, ''); //添加curNode
					$xml->{$curNode}->addAttribute(	$attrName, $data); //添加@属性，并赋值
				
				}
			}else{ //无@属性
			
				
				
				if($xml->{$curNode}){      //已存在curNode
					if(strtolower($value_type)=='xml'){
						
						static::addChildXML($xml->{$curNode}[0],$data);
						//J79_AddXMLChild($xml,'<'.$curNode.'>'.$data.'</'.$curNode.'>',$curNode);
					}else{
						$xml->{$curNode}=$data;
					}
				
				}else{    //不存在curNode
					if(strtolower($value_type)=='xml'){
						
						static::addChildXML($xml,'<'.$curNode.'>'.$data.'</'.$curNode.'>');
						//J79_AddXMLChild($xml,'<'.$curNode.'>'.$data.'</'.$curNode.'>',$curNode);
					}else{
						$xml->addChild(	$curNode, $data);
					}
				}
				
			}
			
		}elseif($curNode!=='' && $subNodeStr!=''){// 不是最底层
			if($xml->{$curNode}){      //已存在curNode					
				
			}else{    //不存在curNode
				$xml->addChild(	$curNode, ''); //添加空白curNode
			}
			static::translateNode($xml->{$curNode},$subNodeStr, $data, $value_type );//递归分析
			
		}	
		
		
		return $xml;
	
	}//-/
	
}//=/
