<?php
global $tmpl,$config,$is_mobile; 
$tmpl -> addStylesheet('vande','blocks/strengths/assets/css');
FSFactory::include_class('fsstring');
?>
<div class="strengths_vande_block cls">
	<div class="str_content">
		<div class="title_cat"><?php echo $cat-> name; ?></div>
		<div class='strengths_why cls'>
			<?php $i=0; ?>
			<?php foreach($list as $item){ $i++; ?>
				<div class="item item_<?php echo $i; ?> cls" >
					<div class="item-inner">
						<span class="summary">
							<svg x="0px" y="0px"
							viewBox="0 0 496.027 496.027" style="enable-background:new 0 0 496.027 496.027;" xml:space="preserve">
							<g>
								<g>
									<g>
										<path d="M493.744,26.443c-3.093-3.163-8.165-3.221-11.328-0.128L168.48,332.419l-106.4-121.6
										c-2.89-3.342-7.942-3.709-11.284-0.819c-3.342,2.89-3.709,7.942-0.819,11.284c0.018,0.021,0.036,0.042,0.055,0.063l112,128
										c1.442,1.632,3.488,2.606,5.664,2.696h0.32c2.092,0.004,4.102-0.812,5.6-2.272l320-312
										C496.78,34.678,496.837,29.606,493.744,26.443z"/>
										<path d="M453.248,156.131c-4.116,1.61-6.146,6.252-4.536,10.368c44.981,115.159-11.911,244.978-127.07,289.959
										c-115.159,44.981-244.978-11.911-289.959-127.07C-13.297,214.228,43.594,84.409,158.753,39.429
										c56.243-21.968,118.976-20.284,173.959,4.67c4.062,1.739,8.764-0.144,10.503-4.206c1.684-3.933-0.025-8.495-3.879-10.354
										C218.66-25.291,76.384,28.089,21.555,148.765c-54.829,120.676-1.45,262.952,119.227,317.781
										c31.167,14.161,65.002,21.49,99.235,21.496c132.632-0.084,240.084-107.672,240-240.304c-0.019-29.79-5.58-59.316-16.4-87.072
										C462.006,156.551,457.364,154.52,453.248,156.131z"/>
									</g>
								</g>
							</g>
						</svg>

						<?php echo $item -> summary; ?>
					</span>
				</div>
			</div>
		<?php } ?>
	</div>
	<div class="str_hotline">
		<div class="inner">
			<a class="hotline" href="tel:<?php echo $config['hotline']; ?>">Tổng đài hỗ trợ: <font><?php echo $config['hotline']; ?></font></a>
			<a class="contact" href="/lien-he.html">Liên hệ</a>
		</div>
	</div>
</div>
<div class="cat_des">
	<?php echo set_image_webp($cat->image,'compress',@$cat-> name,'lazy',1,''); ?>
</div>
</div>

