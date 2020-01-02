<?php
namespace j79frame\lib\util;

use \SimpleXMLElement as SimpleXMLElement;

/**XML
*  xml操作一些静态方法集
*
*  @author: jin rong (rong.king@foxmail.com)
*  @method:
*  		   buildXML    [static]
*  		   addChildXML [static]
*  		   setChildXML [static]
*  		   addChildXML [static]
*  		   addChildXML [static]

*
**/

class XML{
	
	//------------construct
	public function __construct(){
		
		//set default value
	
		
	}//-----------/construct
	
	
	/**
	*  buildXML
	*  build xml (simpleXMLElement) by xmlString and rootNodeName.
	*  - if rootNodeName is empty, then use xmlString as xml content.
	*  - if rootNodeName is not empty,then use <rootNodeName>xmlString</rootNodeName> as xml content
	*  
	*  @param {string}  xmlString    : xml content string.
	*  @param {string}  rootNodeName : root node name, default=<empty>.
	*
	*  @return {xml/bool/NULL}       : xml   - xml object ( simpleXMLElement) to return 
	*                                  false - failed parse xml string 
	*                                          or xmlstring and rootNodeName is emtpy
	*                                  
	*/
	public static function buildXML($xmlString, $rootNodeName=''){
		$xmlString=trim($xmlString);
		$rootNodeName=trim($rootNodeName);
		if($xmlString=='' && $rootNodeName=''){
			return false;	
		}elseif($xmlString==''){
			$xmlStr='<?xml version="1.0" encoding="utf-8"?><'.$rootNodeName.'/>';
			
		}else{
			
			$xmlStr='<?xml version="1.0" encoding="utf-8"?>'.($rootNodeName!=''?'<'.$rootNodeName.'>':'').$xmlString.($rootNodeName!=''? '</'.$rootNodeName.'>':'');
		}
		$xml= simplexml_load_string($xmlStr);
		if($xml){
			return $xml;
		}else{
			return false;	
		}
		
		
	}//-/
	
	
	/**
	*	addChildXML
	*
	*	说明：往$targetXML的根节点上，添加$item节点，包括$item所有子节点和属性
	*	
	*	@param {simpleXMLElement} $targetXML  添加的对象xml
	*	@param {mix} $item  需要添加的xml数据。类型可以为simpleXMLElement  ,或者string。函数根据变量类型自行处理。
	*   
	*   
	*	@return {Boolean} true- 正常添加; false- 出错，$item字串格式有问题，无法解析为xml
	*
	*	例：
	*			$targetXML=simplexml_load_string("<List/>");
	*			XML::addChildXML( $targetXML, '<P><T b="5">123</T></P>')
	*			echo $targetXML->asXML();
	*			--------------------------
	*			<List>
	*				<P>
	*				 <T b="5">123</T>
	*				</P>
	*			</List>
	*			--------------------------
	*
	*			$targetXML=simplexml_load_string("<List><P>0</P></List>");
	*			XML::addChildXML( $targetXML, '<P><T>123</T></P>')
	*			echo $targetXML->asXML();
	*			--------------------------
	*			<List>
	*			    <P>0</P>
	*				<P>
	*				 <T>123</T>
	*				</P>
	*			</List>
	*			--------------------------
	*	
	*
	**/	
	public static function addChildXML( $targetXML, $item){
	
		//参数验证
		if( $item instanceof SimpleXMLElement){ //如果$item是xml类,直接取用.
	  		$xmlItem=$item ;
		}else{//如果$item不是xml类，而是字符串，对其进行解析
	
			@ $xmlItem=simplexml_load_string($item); //试图解析解析$item字符串为xml		
			if(	$xmlItem===false){// 如果$item不是xml字符			
				return false;     //return false， 结束。
			}	  		
		}		
		if( ($targetXML instanceof SimpleXMLElement)==false){
			return false;	
		}
		
		
		//获得 在$targetXML中已经存在的与$item顶级节点同名的节点个数。
		$newItemId=0;
		if(count($targetXML->children())>0 &&  count($targetXML->children()->{$xmlItem->getName()})>0)
			$newItemId=count($targetXML->children()->{$xmlItem->getName()});
		
		//往$targetXML添加$item顶级节点同名的新节点。
		$newXML=$targetXML->addChild($xmlItem->getName());
		
		//添加属性
		if(count($xmlItem->attributes())>0){//如果存在属性, 遍历属性,添加.			
				foreach( $xmlItem->attributes() as $subAttr){				
						$newXML->addAttribute($subAttr->getName(),$subAttr);				
				}				
				
		}		
		
		
		if(count($xmlItem->children())>0){// $xmlItem 有子节点， 递归添加子节点
			foreach( $xmlItem->children() as $subItem){
				static::addChildXML($newXML,$subItem);
			}	
			
		}else{// $xmlItem 没有子节点， 直接赋值。		
			
			
			$valueStr=(string)$xmlItem;
					
			if(static::is_CDATA($xmlItem->asXML())==true){	//判断是否为CDATA					
				static::addCDATA($targetXML->children()->{$xmlItem->getName()}[$newItemId],$valueStr); //添加CDATA
			}else{
				$targetXML->children()->{$xmlItem->getName()}[$newItemId]=$valueStr;	//添加普通字符串
			}		
		}
				
		
		return true;
		
			
	}//----------/addChildXML
  
  
  	/**
	*	setChildXML
	*   
	*	说明:在$targetXML的子节点中,寻找id为$id的节点, 用$item替换找到的节点.
	*	@param {simpleXMLElement}        targetXML : 对象xml
	*	@param {string/simpleXMLElement} item      : 用于替换的新节点, 函数自动判断是xml/string
	*	@param {string}                  idx       : 指定id值, 不一定是数值。
	*	@return {Boolean} true- 正常添加; false- 出错，$item字串格式有问题，无法解析为xml
	*
	*	例：
	*			$targetXML=simplexml_load_string('<List><Item id="1">111</Item><Item id="2">222</Item></List>');
	*			XML::setChildXML( $targetXML, '<Item><T>123</T><P>456</P></Item>',2)
	*			echo $targetXML->asXML();
	*			--------------------------
	*			<List>
	*				<Item id="1">111</Item>
	*				<Item id="2">
	*					<T>123</T><P>456</P>
	*				</Item>				 
	*			</List>
	*			--------------------------
	*	
	*/
	public  static function setChildXML( $targetXML, $item, $idx='0'){
	
		if( $item instanceof SimpleXMLElement){ //如果$item是xml类,直接取用.
	  		$xmlItem=$item ;
		}else{//如果$item不是xml类，而是字符串，对其进行解析
	
			@ $xmlItem=simplexml_load_string($item); //试图解析解析$item字符串为xml		
			if(	$xmlItem===false){// 如果$item不是xml字符			
				return -1;     //return -1， 结束。
			}	  		
		}
	
		$nodeName=$xmlItem->getName();
		$itemAmount=count( $targetXML->children()->{$nodeName});
		
		
		$curItem=NULL; //找到的原有节点
	  	
		
		if($itemAmount>0){
	  		foreach( $targetXML->children()->{$nodeName} as $subItem){			
		  		
		  		
				if(count($subItem->attributes())>0 && $subItem->attributes()->{'id'} && (string)$subItem->attributes()->{'id'}[0]==$idx){//找到id
					
							
					
					XML::removeChildrenAll($subItem);  //删除原有的所有子节点。
		  
		  			static::removeAttrAll($subItem);
					static::setAttrValue($subItem,'id',$idx);
			
	  				
		  			foreach( $xmlItem->attributes() as $subAttr){//把新item的所有属性赋予找到的原有节点。			
						if(	$subAttr->getName()!='id'){ 
			  				static::setAttrValue($subItem, $subAttr->getName(), $xmlItem->attributes()->{$subAttr->getName()}[0]);
						}
					}		  
		  
		  			$curItem=$subItem;	//设置找到的节点指引			
					break;				
				}				
			}
			
		}else{
	  		return -2; //如果原xml不含有子节点， 返回-2
		}
	
		if($curItem==NULL){
	  		return -3;  //如果原xml，不含有指定id的子节点，返回-3
		}
	
		$i=0;
		foreach( $targetXML->children()->{$nodeName} as $subItem){ 
		  		
				if(count($subItem->attributes())>0 && $subItem->attributes()->{'id'} && (string)$subItem->attributes()->{'id'}[0]==$idx){//找到id							
					
					
					
					if( count($xmlItem->children())>0){//如果新item存在子节点
		  				$targetXML->children()->{$nodeName}[$i]='';//原有节点值，清空。
						foreach($xmlItem->children() as $sub){//把新item的所有子节点赋予原有节点之下。	  		
							
							static::addChildXML($curItem,$sub);
						
						}
					}else{//如果新item没有子节点，直接赋值。
						$itemValueStr=(string)$xmlItem;					
						$targetXML->children()->{$nodeName}[$i]=$itemValueStr;
						
					}				
					
					break;
				}
				$i++;
		}
	
		
	
		return true;
	
			
		
	
	
	}//-------------/setChildXML
  
  
  	
  
  
	
	/**
	*	addCDATA
	*
	*	说明：往dataXML的根节点上，添加CDATA，值为$cdataString.
	*	
	*	@param {simpleXMLElement} $targetXML    : 添加的对象xml
	*	@param {string}           $cdataString  : 需要添加的CDATA字符串
	*   
	**/
	public  static function addCDATA($targetXML, $cdataString){
			
		$node=dom_import_simplexml($targetXML);
		$owner=$node->ownerDocument;
		
		$str1=preg_replace('^(<!\[CDATA\[)','',$cdataString);
		$str1=preg_replace('(\]\]>)$','',$str1);
		
		$node->appendChild( $owner->createCDATASection($str1));
	}//-------------/addCDATA
	
