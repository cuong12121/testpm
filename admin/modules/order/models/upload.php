<?php
	include 'PDFMerger-tcpdf/PDFMerger.php';
	use PDFMerger\PDFMerger;
    
    use Escarter\PopplerPhp\PdfToText;
    use Escarter\PopplerPhp\getOutput;
    use Escarter\PopplerPhp\PdfSeparate;
    
  
    
	include 'fpdf/fpdf.php';
   	include 'fpdi/src/autoload.php';
   	require_once('vendor/autoload.php');
   	require_once(PATH_BASE.'vendor/autoload.php');
	class OrderModelsUpload extends FSModels
	{
		var $limit;
		var $page;
		function __construct()
		{
			$limit = 50;
			$page = FSInput::get('page');
			$this->limit = $limit;
			$this -> table_name = 'fs_order_uploads';
		
			parent::__construct();
		}
		
		
		
// 		cuong:them


        function countPdfPage($pdfFilePath){
            
            $parser = new \Smalot\PdfParser\Parser();

            // Parse the PDF file
            $pdf = $parser->parseFile($pdfFilePath);
            
            // Get the details of the PDF
            $details = $pdf->getDetails();
            
            $pageCount = 0;
            
            // Count the number of pages
            if (isset($details['Pages'])) {
                $pageCount = $details['Pages'];
            } 
            return $pageCount;
        }
        
        
        function findMVD($content){
		    
            // $text = trim(PdfToText::getText($filePath));
        
            // Tìm mã vận đơn (sau "Mã vận đơn:" và trên cùng một dòng)
            preg_match_all('/Mã vận đơn:\s*(\S+)/', $content, $maVanDonMatches);
            $maVanDon = isset($maVanDonMatches[1]) ? $maVanDonMatches[1] : null;

            if(empty($maVanDon)){

            	preg_match_all('/Mã đơn hàng:\s*([A-Z0-9]+)/', $content, $maVanDonMatches);
            	$maVanDon = isset($maVanDonMatches[1]) ? $maVanDonMatches[1] : null;
            }
            return $maVanDon;
            
		}   

		function contendTextFindMvd($filePath,$page)
		{
		 	$data = shell_exec('pdftotext -layout -f '.$page.' -l '.$page.' '.$filePath.' -');

		 	$mavd = $this->findMVD($data);

		 	return $mavd;
		} 

		function contendTextFindSku($filePath, $page)
		{
		 	$data = shell_exec('pdftotext  -raw -f '.$page.' -l '.$page.' '.$filePath.' - | cat');

		 	$Sku = $this->convertContentCheck($data);

		 	return $Sku[0]??'';
		} 
		
		
		function textpdfs($filePath){
		    $datas = shell_exec('pdftotext  -raw -f 1 -l 1 '.$filePath.' - | cat');
		    $data = shell_exec('pdftotext -f 1 -l 1 '.$filePath.' -');



		    // $datas = preg_replace('/\n/', '', $datas);

		    // $data = PdfToText::getText($filePath);


		    return($data);

		    // die;



		    $parser = new \Smalot\PdfParser\Parser();
        	$pdf = $parser->parseFile($filePath);
        	
        	$text = $pdf->getText();

        	try {
			    $pdf = $parser->parseFile($filePath); // Đường dẫn đến tệp PDF của bạn

			  
			    $text = $pdf->getText();


			} catch (\Exception $e) {
			    // Xử lý lỗi ở đây
			    echo "Lỗi khi chuyển đổi PDF: " . $e->getMessage();
			}
        	
        // 	$text = $this->convertContentLazada($text);
            $text = preg_replace('/\n/', ' ', $text);

            return $text;
            
            
		    $user_id = $_SESSION['ad_userid'];
		    
		    $folder = PATH_BASE.'files/page_cut_'.$user_id;
		    
		    if (!is_dir($folder)) {
                mkdir($folder, 0755, true); 
            }
		    
		     (new PdfSeparate())->setPdf($filePath)->setDestination($folder.'/file_%d.pdf')->split();
		     
		    
		   
            $files = scandir($folder);
            
            $data_pdf = [];
            
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') { // Loại bỏ các thư mục '.' và '..'
                    $filePath = $folder . '/' . $file;
                    
                  
                    $text = trim(PdfToText::getText($filePath));
                    
                    // preg_match('/^.*$/m', $text, $maVanDonMatches);
                    
                    // $mavandon = !empty($maVanDonMatches[0])?$maVanDonMatches[0]:'';
                    
                    
                    array_push($data_pdf,$text);
                    
                    // xóa file đã đọc
                    
                    // unlink($filePath);

                    // ... và các thông tin khác bạn cần
                }
            }
		 
		    return $data_pdf;
		  
	
		}
		
		function lazada_check_pdf($filePath){
		    
		    $user_id = $_SESSION['ad_userid'];
		    
		    $folder = PATH_BASE.'files/page_cut_'.$user_id;
		    
		    if (!is_dir($folder)) {
                mkdir($folder, 0755, true); 
            }
		    
		     (new PdfSeparate())->setPdf($filePath)->setDestination($folder.'/file_%d.pdf')->split();
		     
		    
		   
            $files = scandir($folder);
            
            $data_pdf = [];
            
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') { // Loại bỏ các thư mục '.' và '..'
                    $filePath = $folder . '/' . $file;
                    
                  
                    $text = trim(PdfToText::getText($filePath));
                    
                    preg_match('/^.*$/m', $text, $maVanDonMatches);
                    
                    $mavandon = !empty($maVanDonMatches[0])?$maVanDonMatches[0]:'';
                    
                    
                    array_push($data_pdf,$maVanDonMatches[0]);
                    
                    // xóa file đã đọc
                    
                    // unlink($filePath);

                    // ... và các thông tin khác bạn cần
                }
            }
		 
		    return $data_pdf;

		  //  $text = trim(PdfToText::getText($filePath));
		  //  // lazada
            
    //         preg_match('/^.*$/m', $text, $maVanDonMatches);
    //         return $maVanDonMatches[0]; // Output: <?php
		}    
        
        
        function get_data_to_text_pdf($path){
            
            $parser = new \Smalot\PdfParser\Parser();
            
            $pdf = $parser->parseFile($path);

            // Retrieve details from the PDF file
            $details  = $pdf->getDetails();
            
            $pageCount =0;
            
            if (isset($details['Pages'])) {
                $pageCount = $details['Pages'];
            } 
            
            // Retrieve the pages
            $pages = $pdf->getPages();
            
            // Get the text from a specific page
            // Assuming you want text from the first page (0-indexed)
            // Change this to the desired page number
            
            $ar_text =[];
            
            if (isset($details['Pages'])) {
                
                $pageCount = $details['Pages'];
            } 
           
                
            if (isset($details['Pages'])) {
                $pageCount = $details['Pages'];
            } 
            for($i=0; $i<$pageCount; $i++){
                
                $pageNumber = $i;
                
                 if (isset($pages[$pageNumber])) {
                
                    $page = $pages[$pageNumber];
                    
                    
                    $text = $page->getText();
                    
                    array_push($ar_text,  $text);
                 }   
                
            }
            
        
            
            return $ar_text;
        }
        
        function get_data_false(){
            global $db;
            
            $page = !empty($_GET['page'])?$_GET['page']:1;
			$query = 'SELECT * FROM fs_order_uploads AS a WHERE 1=1 AND a.date >= "2024-02-01 00:00:00" AND a.date <= "2024-02-29 23:59:59" AND warehouse_id IN (1,2,4) ORDER BY created_time DESC , id DESC';
			
			
			if(!$query)
				return array();
			// echo $query;
			// die;
			$sql = $db->query_limit($query,20,$page);
			
			$total = $db->getTotal($query);
			
			var_dump($total);
			$result = [$db->getObjectList(), $total];
			
			return $result;
        }
        
        
       

        function pdf_to_texts($file_pdf){
        	$server_file = $file_pdf;
        	$server_file = str_replace('/', DS,$server_file);
        	
        	
        	$parser = new \Smalot\PdfParser\Parser();
        	$pdf = $parser->parseFile($server_file);
        	
        	if($pdf != "") {
        	    $original_text = $pdf->getText();
        	    
        	    if ($original_text != ""){
        	        $text = nl2br($original_text); // Paragraphs and line break formatting
        	        $text = clean_ascii_characters($text); // Check special characters
        	        $text = str_replace(array("<br /> <br /> <br />", "<br> <br> <br>"), "<br /> <br />", $text); // Optional
        	        $text = addslashes($text); // Backslashes for single quotes     
        	        $text = stripslashes($text);
        	        $text = strip_tags($text);
        	         
        	        $check_text = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        	        $no_spacing_error = 0;
        	        $excessive_spacing_error = 0;
        	        foreach($check_text as $word_key => $word) {
        	            if (strlen($word) >= 199999) { // 30 is a limit that I set for a word length, assuming that no word would be 30 length long
        	                $no_spacing_error++;
        	            } else if (strlen($word) == 1) { // To check if the word is 1 word length
        	                if (preg_match('/^[A-Za-z]+$/', $word)) { // Only consider alphabetical words and ignore numbers.
        	                    $excessive_spacing_error++;
        	                }
        	            }
        	        }
        	         
        	        if ($no_spacing_error >= 30 || $excessive_spacing_error >= 199999) {
        	            echo "Too many formatting issues<br />";
        	            // echo $text;
        	            $text = trim($text);
        	            $text = str_replace(' -','-',$text);
        	            $text = str_replace('- ','-',$text);
        				$row = array();
        				$row['content'] = $text;
        	            // $this->_update($row,'fs_order_uploads_page_pdf','id ='.$id);
        	        } else {
        	           // echo "Success!<br />";
        	            // echo $text;
        	            $text = trim($text);
        	            $text = str_replace(' -','-',$text);
        	            $text = str_replace('- ','-',$text);
        				$row = array();
        				$row['content'] = $text;
        	            // $this->_update($row,'fs_order_uploads_page_pdf','id ='.$id);
        	        }
        
        	    } 
        
        	} 
        
        	return  $text;
        }
        
        function convertContenttiktok($content){
            
          
            
            if(empty($b[0])){
            	preg_match_all('/[0-9][0-9][0-9][A-Za-z]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[0-9][0-9][0-9]/', $content, $b);
            }
            if(empty($b[0])){
            	preg_match_all('/[A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]/', $content, $b);
            }
            
            return $b;
        }
        
        function convertContentbest($content){
            
           preg_match_all('/[0-9][0-9][0-9][A-Za-z]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[0-9][0-9][0-9]/', $content, $b);
           if(empty($b[0])){
            	preg_match_all('/[0-9][0-9][0-9][A-Za-z]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]/', $content, $b);
            }
            return $b;
        }
        
        function convertContentviettel($content){
            
            preg_match_all('/[0-9][0-9][0-9][A-Za-z]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]/', $content, $b);
            
            
            
            
            	return $b;
        }
        
        function convertContentLazada($content){
            
            if(empty($b[0])){
                preg_match_all('/[A-Z-0-9]{1}[A-Za-z0-9]{1}[A-Za-z0-9]{1}[A-Za-z0-9]{1}+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[0-9][0-9][0-9]/', $content, $b);
            }    
            return $b;
        }
        


        function convertContent($content){
        
           
        	// $content = str_replace('(**) 1','(**)1 ',trim($content));
        	
        	
        	
        
        	// 425M-BL-00-BOS fix lỗi
        	
        // 	if(empty($b[0])){
        // 		preg_match('/[0-9][0-9][0-9][A-Za-z0-9]+-[A-Za-z0-9] [A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]/', $content, $b);
        // 	}
        	// shopee
        	preg_match('/Tổng SL sản phẩm:\s*(\d+)/', $content, $matches);
        	$so_luong_san_pham = 0;
			if (isset($matches[1])) {
			    $so_luong_san_pham = $matches[1];
			   
			} 

        	if(empty($b[0])){
        		 preg_match_all('/[A-Z-0-9]{1}[A-Za-z0-9]{1}[A-Za-z0-9]{1}[A-Za-z0-9]{1}+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-/', trim($content), $b);
        	}



        	// if(empty($b[0])){
        	// 	 preg_match_all('/[A-Z-0-9]{1}[A-Za-z0-9]{1}[A-Za-z0-9]{1}[A-Za-z0-9]{1}+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]/', trim($content), $b);
        	// }
        	
        	// if(empty($b[0])){
        	// 	 preg_match_all('/[A-Z-0-9]{1}[A-Za-z0-9]{1}[A-Za-z0-9]{1}[A-Za-z0-9]{1}+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[0-9][0-9]/', trim($content), $b);
        	// }
        	
        	
        	
        	
        	
        	
        	
        	
        // 	if(empty($b[0])){
        // 		preg_match_all('/[A-Z-0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]/', $content,$b);
        // 	}
        	
        	return $b;
        	
        // 	if(empty($b[0])){
        // 		preg_match('/[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-/', $content, $b);
        // 	}
        
        // 	if(empty($b[0])){
        // 	    echo 1;
        // 		die;
        // 	}
        	
        // 	$arr_c = explode('-', $b[0]);
        // 	if(empty($arr_c[0])){
        // 	    echo 1;
        // 		die;
        // 	}
        
        // 	$sku_split = str_split($arr_c[0],3);
        // 	$row_11 = array();
        // 	$row_11['sku_fisrt'] = @$sku_split[0];
        // 	$row_11['sku_last'] = @$sku_split[1];
       
        // $this->_update($row_11,'fs_order_uploads_page_pdf','id = '. $item_page_pdf-> id);
        
        }

         function convertContentCheckExcel($content){

            // if(empty($b[0])){
            //  preg_match_all('/[A-Z-0-9]{1}[A-Za-z0-9]{1}[A-Za-z0-9]{1}[A-Za-z0-9]{1}+\s*-\s*[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9]+[[0-9]{1,2}|0]/', $content, $b);
            // }

            
            if(empty($b[0])){
                preg_match_all('/[A-Z-0-9]{1}[A-Za-z0-9]{1}[A-Za-z0-9]{1}[A-Za-z0-9]{1}+\s*-\s*[A-Za-z0-9][A-Za-z0-9]+-+\s[A-Za-z0-9][A-Za-z0-9]+\s*-\s*[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+\s*-\s*[A-Za-z0-9][A-Za-z0-9]+\s*-\s*[A-Za-z0-9][A-Za-z0-9][0-9]/', $content, $b);
            }

            // if(empty($b[0])){
            //     preg_match_all('/[A-Z-0-9]{1}[A-Za-z0-9]{1}[A-Za-z0-9]{1}[A-Za-z0-9]{1}+\s*-\s*[A-Za-z0-9][A-Za-z0-9]+\s*-\s*[A-Za-z0-9][A-Za-z0-9]+\s*-\s*[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+\s*-\s*[A-Za-z0-9][A-Za-z0-9]+\s*-\s*[[0-9]{1,2}|0]/', $content, $b);
            // }
         


            return $b;

        }   


        function convertContentCheck($content){

        	// if(empty($b[0])){
        	// 	preg_match_all('/[A-Z-0-9]{1}[A-Za-z0-9]{1}[A-Za-z0-9]{1}[A-Za-z0-9]{1}+\s*-\s*[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9]+[[0-9]{1,2}|0]/', $content, $b);
        	// }

        	
        	if(empty($b[0])){
        		preg_match_all('/[A-Z-0-9]{1}[A-Za-z0-9]{1}[A-Za-z0-9]{1}[A-Za-z0-9]{1}+\s*-\s*[A-Za-z0-9][A-Za-z0-9]+-+\s[A-Za-z0-9][A-Za-z0-9]+\s*-\s*[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+\s*-\s*[A-Za-z0-9][A-Za-z0-9]+\s*-\s*[A-Za-z0-9][A-Za-z0-9][0-9]/', $content, $b);
        	}

        	if(empty($b[0])){
        		preg_match_all('/[A-Z-0-9]{1}[A-Za-z0-9]{1}[A-Za-z0-9]{1}[A-Za-z0-9]{1}+\s*-\s*[A-Za-z0-9][A-Za-z0-9]+\s*-\s*[A-Za-z0-9][A-Za-z0-9]+\s*-\s*[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+\s*-\s*[A-Za-z0-9][A-Za-z0-9]+\s*-\s*[[0-9]{1,2}|0]/', $content, $b);
        	}
            // xóa khoảng trắng trong chuỗi trả về của hàm trên
            $b = array_map(function($match) {
                return preg_replace('/\s+/', '', $match);
            }, $b);


        	return $b;

        } 	


        
        // cuong:end
        
        
        
        
		
		function setQuery()
		{
			// ordering
			$ordering = '';
			$where = "  ";


			// from
			if(isset($_SESSION[$this -> prefix.'text0']))
			{
				$date_from = $_SESSION[$this -> prefix.'text0'];
				if($date_from){
					$date_from = strtotime($date_from);
					$date_new = date('Y-m-d H:i:s',$date_from);
					$where .= ' AND a.date >=  "'.$date_new.'" ';
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
					$where .= ' AND a.date <=  "'.$date_new.'" ';
				}
			}

			if(isset($_SESSION[$this -> prefix.'filter0'])){
				$filter = $_SESSION[$this -> prefix.'filter0'];
				if($filter){
					
					$where .= ' AND a.house_id =  "'.$filter.'" ';
				}
			}


			if(isset($_SESSION[$this -> prefix.'filter1'])){
				$filter = $_SESSION[$this -> prefix.'filter1'];
				if($filter){
					
					$where .= ' AND a.warehouse_id =  "'.$filter.'" ';
				}
			}

			if(isset($_SESSION[$this -> prefix.'filter2'])){
				$filter = $_SESSION[$this -> prefix.'filter2'];
				if($filter){
					
					$where .= ' AND a.platform_id =  "'.$filter.'" ';
				}
			}

			// SP nổi bật theo hãng
			if (isset ( $_SESSION [$this->prefix . 'filter3'] )) {
				$filter = $_SESSION [$this->prefix . 'filter3'];
				if ($filter) {
					if($filter == 1)
						$where .= ' AND a.is_print = 1';
					else 
						$where .= ' AND a.is_print <> 1';
				}

			}

			if(isset($_SESSION[$this -> prefix.'sort_field']))
			{
				$sort_field = $_SESSION[$this -> prefix.'sort_field'];
				$sort_direct = $_SESSION[$this -> prefix.'sort_direct'];
				$sort_direct = $sort_direct?$sort_direct:'asc';
				$ordering = '';
				if($sort_field)
					$ordering .= " ORDER BY $sort_field $sort_direct, created_time DESC, id DESC ";
			}
			
			if(!$ordering)
				$ordering .= " ORDER BY created_time DESC , id DESC ";
			
			if(isset($_SESSION[$this -> prefix.'keysearch'] ))
			{
				if($_SESSION[$this -> prefix.'keysearch'] )
				{
					$keysearch = $_SESSION[$this -> prefix.'keysearch'];
					$where .= " AND name LIKE '%".$keysearch."%' ";
				}
			}

			if($_SESSION['ad_groupid'] == 1){
				$where .= ' AND list_user_id_manage_shop LIKE "%,'.$_SESSION['ad_userid'].',%"';
			}

			$wrap_id_warehouses = $this->get_wrap_id_warehouses();
			$where .= ' AND warehouse_id IN ('.$wrap_id_warehouses.')';
			

			$query = "SELECT * FROM ".$this -> table_name." AS a WHERE 1=1 " . $where. $ordering. " ";
			return $query;
		}
		
		
		function showDataExcelShopee($file_path){
		    require_once("../libraries/PHPExcel-1.8/Classes/PHPExcel.php");
		    $objReader = PHPExcel_IOFactory::createReaderForFile($file_path);
			// $data = new PHPExcel_IOFactory();
			// $data->setOutputEncoding('UTF-8');
			$objReader->setLoadAllSheets();
			$objexcel = $objReader->load($file_path);
			$data =$objexcel->getActiveSheet()->toArray('null',true,true,true);
			// $data->load($file_path);
			unset($heightRow);	
			$heightRow=$objexcel->setActiveSheetIndex()->getHighestRow();
			// printr($data);
			unset($j);
			
			$data_row = [];
			

		
// 			if(!$result_id){
// 				$link = FSRoute::_('index.php?module=order&view=upload&task=add');
// 			}else{
// 				$link = FSRoute::_('index.php?module=order&view=upload&task=edit&id='.$result_id);
// 			}
			
			$row = array();
			
	
			//chạy vòng đầu để check lỗi trước
			for($j=2;$j<=$heightRow;$j++){
			    
			    
			    
			 //   nếu không tồn tại tracking code thì sẽ là đơn hỏa tốc nên lấy mã đơn hàng làm mã tracking
			    
			    $tracking_codes = !empty(trim($data[$j]['F']))?trim($data[$j]['F']):trim($data[$j]['A']);
				
				$row[$j][$tracking_codes] =  $this->convertContent(trim($data[$j]['S']))[0][0];
				
				// if(!$row['code'] || $row['code'] == 'null' ){
				// 	$this->remove_xml($result_id,$file_path);
				// 	$msg = 'Không được để trống Mã đơn hàng(cột A) dòng '.$j;
				// 	setRedirect($link,$msg,'error');
				// 	return false;
				// }
				// $row['sku_nhanh'] = trim($data[$j]['S']);
				// if(!$row['sku_nhanh'] || $row['sku_nhanh'] == 'null' ){
				// 	$this->remove_xml($result_id,$file_path);
				// 	$msg = 'Không được để trống SKU phân loại(cột S) dòng '.$j;
				// 	setRedirect($link,$msg,'error');
				// 	return false;
				// }
				// $row['count'] = trim($data[$j]['Z']);
				// if(!$row['count'] || $row['count'] == 'null' ){
				// 	$this->remove_xml($result_id,$file_path);
				// 	$msg = 'Không được để trống Số lượng(cột Z) dòng '.$j;
				// 	setRedirect($link,$msg,'error');
				// 	return false;
				// }
				// $row['shipping_unit_name'] = trim($data[$j]['G']);
				// if(!$row['shipping_unit_name'] || $row['shipping_unit_name'] == 'null' ){
				// 	$this->remove_xml($result_id,$file_path);
				// 	$msg = 'Không được để trống Đơn vị vận chuyển(cột G) dòng '.$j;
				// 	setRedirect($link,$msg,'error');
				// 	return false;
				// }

				// $row['tracking_code'] = trim($data[$j]['F']);
				// if(!$row['tracking_code'] || $row['tracking_code'] == 'null' ){
				// 	$this->remove_xml($result_id,$file_path);
				// 	$msg = 'Không được để trống Mã vận đơn(cột F) dòng '.$j;
				// 	setRedirect($link,$msg,'error');
				// 	return false;
				// }

				// $row['ma_kien_hang'] = trim($data[$j]['B']);
				// if(!$row['ma_kien_hang'] || $row['ma_kien_hang'] == 'null' ){
				// 	$this->remove_xml($result_id,$file_path);
				// 	$msg = 'Không được để trống Mã kiện hàng (cột B) dòng '.$j;
				// 	setRedirect($link,$msg,'error');
				// 	return false;
				// }
			
				// $data_excel[trim($data[$j]['F'])] = $row['sku_nhanh'];

				// $arr_other = explode('-',$row['sku_nhanh']);
				// $row['sku'] = $arr_other[0];
				// $row['color'] = $arr_other[1];
				// $row['size'] = $arr_other[2];
				// $row['shop_code'] = $arr_other[3];

				// if($row['color'] == '00' && $row['size'] == '00'){
				// 	$product_code = $row['sku'];
				// }else{
				// 	$product_code = $row['sku'].'-'.$row['color'].'-'.$row['size'];
				// }
				
				// array_push($data_row, $row);
			
			}
			
			return $row;
			
		}
		
		

		function upload_excel_shopee($file_path,$result_id,$shop_code,$house_id,$data_file_pf_ar){
			require_once("../libraries/PHPExcel-1.8/Classes/PHPExcel.php");
			$objReader = PHPExcel_IOFactory::createReaderForFile($file_path);
			// $data = new PHPExcel_IOFactory();
			// $data->setOutputEncoding('UTF-8');
			$objReader->setLoadAllSheets();
			$objexcel = $objReader->load($file_path);
			$data =$objexcel->getActiveSheet()->toArray('null',true,true,true);
			// $data->load($file_path);
			unset($heightRow);	
			$heightRow=$objexcel->setActiveSheetIndex()->getHighestRow();
			// printr($data);
			unset($j);
			

		
			if(!$result_id){
				$link = FSRoute::_('index.php?module=order&view=upload&task=add');
			}else{
				$link = FSRoute::_('index.php?module=order&view=upload&task=edit&id='.$result_id);
			}
			

			//chạy vòng đầu để check lỗi trước
			for($j=2;$j<=$heightRow;$j++){
				$row = array();
				$row['code'] = trim($data[$j]['A']);
				if(!$row['code'] || $row['code'] == 'null' ){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Không được để trống Mã đơn hàng(cột A) dòng '.$j;
					setRedirect($link,$msg,'error');
					return false;
				}
				$row['sku_nhanh'] = trim($data[$j]['S']);
				if(!$row['sku_nhanh'] || $row['sku_nhanh'] == 'null' ){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Không được để trống SKU phân loại(cột S) dòng '.$j;
					setRedirect($link,$msg,'error');
					return false;
				}
				$row['count'] = trim($data[$j]['Z']);
				if(!$row['count'] || $row['count'] == 'null' ){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Không được để trống Số lượng(cột Z) dòng '.$j;
					setRedirect($link,$msg,'error');
					return false;
				}
				$row['shipping_unit_name'] = trim($data[$j]['G']);
				if(!$row['shipping_unit_name'] || $row['shipping_unit_name'] == 'null' ){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Không được để trống Đơn vị vận chuyển(cột G) dòng '.$j;
					setRedirect($link,$msg,'error');
					return false;
				}

				// $row['tracking_code'] = trim($data[$j]['F']);
				// if(!$row['tracking_code'] || $row['tracking_code'] == 'null' ){
				// 	$this->remove_xml($result_id,$file_path);
				// 	$msg = 'Không được để trống Mã vận đơn(cột F) dòng '.$j;
				// 	setRedirect($link,$msg,'error');
				// 	return false;
				// }

				// $row['ma_kien_hang'] = trim($data[$j]['B']);
				// if(!$row['ma_kien_hang'] || $row['ma_kien_hang'] == 'null' ){
				// 	$this->remove_xml($result_id,$file_path);
				// 	$msg = 'Không được để trống Mã kiện hàng (cột B) dòng '.$j;
				// 	setRedirect($link,$msg,'error');
				// 	return false;
				// }
				
				$data_excel[trim($data[$j]['F'])] = $row['sku_nhanh'];

				$arr_other = explode('-',$row['sku_nhanh']);
				$row['sku'] = $arr_other[0];
				$row['color'] = $arr_other[1];
				$row['size'] = $arr_other[2];
				$row['shop_code'] = $arr_other[3];

				if($row['color'] == '00' && $row['size'] == '00'){
					$product_code = $row['sku'];
				}else{
					$product_code = $row['sku'].'-'.$row['color'].'-'.$row['size'];
				}

				// $product_code = $row['sku'];

				
				$produt = $this->get_record('code = "'.$product_code.'"','fs_products');




				if(empty($produt)){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Không tìm thấy sản phẩm có sku '.$product_code.' trong kho.';
					setRedirect($link,$msg,'error');
					return false;
				}

	
				global $config;
				if($produt-> price_min > 0 && $house_id != 4 && $house_id != 15 && $house_id != 14 && $config['check_price_min'] == 1){
					$gia_ban = trim($data[$j]['Y']);
					if((float)$produt-> price_min > (float)$gia_ban){
						$this->remove_xml($result_id,$file_path);
						$msg = 'Sản phẩm có sku '.$product_code.' không được bán nhỏ hơn ' . $produt-> price_min;
						setRedirect($link,$msg,'error');
						return false;
					}
				}
				
				$tracking_code_details = trim($data[$j]['F']);
				
                if(empty($data_file_pf_ar[0][$tracking_code_details])){
                    
                    $msg = 'Mã vận đơn, hoặc mã sku của vận đơn '. $tracking_code_details. ' không tồn tại';
            
                    
					setRedirect($link,$msg,'error');
					return false;
                    
                }
                else{
                     if($data_file_pf_ar[0][$tracking_code_details]!= substr($row['sku_nhanh'],0,21)){
                        
                        $msg = 'Mã vận đơn, hoặc mã sku của vận đơn '. $tracking_code_details. ' không trùng với mã ở file pdf xin vui lòng kiểm tra lại!';
                        setRedirect($link,$msg,'error');
                    
					    return false;
                    }
                }

                // cuong:tắt để test

				if(strtolower($shop_code) !== strtolower($row['shop_code'])){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Mã shop không đúng (cột S) dòng '.$j;
					setRedirect($link,$msg,'error');
					return false;
				}
			}
			
		


			$count_ss = 0;
			for($j=2;$j<=$heightRow;$j++){
				$row = array();
				$row['code'] = trim($data[$j]['A']);
				$row['sku_nhanh'] = trim($data[$j]['S']);
				$row['count'] = trim($data[$j]['Z']);
				$row['shipping_unit_name'] = trim($data[$j]['G']);
				$row['tracking_code'] = trim($data[$j]['F']);
				$row['find_pdf'] = $row['code'];

				if(trim($data[$j]['U'])){
					$row['gia_goc'] = trim($data[$j]['U']);
				}else{
					$row['gia_goc'] = 0;
				}

				if(trim($data[$j]['V'])){
					$row['nguoi_ban_tro_gia'] = trim($data[$j]['V']);
				}else{
					$row['nguoi_ban_tro_gia'] = 0;
				}

				if(trim($data[$j]['W'])){
					$row['shopee_tro_gia'] = trim($data[$j]['W']);
				}else{
					$row['shopee_tro_gia'] = 0;
				}


				if(trim($data[$j]['W'])){
					$row['shopee_tro_gia'] = trim($data[$j]['W']);
				}else{
					$row['shopee_tro_gia'] = 0;
				}

				if(trim($data[$j]['X'])){
					$row['tong_so_tien_duoc_nguoi_ban_tro_gia'] = trim($data[$j]['X']);
				}else{
					$row['tong_so_tien_duoc_nguoi_ban_tro_gia'] = 0;
				}

				if(trim($data[$j]['Y'])){
					$row['gia_uu_dai'] = trim($data[$j]['Y']);
				}else{
					$row['gia_uu_dai'] = 0;
				}

				if(trim($data[$j]['AA'])){
					$row['tong_gia_ban'] = trim($data[$j]['AA']);
				}else{
					$row['tong_gia_ban'] = 0;
				}

				if(trim($data[$j]['AL'])){
					$row['shipping_fee'] = trim($data[$j]['AL']);
				}else{
					$row['shipping_fee'] = 0;
				}

				if(trim($data[$j]['AM'])){
					$row['shipping_fee_tro_gia_shopee'] = trim($data[$j]['AM']);
				}else{
					$row['shipping_fee_tro_gia_shopee'] = 0;
				}

				$row['gia_tri_don_hang'] = trim($data[$j]['AB']); // giá trị đơn hàng

				$row['ma_kien_hang'] = trim($data[$j]['B']);

				$row['created_at'] = trim($data[$j]['C']);

				$row['ngay_gui_hang'] = trim($data[$j]['H']);
			
				$upload_exel = $this->save_excel($row,$result_id);
				if(!$upload_exel){
					continue;
				}else{
					$count_ss++;
				}
			}
			return $count_ss;
		}
		
			function flattenArray($array) {
                $flattened = [];
                foreach ($array as $subArray) {
                    foreach ($subArray as $key => $value) {
                        $flattened[$key] = $value;
                    }
                }
                return $flattened;
            }
        



		function upload_excel_don_ngoai($file_path,$result_id,$shop_code,$house_id){
			require_once("../libraries/PHPExcel-1.8/Classes/PHPExcel.php");
			$objReader = PHPExcel_IOFactory::createReaderForFile($file_path);
			// $data = new PHPExcel_IOFactory();
			// $data->setOutputEncoding('UTF-8');
			$objReader->setLoadAllSheets();
			$objexcel = $objReader->load($file_path);
			$data =$objexcel->getActiveSheet()->toArray('null',true,true,true);
			// $data->load($file_path);
			unset($heightRow);	
			$heightRow=$objexcel->setActiveSheetIndex()->getHighestRow();
			// printr($data);
			unset($j);
			

		
			if(!$result_id){
				$link = FSRoute::_('index.php?module=order&view=upload&task=add');
			}else{
				$link = FSRoute::_('index.php?module=order&view=upload&task=edit&id='.$result_id);
			}
			

			//chạy vòng đầu để check lỗi trước
			for($j=2;$j<=$heightRow;$j++){
				$row = array();
				$row['code'] = trim($data[$j]['A']);
				if(!$row['code'] || $row['code'] == 'null' ){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Không được để trống Mã đơn hàng(cột A) dòng '.$j;
					setRedirect($link,$msg,'error');
					return false;
				}
				$row['sku_nhanh'] = trim($data[$j]['L']);
				if(!$row['sku_nhanh'] || $row['sku_nhanh'] == 'null' ){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Không được để trống SKU phân loại(cột L) dòng '.$j;
					setRedirect($link,$msg,'error');
					return false;
				}
				$row['count'] = trim($data[$j]['O']);
				if(!$row['count'] || $row['count'] == 'null' ){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Không được để trống Số lượng(cột O) dòng '.$j;
					setRedirect($link,$msg,'error');
					return false;
				}
				$row['shipping_unit_name'] = trim($data[$j]['E']);
				if(!$row['shipping_unit_name'] || $row['shipping_unit_name'] == 'null' ){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Không được để trống Đơn vị vận chuyển(cột E) dòng '.$j;
					setRedirect($link,$msg,'error');
					return false;
				}

				$row['tracking_code'] = trim($data[$j]['D']);
				if(!$row['tracking_code'] || $row['tracking_code'] == 'null' ){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Không được để trống Mã vận đơn(cột D) dòng '.$j;
					setRedirect($link,$msg,'error');
					return false;
				}

				// $row['ma_kien_hang'] = trim($data[$j]['B']);
				// if(!$row['ma_kien_hang'] || $row['ma_kien_hang'] == 'null' ){
				// 	$this->remove_xml($result_id,$file_path);
				// 	$msg = 'Không được để trống Mã kiện hàng (cột B) dòng '.$j;
				// 	setRedirect($link,$msg,'error');
				// 	return false;
				// }

				$arr_other = explode('-',$row['sku_nhanh']);
				$row['sku'] = $arr_other[0];
				$row['color'] = $arr_other[1];
				$row['size'] = $arr_other[2];
				$row['shop_code'] = $arr_other[3];

				if($row['color'] == '00' && $row['size'] == '00'){
					$product_code = $row['sku'];
				}else{
					$product_code = $row['sku'].'-'.$row['color'].'-'.$row['size'];
				}

				// $product_code = $row['sku'];

				
				$produt = $this->get_record('code = "'.$product_code.'"','fs_products');
				if(empty($produt)){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Không tìm thấy sản phẩm có sku '.$product_code.' trong kho.';
					setRedirect($link,$msg,'error');
					return false;
				}

				if(strtolower($shop_code) !== strtolower($row['shop_code'])){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Mã shop không đúng (cột L) dòng '.$j;
					setRedirect($link,$msg,'error');
					return false;
				}

				// các khung giờ fake và giá = 0 thì không cân check giá min
				// global $config;
				// if($produt-> price_min > 0 && $house_id != 4 && $house_id != 15 && $house_id != 14 && $config['check_price_min'] == 1){
				// 	$gia_tri_don_hang = trim($data[$j]['V']);
				
				// 	if((float)$produt-> price_min > (float)$gia_tri_don_hang){
				// 		$this->remove_xml($result_id,$file_path);
				// 		$msg = 'Sản phẩm có sku '.$product_code.' không được bán nhỏ hơn ' . $produt-> price_min;
				// 		setRedirect($link,$msg,'error');
				// 		return false;
				// 	}
				// }
			}


			$count_ss = 0;
			for($j=2;$j<=$heightRow;$j++){
				$row = array();
				$row['code'] = trim($data[$j]['A']);
				$row['sku_nhanh'] = trim($data[$j]['L']);
				$row['count'] = trim($data[$j]['O']);
				$row['shipping_unit_name'] = trim($data[$j]['E']);
				$row['tracking_code'] = trim($data[$j]['D']);
				$row['find_pdf'] = $row['code'];
				$row['ma_kien_hang'] = trim($data[$j]['B']);
				$row['gia_tri_don_hang'] = trim($data[$j]['Q']); // giá trị đơn hàng

				$row['don_ngoai_tong_gia_tri_don'] = trim($data[$j]['Q']);
				$row['don_ngoai_phi_van_chuyen_du_kien'] = trim($data[$j]['R']);
				$row['don_ngoai_phi_van_chuyen_user_tra'] = trim($data[$j]['S']);
				$row['don_ngoai_phi_co_dinh'] = trim($data[$j]['X']);
				$row['don_ngoai_phi_dich_vu'] = trim($data[$j]['Y']);
				$row['don_ngoai_phi_thanh_toan'] = trim($data[$j]['Z']);
				$row['created_at'] = trim($data[$j]['C']);
				$row['ngay_gui_hang'] = trim($data[$j]['H']);

				$upload_exel = $this->save_excel($row,$result_id);
				if(!$upload_exel){
					continue;
				}else{
					$count_ss++;
				}
			}
			return $count_ss;
		}


		function remove_xml($id,$file_path){
			// $row = array();
			// $row['file_xlsx'] = "";
			// $this->_update($row,'fs_order_uploads','id ='.$id);
			unset($file_path);

			// xóa hết các item của file cũ đi
			$this->delete_item_update_amount_hold($id);
			$this -> _remove('id  = '.$id,$this -> table_name);
			$this -> _remove('order_id  = '.$id,'fs_profits');

		}




		function remove(){
			global $db;
			// check remove
			if(method_exists($this,'check_remove')){
				if(!$this -> check_remove()){
					Errors::_(FSText::_('Can not remove these records because have data are related'));
					return false;
				}
			}
			$cids = FSInput::get('id',array(),'array'); 
			
			if(!count($cids))
				return false;
			$str_cids = implode(',',$cids);
			
			$i = 0;
			foreach ($cids as $id) {
				$data = $this->get_record('id = ' . $id,$this -> table_name);
				if(!empty($data) && $data-> is_print != 1){
					$sql = " DELETE FROM ".$this -> table_name." WHERE id = " . $id;
					$row = $db->affected_rows($sql);
					if($row){
						// xóa hết các item của file cũ đi
						$this->delete_item_update_amount_hold($id);
						$this -> _remove('order_id  = '.$id,'fs_profits');
						$i++;
					}
				}
			}
			return $i;
		}


		function upload_excel_lazada($file_path,$result_id,$shop_code,$house_id,$data_file_pf_ar){
			require_once("../libraries/PHPExcel-1.8/Classes/PHPExcel.php");
			$objReader = PHPExcel_IOFactory::createReaderForFile($file_path);
			// $data = new PHPExcel_IOFactory();
			// $data->setOutputEncoding('UTF-8');
			$objReader->setLoadAllSheets();
			$objexcel = $objReader->load($file_path);
			$data =$objexcel->getActiveSheet()->toArray('null',true,true,true);
			// $data->load($file_path);
			unset($heightRow);	
			$heightRow=$objexcel->setActiveSheetIndex()->getHighestRow();
			// printr($data);
			unset($j);

			$link = FSRoute::_('index.php?module=order&view=upload&task=add');

			//chạy vòng đầu để check lỗi trước
			for($j=2;$j<=$heightRow;$j++){
				$row = array();
				$row['code'] = trim($data[$j]['M']);
				if(!$row['code'] || $row['code'] == 'null' ){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Không được để trống Mã đơn hàng(cột M) dòng '.$j;
					setRedirect($link,$msg,'error');

				}
				$row['sku_nhanh'] = trim($data[$j]['F']);
				if(!$row['sku_nhanh'] || $row['sku_nhanh'] == 'null' ){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Không được để trống SKU phân loại(cột F) dòng '.$j;
					setRedirect($link,$msg,'error');
					return false;
				}
				
				$tracking_code_details = trim($data[$j]['BG']);
				
                if(empty($data_file_pf_ar[0][$tracking_code_details])){
                    
                    
                    $msg = 'Mã vận đơn, hoặc mã sku của vận đơn '. $tracking_code_details. ' không tồn tại';
                    
                  
					setRedirect($link,$msg,'error');
					return false;
                    
                }
                else{
                    
                    if($data_file_pf_ar[0][$tracking_code_details]!= substr($row['sku_nhanh'],0,21)){
                        
                        $msg = 'Mã vận đơn, hoặc mã sku của vận đơn '. $tracking_code_details. ' không trùng với mã ở file pdf xin vui lòng kiểm tra lại!';
                        setRedirect($link,$msg,'error');
                        
					    return false;
                    }
                    
                }
				
				$row['shipping_unit_name'] = trim($data[$j]['BC']);
				if(!$row['shipping_unit_name'] || $row['shipping_unit_name'] == 'null' ){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Không được để trống Đơn vị vận chuyển(cột BC) dòng '.$j;
					setRedirect($link,$msg,'error');
					return false;
				}

				$row['tracking_code'] = trim($data[$j]['BG']);
				if(!$row['tracking_code'] || $row['tracking_code'] == 'null' ){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Không được để trống Mã vận đơn(cột BG) dòng '.$j;
					setRedirect($link,$msg,'error');
					return false;
				}
			
				// kiểm tra mã vận đơn để check pdf với excel
				
				

				$row['lazada_sku'] = trim($data[$j]['G']);
				if(!$row['lazada_sku'] || $row['lazada_sku'] == 'null' ){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Không được để trống Mã vận đơn(cột G) dòng '.$j;
					setRedirect($link,$msg,'error');
					return false;
				}

				

				$arr_other = explode('-',$row['sku_nhanh']);
				$row['sku'] = $arr_other[0];
				$row['color'] = $arr_other[1];
				$row['size'] = $arr_other[2];
				$row['shop_code'] = $arr_other[3];

				if($row['color'] == '00' && $row['size'] == '00'){
					$product_code = $row['sku'];
				}else{
					$product_code = $row['sku'].'-'.$row['color'].'-'.$row['size'];
				}
				

				$produt = $this->get_record('code = "'.$product_code.'"','fs_products');
				if(empty($produt)){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Không tìm thấy sản phẩm có sku '.$product_code.' trong kho.';
					setRedirect($link,$msg,'error');
					return false;
				}

			
				if(strtolower($shop_code) !== strtolower($row['shop_code'])){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Mã shop không đúng (cột S) dòng '.$j;
					setRedirect($link,$msg,'error');
					return false;
				}

				// các khung giờ fake và giá = 0 thì không cân check giá min
				global $config;
				if($produt-> price_min > 0 && $house_id != 4 && $house_id != 15 && $house_id != 14 && $config['check_price_min'] == 1){
					$gia_ban = trim($data[$j]['AV']);
				
					if((float)$produt-> price_min > (float)$gia_ban){
						$this->remove_xml($result_id,$file_path);
						$msg = 'Sản phẩm có sku '.$product_code.' không được bán nhỏ hơn ' . $produt-> price_min;
						setRedirect($link,$msg,'error');
						return false;
					}
				}

			}
			
			die;


			$count_ss = 0;
			for($j=2;$j<=$heightRow;$j++){
				$row = array();
				$row['code'] = trim($data[$j]['M']);
				$row['sku_nhanh'] = trim($data[$j]['F']);
				$row['count'] = 1;
				$row['shipping_unit_name'] = trim($data[$j]['BC']);
				$row['lazada_sku'] = trim($data[$j]['G']);
				$row['tracking_code'] = trim($data[$j]['BG']);
				$row['find_pdf'] = $row['tracking_code'];

				$paid_price = trim($data[$j]['AU']);
				if(!$paid_price){
					$paid_price = 0;
				}

				$row['gia_tri_don_hang'] = $paid_price; // giá trị đơn hàng


				$unit_price = trim($data[$j]['AV']);
				if(!$unit_price){
					$unit_price = 0;
				}
				$row['unit_price'] = $unit_price;


				$seller_discount_total = trim($data[$j]['AW']);
				if(!$seller_discount_total){
					$seller_discount_total = 0;
				}
				$row['seller_discount_total'] = $seller_discount_total;

				$shipping_fee = trim($data[$j]['AX']);
				if(!$shipping_fee){
					$shipping_fee = 0;
				}
				$row['shipping_fee'] = $shipping_fee;

				$row['created_at'] = trim($data[$j]['I']);
				$row['updated_at'] = trim($data[$j]['J']);
				// printr($row);
				
				$upload_exel = $this->save_excel($row,$result_id);

				if(!$upload_exel){
					continue;
				}else{
					$count_ss++;
				}
			}
			return $count_ss;
		}

		function upload_excel_tiki($file_path,$result_id,$shop_code,$house_id){
			require_once("../libraries/PHPExcel-1.8/Classes/PHPExcel.php");
			$objReader = PHPExcel_IOFactory::createReaderForFile($file_path);
			// $data = new PHPExcel_IOFactory();
			// $data->setOutputEncoding('UTF-8');
			$objReader->setLoadAllSheets();
			$objexcel = $objReader->load($file_path);
			$data =$objexcel->getActiveSheet()->toArray('null',true,true,true);
			// $data->load($file_path);
			unset($heightRow);	
			$heightRow=$objexcel->setActiveSheetIndex()->getHighestRow();
			// printr($data);
			unset($j);

			if(!$result_id){
				$link = FSRoute::_('index.php?module=order&view=upload&task=add');
			}else{
				$link = FSRoute::_('index.php?module=order&view=upload&task=edit&id='.$result_id);
			}

			//chạy vòng đầu để check lỗi trước
			for($j=2;$j<=$heightRow;$j++){
				$row = array();

				$row['code'] = trim($data[$j]['C']);
				if(!$row['code'] || $row['code'] == 'null' ){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Không được để trống Mã đơn hàng(cột C) dòng '.$j;
					setRedirect($link,$msg,'error');
					return false;
				}

				$row['sku_nhanh'] = trim($data[$j]['Q']);
				if(!$row['sku_nhanh'] || $row['sku_nhanh'] == 'null' ){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Không được để trống SKU phân loại(cột Q) dòng '.$j;
					setRedirect($link,$msg,'error');
					return false;
				}

				$row['count'] = trim($data[$j]['S']);
				if(!$row['count'] || $row['count'] == 'null' ){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Không được để trống Số lượng(cột S) dòng '.$j;
					setRedirect($link,$msg,'error');
					return false;
				}

				$row['ssku_tiki'] = trim($data[$j]['O']);
				if(!$row['ssku_tiki'] || $row['ssku_tiki'] == 'null' ){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Không được để trống ssku(cột O) dòng '.$j;
					setRedirect($link,$msg,'error');
					return false;
				}

				$row['gia_tri_hang_hoa_tiki'] = trim($data[$j]['V']);
				if(!$row['gia_tri_hang_hoa_tiki'] || $row['gia_tri_hang_hoa_tiki'] == 'null' ){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Không được để trống giá trị hàng hóa (cột V) dòng '.$j;
					setRedirect($link,$msg,'error');
					return false;
				}
			
				// $row['tracking_code'] = trim($data[$j]['C']);
				// if(!$row['tracking_code'] || $row['tracking_code'] == 'null' ){
				// 	$this->remove_xml($result_id,$file_path);
				// 	$msg = 'Không được để trống Mã vận đơn(cột C) dòng '.$j;
				// 	setRedirect($link,$msg,'error');
				// 	return false;
				// }

				$arr_other = explode('-',$row['sku_nhanh']);
				$row['sku'] = $arr_other[0];
				
				$row['color'] = $arr_other[1];
				$row['size'] = $arr_other[2];
				$row['shop_code'] = $arr_other[3];
				if($row['color'] == '00' && $row['size'] == '00'){
					$product_code = $row['sku'];
				}else{
					$product_code = $row['sku'].'-'.$row['color'].'-'.$row['size'];
				}
		
				$produt = $this->get_record('code = "'.$product_code.'"','fs_products');

				if(empty($produt)){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Không tìm thấy sản phẩm có sku '.$product_code.' trong kho.';
					setRedirect($link,$msg,'error');
					return false;
				}
				
				$shop_code."==".$row['shop_code'];
				if(strtolower($shop_code) !== strtolower($row['shop_code'])){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Mã shop không đúng (cột Q) dòng '.$j;
					setRedirect($link,$msg,'error');
					return false;
				}


				// các khung giờ fake và giá = 0 thì không cân check giá min
				global $config;
				if($produt-> price_min > 0 && $house_id != 4 && $house_id != 15 && $house_id != 14 && $config['check_price_min'] == 1){
					$gia_ban = trim($data[$j]['U']);
					if((float)$produt-> price_min > (float)$gia_ban){
						$this->remove_xml($result_id,$file_path);
						$msg = 'Sản phẩm có sku '.$product_code.' không được bán nhỏ hơn ' . $produt-> price_min;
						setRedirect($link,$msg,'error');
						return false;
					}
				}
			}


			$count_ss = 0;
			for($j=2;$j<=$heightRow;$j++){
				$row = array();
				$row['code'] = trim($data[$j]['C']);
				$row['sku_nhanh'] = trim($data[$j]['Q']);
				$row['count'] = trim($data[$j]['S']);
				$row['shipping_unit_name'] = "Tiki";
				$row['tracking_code'] = trim($data[$j]['C']);
				$row['created_at'] = trim($data[$j]['F']);
				$row['don_gia'] = trim($data[$j]['U']);
				$row['gia_tri_hang_hoa_tiki'] = trim($data[$j]['V']);
				$row['shipping_fee'] = trim($data[$j]['AD']);
				$row['ssku_tiki'] = trim($data[$j]['O']);
				$row['find_pdf'] = $row['code'];
				$row['gia_tri_don_hang'] = trim($data[$j]['V']);

				$upload_exel = $this->save_excel($row,$result_id);
				if(!$upload_exel){
					continue;
				}else{
					$count_ss++;
				}
			}
			return $count_ss;
		}


		function upload_excel_viettel($file_path,$result_id){
			require_once("../libraries/PHPExcel-1.8/Classes/PHPExcel.php");
			$objReader = PHPExcel_IOFactory::createReaderForFile($file_path);
			// $data = new PHPExcel_IOFactory();
			// $data->setOutputEncoding('UTF-8');
			$objReader->setLoadAllSheets();
			$objexcel = $objReader->load($file_path);
			$data =$objexcel->getActiveSheet()->toArray('null',true,true,true);
			// $data->load($file_path);
			unset($heightRow);	
			$heightRow=$objexcel->setActiveSheetIndex()->getHighestRow();
			// printr($data);
			unset($j);
			$count_ss = 0;
			for($j=2;$j<=$heightRow;$j++){
				$row = array();
				$row['code'] = trim($data[$j]['B']);
				if(!$row['code'] || $row['code'] == 'null' ){
					continue;
				}
				$row['sku_nhanh'] = trim($data[$j]['O']);
				if(!$row['sku_nhanh'] || $row['sku_nhanh'] == 'null' ){
					continue;
				}
				$row['count'] = trim($data[$j]['P']);
				$row['shipping_unit_name'] = "Viettel Post";
				$upload_exel = $this->save_excel($row,$result_id);

				if(!$upload_exel){
					continue;
				}else{
					$count_ss++;
				}
			}
			return $count_ss;
		}

		function save_excel($row = array(),$result_id){
		    
		   
			$link = FSRoute::_('index.php?module=order&view=upload&task=add');
			if($result_id){
				$data = $this->get_record('id = '.$result_id,'fs_order_uploads');
			}

			if(empty($data)){
				return false;
			}

			$row['record_id'] = $result_id;
			if($row['sku_nhanh']){
				$arr_other = explode('-',$row['sku_nhanh']);
				
				$row['sku'] = $arr_other[0];
				$sku_fisrt = str_split($row['sku'], 3);
				$sku_last = str_split($row['sku'], 4);
				$row['sku_fisrt'] = $sku_fisrt[0];
				$row['sku_last'] = $sku_fisrt[1];
				$row['color'] = $arr_other[1];
				$row['size'] = $arr_other[2];

				if($row['color'] == '00' && $row['size'] == '00'){
					$product_code = $row['sku'];
				}else{
					$product_code = $row['sku'].'-'.$row['color'].'-'.$row['size'];
				}
	

				$produt = $this->get_record('code = "'.$product_code.'"','fs_products');
				
				if(empty($produt)){
					$this -> _remove('id  = '.$result_id,'fs_order_uploads');
					$this -> _remove('record_id  = '.$result_id,'fs_order_uploads_detail');
					setRedirect($link,FSText :: _('Mã sản phẩm '.$product_code.' không tồn tại, vui lòng kiểm tra lại!'),'error');
				}


				$row['product_id'] = $produt->id;
				$row['product_code'] = $produt->code;
				$row['product_price'] = $produt->price;
				$row['product_name'] = $produt->name;
				$row['shop_code'] = $data-> shop_code;
				$row['shop_code'] = $data-> shop_code;
				$row['shop_id'] = $data-> shop_id;
				$row['shop_name'] = $data-> shop_name;
				$row['date'] = $data-> date;
				$row['platform_id'] = $data-> platform_id;
				$row['warehouse_id'] = $data-> warehouse_id;
				$row['house_id'] = $data-> house_id;
				$row['created_time'] = date('Y-m-d H:i:s');
				$row['user_id'] = $_SESSION['ad_userid'];
				$row['user_id_manage_shop'] = $data-> user_id_manage_shop;
				$row['list_user_id_manage_shop'] = $data-> list_user_id_manage_shop;
				$row['is_seeding'] = $data-> is_seeding;
				
				if($row['shipping_unit_name']){
					$check_shipping = $this->get_record('name = "'.$row['shipping_unit_name'].'"','fs_shipping_unit');
					if(!empty($check_shipping)){
						$row['shipping_unit_id'] = $check_shipping-> id;
					}else{
						$row2 = array();
						$row2['name'] = $row['shipping_unit_name'];
						$row2['published'] = 1;
						$add_shipping = $this ->_add($row2,'fs_shipping_unit');
						$row['shipping_unit_id'] = $add_shipping;
					}
				}
			}

			// kiểm tra mã này có phải là mã combo hay ko
			// 1. Nếu là combo thì tách mã combo đó ra để tính toán cho từng mã
			// printr($produt);
			if($produt-> type_id == 9 && $produt-> code_combo && $produt-> code_combo !=''){ // là combo
			
				$code_combo = trim($produt-> code_combo);
				$arr_code_combo = explode(',',$code_combo);
				$count_code_ss = 0; // tính toán xem các mã có chính xác ko.

				foreach ($arr_code_combo as $combo_it){
					$cut_code = explode('/',$combo_it);
					$code_product = $cut_code[0];
					if(!empty($cut_code[1])){
						$quantity_product = $cut_code[1];
					}else{
						$quantity_product = 1;
					}

					$quantity_product = (float)$row['count'] * $quantity_product;
					$row['count'] = $quantity_product;

					$arr_other = explode('-',$code_product);
					$row['sku'] = @$arr_other[0];

					$produt = $this->get_record('code = "'.$code_product.'"','fs_products');
					if(empty($produt)){
						$this -> _remove('id  = '.$result_id,'fs_order_uploads');
						$this -> _remove('record_id  = '.$result_id,'fs_order_uploads_detail');
						setRedirect($link,FSText :: _('Mã sản phẩm '.$code_product.' nhập trong mã combo cha không tồn tại, vui lòng kiểm tra lại!'),'error');
					}
					
					$sku_fisrt = str_split($row['sku'], 3);
					$sku_last = str_split($row['sku'], 4);
					$row['sku_fisrt'] = $sku_fisrt[0];
					$row['sku_last'] = $sku_fisrt[1];
					$row['color'] = @$arr_other[1];
					$row['size'] = @$arr_other[2];
					$row['product_id'] = $produt->id;
					$row['product_code'] = $produt->code;
					$row['product_price'] = $produt->price;
					$row['product_name'] = $produt->name;
					$row['is_combo'] = 1;

					if($data-> platform_id == 1){ // check để tính toán số lượng cho sàn lazada
						$check_count = $this->get_record('tracking_code = "'.$row['tracking_code'].'" AND product_id ='.$produt->id.' AND lazada_sku = "'.$row['lazada_sku'].'" AND sku = "'.$row['sku'].'" AND record_id = '.$result_id,'fs_order_uploads_detail');
						if(!empty($check_count)){
							$row3 = array();
							$row3['count'] = (float)$check_count-> count + $quantity_product;
							$row3['total_price'] = (float)$check_count-> total_price + ((float)$produt->price * $quantity_product);
							$update_id = $this ->_update($row3,'fs_order_uploads_detail','id = '. $check_count->id);
							if($update_id){
								$this->plus_quantity_product($row['warehouse_id'],$row['product_id'],$quantity_product);
							}
							// return $update_id;
						}else{
							$row['total_price'] = (float)$produt->price * $quantity_product;
							// printr($row);
							$add_id = $this->_add($row,'fs_order_uploads_detail');
							if($add_id){
								$this->plus_quantity_product($row['warehouse_id'],$row['product_id'],$quantity_product);
							}
							// return $add_id;
						}
					}else{
						$row['total_price'] = (float)$produt->price * $quantity_product;
						$add_id = $this->_add($row,'fs_order_uploads_detail');
						if($add_id){
							$this->plus_quantity_product($row['warehouse_id'],$row['product_id'],$quantity_product);
						}
						
					}
					$count_code_ss +=1;
				}

				if($count_code_ss == 0){
					return false;
				}
				return $count_code_ss;
			}else{
				if($data-> platform_id == 1){ // check để tính toán số lượng cho sàn lazada

					$check_count = $this->get_record('tracking_code = "'.$row['tracking_code'].'" AND lazada_sku = "'.$row['lazada_sku'].'" AND sku = "'.$row['sku'].'" AND record_id = '.$result_id,'fs_order_uploads_detail');

					if(!empty($check_count)){
						// dd($check_count);
						$row3 = array();
						$row3['count'] = (float)$check_count-> count + 1;
						$row3['total_price'] = (float)$check_count-> total_price + (float)$produt->price;
						$update_id = $this ->_update($row3,'fs_order_uploads_detail','id = '. $check_count->id);
						if($update_id){
							$this->plus_quantity_product($row['warehouse_id'],$row['product_id'],1);
						}
						return $update_id;
					}else{
						
						$row['total_price'] = (float)$produt->price * (float)$row['count'];
						$add_id = $this->_add($row,'fs_order_uploads_detail');
						if($add_id){
							$this->plus_quantity_product($row['warehouse_id'],$row['product_id'],(float)$row['count']);
						}
						return $add_id;
					}
				}else{
					$row['total_price'] = (float)$produt->price * (float)$row['count'];
					$add_id = $this->_add($row,'fs_order_uploads_detail');
					if($add_id){
						$this->plus_quantity_product($row['warehouse_id'],$row['product_id'],(float)$row['count']);
					}
					return $add_id;
				}
			}
		}



        public function showDataExcel($file_path)
        {
            // $files = 'ex2.xlsx';
            // $file_path = PATH_BASE.'files/'.$files;
            require_once("../libraries/PHPExcel-1.8/Classes/PHPExcel.php");
            $objReader = PHPExcel_IOFactory::createReaderForFile($file_path);
            // $data = new PHPExcel_IOFactory();
            // $data->setOutputEncoding('UTF-8');
            $objReader->setLoadAllSheets();
            $objexcel = $objReader->load($file_path);
            $data =$objexcel->getActiveSheet()->toArray('null',true,true,true);
            // $data->load($file_path);
            unset($heightRow);  
            $heightRow=$objexcel->setActiveSheetIndex()->getHighestRow();
            // printr($data);
            unset($j);



            $link = FSRoute::_('index.php?module=order&view=upload&task=add');
             $row = array();

             $k=0;

             $skus = [];
            //chạy vòng đầu để check lỗi trước
            for($j=2;$j<=$heightRow;$j++){
                
               
                $row['maVanDon'][$k] = trim($data[$j]['F']);


                $sku =   $this->convertContentCheckExcel(trim($data[$j]['S']));

                $skuss = ($sku)[0];

                var_dump($skuss);


                // $skus[$k] = $skuss[0];

               
                $k++;

            }  



            $row['Sku'] = $skus;

            return($row);  
        }
		
		
		function retun_file_pdf_test($path){
		    
		    $platform_id = 2;
		    
		    $all_page = $this ->countPdfPage($path);
		    
		    $data_push = [];
		    
		    for($i=0; $i<$all_page;$i++){
			    
			    $data_image_convert = $this->convert_pdf_to_image_to_read_code($i,$path,$platform_id);
			    
			    array_push($data_push, $data_image_convert);
			    
			 //   $command = "zbarimg --raw $data_image_convert";
			    
		    }    
		    
		    array_push($data_push);
		    
		    return $data_push;
		    
		    
		}    
		
		function retun_file_pdf($path, $platform_id){
		    
			$content = $this ->pdf_to_texts($path);
			
			$data = $this->convertContent($content);
			
			$result = [];
			
			$all_page = $this ->countPdfPage($path);
			
			for($i=0; $i<$all_page;$i++){
			    
			    $data_image_convert = $this->convert_pdf_to_image_to_read_code($i,$path,$platform_id);
			    
			    $command = "zbarimg --raw $data_image_convert";
			    
			    
			    
    	        if($platform_id ==1){
    	            
    	            $data_gets = shell_exec($command);
    	        
        	        // Chuyển đổi chuỗi thành mảng bằng cách tách các dòng
                    $outputArray = explode("\n", $data_gets);
                    
                    foreach ($outputArray as $key=> $line) {
                        
                        if($key===1){
                            
                            $data_get = $line;
                        }
                      
                    }
                
    	           
    	        }else{
    	            $data_get = shell_exec($command);
    	        }
    	       // var_dump($data[0][$i]);
    	       // die;
                unlink($data_image_convert);
                
                $result[$i][trim($data_get)] = $data[0][$i];
			    
			}
// 			var_dump($result);
// 			die;
			return $result;
			
		}
		


		function save($row = array(), $use_mysql_real_escape_string = 1) {
			global $config;
			$shop_id = FSInput::get('shop_id');
			$platform_id = FSInput::get('platform_id');
			$house_id = FSInput::get('house_id');
			$warehouse_id = FSInput::get('warehouse_id');
			$id = FSInput::get('id');
			$date = FSInput::get('date');
			$is_seeding = FSInput::get('is_seeding');
			$link = FSRoute::_('index.php?module=order&view=upload&task=add');
			$ar_barcode = [];

			if(!$date || !$shop_id || !$platform_id || !$house_id || !$warehouse_id ){
				$msg = 'Bạn phải nhập đầy đủ thông tin.';
				setRedirect($link,$msg,'error');
				return false;
			}


			$user = $this -> get_record('id = ' . $_SESSION['ad_userid'],'fs_users');
			if($user->parent_id == 0){
				$user_manage_shop = $this->get_record('shop_id LIKE "%,'.$shop_id.',%"','fs_users','*','parent_id ASC');
			}else{
				$user_manage_shop = $this->get_record('shop_id LIKE "%,'.$shop_id.',%"','fs_users','*','parent_id DESC');
			}
			
			
			// dd($user_manage_shop);
			if(empty($user_manage_shop)){
				Errors::_('Shop này chưa được add tài khoản.');
				return false;
			}

			if($user_manage_shop-> money < $config['money_min']){
				Errors::_('Số tiền tạm ứng không đủ ' .format_money($config['money_min'],' đ','0 đ'). ' vui lòng liên hệ chúng tôi để nạp thêm.' );
				return false;
			}

			
			if($user->parent_id == 0){
				$row['user_id_manage_shop'] = $user_manage_shop->id;
				$row['list_user_id_manage_shop'] = ','.$user_manage_shop->id.',';
			}else{
				$row['user_id_manage_shop'] = $user_manage_shop->id;
				$row['list_user_id_manage_shop'] = ','.$user_manage_shop->parent_id.','.$user_manage_shop->id.',';
			}

			
			if($id){
				$link = FSRoute::_('index.php?module=order&view=upload&task=edit&id='.$id);
				// không cho thay đổi nếu đơn này đã đươc IN
				$data = $this->get_record('id = '.$id,'fs_order_uploads');
				if($data-> is_print == 1){
					$msg = 'Đơn này đã được chuyển sang trạng thái in nên không thể thay đổi các thông tin.';
					setRedirect($link,$msg,'error');
					return false;
				}else{

				}
			}else{
				$link = FSRoute::_('index.php?module=order&view=upload&task=add');
			}





			$fsFile = FSFactory::getClass('FsFiles');
			$cyear = date ( 'Y' );
			$cmonth = date ( 'm' );
			$cday = date ( 'd' );
			$path = PATH_BASE.'files/orders/'.$cyear.'/'.$cmonth.'/'.$cday.'/';
			$path = str_replace('/', DS,$path);

			// file pdf
	      	$file_pdf = $_FILES["file_pdf"]["name"];
	      
			if(!empty($file_pdf[0])){
				$file_pdf_name = $fsFile -> upload_file_multiple("file_pdf", $path ,100000000, '_'.time());
				
				if(!$file_pdf_name){
					return false;
				}
			
				$arr_file_pdf_name = explode('t,t',$file_pdf_name);
			
				// printr($arr_file_pdf_name);
				
				$file_pdf_names = "";
				
				// cuong: tắt tạm phần đổi tên file này 
				foreach ($arr_file_pdf_name as $item_file_pdf_name){
					//chuyển file pdf về 1.4(thì thư viện mới cắt và ghép đc)
					$InputFile  = PATH_BASE.'files/orders/'.$cyear.'/'.$cmonth.'/'.$cday.'/'.$item_file_pdf_name;
					$OutputFile = PATH_BASE.'files/orders/'.$cyear.'/'.$cmonth.'/'.$cday.'/'.str_replace('.pdf','_cv.pdf',$item_file_pdf_name);

					if($_SERVER['SERVER_ADDR'] == '127.0.0.1'){ // trên local
						$cmd = "gswin64 -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=".$OutputFile." ".$InputFile;
					}else{ //trên server linux
				
				        $cmd = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE \ -dBATCH -sOutputFile=".$OutputFile." ".$InputFile;
				        
					}
					
					$cmd = str_replace('/',DS,$cmd);
					//lấy theo đường dẫn đã được convert sang bản 1.4
					$file_pdf_names .= $item_file_pdf_name.'t,t';
	
					exec($cmd, $out, $status);
	
				// 	exec($cmd, $out, $status);
					
				// 	echo $cmd;
				    
				    if (0 === $status) {
                        // var_dump($cmd);
                    } else {
                        echo "Command failed with status: $status";
                    }
				    
					@unlink($InputFile);
				}
			

				$file_pdf_names = substr($file_pdf_names,0,-3);
				
			    
				
			    $ar_file_pdf_names = explode('t,t',$file_pdf_names);
			    
			    
			    
			    
			    $data_file_pf_ar =[];
			  
				
				foreach ($ar_file_pdf_names as $value){
				    
				    $row['file_pdf'] = 'files/orders/'.$cyear.'/'.$cmonth.'/'.$cday.'/'.str_replace('.pdf','_cv.pdf',$value);	
				    
				   
				    $data_get_to_pdf = $this->retun_file_pdf(PATH_BASE.$row['file_pdf'], $platform_id);
				    
				   
				    
				    array_push($data_file_pf_ar, $data_get_to_pdf);
				
				}
				
				
			
				// nếu đơn hàng có 2 file pdf thì gộp mã vận đơn với sku vào để check với file excel
				
				if(!empty($ar_file_pdf_names)  && count($ar_file_pdf_names)>1){
				    
				    array_merge($data_file_pf_ar);
				}
				
			
				
				// cuong:viết lại dòng trên này xem sao
				// $row['file_pdf'] = 'files/orders/'.$cyear.'/'.$cmonth.'/'.$cday.'/'.$file_pdf_name;
			    
			}
			
			// file xlsx
	        $file_xlsx = $_FILES["file_xlsx"]["name"];

			if($file_xlsx){
				$file_xlsx_name = $fsFile -> upload_file("file_xlsx", $path ,100000000, '_'.time());
				if(!$file_xlsx_name)
					return false;
				$row['file_xlsx'] = 'files/orders/'.$cyear.'/'.$cmonth.'/'.$cday.'/'.$file_xlsx_name;
			}

			// đổi khung giờ fake sang khung giờ bán thì phải check giá min lại bằng cách bắt nhập lại
			if($id && !$file_xlsx){
				$data = $this->get_record('id = '.$id,'fs_order_uploads');
				if($house_id != $data-> house_id && $house_id != 4 && $house_id != 15 && $house_id != 14 ){
					$file_path_data = PATH_BASE.$data->file_xlsx;
					$file_path_data = str_replace('/', DS,$file_path);
					$this->remove_xml($id,$file_path);
					$msg = 'Bạn đã đổi khung giờ fake sang khung giờ bán, vui lòng nhập lại!';
					$link = FSRoute::_('index.php?module=order&view=upload&task=add');
					setRedirect($link,$msg,'error');
					return false;
				}
			}

			
			$row['date'] = date('Y-m-d',strtotime($date));
			$row['user_id'] = $_SESSION['ad_userid'];
		
			$shop = $this->get_record('id = '.$shop_id,'fs_shops');
			$row['shop_code'] = $shop->code;
			$row['shop_name'] = $shop->name;

			
			$result_id = parent::save ($row);

			if($result_id && $id){
				$row2 = array();
				$row2['date'] = $row['date'];
				$row2['shop_id'] = $shop_id;
				$row2['shop_code'] = $row['shop_code'];
				$row2['shop_name'] = $row['shop_name'];
				$row2['house_id'] = $house_id;
				$row2['warehouse_id'] = $warehouse_id;
				$row2['platform_id'] = $platform_id;
				$this->_update($row2,'fs_order_uploads_detail','record_id = '.$id);
			}

			if($result_id && $file_xlsx && $file_xlsx_name){

				// xóa hết các item của file cũ đi
				$this->delete_item_update_amount_hold($result_id);


				$file_path = PATH_BASE.$row['file_xlsx'];
				$file_path = str_replace('/', DS,$file_path);
				
				
				//lưu vào lấy thông số tạm giữ ở kho
				if($platform_id == 1){
					$add = $this->upload_excel_lazada($file_path,$result_id,$shop->code,$house_id,$data_file_pf_ar);
				}elseif($platform_id == 2){
					$add = $this->upload_excel_shopee($file_path,$result_id,$shop->code,$house_id,$data_file_pf_ar);
				}elseif($platform_id == 3){
					$add = $this->upload_excel_tiki($file_path,$result_id,$shop->code,$house_id);
				}elseif($platform_id == 4){
					$add = $this->upload_excel_viettel($file_path,$result_id,$shop->code,$house_id);
				}else{
				    
					$add = $this->upload_excel_don_ngoai($file_path,$result_id,$shop->code,$house_id);
				}
			}

			//lợi nhuận: đơn fake thì tổng cả đơn mất 6000 phí đóng hàng
			if($result_id){
				$data = $this->get_record('id = '.$result_id,'fs_order_uploads');

				if($data-> house_id == 4 || $data-> house_id == 15 || $data-> house_id == 14){ // khung giờ fake
					$row55 = array();
					$row55['is_seeding'] = 1;
					$this->_update($row55,'fs_order_uploads','id = '.$result_id);
					$this->_update($row55,'fs_order_uploads_detail','record_id = '.$result_id);

					$is_seeding = 1;
				}else{
					$row55 = array();
					$row55['is_seeding'] = 0;
					$this->_update($row55,'fs_order_uploads','id = '.$result_id);

					$this->_update($row55,'fs_order_uploads_detail','record_id = '.$result_id);
					$is_seeding = 0;

				}
				
				$result_id = 145338;

				//lợi nhuận
				$list_code = $this->get_records('record_id = '.$result_id,'fs_order_uploads_detail','tracking_code,code,sku_nhanh','id ASC');

				global $config;
				
				// echo '<pre>';
				//     var_dump($list_code);
				// echo'</pre>';
				// die;
				
				$printr_err =0;
				
				$data_excel = [];
				
		
				foreach ($list_code as $code_item){

					$data_code_item = $this->get_records('record_id = '.$result_id.' AND code = "'.$code_item-> code.'"','fs_order_uploads_detail','tracking_code,id,product_id,product_code,product_price,total_price,gia_tri_don_hang,count,platform_id');
					
		
					////////chi tiết lợi nhuận
					$row3 = array();
					$row3['code'] = $code_item-> code;
					$row3['shop_code'] = $shop->code;
					$row3['shop_name'] = $shop->name;
					$row3['shop_id'] = $shop_id;
					$row3['warehouse_id'] = $warehouse_id;
					$row3['platform_id'] = $platform_id;
					$row3['house_id'] = $house_id;
					$row3['date'] = $data-> date;
					$row3['order_id'] = $result_id;
					$row3['is_print'] = 0;
					$row3['is_shoot'] = 0;
					$row3['is_seeding'] = 0;
					$row3['list_user_id_manage_shop'] = $data-> list_user_id_manage_shop;
					
				
					    
					$row3['tracking_code'] = $data_code_item[0]-> tracking_code;
					
					$str_id = ",";
					$total_product_price = 0; // chi phí sản phẩm
					$profit = 0; // loi nhuan
					$gia_tri_don_hang = 0;
					$profit_company = 0;
					$doanh_thu_cong_ty = 0;
					$gia_von_cong_ty = 0;
					
					foreach($data_code_item as $data_code_it) {

						$str_id .= $data_code_it->id.',';
						$total_product_price += $data_code_it-> total_price; 
						if($data->platform_id != 2){ //nếu ko là sàn shoppe
							$profit += $data_code_it-> gia_tri_don_hang - $data_code_it-> total_price;
							$gia_tri_don_hang += $data_code_item[0]-> gia_tri_don_hang;
						}
						// echo $data_code_it-> product_code ."=";
						$get_product = $this->get_record('code = "'.$data_code_it-> product_code.'"','fs_products','import_price,price');
						
						$profit_company += ($get_product-> price * $data_code_it-> count) - ($get_product-> import_price * $data_code_it-> count);

						$doanh_thu_cong_ty += $get_product-> price * $data_code_it-> count;
						$gia_von_cong_ty += $get_product-> import_price * $data_code_it-> count;

						$row10 = array();
						$row10['import_price_company'] = $get_product-> import_price;
						$row10['total_price_company'] = $get_product-> import_price * $data_code_it-> count;

						if($house_id == 1){ // SL hải sản
							// $row10['count'] = $row10['count'] * 0.1;
						}

						$this->_update($row10,'fs_order_uploads_detail','id = '.$data_code_it-> id);
					}

					$row3['detail_ids'] = $str_id;
					$row3['total_product_price'] = $total_product_price;
					$row3['profit_company'] = $profit_company;
					$row3['doanh_thu_cong_ty'] = $doanh_thu_cong_ty;
					$row3['gia_von_cong_ty'] = $gia_von_cong_ty;
					if($data->platform_id == 2){
						$row3['gia_tri_don_hang'] = $data_code_item[0]-> gia_tri_don_hang;
						$row3['profit'] = $row3['gia_tri_don_hang'] - $row3['total_product_price'];
					}else{
						$row3['gia_tri_don_hang'] = $gia_tri_don_hang;
						$row3['profit'] = $profit;
					}
					// dd($row3);
					if($is_seeding == 1){
						$row3['gia_tri_don_hang'] = 0;
						$row3['is_seeding'] = 1;
						$row3['profit'] = -6000;
						$row3['profit_company'] = 6000;
					}
					
					$check_add = $this->get_record('code = "'.$code_item-> code.'"','fs_profits','id');
					// dd($check_add);
					if(empty($check_add)){
						$this->_add($row3,'fs_profits');
					}else{
						$this->_update($row3,'fs_profits','id = '.$check_add->id);
					}
				}
			}
			return $result_id;
		}

		//xóa các item đồng thời update lại tạm giữ ở kho
		function delete_item_update_amount_hold($id){
			//kiểm tra xem có item không
			$list = $this->get_records('record_id = '.$id,'fs_order_uploads_detail');
			if(!empty($list)){
				$sql = " DELETE FROM fs_order_uploads_detail 
				WHERE record_id = ".$id;
				global $db;
				$rows = $db->affected_rows($sql);
				if($rows){
					foreach($list as $item){
						$get_amount = $this->get_record('warehouses_id = '.$item->warehouse_id . ' AND product_id = ' .$item->product_id,'fs_warehouses_products');
						if(!empty($get_amount)){
							$row = array();
							$row['amount_hold'] = (float)$get_amount-> amount_hold - (float)$item-> count;
							$this->_update($row,'fs_warehouses_products','id = '.$get_amount-> id);
						}
					}
				}
			}
		}

		//tăng số lượng tạm giữ lên
		function plus_quantity_product($warehouse_id,$product_id,$count = 1){
			$data = $this->get_record('warehouses_id = ' . $warehouse_id . ' AND product_id = '. $product_id,'fs_warehouses_products');
			if(!empty($data)){
				$row = array();
				$row['amount_hold'] = (float)$data->amount_hold + (float)$count;
				$this->_update($row,'fs_warehouses_products','id = '.$data->id);
			}else{
				$row = array();
				$row['amount_hold'] = $count;
				$row['product_id'] = (float)$product_id;
				$row['warehouses_id'] = (float)$warehouse_id;
				$this->_add($row,'fs_warehouses_products');
			}
		}


		function split_page_pdf_tiki($file_path_pdf,$id){ //thằng này chia 2 trang
			global $db;
			if(!$file_path_pdf){
				return false;
			}
			$i=0;
			$arr_name = explode('t,t',$file_path_pdf);
			if(!empty($arr_name)){
				foreach($arr_name as $name_item) {
					$base_name = basename($name_item);
					if($i == 0){
						$path = str_replace($base_name,'',$name_item);
					}
					$file_path_pdf_item = PATH_BASE.$path.$base_name;
			        $pdf_check = new \setasign\Fpdi\Fpdi();
			        $count_page = $pdf_check->setSourceFile($file_path_pdf_item);

			      	for ($k = 1; $k <= $count_page; $k++){
			      		if($k%2 == 0){
				            $file_path_pdf_item = PATH_BASE.$path.$base_name;
				            $pdf = new \setasign\Fpdi\Fpdi();
				            $pageCount = $pdf->setSourceFile($file_path_pdf_item);

				            $pageFrom = $k - 1;
				            $pageTo   = $k;
				            $existCounter = 0;

				            for ($i = $pageFrom; $i <= $pageTo; $i++)
				            {
				                $tpl  = $pdf->importPage($i);
				                $pdf->getTemplateSize($tpl);
				                $pdf->addPage();

				                $pdf->useTemplate($tpl,['adjustPa geSize' => true]);
				                $existCounter++;    
				            }

				            $split_filename = PATH_BASE.$path.basename($file_path_pdf_item, '.pdf').'_page'.$k.'.pdf';
				            $pdf->Output($split_filename, "F");

				            $row = array(); // lưu từng page lại
				            $row['file_pdf'] = $path.basename($file_path_pdf_item,'.pdf').'_page'.$k.'.pdf';
				            $row['record_id'] = $id;

				            $check_add_item_page_id = $this->get_record('file_pdf = "'.$row['file_pdf'].'" AND record_id = '.$id,'fs_order_uploads_page_pdf');
				            if(!empty($check_add_item_page_id)){
				            	$this->_update($row,'fs_order_uploads_page_pdf','id = '.$check_add_item_page_id->id);
				            	$add_item_page_id = $check_add_item_page_id->id;
				            }else{
				            	$add_item_page_id = $this->_add($row,'fs_order_uploads_page_pdf');
				            }
				            
				            if($add_item_page_id){
				            	//xử lý tách lấy nội dung của page
				            	$this->pdf_to_text($add_item_page_id,$row['file_pdf'],$path);
				            }
				        }
			        }

					$i++;
				}
			}
			return $i;
		}

		function split_page_pdf($file_path_pdf,$id){
			
			global $db;
			if(!$file_path_pdf){
				return false;
			}
			$i=0;
			$arr_name = explode('t,t',$file_path_pdf);
			if(!empty($arr_name)){
				foreach($arr_name as $name_item) {
					$base_name = basename($name_item);
					if($i == 0){
						$path = str_replace($base_name,'',$name_item);
					}
					$file_path_pdf_item = PATH_BASE.$path.$base_name;
					
			        $pdf_check = new \setasign\Fpdi\Fpdi();
			        $count_page = $pdf_check->setSourceFile($file_path_pdf_item);
			      	for ($k = 1; $k <= $count_page; $k++) {
			            $file_path_pdf_item = PATH_BASE.$path.$base_name;
			            $pdf = new \setasign\Fpdi\Fpdi();
			            $pageCount = $pdf->setSourceFile($file_path_pdf_item);
			            $pageFrom = $k;
			            $pageTo   = $k;
			            $existCounter = 0;

			            for ($i = $pageFrom; $i <= $pageTo; $i++) 
			            {   
			                $tpl  = $pdf->importPage($i);
			                $pdf->getTemplateSize($tpl);
			                $pdf->addPage();

			                $pdf->useTemplate($tpl,['adjustPa geSize' => true]);
			                $existCounter++;    
			            }

			            $split_filename = PATH_BASE.$path.basename($file_path_pdf_item, '.pdf').'_page'.$k.'.pdf';
			            $pdf->Output($split_filename, "F");

			            $row = array(); // lưu từng page lại
			            $row['file_pdf'] = $path.basename($file_path_pdf_item,'.pdf').'_page'.$k.'.pdf';
			            $row['record_id'] = $id;

			            $check_add_item_page_id = $this->get_record('file_pdf = "'.$row['file_pdf'].'" AND record_id = '.$id,'fs_order_uploads_page_pdf');
			            if(!empty($check_add_item_page_id)){
			            	$this->_update($row,'fs_order_uploads_page_pdf','id = '.$check_add_item_page_id->id);
			            	$add_item_page_id = $check_add_item_page_id->id;
			            }else{
			            	$add_item_page_id = $this->_add($row,'fs_order_uploads_page_pdf');
			            }
			            

			            if($add_item_page_id){
			            	//xử lý tách lấy nội dung của page
			            	$this->pdf_to_text($add_item_page_id,$row['file_pdf'],$path);
			            }

			        }
					$i++;
				}
			}
			return $i;
		}

		function pdf_to_text($id,$file_pdf,$path){
			$server_file = PATH_BASE.$file_pdf;
			$server_file = str_replace('/', DS,$server_file);
			$parser = new \Smalot\PdfParser\Parser();
			$pdf = $parser->parseFile($server_file);
			if($pdf != "") {
			    $original_text = $pdf->getText();
			    if ($original_text != ""){
			        $text = nl2br($original_text); // Paragraphs and line break formatting
			        $text = clean_ascii_characters($text); // Check special characters
			        $text = str_replace(array("<br /> <br /> <br />", "<br> <br> <br>"), "<br /> <br />", $text); // Optional
			        $text = addslashes($text); // Backslashes for single quotes     
			        $text = stripslashes($text);
			        $text = strip_tags($text);
			         
			        $check_text = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
			        $no_spacing_error = 0;
			        $excessive_spacing_error = 0;
			        foreach($check_text as $word_key => $word) {
			            if (strlen($word) >= 199999) { // 30 is a limit that I set for a word length, assuming that no word would be 30 length long
			                $no_spacing_error++;
			            } else if (strlen($word) == 1) { // To check if the word is 1 word length
			                if (preg_match('/^[A-Za-z]+$/', $word)) { // Only consider alphabetical words and ignore numbers.
			                    $excessive_spacing_error++;
			                }
			            }
			        }
			         
			        if ($no_spacing_error >= 30 || $excessive_spacing_error >= 199999) {
			            echo "Too many formatting issues<br />";
			            // echo $text;
			            $text = trim($text);
			            $text = str_replace(' -','-',$text);
			            $text = str_replace('- ','-',$text);
						$row = array();
						$row['content'] = $text;
			            $this->_update($row,'fs_order_uploads_page_pdf','id ='.$id);
			        } else {
			            echo "Success!<br />";
			            // echo $text;
			            $text = trim($text);
			            $text = str_replace(' -','-',$text);
			            $text = str_replace('- ','-',$text);
						$row = array();
						$row['content'] = $text;
			            $this->_update($row,'fs_order_uploads_page_pdf','id ='.$id);
			        }

			    } else {
			        // echo "No text extracted from PDF.";
			        if($server_file){
			        	// $this->convert_pdf_to_image($id,$server_file);
			        }
			        
			    }
			} else {
			    // echo "parseFile fns failed. Not a PDF.";
			    if($server_file){
			    	// $this->convert_pdf_to_image($id,$server_file);
			    }
			    

			}
		}
		
		function convert_pdf_to_image_to_read_code($id,$server_file,$platform_id){
		    
		    $page = '['.$id.']';
		    $im = new Imagick();
			$file_img = str_replace('.pdf','_'.$id.'.png',$server_file);
// 			$im->setImageBackgroundColor(new \ImagickPixel('transparent'));

            if($platform_id==1){
                $im->setResolution(300,300);
            }
		
			$im->readimage($server_file.$page); 
			$im->setImageFormat('png');    
			
			$im->setImageBackgroundColor('white');
			$im->setImageCompressionQuality(100);
			if($platform_id==2){
			    $im->scaleImage(800, 800, true);
			}
			$im->stripImage();
// 			$profiles = $im->getImageProfiles("icc", true);
// 			if(!empty($profiles)) {
// 		        $im->profileImage('icc', $profiles['icc']);
// 		    }
			$im->setInterlaceScheme(Imagick::INTERLACE_JPEG);
			$im->setColorspace(Imagick::COLORSPACE_SRGB);
			$im->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
			$im->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
			$im->writeImage($file_img);
			$im->clear(); 
			$im->destroy();
			
			return $file_img;

		}    
		

		//xử lý ko lấy được nội dung của file pfd thì ta phải chuyển file đó sang dạng ảnh rồi chuyển ảnh đó thành text

		function convert_pdf_to_image($id,$server_file){
		    $page = '['.$id.']';
		   $im = new Imagick();
			$file_img = str_replace('.pdf','_'.$id.'.jpg',$server_file);
// 			$im->setImageBackgroundColor(new \ImagickPixel('transparent'));
// 			$im->setResolution(300,300);
			$im->readimage($server_file.$page); 
			$im->setImageFormat('jpg');    
			
			$im->setImageBackgroundColor('white');
			$im->setImageCompressionQuality(100);
			// $im->scaleImage(800, 800, true);
			$im->stripImage();
// 			$profiles = $im->getImageProfiles("icc", true);
// 			if(!empty($profiles)) {
// 		        $im->profileImage('icc', $profiles['icc']);
// 		    }
			$im->setInterlaceScheme(Imagick::INTERLACE_JPEG);
			$im->setColorspace(Imagick::COLORSPACE_SRGB);
			$im->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
			$im->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
			$im->writeImage($file_img);
			$im->clear(); 
			$im->destroy();

// 			if($file_img){
// 				$this->curl_get_content_image($id,$file_img);
// 			}
			
		}


		function curl_get_content_image($id,$file_img){
			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://freeocrapi.com/api',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => array('file'=> new CURLFILE($file_img))));
			$response = curl_exec($curl);

			if(curl_errno($curl)) {
			    $error_msg = curl_error($curl);
			}
			curl_close($curl);
			if (isset($error_msg)) {
			   echo $error_msg;
			}

			$arr = json_decode($response, true);

			if(!empty($arr['text'])){
				$row = array();
				$row['content'] = $arr['text'];
	            $this->_update($row,'fs_order_uploads_page_pdf','id ='.$id);
			}

			@unlink($file_img);

		}
		

		function pdf_to_html($id,$file_pdf,$path){
			//chú ý ko đc xóa file pdftohtml.exe ở thư mục ngoài cùng cạnh index.php trong admin
			$source_pdf = PATH_BASE.$file_pdf;
			$source_pdf = str_replace('/', DS,$source_pdf);
			$output_folder = PATH_BASE.$path;
			$output_folder = str_replace('/', DS,$output_folder);

			// echo $output_folder.basename($file_pdf);
			// die;

		    if (!file_exists($output_folder)) { mkdir($output_folder, 0777, true);}
			$a = passthru("pdftohtml $source_pdf $output_folder".basename($file_pdf),$b);
			$name_html = str_replace('.pdf','.pdfs.html',basename($file_pdf));
			//đọc file để lấy text update
			$myfile = fopen($output_folder.$name_html, "r") or die("Unable to open file!");

			$text = fread($myfile,filesize($output_folder.basename($file_pdf)));
			$text = utf8_encode($text);
			$text = strip_tags($text);
			$text = trim($text);
			$row = array();
			$row['content'] = $text;
			// echo  utf8_encode($text);
			$this->_update($row,'fs_order_uploads_page_pdf','id ='.$id);
			fclose($myfile);

			//xóa các file html khi đã đọc nội dung xong
			@unlink(str_replace('.pdf','.pdfs.html',$source_pdf));
			@unlink(str_replace('.pdf','.pdf_ind.html',$source_pdf));
			@unlink(str_replace('.pdf','.pdf.html',$source_pdf));


			//xóa các file ảnh
			foreach(glob($output_folder . '/*.jpg') as $file_jgp){
		        // check if is a file and not sub-directory
		        if(is_file($file_jgp)){
		            // delete file
		            unlink($file_jgp);
		        }
		    }
			// var_dump($a);
			// die;
		}

		function prints_fix_err(){
			global $db;
			$str_ids = '194,193,192,191,190,189,187,186,185,182,178,177,175,172,171,170,169,168,167,166,165,163,162,161,160,159,155,154,148,145,141,133';
			$get_list_page_pdf = $this->get_records('record_id IN ('.$str_ids.')','fs_order_uploads_page_pdf');

			$pdf = new PDFMerger;
			$i = 0;
			$j = 1;
			$name_pdf = "";
			foreach ($get_list_page_pdf as $item_page_pdf){
				
				$file_path_pdf = PATH_BASE.$item_page_pdf-> file_pdf;
				$file_path_pdf = str_replace('/', DS,$file_path_pdf);

				$pdf->addPDF($file_path_pdf, 'all');
				if($j==1){
					$basename_1 = basename($item_page_pdf-> file_pdf);
					
					$path_pdf_merge_soft = str_replace($basename_1,'',$item_page_pdf-> file_pdf);
					$path_pdf_merge = PATH_BASE.$path_pdf_merge_soft;
					$path_pdf_merge = str_replace('/', DS,$path_pdf_merge);
				}

				// if($j == count($list)){
				// 	$name_pdf .= $item_page_pdf->id;
				// }else{
					$name_pdf .= $item_page_pdf->id . '_';
				//}
				$j++;

				
				$row = array();
				$row['is_print'] = 1;
				$row_update = $this->_update($row,'fs_order_uploads','id = ' . $item_page_pdf-> record_id);
				if($row_update){
					$this->_update($row,'fs_order_uploads_detail','record_id = ' . $item_page_pdf-> record_id);
				}
				$i++;
				
				
			}

			$name_pdf = substr($name_pdf,0,-1);
			// $name_pdf = '133_to_194';
			$pdf->merge('file',$path_pdf_merge.$name_pdf.'.pdf');
	
			
			//lưu lại lịch sử in
			$row2 = array();
			$row2['total_file'] = count($get_list_page_pdf);
			$row2['total_file_success'] = $i;
			$row2['created_time'] = date('Y-m-d H:i:s');
			$row2['action_username'] = 'thang';
			$row2['action_userid'] = 9;
			$row2['file_pdf'] = $path_pdf_merge_soft.$name_pdf.'.pdf';

			$row2['house_id'] = 13;
			$row2['warehouse_id'] = 1;
			$row2['platform_id'] = 1;

			$this->_add($row2,'fs_order_uploads_history_prints');
			return $i;

		}

		
		function prints($value)
		{
			global $db;
			//$ids = FSInput::get('id',array(),'array');
			$query = $this->setQuery();
			$sql = $db->query ( $query );
			$result = $db->getObjectList ();
			$link = 'index.php?module='.$this -> module.'&view='.$this -> view;
			
			$str_ids = '145343,145342';
			
			// $str_ids = implode(',',$ids);
			$list = $this->get_records('id IN ('.$str_ids.')','fs_order_uploads');
			
			//kiểm tra các file pdf với excel có tồn tại hay không
			foreach ($list as $item){
				if(!$item-> file_pdf || !$item-> file_xlsx){
					setRedirect($link,FSText :: _('Đơn chọn in thiếu file tải lên !'),'error');
				}

				$arr_name = explode('t,t',$item-> file_pdf);
				if(!empty($arr_name)){
					$i=0;
					$html ='';
					foreach ($arr_name as $name_item) {
						$base_name = basename($name_item);
						if($i == 0){
							$path = str_replace($base_name,'',$name_item);
						}
						$html .= '<a target="_blank" style="color: rgba(255, 153, 0, 0.79);" href="'.URL_ROOT.$path.$base_name.'">'.$base_name.'</a><br/>';
						if(!file_exists(PATH_BASE.$path.$base_name)) {   
							setRedirect($link,FSText :: _('File PDF lỗi, vui lòng up lại file'),'error');
						}
						$i++;
					}
				}else{
					if(!file_exists(PATH_BASE.$item-> file_pdf)) {   
						setRedirect($link,FSText :: _('File PDF lỗi, vui lòng up lại file'),'error');
					}
				}

				// if(!file_exists(PATH_BASE.$item-> file_xlsx)) {   
				// 	setRedirect($link,FSText :: _('File xlsx lỗi, vui lòng up lại file'),'error');
				// }
			}
		
			foreach ($list as $item){
				//xóa hết các file page pdf trước khi chạy split_page_pdf_tiki
				$this -> _remove('record_id  = '.$item-> id,'fs_order_uploads_page_pdf');
				// xứ lý chém page nhỏ của mỗi file
				if($item-> platform_id == 3){
					$count_split = $this->split_page_pdf_tiki($item-> file_pdf,$item-> id);
				}else{
					$count_split = $this->split_page_pdf($item-> file_pdf,$item-> id);
				}
				//xử lí tìm nội dung pdf có code(mã đơn hàng) là gì
				$list_detail = $this->get_records('record_id = '.$item->id,'fs_order_uploads_detail','DISTINCT find_pdf','sku_fisrt ASC,ABS(sku_fisrt),sku_last ASC,ABS(sku_last),color ASC,ABS(color),size ASC,ABS(size)');

				if(!empty($list_detail)){
					//$stt = 0;
					foreach ($list_detail as $it_detail) {
						$check_find_pdf = $this->get_record('content like "%'.$it_detail-> find_pdf.'%" AND record_id = '.$item->id,'fs_order_uploads_page_pdf');
						if(!empty($check_find_pdf)){
							$row_3 = array();
							$row_3['find_pdf'] = $it_detail-> find_pdf;
						
							$this->_update($row_3,'fs_order_uploads_page_pdf','id = '.$check_find_pdf-> id);
							
						}
					}
				}
			}

			


			//tìm số thứ tự theo cấu trúc tên sản phẩm đầu tiên
			$get_list_page_pdf = $this->get_records('record_id IN ('.$str_ids.')','fs_order_uploads_page_pdf','id,content,find_pdf','id ASC');

			foreach ($get_list_page_pdf as $item_page_pdf){
				if(!$item_page_pdf-> content){
					continue;
				}
				$item_page_pdf-> content = str_replace('(**) 1','(**)1 ',$item_page_pdf-> content);
				
				// cuong:tắt tạm regex
				
				
				
				

				// if($_SESSION[$this -> prefix.'filter2'] == 1){
				// 	//check xem file này có lỗi mã đầu nhảy xuống cuối ko.
				// 	$lzd_content_arr = explode('Lut Bu Chnh.',$item_page_pdf-> content);
				// 	if(!empty($lzd_content_arr[1])){
				// 		preg_match_all('/[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z][A-Za-z]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-/', $item_page_pdf-> content, $b);
				// 		if(!empty($b[0])){
				// 			$b[0] = $b[0][count($b[0]) - 1];
				// 		}else{
				// 			preg_match('/[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z][A-Za-z]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-/', $item_page_pdf-> content, $b);
				// 		}
						
				// 	}else{
				// 		preg_match('/[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z][A-Za-z]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-/', $item_page_pdf-> content, $b);
				// 	}

				// }else{
				// 	preg_match('/[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z][A-Za-z]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-/', $item_page_pdf-> content, $b);
				// }

				// //// c067-S1-02-QSF-200g-tom-ha-tien fix lỗi hải sản
				// if($_SESSION[$this -> prefix.'filter0'] == 1){

				// 	preg_match('/[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-/', $item_page_pdf-> content, $b);

				// 	if(empty($b[0])){
				// 		preg_match('/[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-/', $item_page_pdf-> content, $b);
				// 	}
				// }
				
				// cuong:finish
				
				
				
				
				

				// 425M-BL-00-BOS fix lỗi
				
				if(empty($b[0])){
					preg_match('/[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z][A-Za-z]+-[A-Za-z0-9][A-Za-z0-9]+-/', $item_page_pdf-> content, $b);
				}

				

				if(empty($b[0])){
					preg_match('/[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-/', $item_page_pdf-> content, $b);
				}
				
				if(empty($b[0])){
					preg_match('/[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-/', $item_page_pdf-> content, $b);
				}

				if(empty($b[0])){
					continue;
				}
				
				$arr_c = explode('-', $b[0]);
				if(empty($arr_c[0])){
					continue;
				}

				$sku_split = str_split($arr_c[0],3);
				$row_11 = array();
				$row_11['sku_fisrt'] = @$sku_split[0];
				$row_11['sku_last'] = @$sku_split[1];
				$this->_update($row_11,'fs_order_uploads_page_pdf','id = '. $item_page_pdf-> id);
			}
			// update_stt
			
			$list_detail_soft = $this->get_records('record_id IN ('.$str_ids.') AND sku_fisrt IS NOT NULL','fs_order_uploads_page_pdf','id,content,find_pdf','sku_fisrt ASC,ABS(sku_fisrt),sku_last ASC,ABS(sku_last)');

			$stt = 0;

			foreach ($list_detail_soft as $it_detail_soft){
				$row_4 = array();
				$row_4['ordering'] = $stt;
				$stt++;
				$this->_update($row_4,'fs_order_uploads_page_pdf','id = '. $it_detail_soft-> id);
			}

			
			//KO TÌM THẤY THÌ THỨ TỰ  = VỊ TRÍ THỨ ID - 1
			$get_list_page_pdf = $this->get_records('record_id IN ('.$str_ids.') AND ISNULL(find_pdf)','fs_order_uploads_page_pdf','id,content,find_pdf','id ASC');

			foreach ($get_list_page_pdf as $item_page_pdf){
				// if(!$item_page_pdf-> find_pdf || $item_page_pdf-> find_pdf ==''){
					$id_check = $item_page_pdf-> id - 1;
					$ordering_before = $this->get_record('id = '. $id_check . ' AND record_id IN ('.$str_ids.')','fs_order_uploads_page_pdf','id,ordering');
					if(!empty($ordering_before)){
						$row_44 = array();
						$row_44['ordering'] = $ordering_before-> ordering;
						$this->_update($row_44,'fs_order_uploads_page_pdf','id = '.$item_page_pdf-> id);
					}
					
				// }
			}
			
			// chuyển các mã lỗi ko thấy ordering về 5000  để cho xuống cuối
			$row_10 = array();
			$row_10['ordering'] = 5000;
			$this->_update($row_10,'fs_order_uploads_page_pdf','record_id IN ('.$str_ids.') AND ISNULL(content)');

			//ghép file pdf
			$i = 0;
			$j = 1;

			$name_pdf = "";
			$get_list_page_pdf = $this->get_records('record_id IN ('.$str_ids.')','fs_order_uploads_page_pdf','id,file_pdf,record_id,code,find_pdf,ordering,sku_fisrt,sku_last','ordering ASC,id ASC');
			// dd($get_list_page_pdf);

			$pdf = new PDFMerger;

			foreach ($get_list_page_pdf as $item_page_pdf){
				if($j == 1){
					$name_pdf .= $item_page_pdf->id;
				}elseif($j == count($get_list_page_pdf)){
					$name_pdf .= '_to_'.$item_page_pdf->id;
				}
				
				$file_path_pdf = PATH_BASE.$item_page_pdf-> file_pdf;
				$file_path_pdf = str_replace('/', DS,$file_path_pdf);

				$pdf->addPDF($file_path_pdf, 'all');
				if($j==1){
					$basename_1 = basename($item_page_pdf-> file_pdf);
					
					$path_pdf_merge_soft = str_replace($basename_1,'',$item_page_pdf-> file_pdf);
					$path_pdf_merge = PATH_BASE.$path_pdf_merge_soft;
					$path_pdf_merge = str_replace('/', DS,$path_pdf_merge);
				}
				$j++;
				$row = array();
				$row['is_print'] = 1;
				$row_update = $this->_update($row,'fs_order_uploads','id = ' . $item_page_pdf-> record_id);
				// if($row_update){

				// 	$this->_update($row,'fs_order_uploads_detail','record_id = ' . $item_page_pdf-> record_id);
				// 	$this->_update($row,'fs_profits','order_id = ' . $item_page_pdf-> record_id);
				// }

				$i++;
			}
		

			$pdf->merge('file',$path_pdf_merge.$name_pdf.'.pdf');
			
			print_r($path_pdf_merge.$name_pdf.'.pdf');
			
			die;
			
			
// 			if(!empty($result)){
// 				$str_ids ="";
// 				foreach ($result as $result_it) {
// 					$str_ids .= $result_it-> id.',';
// 				}
// 				$str_ids = substr($str_ids,0,-1);
				
// 				$link = 'index.php?module='.$this -> module.'&view='.$this -> view;
// 				$page = FSInput::get('page',0);
// 				if($page > 1){
// 					$link .= '&page='.$page;
// 				}

// 				$link = FSRoute::_('index.php?module='.$this -> module.'&view='.$this -> view);
// 				if(!isset($_SESSION[$this -> prefix.'text0']) || $_SESSION[$this -> prefix.'text0'] == '' || !isset($_SESSION[$this -> prefix.'text1']) || $_SESSION[$this -> prefix.'text1'] == '' || !isset($_SESSION[$this -> prefix.'filter0']) || $_SESSION[$this -> prefix.'filter0'] == 0 || !isset($_SESSION[$this -> prefix.'filter1']) || $_SESSION[$this -> prefix.'filter1'] == 0 || !isset($_SESSION[$this -> prefix.'filter2']) || $_SESSION[$this -> prefix.'filter2'] == 0 ){
// 					setRedirect($link,FSText :: _('Vui lòng chọn lọc khung ngày, giờ, kho, sàn trước khi in!'),'error');
// 				}


// 				// $str_ids = implode(',',$ids);
// 				$list = $this->get_records('id IN ('.$str_ids.')','fs_order_uploads');
				
// 				//kiểm tra các file pdf với excel có tồn tại hay không
// 				foreach ($list as $item){
// 					if(!$item-> file_pdf || !$item-> file_xlsx){
// 						setRedirect($link,FSText :: _('Đơn chọn in thiếu file tải lên !'),'error');
// 					}

// 					$arr_name = explode('t,t',$item-> file_pdf);
// 					if(!empty($arr_name)){
// 						$i=0;
// 						$html ='';
// 						foreach ($arr_name as $name_item) {
// 							$base_name = basename($name_item);
// 							if($i == 0){
// 								$path = str_replace($base_name,'',$name_item);
// 							}
// 							$html .= '<a target="_blank" style="color: rgba(255, 153, 0, 0.79);" href="'.URL_ROOT.$path.$base_name.'">'.$base_name.'</a><br/>';
// 							if(!file_exists(PATH_BASE.$path.$base_name)) {   
// 								setRedirect($link,FSText :: _('File PDF lỗi, vui lòng up lại file'),'error');
// 							}
// 							$i++;
// 						}
// 					}else{
// 						if(!file_exists(PATH_BASE.$item-> file_pdf)) {   
// 							setRedirect($link,FSText :: _('File PDF lỗi, vui lòng up lại file'),'error');
// 						}
// 					}

// 					if(!file_exists(PATH_BASE.$item-> file_xlsx)) {   
// 						setRedirect($link,FSText :: _('File xlsx lỗi, vui lòng up lại file'),'error');
// 					}
// 				}
			
// 				foreach ($list as $item){
// 					//xóa hết các file page pdf trước khi chạy split_page_pdf_tiki
// 					$this -> _remove('record_id  = '.$item-> id,'fs_order_uploads_page_pdf');
// 					// xứ lý chém page nhỏ của mỗi file
// 					if($item-> platform_id == 3){
// 						$count_split = $this->split_page_pdf_tiki($item-> file_pdf,$item-> id);
// 					}else{
// 						$count_split = $this->split_page_pdf($item-> file_pdf,$item-> id);
// 					}
// 					//xử lí tìm nội dung pdf có code(mã đơn hàng) là gì
// 					$list_detail = $this->get_records('record_id = '.$item->id,'fs_order_uploads_detail','DISTINCT find_pdf','sku_fisrt ASC,ABS(sku_fisrt),sku_last ASC,ABS(sku_last),color ASC,ABS(color),size ASC,ABS(size)');
	
// 					if(!empty($list_detail)){
// 						//$stt = 0;
// 						foreach ($list_detail as $it_detail) {
// 							$check_find_pdf = $this->get_record('content like "%'.$it_detail-> find_pdf.'%" AND record_id = '.$item->id,'fs_order_uploads_page_pdf');
// 							if(!empty($check_find_pdf)){
// 								$row_3 = array();
// 								$row_3['find_pdf'] = $it_detail-> find_pdf;
							
// 								$this->_update($row_3,'fs_order_uploads_page_pdf','id = '.$check_find_pdf-> id);
								
// 							}
// 						}
// 					}
// 				}

				


// 				//tìm số thứ tự theo cấu trúc tên sản phẩm đầu tiên
// 				$get_list_page_pdf = $this->get_records('record_id IN ('.$str_ids.')','fs_order_uploads_page_pdf','id,content,find_pdf','id ASC');

// 				foreach ($get_list_page_pdf as $item_page_pdf){
// 					if(!$item_page_pdf-> content){
// 						continue;
// 					}
// 					$item_page_pdf-> content = str_replace('(**) 1','(**)1 ',$item_page_pdf-> content);
					

// 					if($_SESSION[$this -> prefix.'filter2'] == 1){
// 						//check xem file này có lỗi mã đầu nhảy xuống cuối ko.
// 						$lzd_content_arr = explode('Lut Bu Chnh.',$item_page_pdf-> content);
// 						if(!empty($lzd_content_arr[1])){
// 							preg_match_all('/[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z][A-Za-z]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-/', $item_page_pdf-> content, $b);
// 							if(!empty($b[0])){
// 								$b[0] = $b[0][count($b[0]) - 1];
// 							}else{
// 								preg_match('/[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z][A-Za-z]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-/', $item_page_pdf-> content, $b);
// 							}
							
// 						}else{
// 							preg_match('/[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z][A-Za-z]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-/', $item_page_pdf-> content, $b);
// 						}

// 					}else{
// 						preg_match('/[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z][A-Za-z]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-/', $item_page_pdf-> content, $b);
// 					}

// 					//// c067-S1-02-QSF-200g-tom-ha-tien fix lỗi hải sản
// 					if($_SESSION[$this -> prefix.'filter0'] == 1){

// 						preg_match('/[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-/', $item_page_pdf-> content, $b);

// 						if(empty($b[0])){
// 							preg_match('/[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-/', $item_page_pdf-> content, $b);
// 						}
// 					}

// 					// 425M-BL-00-BOS fix lỗi
					
// 					if(empty($b[0])){
// 						preg_match('/[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z][A-Za-z]+-[A-Za-z0-9][A-Za-z0-9]+-/', $item_page_pdf-> content, $b);
// 					}

					

// 					if(empty($b[0])){
// 						preg_match('/[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-/', $item_page_pdf-> content, $b);
// 					}
					
// 					if(empty($b[0])){
// 						preg_match('/[A-Za-z0-9][A-Za-z0-9][A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-[A-Za-z0-9][A-Za-z0-9]+-/', $item_page_pdf-> content, $b);
// 					}

// 					if(empty($b[0])){
// 						continue;
// 					}
					
// 					$arr_c = explode('-', $b[0]);
// 					if(empty($arr_c[0])){
// 						continue;
// 					}

// 					$sku_split = str_split($arr_c[0],3);
// 					$row_11 = array();
// 					$row_11['sku_fisrt'] = @$sku_split[0];
// 					$row_11['sku_last'] = @$sku_split[1];
// 					$this->_update($row_11,'fs_order_uploads_page_pdf','id = '. $item_page_pdf-> id);
// 				}
// 				// update_stt
				
// 				$list_detail_soft = $this->get_records('record_id IN ('.$str_ids.') AND sku_fisrt IS NOT NULL','fs_order_uploads_page_pdf','id,content,find_pdf','sku_fisrt ASC,ABS(sku_fisrt),sku_last ASC,ABS(sku_last)');

// 				$stt = 0;

// 				foreach ($list_detail_soft as $it_detail_soft){
// 					$row_4 = array();
// 					$row_4['ordering'] = $stt;
// 					$stt++;
// 					$this->_update($row_4,'fs_order_uploads_page_pdf','id = '. $it_detail_soft-> id);
// 				}

				
// 				//KO TÌM THẤY THÌ THỨ TỰ  = VỊ TRÍ THỨ ID - 1
// 				$get_list_page_pdf = $this->get_records('record_id IN ('.$str_ids.') AND ISNULL(find_pdf)','fs_order_uploads_page_pdf','id,content,find_pdf','id ASC');

// 				foreach ($get_list_page_pdf as $item_page_pdf){
// 					// if(!$item_page_pdf-> find_pdf || $item_page_pdf-> find_pdf ==''){
// 						$id_check = $item_page_pdf-> id - 1;
// 						$ordering_before = $this->get_record('id = '. $id_check . ' AND record_id IN ('.$str_ids.')','fs_order_uploads_page_pdf','id,ordering');
// 						if(!empty($ordering_before)){
// 							$row_44 = array();
// 							$row_44['ordering'] = $ordering_before-> ordering;
// 							$this->_update($row_44,'fs_order_uploads_page_pdf','id = '.$item_page_pdf-> id);
// 						}
						
// 					// }
// 				}
				
// 				// chuyển các mã lỗi ko thấy ordering về 5000  để cho xuống cuối
// 				$row_10 = array();
// 				$row_10['ordering'] = 5000;
// 				$this->_update($row_10,'fs_order_uploads_page_pdf','record_id IN ('.$str_ids.') AND ISNULL(content)');

// 				//ghép file pdf
// 				$i = 0;
// 				$j = 1;

// 				$name_pdf = "";
// 				$get_list_page_pdf = $this->get_records('record_id IN ('.$str_ids.')','fs_order_uploads_page_pdf','id,file_pdf,record_id,code,find_pdf,ordering,sku_fisrt,sku_last','ordering ASC,id ASC');
// 				// dd($get_list_page_pdf);

// 				$pdf = new PDFMerger;

// 				foreach ($get_list_page_pdf as $item_page_pdf){
// 					if($j == 1){
// 						$name_pdf .= $item_page_pdf->id;
// 					}elseif($j == count($get_list_page_pdf)){
// 						$name_pdf .= '_to_'.$item_page_pdf->id;
// 					}
					
// 					$file_path_pdf = PATH_BASE.$item_page_pdf-> file_pdf;
// 					$file_path_pdf = str_replace('/', DS,$file_path_pdf);

// 					$pdf->addPDF($file_path_pdf, 'all');
// 					if($j==1){
// 						$basename_1 = basename($item_page_pdf-> file_pdf);
						
// 						$path_pdf_merge_soft = str_replace($basename_1,'',$item_page_pdf-> file_pdf);
// 						$path_pdf_merge = PATH_BASE.$path_pdf_merge_soft;
// 						$path_pdf_merge = str_replace('/', DS,$path_pdf_merge);
// 					}
// 					$j++;
// 					$row = array();
// 					$row['is_print'] = 1;
// 					$row_update = $this->_update($row,'fs_order_uploads','id = ' . $item_page_pdf-> record_id);
// 					if($row_update){

// 						$this->_update($row,'fs_order_uploads_detail','record_id = ' . $item_page_pdf-> record_id);
// 						$this->_update($row,'fs_profits','order_id = ' . $item_page_pdf-> record_id);
// 					}

// 					$i++;
// 				}

// 				$pdf->merge('file',$path_pdf_merge.$name_pdf.'.pdf');
		
	
// 				//lưu lại lịch sử in
// 				$row2 = array();
// 				$row2['total_file'] = count($get_list_page_pdf);
// 				$row2['total_file_success'] = $i;
// 				$row2['created_time'] = date('Y-m-d H:i:s');
// 				$row2['action_username'] = $_SESSION ['ad_username'];
// 				$row2['action_userid'] = $_SESSION ['ad_userid'];
// 				$row2['file_pdf'] = $path_pdf_merge_soft.$name_pdf.'.pdf';

// 				$row2['house_id'] = $_SESSION[$this -> prefix.'filter0'];
// 				$row2['warehouse_id'] = $_SESSION[$this -> prefix.'filter1'];
// 				$row2['platform_id'] = $_SESSION[$this -> prefix.'filter2'];

// 				$row2['date_select_from'] = date('Y-m-d',strtotime($_SESSION[$this -> prefix.'text0']));
// 				$row2['date_select_to'] = date('Y-m-d',strtotime($_SESSION[$this -> prefix.'text1']));
// 				$this->_add($row2,'fs_order_uploads_history_prints');
// 				return $i;
// 			}
			return 0;
		}
	}
	
?>