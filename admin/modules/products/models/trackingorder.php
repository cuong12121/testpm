<?php 
	class ProductsModelstrackingorder extends FSModels
	{
		var $limit;
		var $page;
		function __construct()
		{
			$limit = 100;
			$page = FSInput::get('page');
			$this->limit = $limit;
			$this -> table_name = 'tracking_order';
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
		
		// 		dem so luong 1 san pham trong  model cuong
		
		function showPD(){
		    global $db;
		    
		    $where = "";
		    
		    $ordering = "";
		    
		    $date_add = "";
		    
		    
            // Storing the value in session
            $_SESSION['order_date'] = FSInput::get('order_date');
            
            if(!empty($_SESSION['order_date']))
			{
			    
			    $date = $_SESSION['order_date'];
			    
			    $date_from = strtotime($date);
				$date_new = date('Y-m-d',$date_from);
			    $where .= ' AND order_date =  "'.$date_new.'"';
			    $date_add = $where;
			}
            
            if(!empty($_SESSION[$this -> prefix.'keysearch']))
			{
			    $code = trim($_SESSION[$this -> prefix.'keysearch']);
                // trường hợp tồn tại order_date
                $where = ' AND internal_code =  "'.$code.' "'.$date_add.' OR fast_code =  "'.$code.' "'.$date_add.' OR 	sku =  "'.$code.'"'.$date_add;
			}
			
			
		    if(empty($_SESSION[$this -> prefix.'keysearch']) && empty($_SESSION['order_date'])){
		        
		        $ordering .= " ORDER BY order_date DESC LIMIT 50";
		    
		        $where .= 'and internal_code IS NOT NULL ';
		    }
		    
		   
		    
		   	$query = " 	   SELECT * FROM ".$this -> table_name." 
						  	WHERE 1=1 ".$where.$ordering." ";
					  	
			$query = $db->getObjectList($query);
			
			return 	$query;
			
		
		}
		
	
		
		function setQuery()
		{
			// ordering
			$ordering = '';
			$where = "  ";
			
// 			lấy thời gian trong form post
			if(isset($_SESSION[$this -> prefix.'text0']))
			{
				$date_from = $_SESSION[$this -> prefix.'text0'];
				if($date_from){
					$date_from = strtotime($date_from);
					$date_new = date('Y-m-d H:i:s',$date_from);
					$where .= ' AND created_at >=  "'.$date_new.'" ';
				}
			}
			
				// to
			if(isset($_SESSION[$this -> prefix.'text1']))
			{
				$date_to = $_SESSION[$this -> prefix.'text1'];
				if($date_to){
					$date_to = $date_to . ' 23:59:59';
					$date_to = strtotime($date_to);
					$date_new = date('Y-m-d H:i:s',$date_to);
					$where .= ' AND created_at <=  "'.$date_new.'" ';
				}
			}
			
			
			if(isset($_SESSION[$this -> prefix.'sort_field']))
			{
				$sort_field = $_SESSION[$this -> prefix.'sort_field'];
				$sort_direct = $_SESSION[$this -> prefix.'sort_direct'];
				$sort_direct = $sort_direct?$sort_direct:'asc';
				$ordering = '';
				if($sort_field)
					$ordering .= " ORDER BY $sort_field $sort_direct, created_at DESC, id DESC ";
			}
			
			if(!$ordering)
				$ordering .= " ORDER BY created_at DESC , id DESC ";
			
			if(isset($_SESSION[$this -> prefix.'keysearch'] ))
			{
				if($_SESSION[$this -> prefix.'keysearch'] )
				{
					$keysearch = $_SESSION[$this -> prefix.'keysearch'];
					$where .= " AND name LIKE '%".$keysearch."%' ";
				}
			}
			$query = " 	   SELECT * 
						
						  FROM ".$this -> table_name." 
						  	WHERE 1=1 ".
						 $where.
						 $ordering. " ";
			return $query;
		}


		function save($row = array(), $use_mysql_real_escape_string = 1){
			$row['edited_time'] = date('Y-m-d H:i:s');
			$row['user_edit_id'] = $_SESSION['ad_userid'];
			$row['user_edit_name'] = $_SESSION['ad_username'];
			
			$record_id =  parent::save($row);
			
			return $record_id;
		}
		
		
	}
	
?>