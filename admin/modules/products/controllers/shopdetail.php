<?php
	// models 

		  
	class ProductsControllersShopdetail extends Controllers
	{
		function __construct()
		{
			$this->view = 'shopdetail' ; 
			parent::__construct(); 
		}
		
		
		function display()
		{
		    $id = FSInput::get('id');
		    
		    $date = $_GET['date'];
		    
		    $model  = $this -> model;
		    
		    $code_product = $model->getDataShop($id, $date);
		    
		    if(!empty($code_product)){
		        
		         $result =  $model->getShopOrderData($code_product->code, $date);
	
		        include 'modules/products/views/shop_detail/list.php';
		    }
		    else{
		        echo "Mã shop này không đúng";
		    }
		    
		   
		}	
	}
	
?>