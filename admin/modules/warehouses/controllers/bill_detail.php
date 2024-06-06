<?php
	// models 
//	include 'modules/'.$module.'/models/'.$view.'.php';

class WarehousesControllersBill_detail extends Controllers
{
	function __construct()
	{
			// parent::display();
			// $sort_field = $this -> sort_field;
			// $sort_direct = $this -> sort_direct;
		$this->view = 'bill_detail' ; 
		$this-> arr_type = array('1'=>'Phiếu nhập','2'=>'Phiếu xuất');

		$this-> arr_type_import = array('1'=>'Nhà cung cấp','2'=>'Bán lẻ', '3' => 'Bán sỉ','4' => 'Khác','5' => 'Hoàng hàng');

		$this-> arr_type_discount = array('1'=>'Tiền mặt','2'=>'%');

		global $check_permission_status_warehouses;

		if($check_permission_status_warehouses) {
			$this-> arr_status = array('1' => 'Phiếu tạm','2'=>'Chờ duyệt','3'=>'Đã duyệt', '4'=> 'Hoàn thành', '5' => 'Hủy');
		} else {
			$this-> arr_status = array('1' => 'Phiếu tạm','2'=>'Chờ duyệt');
		}

		$this-> arr_status_show = array('1' => 'Phiếu tạm','2'=>'Chờ duyệt','3'=>'Đã duyệt', '4'=> 'Hoàn thành', '5' => 'Hủy');
		
		$this-> arr_style_import = array('1'=>'Nhập tay','2'=>'Nhập file Excel');
		parent::__construct(); 
	}

	function display()
	{
		parent::display();
		$sort_field = $this -> sort_field;
		$sort_direct = $this -> sort_direct;

		$model  = $this -> model;
		$list = $model->get_data();

		// $categories = $model->get_categories_tree();

		$warehouses = $model->get_records('1=1','fs_warehouses','*');
		$supplier = $model->get_records('1=1','fs_supplier','*');


		$pagination = $model->getPagination();

		$breadcrumbs = array();
		$breadcrumbs[] = array(0=>'Sản phẩm xuất nhập kho', 1 => '');	
		global $tmpl;
		$tmpl->assign ( 'breadcrumbs', $breadcrumbs );
		$tmpl->assign ( 'seo_title', 'Sản phẩm xuất nhập kho');

		include 'modules/'.$this->module.'/views/'.$this->view.'/list.php';
	}

	function add()
	{
		$model = $this -> model;
		$warehouses = $model->get_records('1=1','fs_warehouses','*');
		$supplier = $model->get_records('1=1','fs_supplier','*');
		$maxOrdering = $model->getMaxOrdering();

		$breadcrumbs = array();
		$breadcrumbs[] = array(0=>'Danh sách Xuất nhập kho', 1 => 
			FSRoute::_("index.php?module=".$this-> module."&view=".$this-> view));	
		global $tmpl;
		$tmpl->assign ( 'breadcrumbs', $breadcrumbs );

		$tmpl->assign ( 'seo_title', 'Xuất nhập kho');

		include 'modules/'.$this->module.'/views/'.$this -> view.'/add.php';
	}

	function edit(){

		$ids = FSInput::get('id',array(),'array');
		$id = $ids[0];
		$model = $this -> model;
		$data = $model->get_record_by_id($id);

		$list_products = $model-> get_records('bill_id = '.$id,'fs_warehouses_bill_detail','*');

		$warehouses = $model->get_records('1=1','fs_warehouses','*');
		$supplier = $model->get_records('1=1','fs_supplier','*');

		$breadcrumbs = array();
		$breadcrumbs[] = array(0=>'Danh sách Xuất nhập kho', 1 => 
			FSRoute::_("index.php?module=".$this-> module."&view=".$this-> view));	
		global $tmpl;
		$tmpl->assign ( 'breadcrumbs', $breadcrumbs );
		$tmpl->assign ( 'seo_title', 'Xuất nhập kho');


		include 'modules/'.$this->module.'/views/'.$this->view.'/detail.php';
	}

