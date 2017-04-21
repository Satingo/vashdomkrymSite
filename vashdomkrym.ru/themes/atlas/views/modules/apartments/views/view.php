<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->theme->baseUrl.'/css/jcarousel.ajax.css');
Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl.'/js/jcarousel.ajax.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl.'/js/jquery.jcarousel.min.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl.'/js/easyResponsiveTabs.js', CClientScript::POS_END);

Yii::app()->clientScript->registerScript('generate-phone', '
	function generatePhone(){
		$("span#owner-phone").html(\'<img src="'.Yii::app()->controller->createUrl('/apartments/main/generatephone', array('id' => $model->id)).'" />\');
		$(".phone-show-alert").show();
	}
', CClientScript::POS_END);

Yii::app()->clientScript->registerScript('initizlize-easy-responsive-tabs', "
	$('.resptabscont').easyResponsiveTabs();
", CClientScript::POS_READY);

Yii::app()->clientScript->registerScript('reInitMap', '
	var useYandexMap = '.param('useYandexMap', 1).';
	var useGoogleMap = '.param('useGoogleMap', 1).';
	var useOSMap = '.param('useOSMMap', 1).';

	function reInitMap(elem) {
		// place code to end of queue
		if(useGoogleMap){
			setTimeout(function(){
				var tmpGmapCenter = mapGMap.getCenter();

				google.maps.event.trigger($("#googleMap")[0], "resize");
				mapGMap.setCenter(tmpGmapCenter);

				if (($("#gmap-panorama").length > 0)) {
					initializeGmapPanorama();
				}
			}, 0);
		}

		if(useYandexMap){
			setTimeout(function(){
				ymaps.ready(function () {
					globalYMap.container.fitToViewport();
					globalYMap.setCenter(globalYMap.getCenter());
				});
			}, 0);
		}

		if(useOSMap){
			setTimeout(function(){
				L.Util.requestAnimFrame(mapOSMap.invalidateSize,mapOSMap,!1,mapOSMap._container);
			}, 0);
		}
	}
', CClientScript::POS_END);

$model->references = HApartment::getFullInformation($model->id, $model->type);
$img = null;
?>

<?php
$this->pageTitle .= ' - '.$model->getTitle();
if (isset($model->city) && isset($model->city->name)) {
	$this->pageTitle .=  ', '.tc('City'). ' ' . $model->city->name;
}

if ($model->getStrByLang('description'))
	$this->pageDescription = truncateText($model->getStrByLang('description'), 20);

$searchUrl = Yii::app()->user->getState('searchUrl');
if($searchUrl){
	$this->breadcrumbs=array(
		Yii::t('common', 'Apartment search') => $searchUrl,
		truncateText($model->getTitle(), 10),
	);
} else {
	$this->breadcrumbs=array(
		Yii::t('common', 'Apartment search') => array('/quicksearch/main/mainsearch'),
		truncateText($model->getTitle(), 10),
	);
}
?>
<div class="relative">
	<div class="preview">
		<?php

			if($searchUrl){
				echo CHtml::link('<img src="'.Yii::app()->theme->baseUrl.'/images/design/back2search.png" alt="'.tc('Go back to search results').'" title="'.tc('Go back to search results').'"  />', $searchUrl);
			} elseif (stripos(Yii::app()->request->urlReferrer, Yii::app()->getBaseUrl(true)) !== false)
				echo CHtml::link('<img src="'.Yii::app()->theme->baseUrl.'/images/design/back2search.png" alt="'.tc('Go back to search results').'" title="'.tc('Go back to search results').'"  />', '#', array('onclick'=>'window.history.back(); return false;'));

		?>
	</div>
</div>


<div class="pdg-apartment-block">
	<div class="title highlight-left-right">
		<span>
			<h1><?php echo CHtml::encode($model->getTitle()); ?></h1>
		</span>
			<?php
				echo '&nbsp; ';
				$editUrl = $model->getEditUrl();
				if($editUrl){
					echo CHtml::link('<img src="'.Yii::app()->theme->baseUrl.'/images/design/edit.png" alt="'.tt('Update apartment').'" title="'.tt('Update apartment').'"  />',$editUrl);
				}
		$imgPrint = '<img src="'.Yii::app()->theme->baseUrl.'/images/design/print.png" alt="'.tc('Print version').'" title="'.tc('Print version').'"  />';
		echo CHtml::link($imgPrint, $model->getUrl().'?printable=1', array('target' => '_blank'));
		?>
	</div>


	<?php if($model->rating):?>
		<div class="rating-item-view">
			<?php
				$this->widget('CStarRating',array(
					'model'=>$model,
					'attribute' => 'rating',
					'readOnly'=>true,
					'id' => 'rating_' . $model->id,
					'name'=>'rating'.$model->id,
					'cssFile' => Yii::app()->theme->baseUrl.'/css/rating/rating.css'
				));
			?>
		</div>
		<div class="clear"></div>
	<?php endif;?>


	<?php if($model->is_special_offer):?>
		<div class="big-special-offer">
			<?php
			echo '<h4>'.Yii::t('common', 'Special offer!').'</h4>';

			if($model->is_free_to != '0000-00-00'){
				echo '<p>';
				echo Yii::t('common','Is avaliable');
				if($model->is_free_to != '0000-00-00'){
					echo ' '.Yii::t('module_apartments', 'to');
					echo ' '.Booking::getDate($model->is_free_to);
				}
				echo '</p>';
			}
			?>
		</div>
	<?php endif;?>
</div>

<div class="b_item">
	<div class="b_item__slider">
		<?php if (isset($model->images) && !empty($model->images)):?>
			<div id="imgHolder" style="opacity: 1;"></div>
			<div class="jcarousel-wrapper">
				<div class="mini_gallery jcarousel" data-jcarousel="true">
					<?php
						if ($model->images) {
							$this->widget('application.modules.images.components.ImagesWidget', array(
								'images' => $model->images,
								'objectId' => $model->id,
							));
						}
					?>
				</div>

				<div class="jcarousel-prev jcarousel-control-prev"></div>
				<div class="jcarousel-next jcarousel-control-next"></div>
			</div>
		<?php else: ?>
			<?php
				$res = Images::getMainThumb(640, 400, $model->images);
				$img = CHtml::image($res['thumbUrl'], $res['comment']);
				if($res['link']){
					echo CHtml::link($img, $res['link'], array(
						'data-gal' => 'prettyPhoto[img-gallery]',
						'title' => $res['comment'],
					));
				} else {
					echo $img;
				}
			?>
		<?php endif;?>
	</div>
</div>

<div class="b_item__info block-right <?php if($model->is_special_offer || count(Yii::app()->user->getFlashes(false))) echo ' block-right-with-special-offer ';?>">
	<?php if (issetModule('paidservices') && param('useUserads')):?>
		<?php 
			$wantTypes = HApartment::getI18nTypesArray();
			$typeName = (isset($wantTypes[$model->type]) && isset($wantTypes[$model->type]['current'])) ? mb_strtolower($wantTypes[$model->type]['current'], 'UTF-8') : '';				
		?>
		<?php if ($typeName) :?>
			<div class="promotion-paidservices-in-apartment">
				<div class="paidservices-promotion-title"><?php echo tt('Is it your listing?', 'apartments');?></div>
				<div class="paidservices-promotion-title-promotion-title"><?php echo tt('Would you like to', 'apartments');?>&nbsp;<?php echo $typeName;?>&nbsp;<?php echo tt('quicker?', 'apartments');?></div>
				<div class="paidservices-promotion-description">
					<?php echo tt('Try to', 'apartments');?>&nbsp;
					<?php echo CHtml::link(tt('apply paid services', 'apartments'), Yii::app()->createUrl('/userads/main/update', array('id' => $model->id, 'show' => 'paidservices')), array('target'=>'_blank'));?>
				</div>
				<div class="clear"></div>
			</div>
		<?php endif;?>
	<?php endif;?>
	
	<?php
		if ($model->deleted)
			echo '<div class="name deleted">' .  tt('Listing is deleted', 'apartments') . '</div>';
	?>
	<div class="name">
		<?php
			echo HApartment::getNameByType($model->type).',&nbsp;';
			echo utf8_ucfirst($model->objType->name);
			if ($model->num_of_rooms){
				echo ',&nbsp;';
				echo Yii::t('module_apartments',
					'{n} bedroom|{n} bedrooms|{n} bedrooms', array($model->num_of_rooms));
			}
			if (issetModule('location')) {
				echo '<br />';
				if($model->locCountry || $model->locRegion || $model->locCity)
					echo "<br>";

				if($model->locCountry){
					echo $model->locCountry->getStrByLang('name');
				}
				if($model->locRegion){
					if($model->locCountry)
						echo ',&nbsp;';
					echo $model->locRegion->getStrByLang('name');
				}
				if($model->locCity){
					if($model->locCountry || $model->locRegion)
						echo ',&nbsp;';
					echo $model->locCity->getStrByLang('name');
				}
			} else {
				if(isset($model->city) && isset($model->city->name)){
					echo ',&nbsp;';
					echo $model->city->name;
				}
			}

			
		?>
	</div><br />

	<?php if($model->canShowInView('price')){ ?>
	<div class="price">
		<?php if ($model->is_price_poa)
			echo tt('is_price_poa', 'apartments');
		else
			echo $model->getPrettyPrice();
		?>
	</div>
	<?php } ?>

	<?php
		echo '<div class="bron-box">';
			if(($model->owner_id != Yii::app()->user->getId()) && $model->type == Apartment::TYPE_RENT && !$model->deleted){
				echo CHtml::link(tt('Booking'), array('/booking/main/bookingform', 'id' => $model->id), array('class' => 'bron fancy mgp-open-ajax'));
			}
		echo '</div>';
	?>

	<div class="b_itemlinks">
		<div class="b_itemlinks__links">
			<?php if(issetModule('apartmentsComplain')):?>
				<?php if(($model->owner_id != Yii::app()->user->getId())):?>
					<?php echo CHtml::link(tt('do_complain', 'apartmentsComplain'), $this->createUrl('/apartmentsComplain/main/complain', array('id' => $model->id)), array('class' => 'fancy mgp-open-ajax')); ?>
				<?php endif; ?>
			<?php endif; ?>

			<?php if (issetModule('comparisonList')):?>
				<?php
				$inComparisonList = false;
				if (in_array($model->id, Yii::app()->controller->apInComparison))
					$inComparisonList = true;
				?>
				<div class="compare-check-control view-apartment" id="compare_check_control_<?php echo $model->id; ?>">
					<?php
					$checkedControl = '';

					if ($inComparisonList)
						$checkedControl = ' checked = checked ';
					?>
					<input type="checkbox" name="compare<?php echo $model->id; ?>" class="compare-check compare-float-left" id="compare_check<?php echo $model->id; ?>" <?php echo $checkedControl;?>>

					<a href="<?php echo ($inComparisonList) ? Yii::app()->createUrl('comparisonList/main/index') : 'javascript:void(0);';?>" data-rel-compare="<?php echo ($inComparisonList) ? 'true' : 'false';?>" id="compare_label<?php echo $model->id; ?>" class="compare-label">
						<?php echo ($inComparisonList) ? tt('In the comparison list', 'comparisonList') : tt('Add to a comparison list ', 'comparisonList');?>
					</a>
				</div>
			<?php endif;?>
		</div>
	</div>

	<?php if(param('useShowUserInfo')):?>
		<?php $owner = $model->user;?>
		<div class="block_item">
			<div class="title_block_item"><?php echo tc('Listing provided by');?></div>

			<div class="name_block_item">
				<span><?php echo $owner->getNameForType();?></span>
			</div>

			<?php echo $owner->renderAva(true, '', true, false);?>

			<ul>
				<?php
					if($model->canShowInView('phone')) {
						if (issetModule('tariffPlans') && issetModule('paidservices') && ($model->owner_id != Yii::app()->user->id)) {
							if (Yii::app()->user->isGuest) {
								$defaultTariffInfo = TariffPlans::getFullTariffInfoById(TariffPlans::DEFAULT_TARIFF_PLAN_ID);

								if (!$defaultTariffInfo['showPhones']) {
									echo '<li class="li1">'.Yii::t('module_tariffPlans', 'Please <a href="{n}">login</a> to view', Yii::app()->controller->createUrl('/site/login')).'</li>';
								}
								else {
									echo '<li class="li1"><span id="owner-phone">' . CHtml::link(tc('Show phone'), 'javascript: void(0);', array('onclick' => 'generatePhone();')) . '</span>' . '</li>';
								}
							}
							else {
								if (TariffPlans::checkAllowShowPhone())
									echo '<li class="li1"><span id="owner-phone">' . CHtml::link(tc('Show phone'), 'javascript: void(0);', array('onclick' => 'generatePhone();')) . '</span>' . '</li>';
								else
									echo '<li class="li1">'.Yii::t('module_tariffPlans', 'Please <a href="{n}">change the tariff plan</a> to view', Yii::app()->controller->createUrl('/tariffPlans/main/index')).'</li>';
							}
						}
						else {
							echo '<li class="li1"><span id="owner-phone">' . CHtml::link(tc('Show phone'), 'javascript: void(0);', array('onclick' => 'generatePhone();')) . '</span>' . '</li>';
						}
					}
				?>
				<?php
					if (issetModule('messages') && $model->owner_id != Yii::app()->user->id && !Yii::app()->user->isGuest){
						echo '<li class="li2">' . CHtml::link(tt('Send message', 'messages'), Yii::app()->createUrl('/messages/main/read', array('id' => $owner->id, 'apId' => $model->id))) . '</li>';
					}
					elseif (param('use_module_request_property') && $model->owner_id != Yii::app()->user->id){
						echo '<li class="li2">' . CHtml::link(tt('request_for_property'), $model->getUrlSendEmail(), array('class'=>'fancy mgp-open-ajax')) . '</li>';
					}
				?>
				<?php echo '<li class="li3">' .$owner->getLinkToAllListings() . '</li>';?>
			</ul>

			<?php
			if($model->canShowInView('phone')) {
				$hostname = IdnaConvert::checkDecode(str_replace(array('http://', 'www.'), '', Yii::app()->getRequest()->getHostInfo()));
				echo '<div class="flash-notice phone-show-alert" style="display: none;">'.Yii::t('common', 'Please tell the seller that you have found this listing here {n}', '<strong>'.$hostname.'</strong>').'</div>';
			}
			?>

			<?php $additionalInfo = 'additional_info_'.Yii::app()->language;
			if (isset($model->user->$additionalInfo) && !empty($model->user->$additionalInfo)):?>
				<span><?php echo CHtml::encode(truncateText($model->user->$additionalInfo, 20));?></span>
			<?php endif;?>

		</div>
		<div class="clear"></div>
	<?php endif;?>

	<div class="block_item">
		<?php if (param('qrcode_in_listing_view', 1)):?>
			<?php
				echo '<span class="qr-code">';
				$this->widget('application.extensions.qrcode.QRCodeGenerator', array(
					'data' => $model->URL,
					'filename' => 'listing_' . $model->id . '-' . Yii::app()->language . '.png',
					'matrixPointSize' => 3,
					'fileUrl' => Yii::app()->getBaseUrl(true) . '/uploads',
					'color' => array(33, 72, 131),
				));
				echo '</span>';
			?>
			<div class="clear"></div>
		<?php endif;?>

		<?php if (isset($statistics) && is_array($statistics)) : ?>
			<?php echo tt('views_all') . ': ' . $statistics['all'] ?><br/><?php echo tt('views_today') . ': ' . $statistics['today'];?><br/>
			<?php echo tc('Date created') . ': <span class="nobr">' . $model->getDateTimeInFormat('date_created').'</span>'; ?>
		<?php endif; ?>
	</div>
	<div class="clear"></div>
	
	<?php /* if ((isset($lastEntries) && $lastEntries) || (isset($lastArticles) && $lastArticles)):?>
		<div class="b_item_aux__entries">
			<?php if (isset($lastEntries) && $lastEntries):?>
				<div class="entries">
					<h3 class="title highlight-left-right">
						<span><?php echo tt('News', 'entries');?></span>
					</h3>

					<?php
					$total = count($lastEntries);
					$counter = 0;
					?>
					<?php foreach($lastEntries as $entries) : ?>
						<?php $counter++;?>
						<?php $announce = ($entries->getAnnounce()) ? $entries->getAnnounce() : '&nbsp;';?>

						<div class="new">
							<div class="title">
								<?php //echo CHtml::link(truncateText($entries->getTitle(), 4), $entries->getUrl());?>
								<?php echo CHtml::link($entries->getTitle(), $entries->getUrl());?>
							</div>

							<?php
								$class = 'no-image-text';
								if($entries->image){
									$src = $entries->image->getThumb(80, 60);
									if($src){
										$class = 'text';
										echo CHtml::image(Yii::app()->getBaseUrl().'/uploads/entries/'.$src, $entries->getTitle(), array('align' => 'left'));
									}
								}
							?>


							<div class="<?php echo $class; ?>">
								<?php
									if($class == 'text'){
										//echo truncateText($announce, 10);
										echo truncateText($announce, 25);
									} else {
										//echo truncateText($announce, 15);
										echo truncateText($announce, 40);
									}
								?>
							</div>
						</div>

						<?php if($counter != $total):?>
							<div class="dotted_line"></div>
						<?php endif;?>
					<?php endforeach;?>
				</div>
			<?php endif;?>

			<?php if (isset($lastArticles) && $lastArticles):?>
				<div class="entries article">
					<h3 class="title highlight-left-right">
						<span><?php echo tt('FAQ', 'articles');?></span>
					</h3>

					<?php
					$total = count($lastArticles);
					$counter = 0;
					?>
					<?php foreach($lastArticles as $article) : ?>
						<?php $counter++;?>

						<div class="new">
							<div class="title">
								<?php echo CHtml::link($article->getStrByLang('page_title'), $article->getUrl());?>
							</div>

							<div class="text">
								<?php echo truncateText($article->getStrByLang('page_body'), 40); ?>
							</div>
						</div>

						<?php if($counter != $total):?>
							<div class="dotted_line"></div>
						<?php endif;?>
					<?php endforeach;?>
				</div>
			<?php endif;?>
		</div>
	<?php endif; */?>
</div>


<div class="b_item_aux">
	<div class="b_item_aux__tabs">
		<?php
		$firstTabsItems = array();

		$generalContent = $this->renderPartial('//modules/apartments/views/_tab_general', array(
			'data' => $model,
		), true);

		if($generalContent){
			$firstTabsItems[tc('General')] = array(
				'content' => $generalContent,
				'id' => 'tabs1_1',
				'active' => false,
			);
		}

		if(!param('useBootstrap')){
			Yii::app()->clientScript->scriptMap=array(
				'jquery-ui.css' => false,
			);
		}

		if(issetModule('bookingcalendar') && $model->type == Apartment::TYPE_RENT){
			Bookingcalendar::publishAssets();

			$firstTabsItems[tt('The periods of booking apartment', 'bookingcalendar')] = array(
				'content' => $this->renderPartial('//modules/bookingcalendar/views/calendar', array('apartment'=>$model), true),
				'id' => 'tabs1_2',
				'active' => false,
			);
		}

		$additionFields = HFormEditor::getExtendedFields();
		$existValue = HFormEditor::existValueInRows($additionFields, $model);

		if($existValue){
			$firstTabsItems[tc('Additional info')] = array(
				'content' => $this->renderPartial('//modules/apartments/views/_tab_addition', array(
						'data'=>$model,
						'additionFields' =>$additionFields
					), true),
				'id' => 'tab_3',
				'active' => false,
			);
		}

		if(param('enableCommentsForApartments', 1)){
			if(!isset($comment)){
				$comment = null;
			}

			$firstTabsItems[Yii::t('module_comments','Comments').' ('.Comment::countForModel('Apartment', $model->id).')'] = array(
				'content' => $this->renderPartial('//modules/apartments/views/_tab_comments', array(
						'model' => $model,
					), true),
				'id' => 'tabs1_4',
				'active' => false,
			);
		}
		?>

		<?php if (count($firstTabsItems) > 0):?>
			<?php
				// выставляем открытым первый таб
				$total = count($firstTabsItems);
				if ($firstTabsItems > 1) {
					$counter = 0;
					foreach($firstTabsItems as $key => $tab) {
						$counter++;
						if ($counter == 1)
							$firstTabsItems[$key]['active'] = true;
					}
				}
				else {
					$firstTabsItems[0]['active'] = true;
				}
			?>

			<div class="tabs tabs_1 resptabscont" id="firsttabs">
				<ul class="resp-tabs-list">
					<?php foreach($firstTabsItems as $title => $vals):?>
						<li id="<?php echo $vals['id'];?>">
							<a href="javascript: void(0);" <?php echo ($vals['active']) ? 'class="active_tabs"' : '';?>><?php echo $title;?></a>
						</li>
					<?php endforeach;?>
				</ul>
				<div class="clear"></div>

				<div class="resp-tabs-container">
					<?php foreach($firstTabsItems as $title => $vals):?>
						<div class="<?php echo $vals['id'];?> tab_bl_1" <?php echo (!$vals['active']) ? 'style="display: none;"' : '';?>>
							<?php echo $vals['content'];?>
						</div>
					<?php endforeach;?>
				</div>
			</div>
		<?php endif;?>

		<?php
		$secondTabsItems = array();

		if ($model->type != Apartment::TYPE_BUY && $model->type != Apartment::TYPE_RENTING) {
			if($model->lat && $model->lng){
				if(param('useGoogleMap', 1) || param('useYandexMap', 1) || param('useOSMMap', 1)){
					$secondTabsItems[tc('Map')] = array(
						'content' => $this->renderPartial('//modules/apartments/views/_tab_map', array('data' => $model), true),
						'id' => 'tab2_1',
						'active' => false,
						'onClick' => 'reInitMap();',
					);
				}
			}
		}

		if ($model->panorama){
			$secondTabsItems[tc('Panorama')] = array(
				'content' => $this->renderPartial('//modules/apartments/views/_tab_panorama', array( 'data'=>$model), true),
				'id' => 'tab2_2',
				'active' => false,
			);
		}

		if (isset($model->video) && $model->video){
			$secondTabsItems[tc('Videos for listing')] = array(
				'content' => $this->renderPartial('//modules/apartments/views/_tab_video', array( 'data'=>$model), true),
				'id' => 'tab2_3',
				'active' => false,
			);
		}


		?>

		<?php if (count($secondTabsItems) > 0):?>
			<?php
			// выставляем открытым первый таб
			$total = count($secondTabsItems);
			if ($secondTabsItems > 1) {
				$counter = 0;
				foreach($secondTabsItems as $key => $tab) {
					$counter++;
					if ($counter == 1)
						$secondTabsItems[$key]['active'] = true;
				}
			}
			else {
				$secondTabsItems[0]['active'] = true;
			}
			?>

			<div class="tabs tabs_2 resptabscont" id="secondtabs">
				<ul class="resp-tabs-list">
					<?php foreach($secondTabsItems as $title => $vals):?>
						<li id="<?php echo $vals['id'];?>" <?php echo (isset($vals['onClick']) && $vals['onClick']) ? 'onclick="'.$vals['onClick'].'"' : '';?>>
							<a href="javascript: void(0);" <?php echo ($vals['active']) ? 'class="active_tabs"' : '';?> <?php echo (isset($vals['onClick']) && $vals['onClick']) ? 'onclick="'.$vals['onClick'].'"' : '';?>><?php echo $title;?></a>
						</li>
					<?php endforeach;?>
				</ul>
				<div class="clear"></div>

				<div class="resp-tabs-container">
					<?php foreach($secondTabsItems as $title => $vals):?>
						<div class="<?php echo $vals['id'];?> tab_bl_2" <?php echo (!$vals['active']) ? 'style="display: none;"' : '';?>>
							<?php echo $vals['content'];?>
						</div>
					<?php endforeach;?>
				</div>
			</div>
		<?php endif;?>

		<br>
		<div>
			<?php
			$criteria = new CDbCriteria();
			$criteria->compare('t.parent_id', $model->id);

			$this->widget('application.modules.apartments.components.ApartmentsWidget', array(
				'criteria' => $criteria,
				'widgetTitle' => tt('offers', 'apartments'),
				'numBlocks' => 2,
				'usePagination' => 0,
				'showSorter' => 0,
				'showIfNone' => 0,
			));
			?>
		</div>
	</div>
</div>
<div class="clear"></div>

<?php
	if (issetModule('similarads') && param('useSliderSimilarAds') == 1) {
		Yii::import('application.modules.similarads.components.SimilarAdsWidget');
		$ads = new SimilarAdsWidget;
		$ads->viewSimilarAds($model);
		echo '<div class="clear"></div>';
	}
?>
<?php if (param('useSchemaOrgMarkup')) {
	$jsonLDMain = array();
	$jsonLDMain['@context'] = 'http://schema.org';
	$jsonLDMain['@type'] = 'Offer';
	$jsonLDMain['name'] = CHtml::encode($model->getTitle());
	$jsonLDMain['description'] = strip_tags($model->getStrByLang("description"));
	if (!$model->is_price_poa) {
		$jsonLDMain['price'] = $model->getPriceFrom();
		$jsonLDMain['priceCurrency'] = (issetModule('currency')) ? Currency::getCurrentCurrencyModel()->char_code : param('siteCurrency', '$');
	}
	
	if (isset($model->images) && !empty($model->images)) {
		$res = Images::getMainThumb(640, 400, $model->images);
		$img = CHtml::image($res['thumbUrl'], $res['comment']);
		
		$jsonLDMain['image'] = array(
			'@type' => 'ImageObject',
			'url' => $img,
			'height' => 400,
			'width' => 640
		);
	}
	echo '<script type="application/ld+json">'.  CJavaScript::jsonEncode($jsonLDMain).'</script>';
	
	if ($model->lat && $model->lng) {
		$jsonLDCoordinates = array();
		$jsonLDCoordinates['@context'] = 'http://schema.org';
		$jsonLDCoordinates['@type'] = 'GeoCoordinates';
		$jsonLDCoordinates['latitude'] = $model->lat;
		$jsonLDCoordinates['longitude'] = $model->lng;
		
		echo '<script type="application/ld+json">'.  CJavaScript::jsonEncode($jsonLDCoordinates).'</script>';
	}

	
	if($model->rating) {
		$jsonLDRating = array();
		$jsonLDRating['@context'] = 'http://schema.org';
		$jsonLDRating['@type'] = 'AggregateRating';
		$jsonLDRating['ratingValue'] = (int) $model->rating;
		$jsonLDRating['bestRating'] = 10;
		$jsonLDRating['worstRating'] = 1;
		$commentCount = Comment::countForModel('Apartment', $model->id);
		$jsonLDRating['reviewCount'] = ($commentCount && $commentCount > 1) ? $commentCount : 1;
		$jsonLDRating['itemReviewed'] = array(
			'@type' => 'Offer',
			'name' => CHtml::encode($model->getTitle()),
			'url' => $model->getUrl()
		);
		
		echo '<script type="application/ld+json">'.  CJavaScript::jsonEncode($jsonLDRating).'</script>';
	}
	
	if ($model->canShowInView('address')) {
		$jsonLDAddress = array();
		$isShowAddress = true;
		
		# Search engine crawlers is "GUEST"
		if (Yii::app()->user->isGuest && issetModule('tariffPlans') && issetModule('paidservices')) {
			$defaultTariffInfo = TariffPlans::getFullTariffInfoById(TariffPlans::DEFAULT_TARIFF_PLAN_ID);
			if (!$defaultTariffInfo['showAddress'])
				$isShowAddress = false;
		}
		
		if ($isShowAddress) {
			if (issetModule('location') && isset($model->locCountry) && isset($model->locRegion) && isset($model->locCity)) {
				$jsonLDAddress['@context'] = 'http://schema.org';
				$jsonLDAddress['@type'] = 'PostalAddress';
				$jsonLDAddress['addressCountry'] = $model->locCountry->getStrByLang('name');
				$jsonLDAddress['addressRegion'] = $model->locRegion->getStrByLang('name');
				$jsonLDAddress['addressLocality'] = $model->locCity->getStrByLang('name');
				$jsonLDAddress['streetAddress'] = CHtml::encode($model->getStrByLang('address'));
			}
			elseif (isset($model->city)) {
				$jsonLDAddress['@context'] = 'http://schema.org';
				$jsonLDAddress['@type'] = 'PostalAddress';
				$jsonLDAddress['addressLocality'] = CHtml::encode($model->city->name);
				$jsonLDAddress['streetAddress'] = CHtml::encode($model->getStrByLang('address'));
			}
			
			echo '<script type="application/ld+json">'.  CJavaScript::jsonEncode($jsonLDAddress).'</script>';
		}
	}
}