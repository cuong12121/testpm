<?php
	// models 

		  
	class ProductsControllersTags extends Controllers
	{
		function __construct()
		{
			$this->view = 'tags' ; 
			parent::__construct(); 
		}
		function display()
		{
			parent::display();
			$sort_field = $this -> sort_field;
			$sort_direct = $this -> sort_direct;
			
			dd(1);
			
// 			$model  = $this -> model;
// 			$list = $this -> model->get_data();
// 			$pagination = $model->getPagination();
// 			include 'modules/'.$this->module.'/views/'.$this->view.'/list.php';
		}	
	}
	
?>