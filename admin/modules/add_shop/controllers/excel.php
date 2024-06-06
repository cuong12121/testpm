<?php
	class Add_shopControllersExcel extends Controllers
	{
		function __construct()
		{
			$this->view = 'excel'; 
			parent::__construct();

			$model = $this -> model;
			$file_export_name = "";
			// printr($_SESSION);
			if(isset($_SESSION[$this -> prefix.'filter1'])){
				$warehouse_id = $_SESSION[$this -> prefix.'filter1'];
				if($warehouse_id){
					$warehouse = $model->get_record('id = '.$warehouse_id,'fs_warehouses','code');
					$file_export_name .= '_'.$warehouse->code;
				}
			}

			if(isset($_SESSION[$this -> prefix.'filter2'])){
				$platform_id = $_SESSION[$this -> prefix.'filter2'];
				if($platform_id){
					$platform = $model->get_record('id = '.$platform_id,'fs_platforms','code');
					$file_export_name .= '_'.$platform->code;
				}
			}


			if(isset($_SESSION[$this -> prefix.'text0']))
			{
				$date_from = $_SESSION[$this -> prefix.'text0'];
				if($date_from){
					$date_from = strtotime($date_from);
					$date_new = date('Y-m-d',$date_from);
					$file_export_name .= '_'.$date_new;
				}
			}


			if(isset($_SESSION[$this -> prefix.'filter0'])){
				$house_id = $_SESSION[$this -> prefix.'filter0'];
				if($house_id){
					$house = $model->get_record('id = '.$house_id,'fs_house','name');
					$file_export_name .= '_'.$house->name;
				}
			}

			if(isset($_SESSION[$this -> prefix.'filter3'])){
				$shipping_unit_id = $_SESSION[$this -> prefix.'filter3'];
				$fsstring = FSFactory::getClass('FSString','','../');
				if($shipping_unit_id){
					$shipping_unit = $model->get_record('id = '.$shipping_unit_id,'fs_shipping_unit','name');
					$file_export_name .= '_'.$fsstring -> stringStandart($shipping_unit->name);
				}
			}

			$this->file_export_name = $file_export_name;
		}
		function display()
		{
			parent::display();
			$sort_field = $this -> sort_field;
			$sort_direct = $this -> sort_direct;
			$model  = $this -> model;
			$warehouses = $model -> get_records('published = 1','fs_warehouses');
			$platforms = $model -> get_records('published = 1','fs_platforms');
			$houses = $model -> get_records('published = 1','fs_house');
			$shipping_unit = $model -> get_records('published = 1','fs_shipping_unit');
			$list = $this -> model->get_data();
			$pagination = $model->getPagination();
			$users = $model -> get_record('id = ' . $_SESSION['ad_userid'],'fs_users');
			include 'modules/'.$this->module.'/views/'.$this->view.'/list.php';
		}

		function add()
		{
			$model = $this -> model;
			$warehouses = $model -> get_records('published = 1','fs_warehouses');
			$platforms = $model -> get_records('published = 1','fs_platforms');
			$houses = $model -> get_records('published = 1','fs_house');
			include 'modules/'.$this->module.'/views/'.$this -> view.'/detail.php';
		}

		function edit()
		{
			$model = $this -> model;
			$ids = FSInput::get('id',array(),'array');
			$id = $ids[0];
			$data = $model->get_record_by_id($id);
			$warehouses = $model -> get_records('published = 1','fs_warehouses');
			$platforms = $model -> get_records('published = 1','fs_platforms');
			$houses = $model -> get_records('published = 1','fs_house');

			$users = $model -> get_record('id = ' . $_SESSION['ad_userid'],'fs_users');
			if($users->shop_id){
				$shops = $model -> get_records('id IN ('.$users->shop_id.')','fs_shops');
			}
			
			include 'modules/'.$this->module.'/views/'.$this->view.'/detail.php';
		}


		function import_excel(){
			$model = $this -> model;
			$fsFile = FSFactory::getClass('FsFiles');
			$path = PATH_BASE.'files/excel/warehouse_sales';
			$path = str_replace('/', DS,$path);
	        $excel = $fsFile -> upload_file("excel", $path ,100000000, '_'.time());
	        if(	!$excel){
				return false;
			}else{
				$file_path = $path.$excel;
				require_once("../libraries/PHPExcel-1.8/Classes/PHPExcel.php");
				$objReader = PHPExcel_IOFactory::createReaderForFile($file_path);
				$objReader->setLoadAllSheets();
				$objexcel = $objReader->load($file_path);
				$data_upload =$objexcel->getActiveSheet()->toArray('null',true,true,true);
				// printr($data);

				unset($heightRow);	
				$heightRow=$objexcel->setActiveSheetIndex()->getHighestRow();
				unset($j);

				$count_ss = 0;
				$i = 0;
				$l = 0;
				$row_error = "";
				for($j=1;$j<=$heightRow;$j++){

					$row = array();
					$row['id'] = trim($data_upload[$j]['A']);
					$row['code'] = trim($data_upload[$j]['B']);
					$row['name'] = trim($data_upload[$j]['C']);
					$row['other'] = trim($data_upload[$j]['D']);
					$row['published'] = 1;
					
					$row['created_time'] = date('Y-m-d H:i:s');
					$row['action_userid'] = $_SESSION ['ad_userid'];
					$row['action_username'] = $_SESSION ['ad_username'];
	
					$add_id = $model-> _add($row,'fs_shops');
					
				}
				$link = FSRoute::_('index.php?module=add_shop&view=excel');
				
				setRedirect($link);
			}
		}


		//trừ tạm giữ đi, và trừ số lượng chính đi
		function minus_quantity_product($count = 1,$data){
			$model = $this -> model;
			$row = array();
			$row['amount_hold'] = (float)$data->amount_hold - (float)$count;
			$row['amount'] = (float)$data->amount - (float)$count;
			$model->_update($row,'fs_warehouses_products','id = '.$data->id);
			
		}
		
		//cộng số lượng của sản phẩm khi hoàng hàng thành công
		function plus_quantity_product($warehouse_id,$product_id,$count = 1){
			$model = $this -> model;
			$data = $model->get_record('warehouses_id = ' . $warehouse_id . ' AND product_id = '. $product_id,'fs_warehouses_products');
			if(!empty($data)){
				$row = array();
				$row['amount'] = (int)$data->amount + (int)$count;
				$update = $model->_update($row,'fs_warehouses_products','id = '.$data->id);
				return $update;
			}else{
				return 0;
			}
		}


		function shoot_order(){
			$model = $this -> model;
			$rows = $model->shoot_order(1);
			$link = 'index.php?module='.$this -> module.'&view='.$this -> view;
			$page = FSInput::get('page',0);
			if($page > 1){
				$link .= '&page='.$page;
			}
			$link = FSRoute::_($link);
			if($rows)
			{
				setRedirect($link,$rows.' '.FSText :: _('đơn được bắn ra kho'));	
			}
			else
			{
				setRedirect($link,FSText :: _('Đơn này chưa xác nhận đã đóng hàng hoặc đã hoàn hàng'),'error');	
			}
		}


		function download_file(){
		
			$path_file = PATH_BASE.LINK_AMIN.'export'.DS.'excel'.'mau_ban_hang_ra_kho.xlsx'; 
			$fsstring = FSFactory::getClass('FSString');
			$file_export_name = 'mau_ban_hang_ra_kho';
			$file_ext = $this -> getExt(basename('export'.DS.'excel'.DS.'mau_ban_hang_ra_kho.xlsx'));
			$file_export_name = $file_export_name.'.'.$file_ext;
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: no-cache,must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);			
			header("Content-type: application/force-download");			
			header("Content-Disposition: attachment; filename=\"".$file_export_name."\";" );
			header("Content-Transfer-Encoding: binary");
			header("Cache-Control:no-cache, must-revalidate, post-check=0, pre-check=0");

			header("Content-Length: ".filesize($path_file));

			echo $link_excel = URL_ADMIN.'/export/mau_excel/'. $file_export_name;
			?>
			<?php setRedirect($link_excel); ?>
			<?php 
			readfile($path_file);
			exit();	
		}

		function getExt($file){
			return strtolower(substr($file, (strripos($file, '.')+1),strlen($file)));
		}


	}

?>