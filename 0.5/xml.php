<?php 

if (isset($_GET['xml'])){
	$xml = pathinfo($_GET['xml']);
	
	$file = ($_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].'/'.ltrim($_GET['xml'], '/');
	$content = file_get_contents($file);
	
	$content = preg_replace('`src="`', 'src="'.rtrim($xml['dirname'], '/').'/', $content);
	
	header('Content-type: text/xml');
	echo $content;
}
exit;