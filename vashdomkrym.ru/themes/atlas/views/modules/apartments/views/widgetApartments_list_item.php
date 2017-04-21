<?php
if(empty($apartments)){
	$apartments = HApartment::findAllWithCache($criteria);
}

$findIds = $countImagesArr = array();
foreach($apartments as $item) {
	$findIds[] = $item->id;
}
if (count($findIds) > 0)
	$countImagesArr = Images::getApartmentsCountImages($findIds);

$p = 1;
?>

<?php foreach ($apartments as $item):?>
	<?php
	$addClass = $lastClass = '';

	$isLast = ($p % $this->numBlocks) ? false : true;
	$lastClass = ($isLast) ? 'right_null' : '';

	if ($item->date_up_search != '0000-00-00 00:00:00')
		$addClass = 'up_in_search';
	?>

	<div class="appartment_item block <?php echo $lastClass;?>" data-lat="<?php echo $item->lat;?>" data-lng="<?php echo $item->lng;?>" data-ap-id="<?php echo $item->id; ?>">
		<div class="title_block">
			<?php
			$title = CHtml::encode($item->getStrByLang('title'));

			$description = '';
			if ($item->canShowInView('description')) {
				$description = $item->getStrByLang('description');
			}

			echo CHtml::link($title, $item->getUrl(), array('title' => $title));

			?>
		</div>
		<div class="before-image">
			<div class="image_block">
				<?php if(Yii::app()->user->checkAccess('backend_access') || (param('useUserads') && $item->isOwner())): ?>
					<div class="apartment_item_edit">
						<a href="<?php echo $item->getEditUrl();?>">
							<img src="<?php echo Yii::app()->theme->baseUrl;?>/images/doc_edit.png" alt="<?php echo tt('Update apartment', 'apartments');?>" title="<?php echo tt('Update apartment', 'apartments');?>">
						</a>
					</div>
				<?php endif;?>

				<div class="apartment_type"><?php echo HApartment::getNameByType($item->type); ?></div>

				<?php if ($item->is_special_offer):?>
					<div class="like"></div>
				<?php endif;?>

				<?php if($item->rating):?>
					<div class="rating">
						<?php
						$this->widget('CStarRating',array(
							'model'=>$item,
							'attribute' => 'rating',
							'readOnly'=>true,
							'id' => 'rating_' . $item->id,
							'name'=>'rating'.$item->id,
							'cssFile' => Yii::app()->theme->baseUrl.'/css/rating/rating.css'
						));
						?>
					</div>
				<?php endif;?>

				<?php
				$res = Images::getMainThumb(610,342, $item->images);
				$img = CHtml::image($res['thumbUrl'], $item->getStrByLang('title'), array(
					'title' => $item->getStrByLang('title'),
					'class' => 'apartment_type_img',
					'alt' => $item->getStrByLang('title'),
				));
				echo CHtml::link($img, $item->getUrl(), array('title' =>  $item->getStrByLang('title')));
				?>
			</div>
		</div>

		<div class="clear"></div>

		<div class="mini_block_full_description <?php echo $addClass; ?>">
			<?php if ($item->canShowInView('description')) { ?>
			<div class="desc">
				<div class="desc">
					<?php
					if (utf8_strlen($description) > 110)
						$description = utf8_substr($description, 0, 110) . '...';

					echo $description;

					//echo truncateText($description, 40);
					?>
				</div>
			</div>
			<?php } ?>

			<?php if($item->canShowInView('price')){ ?>
			<div class="mini_block">
				<div class="price">
					<?php
					if ($item->is_price_poa)
						echo tt('is_price_poa', 'apartments');
					else
						echo $item->getPrettyPrice();
					?>
				</div>
			</div>
			<?php } ?>

			<div class="clear"></div>

			<?php if (issetModule('comparisonList')):?>
				<?php
				$inComparisonList = false;
				if (in_array($item->id, Yii::app()->controller->apInComparison))
					$inComparisonList = true;
				?>
				<div class="row compare-check-control" id="compare_check_control_<?php echo $item->id; ?>">
					<?php
					$checkedControl = '';

					if ($inComparisonList)
						$checkedControl = ' checked = checked ';
					?>
					<input type="checkbox" name="compare<?php echo $item->id; ?>" class="compare-check compare-float-left" id="compare_check<?php echo $item->id; ?>" <?php echo $checkedControl;?>>

					<a href="<?php echo ($inComparisonList) ? Yii::app()->createUrl('comparisonList/main/index') : 'javascript:void(0);';?>" data-rel-compare="<?php echo ($inComparisonList) ? 'true' : 'false';?>" id="compare_label<?php echo $item->id; ?>" class="compare-label">
						<?php echo ($inComparisonList) ? tt('In the comparison list', 'comparisonList') : tt('Add to a comparison list ', 'comparisonList');?>
					</a>
				</div>
			<?php endif;?>

			<div class="clear"></div>

			<?php if($item->square || $item->berths):?>
				<dl class="mini_desc">
					<?php $showBerth = false;?>
					<?php if($item->canShowInView('berths')):?>
						<?php $showBerth = true;?>
						<dt>
							<span class="icon-bedroom icon-mrgr"></span>
						</dt><dd><?php echo Yii::t('module_apartments', 'berths').': '.CHtml::encode($item->berths);?></dd>

					<?php endif;?>
					<?php if($item->canShowInView('square')):?>
						<dt>
							<span class="icon-square icon-mrgr"></span>
						</dt><dd><?php echo Yii::t('module_apartments', 'total square: {n}', $item->square)." ".tc('site_square');?></dd>
					<?php endif;?>
				</dl>
			<?php endif;?>
		</div>
	</div>
	<?php $p++;?>
<?php endforeach;?>