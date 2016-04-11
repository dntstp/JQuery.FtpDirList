<?php
##############################
# https://github.com/dntstp/JQuery.FtpDirList.git
# pustolovskiy@gmail.com
##############################

error_reporting(0);

$conf = new stdClass;
$conf -> host = 'localhost';
$conf -> port = 21;
$conf -> timeout = 90;
$conf -> username = 'anonymous';
$conf -> password = '';



function byte_convert($size) {
  # size smaller then 1kb
  if ($size < 1024) return $size . ' Byte';
  # size smaller then 1mb
  if ($size < 1048576) return sprintf("%4.2f KB", $size/1024);
  # size smaller then 1gb
  if ($size < 1073741824) return sprintf("%4.2f MB", $size/1048576);
  # size smaller then 1tb
  if ($size < 1099511627776) return sprintf("%4.2f GB", $size/1073741824);
  # size larger then 1tb
  else return sprintf("%4.2f TB", $size/1073741824);
}
if( ! ini_get('date.timezone') )
{
	date_default_timezone_set('GMT');
}
$result= new stdClass;
header('Content-Type: application/json; charset=utf-8');
try{
	$ftp = ftp_connect($conf -> host, $conf -> port, $conf -> timeout);
		if (!$ftp) throw new Exception ('could not connect.');
	$r = ftp_login($ftp, $conf -> username, $conf -> password);
		if (!$r) throw new Exception ('could not login.');
	$r = ftp_pasv($ftp, true);
		if (!$r) throw new Exception('could not enable passive mode.');
	$path = isset($_GET['path'])? $_GET['path']:"/";

	$imgpath = isset($_GET['images'])? $_GET['images']:"img/";
	$imgfullpath = isset($_GET['images'])? $_SERVER['DOCUMENT_ROOT'].'/'.$_GET['images']:"img/";
	$i = scandir($imgfullpath);
	$images = array();
	foreach ($i as $image){
		$re = "/^[a-z0-9]+/";
		preg_match($re, $image, $matches);
		if ($image != '.' and $image != '..') {
			$images[ $matches[0]] = $imgpath . $image;
		}
	}
	$r = ftp_nlist($ftp, $path);
	$files = array();
	if ($r){
		foreach ($r as $file) {
			$info = pathinfo($file);
			if(ftp_size($ftp, $file)!=-1){
				$type = 'file';
				if(isset($images[$info['extension']])){
					$img = $images[$info['extension']];
				}else{
					$img = $images['blank'];
				}
			}else{
				$type = 'folder';
				$img = $images['folder'];
			}
			array_push($files,  array(	'url' => 'ftp://'.$conf -> host.$file,
										'img' => $img,
										'name'=> preg_replace('/^.*\//', '', $file),
										'date'=> date('d.m.Y H:i:s', ftp_mdtm($ftp, $file)),
										'size'=> $type == 'folder' ? 'Folder': byte_convert(ftp_size($ftp, $file)),
										'type'=> $type,
										'path'=>$file
										));
		}
	}else{

	}
	$result->state = 'OK';
	$result->files = $files;

}catch(Exception $e){
	$result->state = 'ERROR';
	$result->error = $e->getMessage();
}
print json_encode($result, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);