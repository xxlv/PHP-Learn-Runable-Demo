<?php
include('Img.class.php');
if ($_FILES){
    $p=0.1;//设置缩放比率
   $imgUpload=new ImgTool($_FILES['File']);
   $imgUpload->create('./data/');
   $imgUpload->createThumbnail($p);
   $imgUpload->createWaterMarking(false,"./data/water/",50,20,20,"Hello","C WORD");
   $name=$imgUpload->src_full_name;
   $thumbName=$imgUpload->thumbnail_full_name;
   $waterName=$imgUpload->src_full_water_mark_name;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
	<title>
	</title>
</head>
<body>
<?php
echo "原图 ";

echo "<img src='".$name."' />";
list($real_width, $real_height) = getimagesize($name);
echo "高度".$real_height;
echo "宽度".$real_width;

echo "<hr/>";
echo "缩放".$p;
echo "<img src='".$thumbName."'/>";
list($width, $height) = getimagesize($thumbName);
echo "高度".$height;
echo "宽度".$width;


echo "<hr/>";
echo "添加了水印";
echo "<img src='".$waterName."'/>";
list($width, $height) = getimagesize($waterName);
echo "高度".$height;
echo "宽度".$width;
?>

<form action="index.php" method="post"  enctype="multipart/form-data" >
    please select a file:
    <input type="file" name="File"/>
    <div>
        <input type="submit" value="Post it!">
    </div>
</form>

</body>
</html>


