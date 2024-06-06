<link type="text/css" rel="stylesheet" media="all" href="<?php echo URL_ROOT; ?>/libraries/jquery/jquery.ui/jquery-ui.css" />
<script type="text/javascript" src="<?php echo URL_ROOT; ?>/libraries/jquery/jquery.ui/jquery-ui.js"></script>
<link type="text/css" rel="stylesheet" media="all" href="<?php echo URL_ADMIN; ?>/templates/default/css/jquery-ui.css" />

<?php  
global $toolbar;
$toolbar->setTitle(FSText :: _('Danh sách xuất nhập vị trí') );
	// $toolbar->addButton('duplicate',FSText :: _('Duplicate'),'','duplicate.png');
	// $toolbar->addButton('save_all',FSText :: _('Save'),'','save.png'); 
$toolbar->addButton('add',FSText :: _('Add'),'','add.png'); 
$toolbar->addButton('edit',FSText :: _('Edit'),FSText :: _('You must select at least one record'),'edit.png'); 
$toolbar->addButton('remove',FSText :: _('Remove'),FSText :: _('You must select at least one record'),'remove.png'); 
$toolbar->addButton('published',FSText :: _('Published'),FSText :: _('You must select at least one record'),'published.png');
$toolbar->addButton('unpublished',FSText :: _('Unpublished'),FSText :: _('You must select at least one record'),'unpublished.png');

	//	FILTER
$filter_config  = array();
$fitler_config['search'] = 1; 
$fitler_config['filter_count'] = 1;		
$fitler_config['text_count'] = 2;

$text_from_date = array();
$text_from_date['title'] =  FSText::_('Từ ngày'); 

$text_to_date = array();
$text_to_date['title'] =  FSText::_('Đến ngày'); 	

$fitler_config['text'][] = $text_from_date;
$fitler_config['text'][] = $text_to_date;	

$filter_warehouses = array();
$filter_warehouses['title'] = FSText::_('Kho hàng'); 
$filter_warehouses['list'] = @$warehouses; 
$filter_warehouses['field'] = 'name'; 	

		//Loại sản phẩm
$filter_status = array();
$filter_status['title'] = FSText::_('Trạng thái'); 
$filter_status['list'] = $this-> arr_status;

$fitler_config['filter'][] = $filter_warehouses;
// $fitler_config['filter'][] = $filter_status;


	//	CONFIG	
$list_config = array();
$list_config[] = array('title'=>'Id / Thời gian','field'=>'id','ordering'=> 1, 'type'=>'text','col_width' => '10%');
$list_config[] = array('title'=>'Ngày','field'=>'created_time','type'=>'text','no_col'=>1);
$list_config[] = array('title'=>'Name','field'=>'name','ordering'=> 1, 'type'=>'text','col_width' => '15%','arr_params'=>array('size'=> 30));

// $list_config[] = array('title'=>'Kho hàng','field'=>'warehouses_name','ordering'=> 1, 'type'=>'text');
$list_config[] = array('title'=>'Kho hàng','field'=>'id','ordering'=> 1, 'type'=>'text','arr_params'=>array('function'=>'show_ware'));

$list_config[] = array('title'=>'Tổng SP','field'=>'total_product','ordering'=> 1, 'type'=>'text');
$list_config[] = array('title'=>'Tổng SL','field'=>'total_amount','ordering'=> 1, 'type'=>'text');

$list_config[] = array('title'=>'File excel','field'=>'file','ordering'=> 1, 'type'=>'text','arr_params'=>array('function'=>'show_file'));
$list_config[] = array('title'=>'Người tạo','field'=>'create_username','ordering'=> 1, 'type'=>'text');
$list_config[] = array('title'=>'Ghi chú','field'=>'note','ordering'=> 1, 'type'=>'text');

// $list_config[] = array('title'=>'Edit','type'=>'edit');
$list_config[] = array('title'=>'Thao tác','type'=>'list_action','list_action' => array('edit'=> 'Sửa phiếu','print' => 'In phiếu'));
TemplateHelper::genarate_form_liting($this, $this->module,$this -> view,$list,$fitler_config,$list_config,$sort_field,$sort_direct,$pagination);
?>
