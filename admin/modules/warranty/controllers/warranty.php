<?php

class WarrantyControllersWarranty extends Controllers
{
	function __construct()
	{
		$this->view = 'warranty' ; 
		parent::__construct();
		$array_status = array('1'=>'Chưa xử lý','2'=>'Hoàn thành','3'=>'Không hoàn thành');
		$this -> arr_status = $array_status; 

		$array_job = array('1'=>'Xử lý đơn đổi trả','2'=>'Xử lý đơn đổi trả hỏa tốc','3'=>'Hỗ trợ kỹ thuật','4'=>'Tạo đơn đổi trả','5'=>'Thiết hàng, giao sai sản phẩm');
		$this -> array_job = $array_job;
	}
	function display()
	{
		parent::display();
		$sort_field = $this -> sort_field;
		$sort_direct = $this -> sort_direct;

		$model  = $this -> model;
		$list = $model->get_data();
		$array_status = $this -> arr_status;
		$array_job = $this -> array_job;
		$pagination = $model->getPagination();
		$array_group = $model->get_records('','fs_users_groups');
		include 'modules/'.$this->module.'/views/'.$this->view.'/list.php';

		$breadcrumbs = array();
		$breadcrumbs[] = array(0=>'Yêu cầu bảo hành', 1 => '');	

		global $tmpl;
		$tmpl->assign ( 'breadcrumbs', $breadcrumbs );
	}

	function add()
	{
		$model = $this -> model;
		// $platforms = $model -> get_records('published = 1','fs_platforms');
		$maxOrdering = $model->getMaxOrdering();
		$breadcrumbs = array();
		$breadcrumbs[] = array(0=>'Yêu cầu bảo hành', 1 => 
			FSRoute::_("index.php?module=".$this-> module."&view=".$this-> view));	
		global $tmpl;
		$tmpl->assign ( 'breadcrumbs', $breadcrumbs );
		$array_status = $this -> arr_status;
		$array_job = $this -> array_job;
		$array_group = $model->get_records('','fs_users_groups');

		$wrap_id_warehouses = $model->get_wrap_id_warehouses();
		$warehouses = $model -> get_records('published = 1 AND id IN ('.$wrap_id_warehouses.')','fs_warehouses');
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
		$ids = FSInput::get('id',array(),'array');
		$id = $ids[0];
		$model = $this -> model;
		$data = $model->get_record_by_id($id);
		$array_status = $this -> arr_status;
		$array_job = $this -> array_job;

		$array_group = $model->get_records('','fs_users_groups');

		$wrap_id_warehouses = $model->get_wrap_id_warehouses();
		$warehouses = $model -> get_records('published = 1 AND id IN ('.$wrap_id_warehouses.')','fs_warehouses');
		$users = $model -> get_record('id = ' . $_SESSION['ad_userid'],'fs_users');
		if($users->group_id == 1 && $users->shop_id){
			$users->shop_id = substr($users->shop_id, 1, -1);
			$shops = $model -> get_records('id IN ('.$users->shop_id.')','fs_shops');
		}else{
			$shops = $model -> get_records('','fs_shops');
		}
		
		// $platforms = $model -> get_records('published = 1','fs_platforms');
		$breadcrumbs = array();
		$breadcrumbs[] = array(0=>'Yêu cầu bảo hành', 1 => 
			FSRoute::_("index.php?module=".$this-> module."&view=".$this-> view));	
		global $tmpl;
		$tmpl->assign ( 'breadcrumbs', $breadcrumbs );

		if($_SESSION['ad_groupid'] == 1 && $data->creator_id != $_SESSION['ad_userid']){
			echo "Đây không phải bài bạn tạo!";
		}else{
			include 'modules/'.$this->module.'/views/'.$this->view.'/detail.php';
		}
		
	}


	function ajax_warranty_change(){
		$id = FSInput::get('id');
		$warehouses_id = FSInput::get('warehouses_id');
		$respon = array();
		$respon ['error'] = true;
		$model = $this -> model;

		$row = array();
		$row['agree_id'] = $_SESSION['ad_userid'];
		$row['warehouses_id'] = $warehouses_id;
		$row['status_type_1'] = 1;
		$update_id = $model-> _update($row,'fs_warranty','id = '.$id);
		if($update_id){
			$respon ['error'] = false;
			$respon ['message'] = 'Đã duyệt';
			echo json_encode ( $respon );
		}else{
			$respon ['message'] = 'Có lỗi xảy ra !';
			echo json_encode ( $respon );
		}
		return;
	}

