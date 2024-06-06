
<?php 
    $title = @$data ? FSText::_('Edit'): FSText::_('Add'); 
    global $toolbar;
    $toolbar->setTitle($title);
    $toolbar->addButton('apply',FSText::_('Apply'),'','apply.png'); 
    $toolbar->addButton('save',FSText::_('Save'),'','save.png'); 
    $toolbar->addButton('cancel',FSText::_('Cancel'),'','cancel.png');  


    $this -> dt_form_begin(1,4,$title.' '.FSText::_('Đơn hàng'));
    // TemplateHelper::dt_checkbox(FSText::_('Đơn Seeding'),'is_seeding',@$data -> is_seeding,0);
    TemplateHelper::dt_edit_selectbox(FSText::_('Sàn'),'platform_id',@$data -> platform_id,0,$platforms,$field_value = 'id', $field_label='name',$size = 1,0,1);
    TemplateHelper::dt_edit_selectbox(FSText::_('Kho'),'warehouse_id',@$data -> warehouse_id,0,$warehouses,$field_value = 'id', $field_label='name',$size = 1,0,1);
    TemplateHelper::dt_edit_selectbox(FSText::_('Shop'),'shop_id',@$data -> shop_id,0,$shops,$field_value = 'id', $field_label='name',$size = 1,0,1);
    if(!empty($data)){
        TemplateHelper::dt_edit_text(FSText :: _('Ngày'),'date',date('d-m-Y',strtotime(@$data -> date)),'',60,1,0);
    }else{
        TemplateHelper::dt_edit_text(FSText :: _('Ngày'),'date',date('d-m-Y'),'',60,1,0);
    }
    
    TemplateHelper::dt_edit_selectbox(FSText::_('Giờ'),'house_id',@$data -> house_id,0,$houses,$field_value = 'id', $field_label='name',$size = 1,0,1);
    TemplateHelper::dt_edit_text(FSText :: _('File name'),'name',@$data -> name);
    TemplateHelper::dt_edit_file_multiple(FSText :: _('Hóa đơn PDF'),'file_pdf',@$data->file_pdf);
   
    TemplateHelper::dt_edit_file(FSText :: _('Đơn hàng Excel'),'file_xlsx',@$data->file_xlsx);
    if($users-> group_id != 1 && !empty($data) && @$data-> is_print == 0){ ?>
        <div class="form-group">
        <label class="col-md-2 col-xs-12 control-label">Trạng thái in</label>
        <div class="col-md-10  col-xs-12" style="font-size: 20px; color:#337ab7">Chưa In</div>
    
        </div>
    <?php
    }elseif(!empty($data) && @$data-> is_print == 1){
    ?>
    <div class="form-group">
        <label class="col-md-2 col-xs-12 control-label">Trạng thái in</label>
        <div class="col-md-10  col-xs-12" style="font-size: 20px; color:#337ab7">Đã In</div>
     
    </div>

    <?php
    }
    
    $this -> dt_form_end(@$data,1,0);
?>

<script  type="text/javascript" language="javascript">
    $(function() {
        $( "#date" ).datepicker({clickInput:true,dateFormat: 'dd-mm-yy',changeMonth: true, changeYear: true});
    
    });
</script>