<?php
/*Функция нужна для скачивания файла*/
//function downoload($file){
 $filename = $_GET['file'];
  // $filename = dirname(__FILE__).'/'.$file.'';
 // необходимых для IE, в противном случае Content-Disposition игнорируется
if(ini_get('zlib.output_compression'))
  ini_set('zlib.output_compression', 'Off');
 
$file_extension = strtolower(substr(strrchr($filename,"."),1));
 
if( $filename == "" )
{
  echo "Download File ОШИБКА: Не указан файл.";
  exit;
}/* elseif ( ! file_exists( $filename ) ) // сделать проверку на существование удаленного файла
{
  echo "Download FileОШИБКА: Файл не найден.";
  exit;
};*/
switch( $file_extension )
{
  case "pdf": $ctype="application/pdf"; break;
  case "exe": $ctype="application/octet-stream"; break;
  case "zip": $ctype="application/zip"; break;
  case "doc": $ctype="application/msword"; break;
  case "xls": $ctype="application/vnd.ms-excel"; break;
  case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
  case "gif": $ctype="image/gif"; break;
  case "png": $ctype="image/png"; break;
  case "jpeg":
  case "jpg": $ctype="image/jpg"; break;
  default: $ctype="application/force-download";
}
header("Pragma: public"); // требуется
header('Content-Description: File Transfer');
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false); // требуется для некоторых браузеров
header("Content-Type: $ctype");
header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".filesize($filename)); // необходимо доделать подсчет размера файла по абсолютному пути
readfile("$filename");
exit();

