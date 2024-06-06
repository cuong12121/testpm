<?php
	session_start();
	
	#STAR FIREWALL 
	/* if((time() - $_SESSION['limitAccess']) < 1){
    		unset($_SESSION['limitAccess']);
    	}
	 if(strlen($_SESSION['limitAccess']) < 5){
	 	$_SESSION['limitAccess'] = time();
	 	$fw_template = file_get_contents('fw.html');
	 	die($fw_template);
	 }  */
	#END FIREWALL 

	$sort_path = $_SERVER['SCRIPT_NAME'];
	$sort_path = str_replace('index.php','', $sort_path);
	define('URL_ROOT', "http://" . $_SERVER['HTTP_HOST'] . $sort_path);	
	
	define('URL_ROOT_REDUCE',$sort_path); 
	
	if (!defined('DS')) {
		define('DS', DIRECTORY_SEPARATOR);
	}
	$path = $_SERVER['SCRIPT_FILENAME'];
	$path = str_replace('index.php','', $path);
	$path = str_replace('/',DS, $path);
	$path = str_replace('\\',DS, $path);
	define('PATH_BASE', $path);

//	define('PATH_BASE','E:\xampp\htdocs\svn\t_ionevn\code\\'); // edit
//	define('URL_ROOT','http://localhost/svn/t_ionevn/code/'); // edit
//	define('URL_ROOT_REDUCE','/svn/t_ionevn/code/');
//	
//	if (!defined('DS')) {
//		define('DS', DIRECTORY_SEPARATOR);
//	}
//print_r($_SERVER);
	define('IS_REWRITE',1);
	define('USE_CACHE',0);
	define('USE_BENMARCH',1);
	define('USE_MEMCACHE',0);
	define('WRITE_LOG_MYSQL',1);
	define('CACHE_TIME', 20);// for cache global (2)
	define('TEMPLATE','msmobile');
	
	define('COMPRESS_ASSETS',0);// nén js,css
	define('CACHE_ASSETS',1000); // thời gian cache JS,CSS, được sử dụng khi nén js,css
?>
