<?php
namespace j79frame\lib\util;

use j79frame\lib\core\j79obj;
use j79frame\lib\util\XML;

/**
**  类:J79_XML_FormBuilder
**  主要功能： 1- 根据表单格式定义的xml文件，输出表单输入/表单修改的html代码。
**           2- 根据表单格式定义的xml文件， 读取表单提交的数据，构成xml 
**/
class  FormBuilder extends j79obj
{
	
	public $URL_Form_XML; //xml file for form; 表单格式定义
	
	public $JS_Global; //js for global;
	
	public $JS_Doc_Ready; //js put in document.ready
	
	public $Form_HTML; //form html string
	
	public $XML_Form_Setting; //xml read from $URL_Form_XML, form setting.
	
	public $XML_Data;//读取表单数据而形成的xml
	
	
	
	//------------construct
	public function __construct($formXMLurl=''){
		
		if(trim($formXMLurl)!=''){
			$this->URL_Form_XML=$formXMLurl;
		}
		
	}//-----------/construct
	
	
	
	
	/**
	* OpenXMLFile
	* 打开form格式xml文件。
	* @return   true - success; false --error;
	**/
	public function OpenXMLFile(){
		
		if ( file_exists($this->URL_Form_XML)) {
			
			$this->XML_Form_Setting= simplexml_load_file($this->URL_Form_XML);
			return true;
		}else{			
			$this->XML_Form_Setting=NULL;
			return false;	
		}

	}//------/OpenXMLFile
	
	/**
	* Build_Form_AddNew
	* 生成输入表单的代码， 存储到类属性Form_HTML,JS_Global,JS_Doc_Ready上。
	*
	**/
	public function Build_Form_AddNew(){
		if(	$this->OpenXMLFile() ==true){
			
			
			$reArray=$this->_AddNew_Form($this->XML_Form_Setting);
			if(count($reArray)==3){
					$this->Form_HTML=$reArray[0];
					$this->JS_Global=$reArray[1];
					$this->JS_Doc_Ready=$reArray[2];
					return true;
			}else{
				return false;	
			}
			
			
		}else{
			return false;	
		}
		
		
	}//----------/Build_Form_AddNew
	
	
	
