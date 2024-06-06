<?php
	include 'PDFMerger-tcpdf/PDFMerger.php';
	use PDFMerger\PDFMerger;
	include 'fpdf/fpdf.php';
   	include 'fpdi/src/autoload.php';
   	require_once('vendor/autoload.php');
	class OrderModelsUpload extends FSModels
	{
		var $limit;
		var $page;
		function __construct()
		{
			$limit = 100;
			$page = FSInput::get('page');
			$this->limit = $limit;
			$this -> table_name = 'fs_order_uploads';
		
			parent::__construct();
		}
		
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

		function upload_excel_shopee($file_path,$result_id,$shop_code){
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



		function upload_excel_don_ngoai($file_path,$result_id,$shop_code){
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
						$i++;
					}
				}
			}
			return $i;
		}


		function upload_excel_lazada($file_path,$result_id,$shop_code){
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
				$row['code'] = trim($data[$j]['A']);
				if(!$row['code'] || $row['code'] == 'null' ){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Không được để trống Mã đơn hàng(cột A) dòng '.$j;
					setRedirect($link,$msg,'error');

				}
				$row['sku_nhanh'] = trim($data[$j]['F']);
				if(!$row['sku_nhanh'] || $row['sku_nhanh'] == 'null' ){
					$this->remove_xml($result_id,$file_path);
					$msg = 'Không được để trống SKU phân loại(cột F) dòng '.$j;
					setRedirect($link,$msg,'error');
					return false;
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

			}


			$count_ss = 0;
			for($j=2;$j<=$heightRow;$j++){
				$row = array();
				$row['code'] = trim($data[$j]['A']);
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
				$row['paid_price'] = $paid_price;

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

		function upload_excel_tiki($file_path,$result_id,$shop_code){
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
				// die;
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
				// dd($row);
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


		function save($row = array(), $use_mysql_real_escape_string = 1) {
			global $config;
			$shop_id = FSInput::get('shop_id');
			$platform_id = FSInput::get('platform_id');
			$house_id = FSInput::get('house_id');
			$warehouse_id = FSInput::get('warehouse_id');
			$id = FSInput::get('id');
			$date = FSInput::get('date');

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
	      	
	      	// var_dump($file_pdf);
			if(!empty($file_pdf[0])){
				$file_pdf_name = $fsFile -> upload_file_multiple("file_pdf", $path ,100000000, '_'.time());
				if(!$file_pdf_name){
					return false;
				}
				
				$arr_file_pdf_name = explode('t,t',$file_pdf_name);
				// printr($arr_file_pdf_name);
				$file_pdf_names = "";
				foreach ($arr_file_pdf_name as $item_file_pdf_name){
					//chuyển file pdf về 1.4(thì thư viện mới cắt và ghép đc)
					$InputFile  = PATH_BASE.'files/orders/'.$cyear.'/'.$cmonth.'/'.$cday.'/'.$item_file_pdf_name;
					$OutputFile = PATH_BASE.'files/orders/'.$cyear.'/'.$cmonth.'/'.$cday.'/'.str_replace('.pdf','_cv.pdf',$item_file_pdf_name);

					if($_SERVER['SERVER_ADDR'] == '127.0.0.1'){ // trên local
						$cmd = "gswin64 -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=".$OutputFile." ".$InputFile;
					}else{ //trên server linux
						$cmd = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=".$OutputFile." ".$InputFile;
					}
					
					$cmd = str_replace('/',DS,$cmd);
					//lấy theo đường dẫn đã được convert sang bản 1.4
					$file_pdf_names .= $item_file_pdf_name.'t,t';
					shell_exec($cmd);
					@unlink($InputFile);
				}

				$file_pdf_names = substr($file_pdf_names,0,-3);
				
				$row['file_pdf'] = 'files/orders/'.$cyear.'/'.$cmonth.'/'.$cday.'/'.str_replace('.pdf','_cv.pdf',$file_pdf_names);				
			}

			

			// file xlsx
	       $file_xlsx = $_FILES["file_xlsx"]["name"];
	    
			if($file_xlsx){
				$file_xlsx_name = $fsFile -> upload_file("file_xlsx", $path ,100000000, '_'.time());
				if(!$file_xlsx_name)
					return false;
				$row['file_xlsx'] = 'files/orders/'.$cyear.'/'.$cmonth.'/'.$cday.'/'.$file_xlsx_name;
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
					$add = $this->upload_excel_lazada($file_path,$result_id,$shop->code);
				}elseif($platform_id == 2){
					$add = $this->upload_excel_shopee($file_path,$result_id,$shop->code);
				}elseif($platform_id == 3){
					$add = $this->upload_excel_tiki($file_path,$result_id,$shop->code);
				}elseif($platform_id == 4){
					$add = $this->upload_excel_viettel($file_path,$result_id,$shop->code);
				}else{
					$add = $this->upload_excel_don_ngoai($file_path,$result_id,$shop->code);
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
			            if (strlen($word) >= 30) { // 30 is a limit that I set for a word length, assuming that no word would be 30 length long
			                $no_spacing_error++;
			            } else if (strlen($word) == 1) { // To check if the word is 1 word length
			                if (preg_match('/^[A-Za-z]+$/', $word)) { // Only consider alphabetical words and ignore numbers.
			                    $excessive_spacing_error++;
			                }
			            }
			        }
			         
			        if ($no_spacing_error >= 30 || $excessive_spacing_error >= 150) {
			            echo "Too many formatting issues<br />";
			            // echo $text;
			            $text = trim($text);
						$row = array();
						$row['content'] = $text;
			            $this->_update($row,'fs_order_uploads_page_pdf','id ='.$id);
			        } else {
			            echo "Success!<br />";
			            // echo $text;
			            $text = trim($text);
						$row = array();
						$row['content'] = $text;
			            $this->_update($row,'fs_order_uploads_page_pdf','id ='.$id);
			        }

			    } else {
			        echo "No text extracted from PDF.";
			    }
			} else {
			    echo "parseFile fns failed. Not a PDF.";
			}
		}


		// Common function
		

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
			$ids = FSInput::get('id',array(),'array');
			if(!empty($ids))
			{
				$link = 'index.php?module='.$this -> module.'&view='.$this -> view;
				$page = FSInput::get('page',0);
				if($page > 1){
					$link .= '&page='.$page;
				}

				$link = FSRoute::_('index.php?module='.$this -> module.'&view='.$this -> view);
				if(!isset($_SESSION[$this -> prefix.'text0']) || $_SESSION[$this -> prefix.'text0'] == '' || !isset($_SESSION[$this -> prefix.'text1']) || $_SESSION[$this -> prefix.'text1'] == '' || !isset($_SESSION[$this -> prefix.'filter0']) || $_SESSION[$this -> prefix.'filter0'] == 0 || !isset($_SESSION[$this -> prefix.'filter1']) || $_SESSION[$this -> prefix.'filter1'] == 0 || !isset($_SESSION[$this -> prefix.'filter2']) || $_SESSION[$this -> prefix.'filter2'] == 0 ){
					setRedirect($link,FSText :: _('Vui lòng chọn lọc khung ngày, giờ, kho, sàn trước khi in!'),'error');
				}


				$str_ids = implode(',',$ids);
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

					if(!file_exists(PATH_BASE.$item-> file_xlsx)) {   
						setRedirect($link,FSText :: _('File xlsx lỗi, vui lòng up lại file'),'error');
					}
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

				//tìm số thứ tự in
				$list_detail_soft = $this->get_records('record_id IN ('.$str_ids.')','fs_order_uploads_detail','DISTINCT find_pdf','sku_fisrt ASC,ABS(sku_fisrt),sku_last ASC,ABS(sku_last),color ASC,ABS(color),size ASC,ABS(size)');

				$stt = 0;
				foreach ($list_detail_soft as $it_detail_soft){
					$row_4 = array();
					$row_4['ordering'] = $stt;
					$this->_update($row_4,'fs_order_uploads_page_pdf','find_pdf = "'.$it_detail_soft-> find_pdf.'"');
					$stt++;
				}


				//trường hợp đặc biệt ordering lazada hoặc ko tìm đc mã
				$get_list_page_pdf = $this->get_records('record_id IN ('.$str_ids.')','fs_order_uploads_page_pdf','id,content,find_pdf','id ASC');

				
				foreach ($get_list_page_pdf as $item_page_pdf){
					if(!$item_page_pdf-> find_pdf || $item_page_pdf-> find_pdf ==''){
						$id_check = $item_page_pdf-> id - 1;
						$ordering_before = $this->get_record('id = '. $id_check,'fs_order_uploads_page_pdf','id,ordering');
						$row_4 = array();
						$row_4['ordering'] = $ordering_before-> ordering;
						$this->_update($row_4,'fs_order_uploads_page_pdf','id = '.$item_page_pdf-> id);
					}
				}
				

				// chuyển các mã lỗi ko thấy ordering về 5000  để cho xuống cuối
				$row_10 = array();
				$row_10['ordering'] = 5000;
				$this->_update($row_10,'fs_order_uploads_page_pdf','record_id IN ('.$str_ids.') AND ISNULL(ordering)');

				//ghép file pdf

				$i = 0;
				$j = 1;
				$name_pdf = "";
				$get_list_page_pdf = $this->get_records('record_id IN ('.$str_ids.')','fs_order_uploads_page_pdf','id,file_pdf,record_id,code,find_pdf','ordering ASC,id ASC');

				// $err = 0;
				// foreach ($get_list_page_pdf as $item_page_pdf){
				// 	if(!$item_page_pdf-> find_pdf || $item_page_pdf-> find_pdf =='' || !$item_page_pdf-> ordering || $item_page_pdf-> ordering ==''){ 
				// 		//không biết phiếu này của dòng nào excel thì lấy thứ tự theo id
				// 		$err = 1;
				// 		continue;
				// 	}
				// }

				// if($err == 1){
				// 	$get_list_page_pdf = $this->get_records('record_id IN ('.$str_ids.')','fs_order_uploads_page_pdf','id,file_pdf,record_id,code,find_pdf,content','id ASC');
				// }


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
					if($row_update){
						$this->_update($row,'fs_order_uploads_detail','record_id = ' . $item_page_pdf-> record_id);
					}

					$i++;
				}

				$pdf->merge('file',$path_pdf_merge.$name_pdf.'.pdf');
		
	
				//lưu lại lịch sử in
				$row2 = array();
				$row2['total_file'] = count($get_list_page_pdf);
				$row2['total_file_success'] = $i;
				$row2['created_time'] = date('Y-m-d H:i:s');
				$row2['action_username'] = $_SESSION ['ad_username'];
				$row2['action_userid'] = $_SESSION ['ad_userid'];
				$row2['file_pdf'] = $path_pdf_merge_soft.$name_pdf.'.pdf';

				$row2['house_id'] = $_SESSION[$this -> prefix.'filter0'];
				$row2['warehouse_id'] = $_SESSION[$this -> prefix.'filter1'];
				$row2['platform_id'] = $_SESSION[$this -> prefix.'filter2'];

				$row2['date_select_from'] = date('Y-m-d',strtotime($_SESSION[$this -> prefix.'text0']));
				$row2['date_select_to'] = date('Y-m-d',strtotime($_SESSION[$this -> prefix.'text1']));
				$this->_add($row2,'fs_order_uploads_history_prints');
				return $i;
			}
			return 0;
		}
	}
	
?>