<?php
/**
 * 图片上传工具类
 * 直接从表单域上传的图片进行 保存|压缩|添加水印等常见操作
 * 
 * @author		lv xiang <lvxiang119@gmail.com>
 */
class ImgTool {

    private $_name;//名称
    private $_type;//类型
    private $_tmp_name;//临时文件名
    private $_error;//错误
    private $_size;//上传图片的大小
    public $src_full_name;//保存到服务器上的全名
    public $src_full_water_mark_name;//带有水印的名称
    public $thumbnail_full_name;//保存到服务器上缩略图的全名

    /**
     * 检查格式是来满足form表单提交
     * 初始化图像文件
     * @param $file
     */
    public function __construct($file){
        //Check if File was uploaded from form
        $checked=$this->IsImg($file);
        if(!$checked) {
            die('Need File array !');
        }
        $this->_name=$file['name'];
        $this->_type=$file['type'];
        $this->_tmp_name=$file['tmp_name'];
        $this->_error=$file['error'];
        $this->_size=$file['size'];
    }
    /**
     * 创建图片
     * @param $path
     * @param array $accept_type
     * @param string $real_file_name
     * @return string
     */
    public function create($path,$accept_type=['image/gif','image/jpeg','image/png'],$real_file_name=''){
        if(!file_exists($path)){
            mkdir($path,0700);
        }
        if(in_array($this->_type, $accept_type)){
            //do upload here
            if($real_file_name==''){
                $real_file_name=$path.sha1(time()).'_'.$this->_name;
            }
            move_uploaded_file($this->_tmp_name, $real_file_name);
            $this->src_full_name=$real_file_name;
            return $this;
        }else{
            die("Type Not Accept!");
        }

    }
    /**
     * 创建缩略图
     * @param bool $full_pic 带压缩图片完整名称
     * @param string $percent 缩略百分比
     * @param string $path 缩略图保存路径
     * @param bool $new_h  缩略图高
     * @param bool $new_w 缩略图宽
     * @return string  缩略图带地址的名称
     */
    public function createThumbnail($percent='0.5',$full_pic=false,$path="./data/thumbnail/",$new_h=false,$new_w=false){

        if(!$full_pic){
            $full_pic=$this->src_full_name;
        }
        if(!is_callable('imagecreatetruecolor')){
            die('Cannot Initialize new GD image stream!');
        }
        //获取原图尺寸
        list($rel_w,$rel_h)=getimagesize($full_pic);
        //缩放尺寸
        if(!$new_h||!$new_w){
            $new_w=$rel_w*$percent;
            $new_h=$rel_h*$percent;
        }
        $image_p=imagecreatetruecolor($new_w, $new_h);

        $image = imagecreatefromjpeg($full_pic);

        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_w, $new_h, $rel_w, $rel_h);

        $dst_real_safe_name=sprintf($path."%s_thum.jpg",sha1(uniqid(time(), TRUE)));
        imagejpeg($image_p, $dst_real_safe_name, 100);

        $this->thumbnail_full_name=$dst_real_safe_name;
        return $this;
    }
    /**
     * 当不传递src的时候，默认在创建的原图上增加水印
     * 注意将增加了水印的图像作为$this对象的
     * src_full_water_mark_name 属性
     * @param bool $src
     * @param string $dst
     * @param int $percent
     * @param int $marge_right
     * @param int $marge_bottom
     * @param string $information
     * @param string $copyright
     * @return $this
     */
    public function createWaterMarking($src=false,$dst="./data/water/",$percent=20,$marge_right=10,$marge_bottom=10,$information="",$copyright=""){
        if(!$src){
            $src=$this->src_full_name;
        }
        $im = imagecreatefromjpeg($src);
        $stamp = imagecreatetruecolor(100, 70);
        imagefilledrectangle($stamp, 0, 0, 99, 69, 0x0000FF);
        imagefilledrectangle($stamp, 9, 9, 90, 60, 0xFFFFFF);
        imagestring($stamp, 5, 20, 20,$information,0x0000FF);
        imagestring($stamp, 3, 20, 40, $copyright, 0x0000FF);
        //设置水印图像的位置与大小
        $sx=imagesx($stamp);
        $sy = imagesy($stamp);
        imagecopymerge($im, $stamp, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp), $percent);
        $safe_name=$dst.sprintf("%s_water.png",sha1(uniqid(time(),TRUE)));
        imagepng($im, $safe_name);//生成带水印的图片
        imagedestroy($im);
        $this->src_full_water_mark_name=$safe_name;
        return $this;
    }
    /**
     * 检查满足form表单file域传递
     * @param $file
     * @return bool
     */
    private function IsImg($file){
        if(!is_array($file)){
            return false;
        }
        return
            array_key_exists('name', $file)&&
            array_key_exists('type', $file)&&
            array_key_exists('tmp_name', $file)&&
            array_key_exists('size', $file);
    }
}

