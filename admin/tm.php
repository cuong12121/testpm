<?php


require_once ("../includes/config.php");
require_once ("includes/defines.php");
require_once ('../libraries/database/pdo.php');
global $db;
$db = new FS_PDO();

require_once ("../includes/functions.php");
require_once ("../libraries/fsinput.php");
require_once('../libraries/fsfactory.php');
require_once ("../libraries/fstext.php");
require_once ("libraries/fstable.php");
require_once ("libraries/toolbar/toolbar.php");
require_once ("libraries/fsrouter.php");
require_once ("libraries/pagination.php");
require_once ("libraries/template_helper.php");
require_once ('../libraries/errors.php');
require_once ('../libraries/fsfactory.php');
require_once ("libraries/fssecurity.php");
require_once ('libraries/controllers.php');
require_once ('libraries/models.php');
include_once '../libraries/ckeditor/fckeditor.php';
require_once ('libraries/controllers_categories.php');
require_once ('libraries/models_categories.php');

class OrderControllersUpload extends Controllers
	{
		function __construct()
		{
			$this->view = 'upload'; 
			parent::__construct(); 
		}

		function returnDataPDF($path)
		{
			$data =1;

		   	return $data;
		}	
}	

$code = new OrderControllersUpload();

echo $code->returnDataPDF;	