	/**
	* is_CDATA
	* 检查字符串valueStr是否为CDATA	
	* 即，是否以<![CDATA[开头，以]]>结束
	**/
	public  static function is_CDATA($valueStr){
			if(preg_match('^(<!\[CDATA\[)',$valueStr)>0 && preg_match('(\]\]>)$',$valueStr)>0){
				return true;
			}else{
				return false;	
			}
	}//-----------/is_CDATA
	
	
	/**
	* decodeCDATA
	* 去掉字符串中的头尾CDATA标记.
	*
	**/
	public  static function decodeCDATA($valueStr){
		if(stripos( $valueStr,'<![CDATA[')!==false && stripos( $valueStr,']]>')!==false){
			
			$valueStr=preg_replace('^(<!\[CDATA\[)','',$valueStr);
		    $valueStr=preg_replace('(\]\]>)$','',$valueStr);
			
				
		}
		
		return $valueStr;
	}//----------/decodeCDATA
	
	
	/**
	* setAttrValue
	* 设置属性值，不管存在与否。
	* 如果存在，直接赋值； 如果不存在， 创建属性，并赋值。
	* @param {simpleXMLElement} xml  目标xml
	* @param {string} attrName 属性名
	* @param {string} value 要赋的值
	* @return 无
	*
	**/
	public  static function setAttrValue($xml,$attrName, $value){
		if(	$xml->attributes()->{$attrName}){//if already have the attribute
			$xml->attributes()->{$attrName}=$value;
		}else{//not exist
			$xml->addAttribute($attrName, $value);
		}
		
	}//----------/setAttrValue
	
	
	/**
	* setNodeValue
	* 设置节点值，不管节点存在与否。
	* 如果存在，直接赋值，如果有多个节点，则赋值$idx索引的节点； 如果不存在， 创建节点，并赋值。
	* 如果$value值是xml格式字符串，则会完整添加到$nodeName节点上。
	* @param {simpleXMLElement} xml  目标xml
	* @param {string} nodeName 节点名 
	* @param {string} value 要赋的值, 可以是xml字符串。
	* @param {int} idx 要赋的值的节点的编号，第一个节点为0，默认值。
	*								比如：
	*								xml=<list><item>AA</item><item>BB</item></list>
	*								XML::setNodeValue( $xml,'item','CC',1)
	*								=>xml=	<list><item>AA</item><item>CC</item></list>
	*									
	* @return 无
	* 
	*	例：
	*			$targetXML=simplexml_load_string("<List><Item></Item></List>");
	*			XML::setNodeValue( $targetXML,'Item','<P><T b="5">123</T></P>')
	*			echo $targetXML->asXML();
	*			--------------------------
	*			<List>
	*				<Item>
	*					<P>
	*				 		<T b="5">123</T>
	*					</P>
	*				</Item>
	*			</List>
	*			--------------------------
	*
	*			$targetXML=simplexml_load_string("<List><Item></Item></List>");
	*			XML::setNodeValue( $targetXML,'Item','123')
	*			echo $targetXML->asXML();
	*			--------------------------
	*			<List>
	*				<Item>
	*					123
	*				</Item>
	*			</List>
	*			--------------------------
	*
	*			$targetXML=simplexml_load_string("<List><Item>aaa</Item><Item>bbb</Item></List>");
	*			XML::setNodeValue( $targetXML,'Item','123')
	*			echo $targetXML->asXML();
	*			--------------------------
	*			<List>
	*				<Item>
	*					123
	*				</Item>
	*				<Item>
	*					bbb
	*				</Item>
	*			</List>
	*			--------------------------
	*
	*
	**/
	public  static function setNodeValue($xml,$nodeName, $value, $idx=0){

			@ $xmlItem=simplexml_load_string($value); //试图解析解析$value字符串为xml
		
			if(	$xmlItem===false){// 如果$value不是xml字符串
			
				if( $xml->children()->{$nodeName}){//if already have the node						
					static::removeChildrenAll($xml->children()->{$nodeName}[$idx]);
				}else{//if node not exist				
					$xml->addChild($nodeName);
					$idx=0;
				}				
				
				if(static::is_CDATA($value)===true){//如果是CDATA
					static::addCDATA($xml->children()->{$nodeName}[$idx],$value);
				}else{//不是CDATA，直接赋值
					$xml->children()->{$nodeName}[$idx]=$value;
				}				
				
			}else{//如果$value是xml字符串
				
				if( $xml->children()->{$nodeName}){//if already have the node
					static::removeChildrenAll($xml->children()->{$nodeName}[$idx]);
					$xml->children()->{$nodeName}[$idx]='';
					$newXML=$xml->children()->{$nodeName}[$idx];
				}else{//if node not exist							
					$newXML=$xml->addChild($nodeName); 					
					$idx=0;
				}					
										
				//添加node
				static::addChildXML($newXML, $xmlItem);// 添加value的所有子节点到$xml->{$nodeName}上。
			}			
		
		
	}
	//----------/setNodeValue
	
	/**
	*	removeChildrenAll
	*	删除所有子节点
	*
	**/
	public  static function removeChildrenAll($xml){
		if(count($xml->children())>0){
			
			$nameArray=array();
			foreach( $xml->children() as $itemX){
				array_push($nameArray,$itemX->getName());
			}
			
			for($i=0;$i<count($nameArray);$i++){
				unset($xml->{$nameArray[$i]});
			}		  
		}
	}//----------------/removeChildrenAll
	
	/**
	*	removeAttrAll
	*	删除所有属性点
	*
	**/
	public  static function removeAttrAll($xml){
		if(count($xml->attributes())>0){
			
			$nameArray=array();
			foreach( $xml->attributes() as $itemX){
				array_push($nameArray,$itemX->getName());
			}
			
			for($i=0;$i<count($nameArray);$i++){
				unset($xml->attributes()->{$nameArray[$i]});
			}		  
		}
	}//----------------/removeAttrAll
	
	
	
	
	
}//=/
