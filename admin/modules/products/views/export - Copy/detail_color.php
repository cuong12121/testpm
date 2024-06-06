	<table border="1" class="tbl_form_contents" width="100%" cellspacing="4" cellpadding="4" bordercolor="#CCC">
		<thead>
			<tr>
				<th align="center" >
					<?php echo FSText::_('Màu');?>	
				</th>
			
				<th align="center" >
					<?php echo FSText::_('Giá');?>	
				</th>
				<th align="center"  width="15" >
					<?php echo FSText::_('Chọn màu');?>	
				</th>
			</tr>
		</thead>
		<tbody>
		
		<?php
			if(isset($colors) && !empty($colors)){
				foreach ($colors as $item) { 
					@$data_by_color = $array_data_by_color[$item->id];
//					echo '<pre>';
//					print_r($data_by_color);
//					echo '</pre>';
		?>
			<?php if(@$data_by_color){?>
				<tr>
					<td>
						
						<?php echo $item -> name;?><br/>
						<span id="colorSelector"><span style="background-color: <?php echo ($item -> code)?'#'.$item -> code:'#0000ff';?>"></span></span>
					</td>
					<td>
						 <input type="text" size="20" id="color_price_exit_<?php echo $item->id;?>" onkeypress="nurZahlen(this)" name="color_price_exist_<?php echo $item->id;?>"  value="<?php echo @$data_by_color->price;?>">
					</td>
					<td>
						<input type="checkbox"  value="<?php echo $item->id; ?>"  name="other_color_exit[]" id="other_color_exit<?php echo $item->id; ?>" checked/>
						<input type="hidden" value="<?php echo @$data_by_color -> id; ?>" name="id_exist_<?php echo $item->id;?>">
						<input type="hidden" value="<?php echo $item->id; ?>" name="color_exist_total[]"  />
					</td>
				</tr>
				
			<?php }else{?>
				<tr>
					<td>
						
						<?php echo $item -> name;?><br/>
						<span id="colorSelector"><span style="background-color: <?php echo ($item -> code)?'#'.$item -> code:'#0000ff';?>"></span></span>
					</td>
					<td>
						 <input type="text" size="20" id="new_color_price_<?php echo $item->id;?>" onkeypress="nurZahlen(this)" name="new_color_price_<?php echo $item->id;?>" >
					</td>
					<td>
						<input type="checkbox"  value="<?php echo $item->id; ?>"  name="other_color[]" id="other_color<?php echo $item->id; ?>" />
					</td>
				</tr>
			<?php }?>
				<?php
				}
			}
			?>
	</tbody>		
	</table>
	
<style>
#colorSelector {
   border: 1px solid #9F9F9F;
    display: inline-block;
    height: 16px;
    position: relative;
    width: 16px;
}
#colorSelector span {
   height: 16px;
    left: 0;
    position: absolute;
    top: 0;
    width: 16px;
}
</style>