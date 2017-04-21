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


foreach ($apartments as $item) {
	$addClass = '';

	if ($item->is_special_offer) {
		$addClass = 'special_offer_highlight';
	} elseif ($item->date_up_search != '0000-00-00 00:00:00'){
		$addClass = 'up_in_search';
	}
	?>
	<div class="appartment_item <?php echo $addClass; ?>" data-lat="<?php echo $item->lat;?>" data-lng="<?php echo $item->lng;?>" data-ap-id="<?php echo $item->id; ?>" >

		<?php if(Yii::app()->user->checkAccess('backend_access') || (param('useUserads') && $item->isOwner())){ ?>
			<div class="apartment_item_edit">
				<a href="<?php echo $item->getEditUrl();?>">
					<img src="<?php echo Yii::app()->theme->baseUrl;?>/images/doc_edit.png" alt="<?php echo tt('Update apartment', 'apartments');?>" title="<?php echo tt('Update apartment', 'apartments');?>">
				</a>
			</div>
		<?php } ?>

		<div class="offer">
			<div class="offer-photo" align="left">
				<div class="offer-photo-wrapper">
					<?php if(array_key_exists($item->id, $countImagesArr) && $countImagesArr[$item->id] > 1): ?>
						<div class="apartment_count_img"><img src="<?php echo Yii::app()->theme->baseUrl;?>/images/photo_count.png"><b><?php echo $countImagesArr[$item->id];?></b></div>
					<?php endif; ?>

					<div class="apartment_type"><?php echo HApartment::getNameByType($item->type); ?></div>

					<?php
						$res = Images::getMainThumb(150,100, $item->images);
						$img = CHtml::image($res['thumbUrl'], $item->getStrByLang('title'), array(
							'title' => $item->getStrByLang('title'),
							'class' => 'apartment_type_img'
						));
						echo CHtml::link($img, $item->getUrl(), array('title' =>  $item->getStrByLang('title')));
					?>
                </div>
			</div>
			<div class="offer-text">
				<div class="apartment-title">
						<?php
							$title = CHtml::encode($item->getStrByLang('title'));

							echo CHtml::link($title,
							$item->getUrl(), array('class' => 'offer'));
						?>
				</div>
				<?php
					if($item->rating && !isset($booking)){
						echo '<div class="ratingview">';
						$this->widget('CStarRating',array(
							'model'=>$item,
							'attribute' => 'rating',
							'readOnly'=>true,
							'id' => 'rating_' . $item->id,
							'name'=>'rating'.$item->id,
						));
						echo '</div>';
					}
				?>

				<div class="clear"></div>

				<?php if($item->canShowInView('price')){ ?>
				<p class="cost">
					<?php
						if ($item->is_price_poa)
							echo tt('is_price_poa', 'apartments');
						else
							echo $item->getPrettyPrice();

					?>
				</p>
				<?php } ?>

				<?php
					if( $item->floor || $item->floor_total || $item->square || $item->berths){
						echo '<p class="desc">';

						$echo = array();

						if($item->canShowInView('floor_all')){
							if($item->floor && $item->floor_total){
								$echo[] = Yii::t('module_apartments', '{n} floor of {total} total', array($item->floor, '{total}' => $item->floor_total));
							} else {
								if($item->floor){
									$echo[] = $item->floor.' '.tt('floor', 'common');
								}
								if($item->floor_total){
									$echo[] = tt('floors', 'common').': '.$item->floor_total;
								}
							}
						}

						if($item->canShowInView('square')){
							$echo[] = '<span class="nobr">'.Yii::t('module_apartments', 'total square: {n}', $item->square)." ".tc('site_square')."</span>";
						}
						if($item->canShowInView('berths')){
							$echo[] = '<span class="nobr">'.Yii::t('module_apartments', 'berths').': '.CHtml::encode($item->berths)."</span>";
						}
						echo implode(', ', $echo);
						unset($echo);

						echo '</p>';
					}
				?>
			</div>
			<?php if (issetModule('comparisonList')):?>
					<div class="clear"></div>
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
		</div>
	</div>
<?php
}
