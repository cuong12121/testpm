<?php
FSFactory::include_class('fsstring');
$max = IS_MOBILE?2:4;
// echo 'xxxx';
?>
<div class="product_grid_pk">
<div class='product_grid product_grid_cat'>
    <?php $j = 0; ?>
    <?php if(count($list)){?>
		<?php foreach($list as $item){?>			
			<?php 
			include 'default_item.php';
  		?>
			<?php $j ++; ?>
	    <?php }?>
    <?php }?>
    <div class="clear"></div>
</div><!--end: .vertical-->
</div>

