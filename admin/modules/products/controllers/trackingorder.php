<?php
	// models 

		  
	class ProductsControllerstrackingorder extends Controllers
	{
		function __construct()
		{
			$this->view = 'trackingorder' ; 
			parent::__construct(); 
		}
		function display()
		{
			parent::display();
			$sort_field = $this -> sort_field;
			$sort_direct = $this -> sort_direct;
			$model  = $this -> model;
	
			
			$result  = $this -> model->showPD();
			
		 
			
		  //  foreach($list as $val){
		  //      echo $val->code.'<br>';
		  //  }
// 		    die();
// 			$pagination = $model->getPagination();
			include 'modules/products/views/trackingorder/list.php';
		}	
	}
	
?>