	function ajax_return(){
		$id = FSInput::get('id');
		$warehouses_id = FSInput::get('warehouses_id');
		$respon = array();
		$respon ['error'] = true;
		$model = $this -> model;

		$row = array();
		$row['agree_id'] = $_SESSION['ad_userid'];
		$row['warehouses_id'] = $warehouses_id;
		$row['status_type_2'] = 1;
		$update_id = $model-> _update($row,'fs_warranty','id = '.$id);
		if($update_id){
			$data = $model-> get_record('id = '.$id,'fs_warranty');
			$product = $model-> get_record('code = "'.$data->code.'"','fs_products');
			$warehouses_product = $model-> get_record('product_id ='.$product->id. ' AND warehouses_id = '.$warehouses_id,'fs_warehouses_products');
			if(!empty($warehouses_product)){
				$row2 = array();
				$row2['amount'] = $warehouses_product->amount + $data->amount;
				$update_id2 = $model-> _update($row2,'fs_warehouses_products','id = '.$warehouses_product->id);
			}else{
				$row2 = array();
				$row2['amount'] = $data->amount;
				$row2['product_id'] = $product->id;
				$row2['warehouses_id'] = $warehouses_id;
				$add_id = $model-> _add($row2,'fs_warehouses_products');
			}
			$respon ['error'] = false;
			$respon ['message'] = 'Đã duyệt';
			echo json_encode ( $respon );
		}else{
			$respon ['message'] = 'Có lỗi xảy ra !';
			echo json_encode ( $respon );
		}
		return;
	}


	function ajax_warranty_accept(){
		$id = FSInput::get('id');
		$respon = array();
		$respon ['error'] = true;
		$model = $this -> model;
		$row = array();
		$row['agree_id'] = $_SESSION['ad_userid'];
		$row['status_type_3'] = 1;
		$update_id = $model-> _update($row,'fs_warranty','id = '.$id);
		if($update_id){
			$respon ['error'] = false;
			$respon ['message'] = 'Đã mang đi sửa';
			echo json_encode ( $respon );
		}else{
			$respon ['message'] = 'Có lỗi xảy ra !';
			echo json_encode ( $respon );
		}
		return;
	}

	function ajax_warranty_return(){
		$id = FSInput::get('id');
		$respon = array();
		$respon ['error'] = true;
		$model = $this -> model;
		$row = array();
		$row['agree_id'] = $_SESSION['ad_userid'];
		$row['status_type_3'] = 2;
		$update_id = $model-> _update($row,'fs_warranty','id = '.$id);
		if($update_id){
			$respon ['error'] = false;
			$respon ['message'] = 'Đã sửa xong';
			echo json_encode ( $respon );
		}else{
			$respon ['message'] = 'Có lỗi xảy ra !';
			echo json_encode ( $respon );
		}
		return;
	}
}





function view_room($controle,$room_id){
	$model = $controle -> model;
	if(!$room_id){
		return "";
	}

	$room = $model->get_record('id = '.$room_id,'fs_users_groups');
	if(!empty($room)){
		return $room->name;
	}
	
	return "";
}


function view_job($controle,$status){
	$model = $controle -> model;
	$array_job = array('1'=>'Xử lý đơn đổi trả','2'=>'Xử lý đơn đổi trả hỏa tốc','3'=>'Hỗ trợ kỹ thuật','4'=>'Tạo đơn đổi trả','5'=>'Thiết hàng, giao sai sản phẩm');
	return $array_job[$status];
}

function view_status($controle,$status){
	$model = $controle -> model;
	$array_status = array('1'=>'Chưa xử lý','2'=>'Hoàn thành','3'=>'Không hoàn thành');
	return $array_status[$status];
}
function view_star($controle,$star){
	$model = $controle -> model;
	if($star == 0 || $star == ''){
		return '';
	}
	return $star;
}


	
	

?>