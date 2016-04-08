<?php
##############################
#
#
#
#
##############################
$conf = new stdClass;
$conf -> host = '101.25.1.252';
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

$result= new stdClass;
header('Content-Type: application/json; charset=utf-8');
try{
	$ftp = ftp_connect($conf -> host, $conf -> port, $conf -> timeout);
		if (!$ftp) throw new Exception ('could not connect.');
	$r = ftp_login($ftp, "anonymous", "");
		if (!$r) throw new Exception ('could not login.');
	$r = ftp_pasv($ftp, true);
		if (!$r) throw new Exception('could not enable passive mode.');

	$r = ftp_nlist($ftp, isset($_GET['path'])? $_GET['path']:"/");
	$files = array();
	if ($r){
		foreach ($r as $file) {
			array_push($files,  array(	'url' => 'ftp://'.$conf -> host.$file,
										'name'=> preg_replace('/^.*\//', '', $file),
										'date'=>date('d.m.Y H:i:s', ftp_mdtm($ftp, $file)),
										'size'=>ftp_size($ftp, $file)==-1? 'Folder': byte_convert(ftp_size($ftp, $file)),
										'type'=>ftp_size($ftp, $file)==-1? 'folder':'file',
										'path'=>$file
										));
		}
	}else{

	}
	$result->files = $files;

}catch(Exception $e){
	$result->error = $e->getMessage();
}
print json_encode($result, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);