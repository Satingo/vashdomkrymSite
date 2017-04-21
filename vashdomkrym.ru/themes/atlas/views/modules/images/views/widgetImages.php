<?php
	$count = 1;
	if($this->images) : ?>
	<ul style="left: 0px; top: 0px;" id="jcarousel">
		<?php foreach($this->images as $image) :?>
			<li>
				<?php
					$imgTag = CHtml::image(Images::getThumbUrl($image, 69, 66), Images::getAlt($image), array(
						'onclick' => 'setImgGalleryIndex("'.$count.'");',
					));
					echo CHtml::link($imgTag, Images::getFullSizeUrl($image), array(
						'data-gal' => 'prettyPhoto[img-gallery]',
						'title' => Images::getAlt($image),
					));
					$count++;
				?>
			</li>
		<?php endforeach;?>
	</ul>
<?php endif;?>