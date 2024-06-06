<?php 
	class ProductsModelsShopdetail extends FSModels
	{
		var $limit;
		var $page;
		function __construct()
		{
			$limit = 100;
			$page = FSInput::get('page');
			$this->limit = $limit;
			$this -> table_name = 'booth_api';
			$this -> table_name2 = 'small_price';
			
			$this -> table_name_1 = 'details_shop_order';
			$this -> arr_img_paths = array(array('resized',370,247,'cut_image'),array('small',127,72,'cut_image'),array('large',600,315,'cut_image'));
			$cyear = date ( 'Y' );
			$cmonth = date ( 'm' );
			$cday = date ( 'd' );
			$this->img_folder = 'images/tags/' . $cyear . '/' . $cmonth . '/' . $cday;
			$this->check_alias = 0;
			$this->field_img = 'image';
			$this -> check_alias = 1;
			parent::__construct();
		}
		
		
		function getDataShop($id, $date){
		    
		    global $db;
		    
		    if(!empty($date)){
		        $query = " SELECT code FROM ".$this -> table_name." WHERE 1=1 AND id=".$id." AND created_at='".$date."'" ;
		        
		    }
		    else{
		         $query = " SELECT code FROM ".$this -> table_name." WHERE 1=1 AND id=".$id;
		    }
		    
		    
        	$data = $db->query($query);		
        	
        	$result = $db->getObject();
        	
		    return $result;
		}  
		
		function showPriceSmall($code){
		    
		     global $db;
		    
		    $query = " SELECT price FROM ".$this -> table_name2." WHERE 1=1 AND code='".$code."'";
		    
        	$data = $db->query($query);		
        	
        	$result = $db->getObject();
        	
		    return $result;
		    
		}
		
		function getShopOrderData($code, $date){
		    
		    global $db;
		    
		     if(!empty($date)){
		        $query = " SELECT * FROM ".$this -> table_name_1." WHERE 1=1 AND client_id='".$code."' AND date_document='".$date."'" ;
		        
		       
		    }
		    else{
		         $query = " SELECT * FROM ".$this -> table_name_1." WHERE 1=1 AND client_id=".$code;
		    }
		    
		   
		   
        	$data = $db->query($query);		
        	
        	$result = $db->getObjectList();
        	
        	return $result;
		}
	
		
	}
	
?>