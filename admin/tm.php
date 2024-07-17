<?php
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
			$data =1

		   	return $data;
		}	
}	

$code = new OrderControllersUpload();

echo $code->returnDataPDF;	