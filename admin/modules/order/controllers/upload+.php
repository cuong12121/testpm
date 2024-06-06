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
			var_dump(1);

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
			$html ='<strong style="color:red">Lỗi  thiếu file</strong>';
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
					$html .= '<a target="_blank" style="color: red;" href="javascript:void(0)">Lỗi file</a><br/>';
				}else{
					$html .= '<a target="_blank" style="color: rgba(255, 153, 0, 0.79);" href="'.URL_ROOT.$path.$base_name.'">'.$base_name.'</a><br/>';
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
			return '<a style="color: red;" target="_blink" href="' . $link . '">Lỗi file</a>';
		}else{
			return $html;
		}
		
	}

	function view_excel($controle,$id){
		$model = $controle -> model;
		$data = $model->get_record('id = ' .$id,'fs_order_uploads','id,file_xlsx');
		if(!$data-> file_xlsx){
			$html ='<strong style="color:red">Lỗi thiếu file</strong>';
			return $html;
		}
		$link = URL_ROOT.$data-> file_xlsx;
		if(!file_exists(str_replace('admin/order/','',PATH_BASE.$data-> file_xlsx))){
			return '<a style="color: red;" target="_blink" href="javascript:void(0)">Lỗi file</a>';
		}

		$data_detail = $model->get_record('record_id = ' .$id,'fs_order_uploads_detail','id');
		if(empty($data_detail)){
			return '<a style="color: red;" target="_blink" href="' . $link . '">Lỗi file</a>';
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