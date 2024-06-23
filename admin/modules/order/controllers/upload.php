<?php
	class OrderControllersUpload extends Controllers
	{
		function __construct()
		{
			$this->view = 'upload'; 
			parent::__construct(); 
		}
		
	    
		
		
		
		function display()
		{
		    
            parent::display();
			global $config;
			$sort_field = $this -> sort_field;
			$sort_direct = $this -> sort_direct;
			$model  = $this -> model;
			$wrap_id_warehouses = $model->get_wrap_id_warehouses();
			$warehouses = $model -> get_records('published = 1 AND id IN ('.$wrap_id_warehouses.')','fs_warehouses');

			$platforms = $model -> get_records('published = 1','fs_platforms');
			$houses = $model -> get_records('published = 1','fs_house');
			$users = $model -> get_record('id = ' . $_SESSION['ad_userid'],'fs_users');
			
		
			$list = $this -> model->get_data();
			
// 			$total = $this -> model->get_data_false()[1];
			
	

			// $iddd="";
			// foreach ($list as $key => $l) {
			// 	$iddd .= $l->id.',';
			// }
			// echo $iddd;


			// foreach ($list as $key => $l) {
			// 	$row = array();
			// 	$row['house_id'] = $l->house_id;
			// 	$row['warehouse_id'] = $l->warehouse_id;
			// 	$model->_update($row,'fs_order_uploads_detail','record_id = '.$l->id);
			// }
		


			$pagination = $model->getPagination();
			include 'modules/'.$this->module.'/views/'.$this->view.'/list.php';
		}
		
		
		
		
		function test(){
		    
		    
		    
		    
		    $model  = $this -> model;
		    
		  //  $path_file_ex = PATH_BASE.'files/ex5.xlsx';
		    
		  //  $data_excel = $model->showDataExcelShopee($path_file_ex);
		    
		  //  echo'<pre>';
		    
		  //  print_r($data_excel);
		    
		  //  echo '</pre>';
		    
		  //  die;
		    
		  //  print_r($data_excel);
		    
		  //  die



		    $path = PATH_BASE.'files/sp4.pdf';
		    
		    $content = $model->textpdfs($path);

		    // print_r($content);

		    // die;
		    
		  //  print_r($model->convertContentLazada($content[0]));
		  
		 
            // $text = preg_replace('/\s+/', ' ', $content[0]);
            
            // print_r($content);

            $datas = $model->convertContent($content);

            $check  = $model->convertContentCheck($content);

            print_r($check);





		    // print_r(array_unique($datas[0]));
		    
		    die;
		    
		  //  1 là lazada 2 là shopee
		  
		    $data_track = $model ->retun_file_pdf($path,2);
		    
		    
		    
		    $flatArray1 = $model->flattenArray($data_track);
            $flatArray2 = $model->flattenArray($data_excel);
            
            // Find the differences
            $difference1 = array_diff_assoc($flatArray1, $flatArray2);
            // $difference2 = array_diff_assoc($flatArray2, $flatArray1);
            
            // Combine differences
            $differences = array_merge($difference1);
            
             echo'<pre>';
		    
		    print_r($differences);
		    
		    echo '</pre>';
		    
		    die;
		    
            
            
		  
		  //  print_r(count($data_track));
		  
		  //  echo '<pre>';
		    
		  //  print_r($data_track);
		  
		  //  echo '</pre>';
		    
		 
		  //  die;
		    
		    if(!empty(array_diff_assoc($data_track, $data_excel))){
		        
		        $different = array_diff_assoc($data_track, $data_excel);
		        
		        foreach($different as $key=>$value){
		            
		            echo 'kiểm tra lại mã vận đơn '.$key.' có mã sku '.$value. ' trên file pdf không giống với file excel xin kiểm tra lại'; 
		        }
		        
		       
		        
		    }
		    else{
		        print_r('2 file pdf và excel trùng khớp');
		    }
		    
		    die;
		    


		    $content = $model ->get_data_to_text_pdf($path);
		    
		    $datas = [];
		    
		    
		    
		    if(trim($content[0]) ==''){
                        
                echo "vui lòng tạo lại file pdf đơn hàng, phần mềm  không thể nhận diện được!";
                
                die;
                
            }
            
        
            if(!empty($content) && count($content)>0){
                
                foreach ($content as $val){
                    
                     //  $content = $model ->get_data_to_text_pdf($path);
		    
        		    $convert =  str_replace("\n", "", $val); 
        		    
        		  //  đơn shopee, lazada không xóa dấu cách
        		    
        		    $convert =  str_replace(" ", "", $convert); 
        		    
        			
        			$data = $model->convertContent($convert);
                    
                    array_push($datas, $data[0][0]);
                    
                }
                
            }
            
            print_r($datas);
            
            die;
            
            
		  //  $data = $model->retun_file_pdf_test($path);
		    
		  //  print_r(str_replace(' ', '', $content[0]));
		  
		   
		}
		
	
        
		
		function retun_file_pdf($path){
		    
		    $model  = $this -> model;

			$content = $model ->pdf_to_texts($path);
			
			$data = $model->convertContent($content);
			
			$result = [];
			
			foreach ($data[0] as $key => $value){
			    
			    $data_image_convert = $model->convert_pdf_to_image_to_read_code($key,$path);
			    
			    $command = "zbarimg --raw $data_image_convert";
    	    
    	        $data_get = shell_exec($command);
    	        
    	        if (file_exists($data_image_convert)) {
    	            
                    unlink($data_image_convert);
                       
                }
                
                $result[$data_get] = $value;
			    
			    
			}
			
			return $result;
		}
		
		
		
		

		function add()
		{
			global $config;
			$model = $this -> model;
			$wrap_id_warehouses = $model->get_wrap_id_warehouses();
			$warehouses = $model -> get_records('published = 1 AND id IN ('.$wrap_id_warehouses.')','fs_warehouses');
			$platforms = $model -> get_records('published = 1','fs_platforms');
			$houses = $model -> get_records('published = 1','fs_house');
			$users = $model -> get_record('id = ' . $_SESSION['ad_userid'],'fs_users');
			if($users->group_id == 1 && $users->shop_id){
				$users->shop_id = substr($users->shop_id, 1, -1);
				$shops = $model -> get_records('id IN ('.$users->shop_id.')','fs_shops');
			}else{
				$shops = $model -> get_records('','fs_shops');
			}
			include 'modules/'.$this->module.'/views/'.$this -> view.'/detail.php';
		}

		function edit()
		{
			global $config;
			$model = $this -> model;
			$ids = FSInput::get('id',array(),'array');
			$id = $ids[0];
			$data = $model->get_record_by_id($id);
			$wrap_id_warehouses = $model->get_wrap_id_warehouses();
			$warehouses = $model -> get_records('published = 1 AND id IN ('.$wrap_id_warehouses.')','fs_warehouses');
			$platforms = $model -> get_records('published = 1','fs_platforms');
			$houses = $model -> get_records('published = 1','fs_house');

			$users = $model -> get_record('id = ' . $_SESSION['ad_userid'],'fs_users');
			if($users->group_id == 1 && $users->shop_id){
				$users->shop_id = substr($users->shop_id, 1, -1);
				$shops = $model -> get_records('id IN ('.$users->shop_id.')','fs_shops');
			}else{
				$shops = $model -> get_records('','fs_shops');
			}
			
			include 'modules/'.$this->module.'/views/'.$this->view.'/detail.php';
		}

		function fix_uploads_page_pdf(){
			$model = $this -> model;
			$model->fix_uploads_page_pdf();
		}

		function add_uploads_page_pdf(){
			$model = $this -> model;
			$model->add_uploads_page_pdf();
		}

		function prints_fix_err()
		{
			// echo 111;
			// die;
			$model = $this -> model;
			$rows = $model->prints_fix_err();
			$link = 'index.php?module='.$this -> module.'&view='.$this -> view;
			$page = FSInput::get('page',0);
			if($page > 1){
				$link .= '&page='.$page;
			}
				$link = FSRoute::_('index.php?module='.$this -> module.'&view='.$this -> view);	
			if($rows)
			{
				setRedirect($link,$rows.' '.FSText :: _('hóa đơn đã được in'));	
			}
			else
			{
				setRedirect($link,FSText :: _('Không có đơn nào được in, vui lòng kiểm tra lại file đã nhập lên có khớp nhau không'),'error');	
			}
		}

		function prints()
		{
			$model = $this -> model;
		
			$rows = $model->prints(1);
			
			die;
			
			
			
			$link = 'index.php?module='.$this -> module.'&view='.$this -> view;
			$page = FSInput::get('page',0);
			if($page > 1){
				$link .= '&page='.$page;
			}
				$link = FSRoute::_('index.php?module='.$this -> module.'&view='.$this -> view);	
			if($rows)
			{
				setRedirect($link,$rows.' '.FSText :: _('hóa đơn đã được in'));	
			}
			else
			{
				setRedirect($link,FSText :: _('Không có đơn nào được in, vui lòng kiểm tra lại file đã nhập lên có khớp nhau không'),'error');	
			}
		}
		
	}

	function view_pdf($controle,$id){
		$model = $controle -> model;
		
		
		$data = $model->get_record('id = ' .$id,'fs_order_uploads','id,file_pdf,total_page_pdf');
		if(!$data-> file_pdf){
			$html ='<strong style="color:red">Lỗi thiếu file</strong>';
			return $html;
		}
		$link = $data-> file_pdf;
		
		
		
		$arr_name = explode('t,t',$link);
		
		$html ="";
		if(!empty($arr_name)){
			$i=0;
			foreach ($arr_name as $name_item) {
				$base_name = basename($name_item);
				if($i == 0){
					$path = str_replace($base_name,'',$name_item);
				}
				if(!file_exists(str_replace('admin/order/','',PATH_BASE.$path.$base_name))){ 
				    
				    $file_direct_check = trim($path.$base_name); 
				    
				    $checkfile = $model->get_record('file_name = "' .$base_name.'"','file_id_drive','id_file_drive,file_name ');
				    
		    		// echo "<pre>";

        //     		var_dump($checkfile);
            		
        //     		echo "</pre>";
            		
        //     		die;
		
				    
				//     	$query = " SELECT " . $select . "
				// 	  FROM " . $table_name . "
				// 	  WHERE " . $where;
					 
		
    //             		global $db;
    //             		$db->query ( $query );
    //             		$result = $db->getObject ();
				    
				    if(!empty($checkfile)){
				         $url = 'https://drive.dienmayai.com/get.php?mime=pdf&showfile='.$checkfile->id_file_drive;
				         
				        $html .= '<a target="_blank" style="color: rgba(255, 153, 0, 0.79);" href="'.$url.'">'.$base_name.'</a><br/>';
				    }
				    else{
				        
				        
				        $html .= '<a target="_blank" style="color: red;" href="javascript:void(0)">'.$file_direct_check.'</a><br/>';
				    }
				    
				
				
				}else{
				    
					$html .= '<a target="_blank" style="color: rgba(255, 153, 0, 0.79);" href="'.URL_ROOT.$path.$base_name.'">Lỗi file</a><br/>';
				}
				
				$i++;
			}
		}else{
		    
			$html .= '<a style="color: rgba(255, 153, 0, 0.79);" target="_blink" href="'.URL_ROOT.$value.'">'.$value.'</a><br/>';
		}

		//kiểm tra page cod cắt đủ ko
		$data_file_pdf = $model->get_records('record_id = ' .$id,'fs_order_uploads_page_pdf','id');
		if($id > 3571800000000000000000000){
			if(empty($data_file_pdf) || count($data_file_pdf) != $data -> total_page_pdf){
				return '<a style="color: red;" target="_blink" href="' . $link . '">Lỗi không nhận đủ trang PDF, Vui lòng up lại file</a>';
			}
		}
		
	
		$data_detail = $model->get_record('record_id = ' .$id,'fs_order_uploads_detail','id');
		if(empty($data_detail)){
		    
		    if(!empty($link)){
		        return '<a style="color: rgba(255, 153, 0, 0.79);" target="_blink" href="/'. $link .'">'.basename($link).'</a>';
		    }
		    else{
		        return '<a style="color: red;" target="_blink" href="' . $link . '">Lỗi file</a>';
		    }
			
		}else{
			return $html;
		}
		
	}

	function view_excel($controle,$id){
		$model = $controle -> model;
		
		
		$data = $model->get_record('id = ' .$id,'fs_order_uploads','id,file_xlsx,file_excel_drive');
		
	
		if(!$data-> file_xlsx){
			$html ='<strong style="color:red">Lỗi thiếu file</strong>';
			return $html;
		}
		$link = URL_ROOT.$data-> file_xlsx;
		
		if(!file_exists(str_replace('admin/order/','',PATH_BASE.$data-> file_xlsx))){
		    
		    $url = 'https://drive.dienmayai.com/get.php??mime=excel&showfile='.$data->file_excel_drive;
		    
		    if (!empty($data->file_excel_drive)) {
		        
              return '<a style="color: rgba(255, 153, 0, 0.79);" target="_blink" href="' . $url . '">'.$url.'</a>';
              
            } else {
              	return '<a style="color: red;" target="_blink" href="javascript:void(0)">Lỗi file</a>';
            }
		    
		   
		}

		$data_detail = $model->get_record('record_id = ' .$id,'fs_order_uploads_detail','id');
		
		
		if(empty($data_detail)){
		   
			return '<a style="color: red;" target="_blink">Lỗi </a>';
		}else{
			return '<a style="color: rgba(255, 153, 0, 0.79);" target="_blink" href="' . $link . '">'.basename($data-> file_xlsx).'</a>';
		}

		
	}

	function view_print($controle,$id){
		$model = $controle -> model;
		$data = $model->get_record('id = ' .$id,'fs_order_uploads','id,is_print');
		if($data-> is_print == 1){
			$txt = 'Đã In';
		}else{
			$txt = 'Chưa In';
		}
		return $txt;
	}

	
	
?>