	/**
	* Build_Form_Edit
	* 生成修改表单的代码， 存储到类属性Form_HTML,JS_Global,JS_Doc_Ready上。
	*
	**/
	public function Build_Form_Edit($xml_data_str){
		
		@ $this->XML_Data=simplexml_load_string($xml_data_str);
		
		
		
		if(	$this->OpenXMLFile() ==true){
			
			
			$reArray=$this->_Edit_Form($this->XML_Form_Setting, $this->XML_Data);
			if(count($reArray)==3){
					$this->Form_HTML=$reArray[0];
					$this->JS_Global=$reArray[1];
					$this->JS_Doc_Ready=$reArray[2];
					return true;
			}else{
				return false;	
			}
			
			
		}else{
			return false;	
		}
		
		
	}//----------/Build_Form_Edit
	
	
	
	
	
	
	/**
	* Build_XML
	* 根据FormSettingXML,读取表单提交过来的数据，构成xml.
	* 返回值:{boolean} true - 成功； false-失败
	**/
	public function Build_XML(){
			
		if(	$this->OpenXMLFile() ==true){
			$this->XML_Data=$this->_ReadFormToXML($this->XML_Form_Setting);			
			return true;
		
		}else{
			return false;	
		}
	}//---------------/Build_XML
	
	
	/**
	* _AddNew_Form
	* 根据FormSettingXML 来输出form 的html.
	* 返回值: 数组, 第一个: form html string; 第二个:全局js代码; 第三个: document.ready里面放的js代码.
	**/
	protected function _AddNew_Form( $xml){
		$reStr='';
		$js=''; //js global
		$js_ready='';//js for doc reday
		
		$js_validate='';
		
		$id='';
		$xmlNode='';
		
		$str_line_start='<div class="form-group">';
		
		
		foreach( $xml->children() as $itemX){
			
			$reStr.=$str_line_start;
			
			$str_line_end='';
			
			
			
			if($itemX->attributes()->{'lineEnd'}){
				$str_line_end='</div>';
				$str_line_start='<div class="form-group">';	
			}else{
				$str_line_end='';
				$str_line_start='';	
			}
			
						
			$reStr.= $itemX->{'label'}->asXML();
			
			$ctr_list=$itemX->{'controls'};
			
			$extraBlock='';
			
			$reStr.='<div class="'.($ctr_list->attributes()->{'class'}).'">';
			foreach($ctr_list->children() as $ctrX){
				$ctr_text='';
				$ctr_type=	$ctrX->attributes()->{'type'};
				
				$ctr_required=(strtolower($ctrX->attributes()->{'required'})=='yes')? ' required ':'';
				$ctr_id=$ctrX->attributes()->{'id'};
				$ctr_placeholder=$ctrX->attributes()->{'placeholder'};
				
				$ctr_default=$ctrX->attributes()->{'default'};
				
				$trigger_event='';				
				if($ctrX->{'trigger'}){
					$js.=$ctrX->{'trigger'};
					$trigger_event=$ctrX->{'trigger'}->attributes()->{'event'}.'="'.$ctr_id.'_'.$ctrX->{'trigger'}->attributes()->{'event'}.'();"';					
							  
				}
				$attribute_validate='';
				if($ctrX->{'validator'}){
					$valRegExp=	$ctrX->{'validator'};
					$valRegExp=str_replace("\n",'',$valRegExp);
					$valRegExp=str_replace(" ",'',$valRegExp);
					$valMsg=$ctrX->{'validator'}->attributes()->{'msg'};
					$attribute_validate='validator="'.$valRegExp.'" validator-msg="'.$valMsg.'"';
					
				}
				
				switch(strtolower($ctr_type)){// 根据不同输入空间类型，建立相应html
					case 'text':
						  $ctr_text='<input form-input '.$ctr_required.' '.$trigger_event.' '.$attribute_validate.' type="text" class="form-control"  id="'.$ctr_id.'" name="'.$ctr_id.'" placeholder="'.$ctr_placeholder.'" value="'.$ctr_default.'" >';
						  break;
					case 'checkbox':
					    $ctr_text='<input form-input '.$ctr_required.' '.$trigger_event.' '.$attribute_validate.' type="checkbox" class="form-control-checkbox"  id="'.$ctr_id.'" name="'.$ctr_id.'" placeholder="'.$ctr_placeholder.'" value="'.$ctr_default.'" >'.$itemX->{'label'}[0];
						
					    break;
					case 'password':
						  $ctr_text='<input form-input '.$ctr_required.' '.$trigger_event.' '.$attribute_validate.' type="password" class="form-control"  id="'.$ctr_id.'" name="'.$ctr_id.'" placeholder="'.$ctr_placeholder.'">';
						  break;
					case 'hidden':
						  $ctr_text='<input form-input '.$ctr_required.' '.$trigger_event.' '.$attribute_validate.' type="hidden"  id="'.$ctr_id.'" name="'.$ctr_id.'" >';
						  break;
					case 'color':
						  $ctr_text='<input form-input '.$ctr_required.' '.$trigger_event.' type="text" class="form-control ctr-color-pick"  id="'.$ctr_id.'" name="'.$ctr_id.'" >';
						  break;
					case 'select':
					 
						  $ctr_text='<select form-input  '.$trigger_event.' type="text" class="form-control"  id="'.$ctr_id.'" name="'.$ctr_id.'" >';
						  
						  $dataSourceObj=$ctrX->{'values'}->attributes()->{'data-source-object'}[0];
						  
						  if( is_null($dataSourceObj)){//data source not from object.
						  
						  	$ctr_select_value=$ctrX->{'values'}->asXML();
						  	$ctr_text.=$ctr_select_value;
							
						  }else{//data source  from object.
						  	 
							 $selectHtml='';
							 
							 $dataSourceObj=(string)$dataSourceObj;
							 if(stripos($dataSourceObj,'->')>0){//public method
							 
							 	$fullClassName=preg_replace('/->[a-zA-Z0-9_]+$/','',$dataSourceObj);
								$methodName=str_replace($fullClassName.'->','',$dataSourceObj);
								
								$selectValueObj=new $fullClassName();
								
								$selectValueArr=$selectValueObj -> $methodName();
								
								 
							 }else if(stripos($dataSourceObj,'::')>0){//static method
							 
							    $fullClassName=preg_replace('/::[a-zA-Z0-9_]+$/','',$dataSourceObj);
								$methodName=str_replace($fullClassName.'::','',$dataSourceObj);
								
								$selectValueObj=new $fullClassName();
								
								$selectValueArr=$selectValueObj->$methodName();
							 
							 }
							 //build option html with result array ($selectValueArr).
							 if(is_array($selectValueArr)){
									
							     for($selectIdx=0;$selectIdx<count($selectValueArr);$selectIdx++){
										$selectValue=$selectValueArr[$selectIdx];
										if(!is_array($selectValue)){
											continue;	
										}
										$sLabel=array_key_exists('label',$selectValue)?$selectValue['label']:'';
										$sValue=array_key_exists('value',$selectValue)?$selectValue['value']:'';
										$selectHtml.='<option value="'.$sValue.'">'.$sLabel.'</option>';											
										
										
								 }
								 $ctr_text.=$selectHtml;
								 
									
							}
							  
							  
							  
						  }
						  $ctr_text.='</select>';
						  break;
						  
					case 'textarea':
						  $ctr_text='<textarea form-input '.$ctr_required.' '.$trigger_event.'  '.$attribute_validate.'class="form-control"  id="'.$ctr_id.'" name="'.$ctr_id.'"  placeholder="'.$ctr_placeholder.'"></textarea>';
						  break;
						  
					case 'imguploader':
					
						  $data_saver=($ctrX->attributes()->{'data-saver'})? 'data-saver="'.$ctrX->attributes()->{'data-saver'}[0].'"':'';
						  
						  $save_path=($ctrX->attributes()->{'save-path'})? 'save-path="'.$ctrX->attributes()->{'save-path'}[0].'"':'';
						  
						  $file_multi= ($ctrX->attributes()->{'data-multi'})? 'data-multi="data-multi"':'';
						  $file_compress_w= ($ctrX->attributes()->{'data-compress-width'})? 'data-compress-width="'.$ctrX->attributes()->{'data-compress-width'}.'"':'';
						  $file_compress_h= ($ctrX->attributes()->{'data-compress-height'})? 'data-compress-height="'.$ctrX->attributes()->{'data-compress-height'}.'"':'';
						  $file_compress_s= ($ctrX->attributes()->{'data-compress-limit-size'})? 'data-compress-limit-size="'.$ctrX->attributes()->{'data-compress-limit-size'}.'"':'';
						  $file_compress_crop= ($ctrX->attributes()->{'data-compress-crop'})? 'data-compress-crop':'';
						  
						  
						  $thumbnail_width=($ctrX->attributes()->{'thumbnail-width'})? 'thumbnail-width="'.$ctrX->attributes()->{'thumbnail-width'}[0].'"':'';
						  $thumbnail_height=($ctrX->attributes()->{'thumbnail-height'})? 'thumbnail-height="'.$ctrX->attributes()->{'thumbnail-height'}[0].'"':'';
						  
						  
						  $ctr_text='<div class="img-uploader" '.$data_saver.' '.$save_path.' '.$file_multi.' '.$thumbnail_width.' '.$thumbnail_height.' '.$file_compress_w.'  '.$file_compress_h.'  '.$file_compress_s.'   '.$file_compress_crop.' id="'.$ctr_id.'" name="'.$ctr_id.'"  title="'.$ctr_placeholder.'" ></div>';
						  break;
					case 'tree-selector':
						  
						  $data_saver=($ctrX->attributes()->{'data-saver'})? 'data-saver="'.$ctrX->attributes()->{'data-saver'}[0].'"':'';
						  $xml_url=($ctrX->attributes()->{'data-xml'})? 'data-xml="'.$ctrX->attributes()->{'data-xml'}[0].'"':'';
						  
						  
						  $ctr_text='<div class="tree-selector" '.$data_saver.' '.$xml_url.' id="'.$ctr_id.'" name="'.$ctr_id.'"  title="'.$ctr_placeholder.'" ></div>';
						  
						  break;
					case 'richtext-editor':
						  $data_saver=($ctrX->attributes()->{'data-saver'})? 'data-saver="'.$ctrX->attributes()->{'data-saver'}[0].'"':'';					
						  
						  $toolbar_set_idx=($ctrX->attributes()->{'toolbar-set-idx'})? 'toolbar-set-idx="'.$ctrX->attributes()->{'toolbar-set-idx'}[0].'"':'';
						  
						  $editor_height=($ctrX->attributes()->{'height'})? 'style="height:'.$ctrX->attributes()->{'height'}[0].'px"':'';
						  
						  $ctr_text='<div class="richtext-editor" '.$data_saver.' '.$toolbar_set_idx.' '.$editor_height.' id="'.$ctr_id.'" name="'.$ctr_id.'"  title="'.$ctr_placeholder.'" ></div>';
						  
						  break;
					
					case 'address-editor':
						
						  $data_saver_region=($ctrX->attributes()->{'data-saver-region'})? 'data-saver-region="'.$ctrX->attributes()->{'data-saver-region'}[0].'"':'';	
						  $data_saver_address=($ctrX->attributes()->{'data-saver-address'})? 'data-saver-address="'.$ctrX->attributes()->{'data-saver-address'}[0].'"':'';							   
						  $ctr_text='<div class="address-editor" '.$data_saver_region.' '.$data_saver_address.' id="'.$ctr_id.'" name="'.$ctr_id.'"  title="'.$ctr_placeholder.'" ></div>';
						  break;
						  
									  
						 
				}
				$reStr.=$ctr_text;
				
				//需要额外加的部分				
				if($ctrX->children()->{'htmlBlock'}){
					$extraBlock.=$ctrX->children()->{'htmlBlock'};
					
				}
				
			}
			$reStr.='</div>';
			$reStr.=$extraBlock;
			
			$reStr.=$str_line_end;
		}
		
		//$js.="\n//regExp values\n".$js_validate;
		
		return array($reStr,$js,$js_ready);
	}//------------------/_AddNew_Form
	
	
	
	
	/**
	* _Edit_Form
	* 根据FormSettingXML 来输出修改数据的form html.
	* @param {xml} 表单格式
	* @param {xml_data} 需要修改的数据xml
	* 返回值: 数组, 第一个: form html string; 第二个:全局js代码; 第三个: document.ready里面放的js代码.
	**/
	protected function _Edit_Form( $xml, $xml_data){
		
				
		$reStr='';
		$js=''; //js global
		$js_ready='';//js for doc reday
		
		$js_validate='';
		
		$id='';
		$xmlNode='';
		
		$str_line_start='<div class="form-group">';
		
		
		foreach( $xml->children() as $itemX){
			
			$reStr.=$str_line_start;
			
			$str_line_end='';
			
			
			
			if($itemX->attributes()->{'lineEnd'}){
				$str_line_end='</div>';
				$str_line_start='<div class="form-group">';	
			}else{
				$str_line_end='';
				$str_line_start='';	
			}
			
			
			$reStr.= $itemX->{'label'}->asXML();
			
			$ctr_list=$itemX->{'controls'};
			
			$extraBlock='';
			
			$reStr.='<div class="'.($ctr_list->attributes()->{'class'}).'">';
			
			foreach($ctr_list->children() as $ctrX){
				$ctr_text='';
				$ctr_type=	$ctrX->attributes()->{'type'};
				
				$ctr_required=(strtolower($ctrX->attributes()->{'required'})=='yes')? ' required ':'';
				$ctr_id=$ctrX->attributes()->{'id'};
				$ctr_placeholder=$ctrX->attributes()->{'placeholder'};
				$trigger_event='';				
				if($ctrX->{'trigger'}){
					$js.=$ctrX->{'trigger'};
					$trigger_event=$ctrX->{'trigger'}->attributes()->{'event'}.'="'.$ctr_id.'_'.$ctrX->{'trigger'}->attributes()->{'event'}.'();"';					
							  
				}
				$attribute_validate='';
				if($ctrX->{'validator'}){
					$valRegExp=	$ctrX->{'validator'};
					$valRegExp=str_replace("\n",'',$valRegExp);
					$valRegExp=str_replace(" ",'',$valRegExp);
					$valMsg=$ctrX->{'validator'}->attributes()->{'msg'};
					$attribute_validate='validator="'.$valRegExp.'" validator-msg="'.$valMsg.'"';
					
				}
				
				
				
				//取得现有值				
				$form_value='';
				if($ctrX->attributes()->{'node'}){
					$nodeStr=$ctrX->attributes()->{'node'};
					if($nodeStr!=''){
						
						$nodeStr='//'.$nodeStr;
						
						$searchValue=false;
						@ $searchValue=$xml_data->xpath($nodeStr);
						if($searchValue===false || $searchValue==NULL){
							$form_value='';
						}else{
							if(count($searchValue[0]->children())>0){
								foreach($searchValue[0]->children() as $itemValueChild){
									$form_value.=$itemValueChild->asXML();									
								}
								
								
							}else{
								$form_value= $searchValue[0];	
							}
							
						}
					}
				}
				
				//是否需要uriEncoding							
				if($ctrX->attributes()->{'uri-coding'}  && strtolower($ctrX->attributes()->{'uri-coding'})=='yes' ){
					$form_value=rawurlencode($form_value);	
				}
				
				//是否存在联动控件
				$linkedUploader='';
				if($ctrX->attributes()->{'linked-uploader'}){
					$linkedUploader='linked-uploader="'.$ctrX->attributes()->{'linked-uploader'}[0].'" ';
				}
				
				
				//echo $nodeStr.' =='.$form_value.'<br/>';
				
				
				
				
				switch(strtolower($ctr_type)){// 根据不同输入空间类型，建立相应html
					case 'text':
						  $ctr_text='<input form-input '.$ctr_required.' '.$trigger_event.' '.$attribute_validate.' type="text" class="form-control"  id="'.$ctr_id.'" name="'.$ctr_id.'" placeholder="'.$ctr_placeholder.'"  value="'.$form_value.'" '.$linkedUploader.' >';
						  break;
					case 'password':
						  $ctr_text='<input form-input '.$ctr_required.' '.$trigger_event.' '.$attribute_validate.' type="password" class="form-control"  id="'.$ctr_id.'" name="'.$ctr_id.'" placeholder="'.$ctr_placeholder.'">';
						  break;
					case 'hidden':
						  $ctr_text='<input form-input '.$ctr_required.' '.$trigger_event.' '.$attribute_validate.' type="hidden"  id="'.$ctr_id.'" name="'.$ctr_id.'"   value="'.$form_value.'"  '.$linkedUploader.' >';
						  break;
					case 'color':
						  $ctr_text='<input form-input '.$ctr_required.' '.$trigger_event.' type="text" class="form-control ctr-color-pick"  id="'.$ctr_id.'" name="'.$ctr_id.'"   value="'.$form_value.'">';
						  break;
					case 'select':
					 
						  $ctr_text='<select form-input  '.$trigger_event.' type="text" class="form-control"  id="'.$ctr_id.'" name="'.$ctr_id.'" >';
						  if($form_value==''){ //表单值为空
						  	$ctr_select_value=$ctrX->{'values'}->asXML();
						  }else{//表单值非空，设置selected							
							foreach(	$ctrX->{'values'}->children() as $optionItem){
								
								$curOptionV=(string)$optionItem->attributes()->{'value'};								
								
								if($form_value==$curOptionV){									
									$optionItem->addAttribute('selected','');	
								}
							}
							$ctr_select_value=$ctrX->{'values'}->asXML();
						  }
						  $ctr_text.=$ctr_select_value;
						  $ctr_text.='</select>';
						  break;
					case 'textarea':
						  $ctr_text='<textarea form-input '.$ctr_required.' '.$trigger_event.'  '.$attribute_validate.'class="form-control"  id="'.$ctr_id.'" name="'.$ctr_id.'"  placeholder="'.$ctr_placeholder.'">'.$form_value.'</textarea>';
						  break;
					case 'imguploader':
					
							
						  $data_saver=($ctrX->attributes()->{'data-saver'})? 'data-saver="'.$ctrX->attributes()->{'data-saver'}[0].'"':'';
						  $save_path=($ctrX->attributes()->{'save-path'})? 'save-path="'.$ctrX->attributes()->{'save-path'}[0].'"':'';
						  
						  $file_multi= ($ctrX->attributes()->{'data-multi'})? 'data-multi="data-multi"':'';
						  $file_compress_w= ($ctrX->attributes()->{'data-compress-width'})? 'data-compress-width="'.$ctrX->attributes()->{'data-compress-width'}.'"':'';
						  $file_compress_h= ($ctrX->attributes()->{'data-compress-height'})? 'data-compress-height="'.$ctrX->attributes()->{'data-compress-height'}.'"':'';
						  $file_compress_s= ($ctrX->attributes()->{'data-compress-limit-size'})? 'data-compress-limit-size="'.$ctrX->attributes()->{'data-compress-limit-size'}.'"':'';
						  
						  $file_compress_crop= ($ctrX->attributes()->{'data-compress-crop'})? 'data-compress-crop':'';
						  
						  //$thumbnail_upload=($ctrX->attributes()->{'thumbnail-upload'})? 'thumbnail-upload="yes"':'';
						  //$thumbnail_data_saver=($ctrX->attributes()->{'thumbnail-data-saver'})? 'thumbnail-data-saver="'.$ctrX->attributes()->{'thumbnail-data-saver'}[0].'"':'';
						  $thumbnail_width=($ctrX->attributes()->{'thumbnail-width'})? 'thumbnail-width="'.$ctrX->attributes()->{'thumbnail-width'}[0].'"':'';
						  $thumbnail_height=($ctrX->attributes()->{'thumbnail-height'})? 'thumbnail-height="'.$ctrX->attributes()->{'thumbnail-height'}[0].'"':'';
						  
						  
						  $ctr_text='<div class="img-uploader" '.$data_saver.' '.$save_path.' data-uploaded-list="'.$form_value.'" '.$file_multi.'  '.$thumbnail_width.' '.$thumbnail_height.' '.$file_compress_w.'  '.$file_compress_h.'  '.$file_compress_s.'   '.$file_compress_crop.' id="'.$ctr_id.'" name="'.$ctr_id.'"  title="'.$ctr_placeholder.'" ></div>';
						  break;
						  
					case 'tree-selector':
						  
						  $data_saver=($ctrX->attributes()->{'data-saver'})? 'data-saver="'.$ctrX->attributes()->{'data-saver'}[0].'"':'';
						  $xml_url=($ctrX->attributes()->{'data-xml'})? 'data-xml="'.$ctrX->attributes()->{'data-xml'}[0].'"':'';
						  
						  
						  $ctr_text='<div class="tree-selector" '.$data_saver.' '.$xml_url.' id="'.$ctr_id.'" name="'.$ctr_id.'"  title="'.$ctr_placeholder.'" ></div>';
						  
						  break;
						  
					case 'richtext-editor':
						  $data_saver=($ctrX->attributes()->{'data-saver'})? 'data-saver="'.$ctrX->attributes()->{'data-saver'}[0].'"':'';					
						  
						  $toolbar_set_idx=($ctrX->attributes()->{'toolbar-set-idx'})? 'toolbar-set-idx="'.$ctrX->attributes()->{'toolbar-set-idx'}[0].'"':'';
						  
						  $editor_height=($ctrX->attributes()->{'height'})? 'style="height:'.$ctrX->attributes()->{'height'}[0].'px"':'';
						  
						  $ctr_text='<div class="richtext-editor" '.$data_saver.' '.$toolbar_set_idx.' '.$editor_height.' id="'.$ctr_id.'" name="'.$ctr_id.'"  title="'.$ctr_placeholder.'" ></div>';
						  
						  break;
					
						  
									  
						 
				}
				$reStr.=$ctr_text;
				
				//需要额外加的部分				
				if($ctrX->children()->{'htmlBlock'}){
					$extraBlock.=$ctrX->children()->{'htmlBlock'};
					
				}
			}
			$reStr.='</div>';
			$reStr.=$extraBlock;
			$reStr.=$str_line_end;
		}
		
		//$js.="\n//regExp values\n".$js_validate;
		
		return array($reStr,$js,$js_ready);
	}//------------------/_Edit_Form
	
	
	
	
	/**
	* _ReadFormToXML
	* 根据xmlSetting的格式, 读取来自Form的数据，并组织成xml返回.
	* @param $xmlSetting {xml} form的格式定义
	* 返回值:{xml} 形成的xml。
	**/
	