	function print(){
		$ids = FSInput::get('id',array(),'array');
		$id = $ids[0];
		$model = $this -> model;
		$data = $model->get_record_by_id($id);

		$list_products = $model-> get_records('bill_id = '.$id,$model-> table_name_detail,'*');
		
		global $tmpl;
		$tmpl->assign ( 'seo_title', 'Xuất nhập kho');

		include 'modules/'.$this->module.'/views/'.$this->view.'/print.php';
	}

	function ajax_products_search_keyword(){
		// echo '111';die;
		$model = $this -> model;
		$type_products_search = FSInput::get('type_products_search');
		$products_search_keyword = FSInput::get('products_search_keyword');
		$warehouses_id = FSInput::get('warehouses_id');

		$list_products = $model-> get_records('name LIKE "%'.$products_search_keyword.'%" OR code LIKE "%'.$products_search_keyword.'%"','fs_products','name,code,id,price,image,weight', '', 50);

		// print_r($list_products);

		if(!empty($list_products)) {
			foreach ($list_products as &$product) {
				$record_amount = $model-> get_record('product_id = '.$product-> id.' AND warehouses_id = '.$warehouses_id,'fs_warehouses_products','amount');
				if(!empty($record_amount)) {
					$product-> amount = $record_amount-> amount;
				} else {
					$product-> amount = 0;
				}
			}
		}

		$html = $this -> ajax_products_search_keyword_genarate($list_products);
		echo $html;
		return;

		// echo $products_search_keyword;
	}

	function ajax_products_search_keyword_genarate($data){
		if(empty($data)) return;
		$html = '';
		foreach ($data as $item) {
			$html .= '<div class="item"><a class="cls" href="javascript:void(0)" onclick="set_products('.$item-> id.')"><div class="image"><img src="'.URL_ROOT.str_replace('/original','/resized/',$item-> image).'"></div><div class="item_inner"><div class="name">'.$item-> code.' - '.$item->name.'</div><div class="info">('.format_money($item-> price,'đ','0đ').') (Tồn: '.$item-> amount.')</div></div></a><input type="hidden" id="data_product_name_'.$item->id.'" value="'.$item-> name.'"><input type="hidden" id="data_product_weight_'.$item->id.'" value="'.$item-> weight.'"><input type="hidden" id="data_product_price_'.$item->id.'" value="'.format_money($item-> price,'','0').'"><input type="hidden" id="data_product_price_name_'.$item->id.'" value="'.format_money($item-> price,'đ','0đ').'"><input type="hidden" id="data_product_amount_'.$item->id.'" value="'.$item-> amount.'"></div>';
		}
		return $html;

	}

	function ajax_get_manufactory_related(){
		$model = $this -> model;
		$data = $model->ajax_get_manufactory_related();
		$html = $this -> manufactory_genarate_related($data);
		echo $html;
		return;
	}


	function manufactory_genarate_related($data){
		$str_exist = FSInput::get('str_exist');
		$html = '';
		$html .= '<div class="manufactory_related">';
		foreach ($data as $item){
			if($str_exist && strpos(','.$str_exist.',', ','.$item->id.',') !== false ){
				$html .= '<div class="red manufactory_related_item  manufactory_related_item_'.$item -> id.'" onclick="javascript: set_manufactory_related('.$item->id.')" style="display:none" >';	
				$html .= $item -> name;				
				$html .= '</div>';					
			}else{
				$html .= '<div class="manufactory_related_item  manufactory_related_item_'.$item -> id.'" onclick="javascript: set_manufactory_related('.$item->id.')">';	
				$html .= $item -> name;				
				$html .= '</div>';	
			}
		}
		$html .= '</div>';
		return $html;
	}
}

