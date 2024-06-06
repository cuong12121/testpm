<?php  
	global $toolbar;
	$toolbar->setTitle(FSText :: _('Bán hàng ra kho') );
	$toolbar->addButton('import_barcode_warehouse_sales',FSText :: _('Nhập từ barcode'),'','barcode.png');
	$toolbar->addButton('import_excel_warehouse_sales',FSText :: _('Import excel'),'','Excel-icon.png');
	// $toolbar->addButton('add',FSText :: _('Add'),'','add.png'); 
	// $toolbar->addButton('edit',FSText :: _('Edit'),FSText :: _('You must select at least one record'),'edit.png'); 
	// $toolbar->addButton('remove',FSText :: _('Remove'),FSText :: _('You must select at least one record'),'remove.png'); 
	//$toolbar->addButton('published',FSText :: _('Published'),FSText :: _('You must select at least one record'),'published.png');
	//$toolbar->addButton('unpublished',FSText :: _('Unpublished'),FSText :: _('You must select at least one record'),'unpublished.png');
    
    //	FILTER
	$filter_config  = array();
	$fitler_config['search'] = 1; 
    $fitler_config['filter_count'] = 4;
    $fitler_config['text_count'] = 2;


    $filter_houses = array();
	$filter_houses['title'] = FSText::_('Giờ'); 
	$filter_houses['list'] = @$houses; 
	$filter_houses['field'] = 'name'; 
	$fitler_config['filter'][] = $filter_houses;


    $filter_warehouses = array();
	$filter_warehouses['title'] = FSText::_('Kho'); 
	$filter_warehouses['list'] = @$warehouses; 
	$filter_warehouses['field'] = 'name'; 
	$fitler_config['filter'][] = $filter_warehouses;


	$filter_platforms = array();
	$filter_platforms['title'] = FSText::_('Sàn'); 
	$filter_platforms['list'] = @$platforms; 
	$filter_platforms['field'] = 'name'; 
	$fitler_config['filter'][] = $filter_platforms;

	$filter_shipping_unit = array();
	$filter_shipping_unit['title'] = FSText::_('Đơn vị vận chuyển'); 
	$filter_shipping_unit['list'] = @$shipping_unit; 
	$filter_shipping_unit['field'] = 'name'; 
	$fitler_config['filter'][] = $filter_shipping_unit;	



	$text_from_date = array();
	$text_from_date['title'] =  FSText::_('Ngày'); 

	$text_to_date = array();
	$text_to_date['title'] =  FSText::_('Ngày bán hàng ra kho');

	$fitler_config['text'][] = $text_from_date;
	$fitler_config['text'][] = $text_to_date;

	//	CONFIG	
	$list_config = array();

	$list_config[] = array('title'=>'Mã vận đơn','field'=>'code','ordering'=> 1, 'type'=>'text','col_width' => '15%','arr_params'=>array('size'=> 20));

	$list_config[] = array('title'=>'Shop','field'=>'shop_code','ordering'=> 1, 'type'=>'text','col_width' => '10%','arr_params'=>array('size'=> 20));

	$list_config[] = array('title'=>'Mã sku','field'=>'sku','ordering'=> 1, 'type'=>'text','col_width' => '10%','arr_params'=>array('size'=> 20));

	$list_config[] = array('title'=>'Mã màu','field'=>'color','ordering'=> 1, 'type'=>'text','col_width' => '10%','arr_params'=>array('size'=> 20));

	$list_config[] = array('title'=>'Mã size','field'=>'size','ordering'=> 1, 'type'=>'text','col_width' => '10%','arr_params'=>array('size'=> 20));

	$list_config[] = array('title'=>'SKU nhanh','field'=>'sku_nhanh','ordering'=> 1, 'type'=>'text','col_width' => '15%','arr_params'=>array('size'=> 20));

	$list_config[] = array('title'=>'Giá SP','field'=>'product_price','ordering'=> 1, 'type'=>'format_money','col_width' => '10%','arr_params'=>array('size'=> 20));

	$list_config[] = array('title'=>'Số lượng','field'=>'count','ordering'=> 1, 'type'=>'text','col_width' => '6%','arr_params'=>array('size'=> 20));

	$list_config[] = array('title'=>'Tổng số tiền','field'=>'total_price','ordering'=> 1, 'type'=>'format_money','col_width' => '10%','arr_params'=>array('size'=> 20));

	$list_config[] = array('title'=>'Ngày','field'=>'date','ordering'=> 1, 'type'=>'datetime');

	$list_config[] = array('title'=>'Đơn vị vận chuyển','field'=>'shipping_unit_name','ordering'=> 1, 'type'=>'text','col_width' => '15%','arr_params'=>array('size'=> 20));

	$list_config[] = array('title'=>'Edit','type'=>'edit');
    //$list_config[] = array('title'=>'Created time','field'=>'created_time','ordering'=> 1, 'type'=>'datetime');
	//$list_config[] = array('title'=>'Id','field'=>'id','ordering'=> 1, 'type'=>'text');
	
	TemplateHelper::genarate_form_liting($this, $this->module,$this -> view,$list,$fitler_config,$list_config,$sort_field,$sort_direct,$pagination);
    
?>


<script>
	$(function() {
		$( "#text0" ).datepicker({clickInput:true,dateFormat: 'dd-mm-yy'});
		$( "#text1" ).datepicker({clickInput:true,dateFormat: 'dd-mm-yy'});
	});
</script>
