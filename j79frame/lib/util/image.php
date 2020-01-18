<?php
namespace j79frame\lib\util;

use j79frame\lib\core\j79obj;
use j79frame\lib\util\Log;

/**
 *  Image
 *  主要功能： 图片处理类
 *
 *  对象属性:
 *            Width   -图片宽度,只读
 *            Height  -图片高度,只读
 *            Type    -图片扩展名,只读，不带点；小写英文。例：jpg，png
 *            Path    -图片文件完整路径.
 *  Method:
 *           GetInfo -取得图片文件的信息（宽高，文件类型)
 *           Resize  -缩放图片文件
 *
 */
class  Image extends j79obj
{

    protected $_path = '';    // file full path and name.
    protected $_width = 0;    // img width
    protected $_height = 0;
    protected $_type;         //img type. like 'jpg'
    protected $_fileSize = 0; // img file size

    /**
     *  __construct
     */
    public function __construct($filePath = '')
    {
        if ($filePath != '') {
            $filePath = File::getAbsPath($filePath);
            if (file_exists($filePath) && !is_dir($filePath)) {
                $this->_path = $filePath;
                $this->GetInfo();
            }
        }

    }//-/

    public function __get($name)
    {
        switch ($name) {
            case "Width": //图片宽度
                return $this->_width;
                break;

            case "Height"; //图片高度
                return $this->_height;
                break;
            case "Type";  // 文件类型
                return $this->_type;
                break;
            case "Path";  // 文件路径
                return $this->_path;
                break;
            case "Size";  // 文件路径
                return $this->_fileSize;
                break;

        }
    }//-/


    public function __set($name, $value)
    {
        switch ($name) {

            case "Path";  // 文件路径
                if (file_exists($value) && !is_dir($value)) {
                    $this->_path = $value;
                    $this->GetInfo();
                }
                break;

        }
    }//-/

    /**
     *  GetInfo
     *  read image file and get width height and type info.
     */
    public function GetInfo()
    {
        if ($this->_path != '' && file_exists($this->_path) && !is_dir($this->_path)) {
            list($width, $height, $type, $attr) = getimagesize($this->_path);

            $this->_fileSize = filesize($this->_path);
            $this->_fileSize = $this->_fileSize === false ? 0 : $this->_fileSize;

            $this->_width = $width;
            $this->_height = $height;
            $this->_type = strtolower(image_type_to_extension($type, false));
        }

    }//-/


