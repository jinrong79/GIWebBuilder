/**
*  dragSort
*  drag items to sort.
*  list and drag direction is defined by class or params. default is veritcal. 
*  
*  <ul class="drag-sortable [horizontal]">
*    <li>A</li>
*    <li>B</li>
*    <li>C</li>
*  </ul>
*
*  
*
*  -------------------- 
*  plugin usage: $('.list').dragSort(childSelector); 
*  plugin param 
*                childSelector : [default]'.sort-item' 
*                                      
*              
*/

(function($){
   
   $.fn.dragSort = function( childSelector){
	   
	   var List=this;
	   
	   //sort item selector, default:'.sort-item':
	   var sortItemSelector =childSelector ? childSelector :'.sort-item';
	   
	   //list direction: 0[default]- vertical; 1- horizontal;
	   var ListDirection=$(this).hasClass('horizontal')? 1:0;;  	       
	   
	  
	   
	   
	   
	   /**
	   *  inArea
	   *  check whether targetObj in the areaObj.
	   *  @param {jquery selector} areaObj, targetObj : area and target object selector.
	   *  @param {int}             direction          : 0- vertical; 1- horizontal
	   *  @return {int} : return position status: 
	   *                  -1: inArea, at the front part;  
	   *                  1 : inArea, at the back  part;
	   *                  -2: not inArea, at the front of areaObj;
	   *                  2 : not inArea, at the back of areaObj;
	   *                  
	   */
	   var inArea=function(areaObj, targetObj, direction){
		    //console.log('area:');
			// console.log(areaObj);
		   
		   var itemX, itemY, itemW, itemH;
		   itemY=Math.round($(areaObj).offset().top);
		   itemX=Math.round($(areaObj).offset().left);
		   itemW=$(areaObj).outerWidth();
		   itemH=$(areaObj).outerHeight();
		   
		  
		    
		   //console.log(itemX+':'+itemY+':'+itemW+':'+itemH);
		   
		   var curX, curY, curW, curH;
		   curY=Math.round($(targetObj).offset().top);
		   curX=Math.round($(targetObj).offset().left);
		   curW=$(targetObj).outerWidth();
		   curH=$(targetObj).outerHeight();
		   
		   //console.log('check item:');		   
		   //console.log(curX+':'+curY+':'+curW+':'+curH);
		
		   
		   
		   
		   if(direction==0){ //vertical list
		       
			   if(curY<itemY){
				 return -2;   
			   }
			   if(curY>itemY+itemH){
				 return 2;   
			   }
			   
			   if(curY<itemY+itemH/2){
				  return -1;   
			   }else{
				  return 1;   
			   }
			   
		   }else{//horizontal list
		   
		       if(curX<itemX){
				 return -2;   
			   }
			   if(curX>itemX+itemW){
				 return 2;   
			   }
		   
			   if(curX<itemX+itemW/2){
				  return -1;   
			   }else{
				  return 1;   
			   }
			   
		   }
		   
	   };
	   
	   
	   /**
	   *
	   */
	   var setPos=function(draged, e,dx,dy){
		  
		  var deltaX,deltaY;
		  deltaY=Math.round($(List).offset().top);
		  deltaX=Math.round($(List).offset().left);
		  
		  if(ListDirection==0){		  
			  
			  $(draged).css('top', e.pageY-deltaY-dy); 
			  $(draged).css('left', -10); 
		  }else{
			  $(draged).css('left', e.pageX-deltaX-dx); 
			  $(draged).css('top', -10); 
		  }  
		   
	   };//-/
	   
	   
	   
	   
	   //bind change to check validation
	   var bindDrag=function(targetElement){
		   
		    
		   
		    $(targetElement).delegate(sortItemSelector,"dragstart",function(e){
				return false;
			});
			
					   
		   $(targetElement).delegate(sortItemSelector,"mousedown",function(e){
			   
			   //console.log('md actived!');
			   
			   var $dragItem=$(this); 
			   var dragIdx=$(this).index();			
			   
			   var deltaX=e.pageX-Math.round($(List).offset().left)-Math.round($(this).position().left);
			   var deltaY=e.pageY-Math.round($(List).offset().top)-Math.round($(this).position().top);	   
			   
			   $(this).css('position','absolute');
			   $(this).css('z-index','999');
			   
			   setPos(this,e,deltaX,deltaY);		   
			 
			   
			   var handleMove=function(e) {		   
				  
					setPos($dragItem,e,deltaX,deltaY);				
					
               };
			   
			   $(List).mousemove(handleMove);
			   $(List).mouseup(function(e){
						
						//console.log('mu actived!');
						
						$(List).unbind('mousemove');
						$(List).unbind('mouseup');
						
						//calculate position:
						var  curY=$dragItem.offset().top;
						var  curX=$dragItem.offset().left;
						
						var itemX, itemY, itemW, itemH;
						
						var itemList=$(List).children();
						
						var result;
						var curItem;
						
						var startIdx=dragIdx==0? 1:0;
						var endIdx=dragIdx==itemList.length-1? itemList.length-1: itemList.length;
						
						
						for(var i=startIdx; i< endIdx; i++){
							
							curItem=itemList[i];						
							
							//console.log('check start');
							
							if(dragIdx!=i){
								
							   //console.log('check need'+i);
							   
							   result = inArea(curItem,$dragItem,ListDirection);		   
							   
							   if(result==-2 && i==startIdx){
								  console.log('add at first:');								  
								  $dragItem.insertBefore($(curItem));
								  $(List).trigger('change');
								  break;   
								   
							   }
							   
							   if(result==2 && i==endIdx-1){
								  console.log('add at tail:');								  
								  $dragItem.insertAfter($(curItem));
								  $(List).trigger('change');
								  break;   
								   
							   }
							    
							  
							   if(result==-1){
								  console.log('add before:');								  
								  $dragItem.insertBefore($(curItem));
								  $(List).trigger('change');
								  break;					 
								   
							   }
							   if(result==1){
								  console.log('add after:');
								  $dragItem.insertAfter($(curItem));
								  $(List).trigger('change');
								  break; 
							   }
							   
							   
						    }
						}
						
						//console.log('end match!');
						
						
						
						
					   	$dragItem.css('position','relative');
			            $dragItem.css('left', 'auto'); 
			            $dragItem.css('top', 'auto');
						$dragItem.css('z-index','0');
						
						
						
						
				});
			   
		   });
		   
	   };
	   
	   //set this elelment position: relative;	   
	   $(this).css('position','relative');
	   
	   
	  
	   
	   bindDrag(List);
	   
	     
	   
	  
   }
})(jQuery)//-----------------------------------------


function Setup_DragSort(){	

    var Control;
	var data_saver_id;
	
	var treeSel = $('.drag-sortable');
	var listDir='';
	for(i=0;i<treeSel.length;i++)
	{
		
		//listDir=$(treeSel[i]).hasClass('horizontal')? 1:0;
		$(treeSel[i]).dragSort();
		
		
		
	}	

}