	protected function _ReadFormToXML($xmlSetting){
		
		$xmlData=simplexml_load_string('<item></item>');
		foreach( $xmlSetting->children() as $itemX){
			
			$controlList=$itemX->{'controls'}[0];
			
			foreach($controlList->children() as $ctrX){
				
				
				
				
				if($ctrX->attributes()->{'type'})
					$ctr_type=$ctrX->attributes()->{'type'};
				
				if($ctrX->attributes()->{'node'} && $ctrX->attributes()->{'node'}!='' ){
					//取得request的值
					@ $formValue=$_REQUEST[(string)$ctrX->attributes()->{'id'}];
					
					//echo '<br/>node:'.$ctrX->attributes()->{'node'};
					
					//echo '<br/>value:'.$formValue;
					
					
					$valueType='';
					$xmlType='';
					if($ctrX->attributes()->{'value-type'}){
							$valueType=$ctrX->attributes()->{'value-type'};
					}
					if($ctrX->attributes()->{'uri-coding'}){
							$formValue=urldecode($formValue);
							
					}
				
					
					if($ctrX->attributes()->{'xml-type'}){
							$xmlType=$ctrX->attributes()->{'xml-type'};
					}
					
					if(strtolower($ctr_type)=='password'){
						$formValue=	md5($formValue);
					}
					
					//xml数据， 而且是not-closed格式的时候， 强制添加 "/>".
					if(strtolower($valueType)=='xml' && strtolower($xmlType)=='not-closed' ){
						//echo 'not-closed xml string='.$formValue;
						
						$formValue=str_replace('>','/>',$formValue);
						$formValue=str_replace('//>','/>',$formValue);						
						
							
					}		
					if(strtolower($valueType)=='html'){
						
						
						$formValue='<![CDATA['.$formValue.']]>';
						$valueType='xml';
						
						//$formValue=addslashes($formValue);
					}
					
							
					//解析node string， 构成xml。
					$dataXMLStr=J79_XML::TranslateNode($xmlData,$ctrX->attributes()->{'node'},$formValue,$valueType);
					
					
				}
				
			}
				
		}
		
		//echo $xmlData->asXML();
		
		return $xmlData;
		
		
	}//-------------/_ReadFormToXML
	
	
	
	
	
}//=/class J79_XML_FormBuilder
 