    /**
     *  Resize
     *  大小缩放，仅支持jpg，jpeg，png
     *
     * @param  {int}     newWidth, newHeight : 缩放后的宽高值。
     * @param  {string}  newFileFullName     : 处理后的结果文件，存放路径和文件名。默认：空，意味着覆盖原来的文件。
     * @param  {bool}    preserveRatio       : 压缩时，是否保留原始图片比例。
     *                                          false- 变形原始图片，使图片全部内容压缩为指定宽高。
     *                                          true- 从原始图片内部，裁剪出指定比例的图片，放大/缩小成指定宽高的图片。
     * @param  {int}     quality             : 压缩质量。默认100.
     * @param {bool}     keepWhole           : 在preserveRatio=true时，起作用。
     *                                          true- 不是从原始图片内部裁剪，而是把图片压缩/放大入指定的宽高。
     *                                                产生的图片款高的某一个值可以小于指定宽高。
     *                                                可以理解为，只是限制目标图片的宽高，保持原始比例的压缩/放大。
     *
     * @return {bool} ： true  - successfully done;
     *                   false - error occured.(path is empty, or file extension is not jpg,jpeg,png.)
     **/
    public function Resize($newWidth, $newHeight, $newFileFullName = '', $preserveRatio = true, $quality = 75, $keepWhole=false)
    {

        if (trim($this->_path) == '' || $this->_width <= 0 || $this->_height <= 0) {
            return false;
        }


        if ($newFileFullName == '') {
            $newFileFullName = $this->_path;
        } else {
            $newFileFullName = File::getAbsPath($newFileFullName);
        }


        if (intval($newWidth) == 0 && intval($newHeight) == 0) {
            return false;
        }

        $srcW = $this->_width;
        $srcH = $this->_height;
        $srcX = 0;
        $srcY = 0;
        $targetX=0;
        $targetY=0;
        $targetW=$newWidth;
        $targetH=$newHeight;
        if ($newWidth != 0 && $newHeight != 0) {

            if ($preserveRatio == true) {

                $newRatio = $newWidth / $newHeight;
                $oriRatio = $this->_width / $this->_height;

                if ($keepWhole == false) {
                    $srcW = $newRatio > $oriRatio ? $this->_width : round($this->_height * $newRatio);
                    $srcH = $newRatio > $oriRatio ? round($this->_width / $newRatio) : $this->_height;
                    $srcX = $newRatio > $oriRatio ? 0 : ($this->_width - round($this->_height * $newRatio)) / 2;
                    $srcY = $newRatio > $oriRatio ? ($this->_height - round($this->_width / $newRatio)) / 2 : 0;
                }else{


                    $targetW=$newRatio > $oriRatio ? round($newHeight*$oriRatio) : $newWidth ;
                    $targetH=$newRatio > $oriRatio ?  $newHeight : round($newWidth/$oriRatio) ;
                    $targetX=$newRatio > $oriRatio ? ($newWidth- round($targetH*$oriRatio))/2 :0 ;
                    $targetY=$newRatio > $oriRatio? 0: ($newHeight- round( $targetW /$oriRatio))/2;
                }
            }

        } else {
            $newWidth = $newWidth == 0 ? $newHeight * $this->_width / $this->_height : $newWidth;
            $newHeight = $newHeight == 0 ? $newWidth * $this->_height / $this->_width : $newHeight;
            $targetW=$newWidth;
            $targetH=$newHeight;
        }


        switch ($this->_type) {

            case 'jpg':
            case 'jpeg':

                $src_im = imagecreatefromjpeg($this->_path);
                break;
            case 'png':
                $src_im = imagecreatefrompng($this->_path);
                break;
            default:
                return false;
                break;

        }

        $dst_im = imagecreatetruecolor ($newWidth, $newHeight);
        $white = imagecolorallocate($dst_im, 255, 255, 255);
        imagefill($dst_im,0,0,$white);
        imagecopyresampled($dst_im, $src_im, $targetX, $targetY, $srcX, $srcY, $targetW, $targetH, $srcW, $srcH);


        //if log dir not exist, then make dir.

        //$newPath=preg_replace("/[^\/\\\\]+$/",'',$newFileFullName);
        $newPath = File::getPath($newFileFullName);


        if (!is_dir($newPath)) {

            $re_md = mkdir($newPath, 0777, true);
            if (!$re_md) {
                //echo 'Exception: failed creating thumb dir';
                return false;
            }
        }
        //$curERCode=error_reporting();
        //error_reporting(0);
        imagedestroy($src_im);
        switch ($this->_type) {

            case 'jpg':
            case 'jpeg':

                imagejpeg($dst_im, $newFileFullName, $quality); //输出压缩后的图片
                break;
            case 'png':
                imagepng($dst_im, $newFileFullName, floor(9*$quality/100)); //输出压缩后的图片
                break;
            default:
                return false;
                break;

        }


        imagedestroy($dst_im);


        //error_reporting($curERCode);
        return true;
    }//-/

    /**
     *  Shrink
     *  shrink img if its width or height exceed max width/height.
     *  if not exceed, then just do rename by newFileFullName.
     *
     * @param  {int}     maxWidth, maxHeight : max width/height
     * @param  {string}  newFileFullName     : 处理后的结果文件，存放路径和文件名。默认：空，意味着覆盖原来的文件。
     * @param  {int}     quality             : 压缩质量。默认100.
     *
     * @return {bool} ： true  - successfully done;
     *                    false - error occured.(path is empty, or file extension is not jpg,jpeg,png.)
     */
    public function Shrink($maxWidth, $maxHeight, $newFileFullName = '', $quality = 75)
    {

        if ($this->_width == 0 || $this->_height == 0) {
            return false;
        }

        if ($this->_width > $maxWidth || $this->_height > $maxHeight) {//if exceed max width/height

            $newW = 0;
            $newH = 0;
            $ratio = $this->_width / $this->_height;

            if ($maxHeight > 0 && $maxWidth > 0) { //max width and height all set

                $stanRatio = $maxWidth / $maxHeight;

                if ($ratio > $stanRatio) {
                    $newW = $maxWidth;
                    $newH = 0;
                } else {
                    $newH = $maxHeight;
                    $newW = 0;
                }

            } else {//max width or height is 0, then no limit to width or height

                $newW = $maxWidth > 0 && $this->_width > $maxWidth ? $maxWidth : 0;
                $newH = $maxHeight > 0 && $this->_height > $maxHeight ? $maxHeight : 0;

            }
            $re = $this->Resize($newW, $newH, $newFileFullName, true, $quality);


            return $re;


        } else {//not exceed, then just rename;
            $re = true;

            if ($newFileFullName != '') {
                $re = File::copyFile($this->_path, $newFileFullName);
                if ($re === true) {
                    $this->_path = File::getAbsPath($newFileFullName);
                }
            }


            return $re;
        }


    }//-/