function show_ware($controle,$id){
	$model = $controle-> model;

	$data = $model-> get_record('id = '.$id,'fs_warehouses_bill_detail_history','*');


	$txt = '';
	if($data-> type == 1 || $data-> type == 2 || $data-> type == 5) {
		$txt .= '<p>'.$data-> warehouses_name.'</p>';
	} elseif($data-> type == 3 || $data-> type == 4) {
		$txt .= '<p>'.$data-> from_warehouses_name.' &#8594; '.$data-> to_warehouses_name.'</p>';
	}


	if($data-> type == 1) {
		$txt .= "<div class='alert-success'>Phiếu Nhập</div>";
	}elseif($data-> type == 2) {
		$txt .= "<div class='alert-danger'>Phiếu Xuất</div>";
	}elseif($data-> type == 3) {
		$txt .= "<div class='alert-danger'>Phiếu Chuyển Kho (-)</div>";
		if($data->edit == 1){
			$txt .= "<br/>" .$data->type_action_name ;
		}
	}elseif($data-> type == 4) {
		$txt .= "<div class='alert-success'>Phiếu Chuyển Kho (+)</div>";
		if($data->edit == 1){
			$txt .= "<br/>" .$data->type_action_name ;
		}
	}elseif($data-> type == 5) {
		if($data->amount >= 0){
			$txt .= "<div class='alert-success'>Phiếu kiểm kho (+)</div>";
		}else{
			$txt .= "<div class='alert-danger'>Phiếu kiểm kho (-)</div>";
		}
	}
	return $txt;
}




function show_ware_old($controle,$id){
	$model = $controle-> model;
	
	$data = $model-> get_record('id = '.$id,'fs_warehouses_bill','*');

	$txt = '';
	if($data-> type == 1 || $data-> type == 2) {
		$txt .= '<p>'.$data-> warehouses_name.'</p>';
	} elseif($data-> type == 3 || $data-> type == 4) {
		$txt .= '<p>'.$data-> warehouses_name.' &#8594; '.$data-> to_warehouses_name.'</p>';
	}

	$class ='';
	if($data-> type == 1) {
		$class ='alert-success';
		$text_type = $controle-> arr_type[$data-> type].' '.$controle-> arr_type_import[$data-> type_import];
	} elseif($data-> type == 2) {
		$class ='alert-danger';
		$text_type = $controle-> arr_type[$data-> type].' '.$controle-> arr_type_import[$data-> type_import];
	} elseif($data-> type == 3) {
		$class ='alert-warning';
		$text_type = $controle-> arr_type[$data-> type];
	}
	$txt .= '<span class="'.$class.'">'.$text_type.'</span>';
	return $txt;
}

function show_file($controle,$file){
	if(!$file) return;
	return '<a style="color:green" target="_blank" title="Xem file" href="'.URL_ROOT.$file.'">Xem file</a>';
}


function show_discount_type($controle,$type){
	return $controle-> arr_type_discount[$type];
}

function show_discount($controle,$discount){
	return format_money($discount,' ','0');
}

function show_status($controle,$status){
	$class ='';
	if($status == 2) {
		$class ='alert-danger';
	} elseif($status == 3) {
		$class ='alert-info';
	} elseif($status == 4) {
		$class ='alert-success';
	} elseif($status == 5) {
		$class ='alert-dark';
	}else
	{
		$class ='alert-warning';
	}
	return '<span class="'.$class.'">'.$controle-> arr_status_show[$status].'</span>';
}

function view_name_product($controle,$product_id){
	$model = $controle-> model;
	$data = $model-> get_record('id = '.$product_id,'fs_products','name');
	return $data->name;
}

function show_total($controle,$id){
	$model = $controle-> model;
	$data = $model-> get_record('id = '.$id,'fs_warehouses_bill_detail_history','price,amount,type');
	if($data->type == 1 || $data->type == 2){
		return format_money($data->price * $data->amount,' ','0');
	}else{
		return '0';
	}
	
}

function show_record_id($controle,$id){
	$model = $controle-> model;
	$text ="";
	$data = $model-> get_record('id = '.$id,'fs_warehouses_bill_detail_history','created_time,bill_id,type');
	if($data->type == 1 || $data->type == 2){
		$text = '<a href="'.URL_ROOT.'admin/warehouses/bill/edit/'.$data-> bill_id.'" >'.$data-> bill_id.'</a>';
	}elseif($data->type == 3 || $data->type == 4){
		$text = '<a href="'.URL_ROOT.'admin/warehouses/bill_transfer/edit/'.$data-> bill_id.'" >'.$data-> bill_id.'</a>';
	}
	return $text;
}

?>