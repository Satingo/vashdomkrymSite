<div>
	<?php
	echo '<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>';
	echo '<td>';
	?>
	<div class="logo">
		<a title="<?php echo Yii::t('common', 'Go to main page'); ?>" href="<?php echo Yii::app()->controller->createAbsoluteUrl('/'); ?>">
			<div class="logo-img"> <img width="77" height="70" alt="" src="<?php echo Yii::app()->theme->baseUrl; ?>/images/pages/logo-open-ore.png" /></div>
			<div class="logo-text"><?php echo CHtml::encode(Yii::app()->name);?></div>
		</a>
	</div>
	<?php
	echo '</td>';
	echo '<td align="right">';
	$this->widget('application.extensions.qrcode.QRCodeGenerator', array(
		'data' => $model->URL,
		'filename' => 'listing_' . $model->id . '-' . Yii::app()->language . '.png',
		'matrixPointSize' => 3,
		'fileUrl' => Yii::app()->getBaseUrl(true) . '/uploads',
	));
	echo '</td>';
	echo '</tr></table>';
	?>

	<h1><?php echo CHtml::encode($model->getStrByLang('title')); ?></h1>

	<div class="print">
		<div>
			<?php
			if ($model->is_special_offer) {
				?>
				<div class="is_special_offer_block">
					<?php
					echo '<h3>' . Yii::t('common', 'Special offer!') . '</h3>';

					if ($model->is_free_to != '0000-00-00') {
						echo '<div>';
						echo '<strong>' . Yii::t('common', 'Is avaliable') . '</strong>';
						if ($model->is_free_to != '0000-00-00') {
							echo ' ' . Yii::t('common', 'to');
							echo ' ' . Booking::getDate($model->is_free_to);
						}
						echo '</div><br />';
					}
					?>
				</div>
				<?php
			}
			?>

			<div>
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr>
						<td width="320px" valign="top">
							<?php
							$res = Images::getMainThumb(300, 200, $model->images);
							echo CHtml::image($res['thumbUrl'], $model->getStrByLang('title'), array(
								'title' => $model->getStrByLang('title'),
							));
							?>
						</td>
					</tr>
				</table>
			</div>
			<br />

			<?php
			$images = Images::getObjectThumbs(150, 100, $model->images);

			if ($images) {
				$countArr = count($images);
				$i = 1;

				if ($countArr) {
					echo '<div>';
					echo '<table cellpadding="0" cellspacing="0" border="0">';
					foreach ($images as $value) {
						$index = $i % 7;
						$k = $i + 1;
						$indexNext = ($i + 1) % 7;

						if ($index == 0 || $i == 1) {
							echo '<tr>';
						}
						echo '<td>';
							echo '<div style="height: 105px; width: 160px;">';
								echo CHtml::image($value['thumbUrl'], '', array('style' => 'width: 150px; height: 100px;'));
							echo '</div>';
						echo '</td>';
						if ($indexNext == 0 || $countArr == $i) {
							echo '</tr>';
						}

						$i++;
					}
					echo '</table>';
					echo '</div>';
				}
			}
			?>

			<div>
				<?php
				$this->renderPartial('//modules/apartments/views/_tab_general', array(
					'data' => $model,
					'isPrintable' => true,
				));
				?>
			</div>
			<br />

			<?php
			$model->references = HApartment::getFullInformation($model->id, $model->type);
			$additionFields = HFormEditor::getExtendedFields();
			$existValue = HFormEditor::existValueInRows($additionFields, $model);

			if($existValue){
				echo '<div>';
				$this->renderPartial('//modules/apartments/views/_tab_addition', array(
					'data'=>$model,
					'additionFields' =>$additionFields,
					'isPrintable' => true,
				));
				echo '</div><br />';
			}
			?>

			<?php if (isset($staticImageUrl) && $staticImageUrl) :?>
				<div>
					<!--<h2><?php // echo Yii::t('common', 'Map');?></h2>-->
					<img src="<?php echo $staticImageUrl;?>" width="<?php echo $sWidth;?>" height="<?php echo $sHeight;?>" />
				</div>
			<?php endif;?>
		</div>
	</div>
	<div class="footer" style="padding-top: 30px;">
		<p class="slogan">&copy;&nbsp;<?php echo CHtml::encode(Yii::app()->name) . ', ' . date('Y'); ?></p>
	</div>
</div>