    /**
     *  getImgSizeByRatio
     *  get img crop-size by given imgRatio.
     *
     *  e.g.:
     *       original img=120X80, given ratio=1:1, then return 80*80;
     *       original img=120X80, given ratio=2:1, then return 120*60;
     *
     * @param  {float}  imgRatio : given img ratio
     * @return {array}           : calculated img with and height.={ width, height}
     */
    public function getImgSizeByRatio($imgRatio)
    {
        if ($this->_width == 0 || $this->_height == 0) {
            return array(0, 0);
        }

        $curRatio = $this->_width / $this->_height;
        //$imgRatio=$imgW/$imgH;
        if ($curRatio > $imgRatio) {
            $newH = $this->_height;
            $newW = round($this->_height * $imgRatio);
        } else {
            $newW = $this->_width;
            $newH = round($this->_width / $imgRatio);
        }

        return array($newW, $newH);

    }//-/


    /**
     *  ShrinkAndCrop
     *  1) if img width/height exceed maxW/maxH, then shrink img
     *  2) crop img by ratio: maxW/maxH.
     *
     * @param {int}    maxW, maxH      : img max-width and max-height. And crop img by maxW/maxH ratio
     * @param {string} newFileFullName : new file full url and name
     * @param {int}    quality         : quality of operation.\
     * @param {bool}  keepWhole        : 压缩后保留全部图片，不进行任何裁剪。
     *
     * @return {bool}                  : true  - success or do not need adjust.
     *                                    false - failed file operation.
     *
     */
    public function ShrinkAndCrop($maxW, $maxH, $newFileFullName = '', $quality = 75, $keepWhole=false)
    {

        if ($this->_width == 0 || $this->_height == 0) {
            return false;
        }

        if ($maxW <= 0 || $maxH <= 0) {
            return false;
        }

        list($newW, $newH) = $this->getImgSizeByRatio($maxW / $maxH);
        if ($newW > $maxW) {
            $newW = $maxW;
            $newH = $maxH;
        }
        //if no need to adjust img then return true:
        if ($this->_width == $newW && $this->_height == $newH) {
            if ($newFileFullName != '' && File::getAbsPath($newFileFullName) != File::getAbsPath($this->_path)) {
                return File::copyFile($this->_path, $newFileFullName);

            }
            return true;
        }
        //resize and crop
        $re = $this->Resize($newW, $newH, $newFileFullName, true, $quality, $keepWhole);
        return $re;


    }//-/


    /**
     * mosaics
     * 图片局部打马赛克
     *
     * @param  String $destFileUrl 生成的图片
     * @param  int $x1 起点横坐标
     * @param  int $y1 起点纵坐标
     * @param  int $x2 终点横坐标
     * @param  int $y2 终点纵坐标
     * @param  int $deep 深度，数字越大越模糊
     * @return boolean
     */
    public function mosaics($destFileUrl, $x1, $y1, $x2, $y2, $deep)
    {

        $destFileUrl = File::getAbsPath($destFileUrl);

        $source=$this->_path;
        // 判断原图是否存在
        if (!file_exists($source)) {
            return false;
        }

        // 获取原图信息
        list($owidth, $oheight, $otype) = getimagesize($source);

        // 判断区域是否超出图片
        if ($x1 > $owidth || $x1 < 0 || $x2 > $owidth || $x2 < 0 || $y1 > $oheight || $y1 < 0 || $y2 > $oheight || $y2 < 0) {
            return false;
        }

        switch ($otype) {
            case 1:
                $source_img = imagecreatefromgif($source);
                break;
            case 2:
                $source_img = imagecreatefromjpeg($source);
                break;
            case 3:
                $source_img = imagecreatefrompng($source);
                break;
            default:
                return false;
        }

        // 打马赛克
        for ($x = $x1; $x < $x2; $x = $x + $deep) {
            for ($y = $y1; $y < $y2; $y = $y + $deep) {
                $color = imagecolorat($source_img, $x + round($deep / 2), $y + round($deep / 2));
                imagefilledrectangle($source_img, $x, $y, $x + $deep, $y + $deep, $color);
            }
        }

        // 生成图片
        if(empty($destFileUrl)){
            $destFileUrl=$this->_path;
        }
        switch ($otype) {
            case 1:
                imagegif($source_img, $destFileUrl);
                break;
            case 2:
                imagejpeg($source_img, $destFileUrl);
                break;
            case 3:
                imagepng($source_img, $destFileUrl);
                break;
        }

        return is_file($destFileUrl) ? true : false;

    }//-/


    public function GenerateFitImg($destFileUrl,$tw,$th, $bgColor=0xffffff){
        $destFileUrl = File::getAbsPath($destFileUrl);
        $im = imagecreate($tw,$th);
        $white = imagecolorallocate($im,$bgColor >>32,0xFF,0xFF);

    }//-/




}//=/

