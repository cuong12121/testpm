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


		function returnDataPDF($path)
		{
			$model  = $this -> model;

			$data  = [];

			$datas = shell_exec('pdftk '.$path.' dump_data | grep NumberOfPages');

		    $number_page = intval(str_replace('NumberOfPages: ', '', $datas));

		   	if($number_page>0){

		   		for($i =0; $i<$number_page; $i++){

		   			$page = $i+1;

		   			$mvd = $model->contendTextFindMvd($path,$page);

				    $sku = $model->contendTextFindSku($path,$page);

				    $data['mavandon'][$i] = $mvd[0]??'';

				    $data['sku'][$i] = $sku[0]??'';

		   		}

		   	}

		   	return $data;
		}

		function dataPDF($files)
		{

			$all_data = [];

			$mvd = [];

			$sku = [];



			$dem = 0;

			$dems =0;

			$model = $this -> model;

			foreach ($files as $key => $value) {
				
				$file  = $value;
		   
			    $path  = PATH_BASE.'files/'.$file;
				
				$data  = $this->returnDataPDF($path);
	
			    array_push($all_data, $data);



			}
			if(count($all_data)){

				

				foreach ($all_data as $key => $vals) {


					if(count($vals['mavandon'])>0){

						foreach ($vals['mavandon'] as $key => $value) {
							$dem++;

							$mvd[$dem] = $value;
						
						}

					}

					if(count($vals['sku'])>0){

						foreach ($vals['sku'] as $key => $vals) {
							$dems ++;

							$sku[$dems] = $vals;
							

						}

					}
					
				}

			}

			$result['mavandon'] = $mvd;

			$result['sku'] = $sku;

			return $result;

			 // echo'<pre>'; var_dump($result); echo '</pre>';

			
		}

		function print_auto(){

			global $db;

			$model  = $this -> model;

            $platform = [1,2,3,4,6,8,9,10,11];

            // chạy đơn lúc 7h10

            $H = date('G');

            $house_id = $H<8?13:18;

            $data_info = [];

            for($i=1; $i<3; $i++){

                foreach ($platform as  $platforms) {
                    
                     $query =  "SELECT id FROM fs_order_uploads AS a WHERE 1=1 AND warehouse_id = ".$i." AND house_id = ".$house_id." AND platform_id = ".$platforms." AND date ='".date('Y-m-d')."' ORDER BY created_time DESC , id DESC";

                    $sql = $db->query ($query);
                    $result = $db->getObjectList ();

                    $list_Ar = [];

                    if(!empty($result)){

                        foreach ($result as $key => $value) {
                       
                            array_push($list_Ar, $value->id);
                        }

                    } 

                    $data_info['house_id'] = $house_id;

                    $data_info['platforms'] = $platforms;

                    $data_info['warehouse_id'] = $i;

                    $list_ar_str = implode(',', $list_Ar);

                    $model->prints_auto($list_ar_str, $data_info);


                }
            }    

		}


		function testOutPdf()
		{
			
			$print = $this->dataPDFBest();

			print_r($print);
			
		}

		function dataPDFBest()
		{

			$model  = $this -> model;

			$file = "best1.pdf";

			$filePath =  $path = PATH_BASE.'files/'.$file;

			$number_page = shell_exec('pdftk '.$filePath.' dump_data | grep NumberOfPages');

			$number_page = str_replace('NumberOfPages:', '', $number_page);

			$data = [];

			for ($i=0; $i < intval($number_page); $i++) { 
				
				$datas = shell_exec('pdftotext  -raw -f '.$i.' -l '.$i.' '.$filePath.' -');

				echo $datas;

				die;

				// thay thế ký tự xuống dòng bằng chuỗi rỗng

				$datas =  preg_replace("/\r?\n/", '', $datas);

				$mau_regex = '/(.*?)Người nhậ:/s'; // s cho phép . khớp với cả newline

				if (preg_match($mau_regex, $datas, $matches)) {

					$data[$i]['mavandon'] = $matches[1];
				   
				} 

				$data_convert = $model->convertContentbest($datas);

				$data[$i]['sku'] =  $data_convert[0];

			
			}

			return $data;


		}

		//lấy thông số file pdf lazada

		function dataPDFLazada()
		{
			$model  = $this -> model;

			$file = "la11.pdf";

			$filePath =  $path = PATH_BASE.'files/'.$file;

			$number_page = shell_exec('pdftk '.$filePath.' dump_data | grep NumberOfPages');

			$number_page = str_replace('NumberOfPages:', '', $number_page);

			$data = [];


			if( intval($number_page)>0){
				
				for ($i=1; $i<=$number_page; $i++) {
					

					$datas = shell_exec('pdftotext  -raw -f '.$i.' -l '.$i.' '.$filePath.' -');

					$data_convert = $model->convertContentLazada($datas);

				    $pattern = "/\d{10}VNA/";

					// Kiểm tra và lấy chuỗi phù hợp
					if (preg_match($pattern, $datas, $matches)) {
					    // In chuỗi phù hợp

					    $known = $matches[0];
					  
					   	$pattern = "/(.*?)" . preg_quote($known) . "/";

						// Kiểm tra và lấy phần chuỗi trước chuỗi đã biết
						if (preg_match($pattern, $datas, $matchess)) {
						    // In phần chuỗi phù hợp
						    // echo "Phần chuỗi trước '$known' là: " . $matchess[1];

						    $results = $matchess[1].$known;

						    $data[$i]['mavandon'] = $results;

						     $data[$i]['sku'] = @$data_convert[0][0];

						    // array_push($data, $results);
						}
					}

				}

			}
		}
		
		function test(){

			$model  = $this -> model;
			
			$file = !empty($_GET['file'])?$_GET['file']:'sp4.pdf';
		   
		    $path = PATH_BASE.'files/'.$file;

		    $test =  $model->showDataExcel($path);
		   
		    $filePDF = ['kgh-vnpost_1719639461_cv.pdf','kgh-spx_1719639461_cv.pdf','kgh-ghn_1719639461_cv.pdf'];

		    $data_pdf = $this->dataPDF($filePDF);

		    $checkMVD =  array_diff($data_pdf['mavandon'], $test['maVanDon']);

		    $checkSku =  array_diff($data_pdf['sku'], $test['Sku']);

		    // echo"<pre>"; var_dump($data_pdf['sku']); echo"</pre>"; echo "<br>"; echo"<pre>";var_dump($test['Sku']); echo"</pre>";

		    // die;



		    if(empty($checkMVD) && empty($checkSku)){

		    	echo "đơn hàng không bị lỗi";
		    }
		    else{

		    	if(!empty($checkMVD)){

		    		// print_r($checkMVD);
		    		echo 'kiểm tra lại các mã vận đơn sau ở file pdf <br>'. implode("<br>",$checkMVD). '<br>
		    		 không giống với file excel';
		    	}

		    	if(!empty($checkSku)){

		    		echo 'kiểm tra lại sku sau ở file pdf <br>'. implode("<br>",$checkSku). '<br>
		    		 không giống với file excel';
		    	}
		    }

		   

		    die;


		    
		    
		    $data = [];
		    
		 

		    $file = !empty($_GET['file'])?$_GET['file']:'sp4.pdf';
		   
		    $path = PATH_BASE.'files/'.$file;

		    $datas = shell_exec('pdftk '.$path.' dump_data | grep NumberOfPages');

		    $number_page = intval(str_replace('NumberOfPages: ', '', $datas));

		   	if($number_page>0){

		   		for($i =0; $i<$number_page; $i++){

		   			$mvd = $model->contendTextFindMvd($path,1);

				    $sku = $model->contendTextFindSku($path,1);

				    $data[$i]['mavandon'] = $mvd[0]??'';

				    $data[$i]['sku'] = $sku??'';

		   		}

		   	}

		    die;

		    // $option = !empty($_GET['option'])?$_GET['option']:'';

		    // $datass = shell_exec('pdftotext '.$option.' -f 1 -l  1 '.$path.' -');

		    // $convert = $model->convertContentCheck($datass);

		    // $select = !empty($_GET['select'])?1:'0';

		    // if($select ==1){
		    // 	print_r($convert);
		    // }
		    // else{
		    // 	echo $datass;
		    // }

			
		    // die;

		    $mvd = $model->contendTextFindMvd($path,1);

		    $sku = $model->contendTextFindSku($path,1);

		    $data[1]['mavandon'] = $mvd[0];

		    $data[1]['sku'] = $sku;

		    print_r($data[1]);

		    die;

		    // $mvd = $model->testpdf($path);

		    // print_r($mvd);

		    // die;


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