	<div class="apartment-description">
		<?php
			if($data->is_special_offer){
				?>
				<div class="big-special-offer">
					<?php
					echo '<h4>'.Yii::t('common', 'Special offer!').'</h4>';

					if($data->is_free_to != '0000-00-00'){
						echo '<p>';
						echo Yii::t('common','Is avaliable');
						if($data->is_free_to != '0000-00-00'){
							echo ' '.Yii::t('module_apartments', 'to');
							echo ' '.Booking::getDate($data->is_free_to);
						}
						echo '</p>';
					}
					?>
				</div>
				<?php
			}
		?>
		
		<?php if (issetModule('paidservices') && param('useUserads')):?>
			<?php 
				$wantTypes = HApartment::getI18nTypesArray();
				$typeName = (isset($wantTypes[$data->type]) && isset($wantTypes[$data->type]['current'])) ? mb_strtolower($wantTypes[$data->type]['current'], 'UTF-8') : '';				
			?>
			<?php if ($typeName) :?>
				<div class="promotion-paidservices-in-apartment">
					<div class="paidservices-promotion-title"><?php echo tt('Is it your listing?', 'apartments');?></div>
					<div class="paidservices-promotion-title-promotion-title"><?php echo tt('Would you like to', 'apartments');?>&nbsp;<?php echo $typeName;?>&nbsp;<?php echo tt('quicker?', 'apartments');?></div>
					<div class="paidservices-promotion-description">
						<i class="i-paidservices-promotion"></i>
						<span>
							<?php echo tt('Try to', 'apartments');?>&nbsp;
							<?php echo CHtml::link(tt('apply paid services', 'apartments'), Yii::app()->createUrl('/userads/main/update', array('id' => $data->id, 'show' => 'paidservices')), array('target'=>'_blank'));?>
						</span>
					</div>
					<div class="clear"></div>
				</div>
			<?php endif;?>
		<?php endif;?>

        <?php
        if(param('useShowUserInfo')){
            echo '<div class="apartment-user-info">';
            $this->widget('zii.widgets.jui.CJuiTabs', array(
                'tabs' => array(tc('Listing provided by') => $this->renderPartial('//modules/apartments/views/_user_info', array('data' => $data), true)),
                'htmlOptions' => array('class' => 'info-tabs'),
            ));
            echo '</div>';
        }
        ?>
		<div class="viewapartment-left">
			<div class="viewapartment-main-photo">
				<div class="apartment_type"><?php echo HApartment::getNameByType($data->type); ?></div>
				<?php
					$img = null;
					$res = Images::getMainThumb(300, 200, $data->images);
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
			</div>

			<div class="viewapartment-description-top">
				<?php
				if ($data->deleted)
					echo '<div class="deleted">' .  tt('Listing is deleted', 'apartments') . '</div>';
				?>
				<div>
					<strong>
					<?php
						echo utf8_ucfirst($data->objType->name);

						if ($data->num_of_rooms){
							echo ',&nbsp;';
							echo Yii::t('module_apartments',
								'{n} bedroom|{n} bedrooms|{n} bedrooms', array($data->num_of_rooms));
						}
						if (issetModule('location')) {
							if($data->locCountry || $data->locRegion || $data->locCity)
								echo "<br>";

							if($data->locCountry){
								echo $data->locCountry->getStrByLang('name');
							}
							if($data->locRegion){
								if($data->locCountry)
									echo ',&nbsp;';
								echo $data->locRegion->getStrByLang('name');
							}
							if($data->locCity){
								if($data->locCountry || $data->locRegion)
									echo ',&nbsp;';
								echo $data->locCity->getStrByLang('name');
							}
						} else {
							if(isset($data->city) && isset($data->city->name)){
								echo ',&nbsp;';
								echo $data->city->name;
							}
						}

					?>
					</strong>
				</div>

				<p class="cost padding-bottom10">
					<?php
					if($data->canShowInView('price')) {
						if ($data->is_price_poa)
							echo tt('is_price_poa', 'apartments');
						else
							echo tt('Price from') . ': ' . $data->getPrettyPrice();
					}
					?>
				</p>
				<div class="overflow-auto">
					<?php
						if(($data->owner_id != Yii::app()->user->getId()) && $data->type == Apartment::TYPE_RENT && !$data->deleted){
							echo '<div>'.CHtml::link(tt('Booking'), array('/booking/main/bookingform', 'id' => $data->id), array('class' => 'apt_btn fancy mgp-open-ajax')).'</div><div class="clear"></div>';
						}


						if(issetModule('apartmentsComplain')){
							if(($data->owner_id != Yii::app()->user->getId())){ ?>
								<div>
									<?php echo CHtml::link(tt('do_complain', 'apartmentsComplain'), $this->createUrl('/apartmentsComplain/main/complain', array('id' => $data->id)), array('class' => 'fancy mgp-open-ajax')); ?>
								</div>
								<?php
							}
						}
					?>
					<?php if (issetModule('comparisonList')):?>
						<div class="clear"></div>
						<?php
						$inComparisonList = false;
						if (in_array($data->id, Yii::app()->controller->apInComparison))
							$inComparisonList = true;
						?>
						<div class="compare-check-control view-apartment" id="compare_check_control_<?php echo $data->id; ?>">
							<?php
							$checkedControl = '';

							if ($inComparisonList)
								$checkedControl = ' checked = checked ';
							?>
							<input type="checkbox" name="compare<?php echo $data->id; ?>" class="compare-check compare-float-left" id="compare_check<?php echo $data->id; ?>" <?php echo $checkedControl;?>>

							<a href="<?php echo ($inComparisonList) ? Yii::app()->createUrl('comparisonList/main/index') : 'javascript:void(0);';?>" data-rel-compare="<?php echo ($inComparisonList) ? 'true' : 'false';?>" id="compare_label<?php echo $data->id; ?>" class="compare-label">
								<?php echo ($inComparisonList) ? tt('In the comparison list', 'comparisonList') : tt('Add to a comparison list ', 'comparisonList');?>
							</a>
						</div>
					<?php endif;?>
				</div>
			</div>

			<?php
				if ($data->images) {
					$this->widget('application.modules.images.components.ImagesWidget', array(
						'images' => $data->images,
						'objectId' => $data->id,
					));
				}
			?>

		</div>

	</div>


	<div class="clear"></div>

	<div class="viewapartment-description">
		<?php
            $data->references = HApartment::getFullInformation($data->id, $data->type);
			$generalContent = $this->renderPartial('//modules/apartments/views/_tab_general', array(
				'data'=>$data,
			), true);

			if($generalContent){
				$items[tc('General')] = array(
					'content' => $generalContent,
					'id' => 'tab_1',
				);
			}

			if(!param('useBootstrap')){
				Yii::app()->clientScript->scriptMap=array(
					'jquery-ui.css'=>false,
				);
			}

			if(issetModule('bookingcalendar') && $data->type == Apartment::TYPE_RENT){
				Bookingcalendar::publishAssets();

				$items[tt('The periods of booking apartment', 'bookingcalendar')] = array(
					'content' => $this->renderPartial('//modules/bookingcalendar/views/calendar', array(
						'apartment'=>$data,
					), true),
					'id' => 'tab_2',
				);
			}

            $additionFields = HFormEditor::getExtendedFields();
            $existValue = HFormEditor::existValueInRows($additionFields, $data);

            if($existValue){
                $items[tc('Additional info')] = array(
                    'content' => $this->renderPartial('//modules/apartments/views/_tab_addition', array(
                        'data'=>$data,
                        'additionFields' =>$additionFields
                    ), true),
                    'id' => 'tab_3',
                );
            }

			if ($data->panorama){
				$items[tc('Panorama')] = array(
					'content' => $this->renderPartial('//modules/apartments/views/_tab_panorama', array(
						'data'=>$data,
					), true),
					'id' => 'tab_7',
				);
			}

			if (isset($data->video) && $data->video){
				$items[tc('Videos for listing')] = array(
					'content' => $this->renderPartial('//modules/apartments/views/_tab_video', array(
						'data'=>$data,
					), true),
					'id' => 'tab_4',
				);
			}


			/*if(!Yii::app()->user->checkAccess('backend_access') && (Yii::app()->user->hasFlash('newComment') || $comment->getErrors())){
				Yii::app()->clientScript->registerScript('comments','
				setTimeout(function(){
					$("a[href=#tab_5]").click();
				}, 0);
				scrollto("comments");
			',CClientScript::POS_READY);
			}*/


			if(param('enableCommentsForApartments', 1)){
				if(!isset($comment)){
					$comment = null;
				}

				$items[Yii::t('module_comments','Comments').' ('.Comment::countForModel('Apartment', $data->id).')'] = array(
					'content' => $this->renderPartial('//modules/apartments/views/_tab_comments', array(
						'model' => $data,
					), true),
					'id' => 'tab_5',
				);
			}

			if ($data->type != Apartment::TYPE_BUY && $data->type != Apartment::TYPE_RENTING) {
				if($data->lat && $data->lng){
					if(param('useGoogleMap', 1) || param('useYandexMap', 1) || param('useOSMMap', 1)){
						$items[tc('Map')] = array(
							'content' => $this->renderPartial('//modules/apartments/views/_tab_map', array(
								'data' => $data,
							), true),
							'id' => 'tab_6',
						);
					}
				}
			}

			$this->widget('zii.widgets.jui.CJuiTabs', array(
				'tabs' => $items,
				'htmlOptions' => array('class' => 'info-tabs'),
				'headerTemplate' => '<li><a href="{url}" title="{title}" onclick="reInitMap(this);">{title}</a></li>',
				'options' => array(
				),
			));
		?>
	</div>

	<div class="clear">&nbsp;</div>
	<?php
		if(!Yii::app()->user->checkAccess('backend_access')) {
			if (issetModule('similarads') && param('useSliderSimilarAds') == 1) {
				Yii::import('application.modules.similarads.components.SimilarAdsWidget');
				$ads = new SimilarAdsWidget;
				$ads->viewSimilarAds($data);
			}
		}

		Yii::app()->clientScript->registerScript('reInitMap', '
			var useYandexMap = '.param('useYandexMap', 1).';
			var useGoogleMap = '.param('useGoogleMap', 1).';
			var useOSMap = '.param('useOSMMap', 1).';

			function reInitMap(elem) {
				if($(elem).attr("href") == "#tab_6"){
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
			}
		',
		CClientScript::POS_END);
	?>
<br />
