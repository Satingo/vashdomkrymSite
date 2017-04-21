<?php Yii::app()->clientScript->registerCoreScript( 'jquery.ui' ); ?>
<?php Yii::app()->clientScript->registerCssFile( Yii::app()->theme->baseUrl.'/css/ui/jquery-ui-1.8.16.custom.css', 'screen' ); ?>

<div><h2><?php echo tFile::getT('module_install', 'Installation in 1 step'); ?></h2></div>

<div class="form">
	<?php
		foreach(Yii::app()->user->getFlashes() as $key => $message) {
			if ($key=='error' || $key == 'success' || $key == 'notice'){
				echo "<div class='flash-{$key}'>{$message}</div>";
			}
		}
	?>

	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'install-form',
		//'enableAjaxValidation'=>true,
	)); ?>

		<p class="note"><?php echo tFile::getT('module_install', 'Fields with <span class="required">*</span> are required.'); ?></p>

		<?php echo $form->errorSummary($model); ?>

		<div class="install_color">
			<div class="span-12">
				<fieldset>
					<legend><?php echo tFile::getT('module_install', 'Database settings'); ?></legend>
					<div class="row">
						<?php echo $form->labelEx($model,'dbUser'); ?>
						<?php echo $form->textField($model,'dbUser'); ?>
						<?php echo $form->error($model,'dbUser'); ?>
					</div>

					<div class="row">
						<?php echo $form->labelEx($model,'dbPass'); ?>
						<?php echo $form->textField($model,'dbPass'); ?>
						<?php echo $form->error($model,'dbPass'); ?>
					</div>

					<div class="row">
						<?php echo $form->labelEx($model,'dbHost'); ?>
						<?php echo $form->textField($model,'dbHost'); ?>
						<?php echo $form->error($model,'dbHost'); ?>
					</div>

					<div class="row">
						<?php echo $form->labelEx($model,'dbPort'); ?>
						<?php echo $form->textField($model,'dbPort'); ?>
						<?php echo $form->error($model,'dbPort'); ?>
					</div>

					<div class="row">
						<?php echo $form->labelEx($model,'dbName'); ?>
						<?php echo $form->textField($model,'dbName'); ?>
						<?php echo $form->error($model,'dbName'); ?>
					</div>

					<div class="row">
						<?php echo $form->labelEx($model,'dbPrefix'); ?>
						<?php echo $form->textField($model,'dbPrefix'); ?>
						<?php echo $form->error($model,'dbPrefix'); ?>
					</div>
				</fieldset>

				<?php if(!isFree()) : ?>
					<fieldset>
						<legend><?php echo tFile::getT('module_install', 'Other settings'); ?></legend>
						<div class="row">
							<?php echo $form->labelEx($model,'language'); ?>
							<?php echo $form->dropDownList($model, 'language', $model->getLangs()); ?>
							<?php echo $form->error($model,'language'); ?>
						</div>
					</fieldset>
				<?php endif;?>
			</div>

			<div class="span-12">
				<fieldset>
					<legend><?php echo tFile::getT('module_install', 'Administrator settings'); ?></legend>
					<div class="row">
						<?php echo $form->labelEx($model,'adminEmail'); ?>
						<?php echo $form->textField($model,'adminEmail'); ?>
						<?php echo $form->error($model,'adminEmail'); ?>
					</div>

					<div class="row">
						<?php echo $form->labelEx($model,'adminName'); ?>
						<?php echo '<div style="padding: 0px; line-height: 15px;"><small>'.tFile::getT('module_install', 'The name will be used when sending emails from the site.').'</small></div>';?>
						<?php echo $form->textField($model,'adminName'); ?>
						<?php echo $form->error($model,'adminName'); ?>
					</div>

					<div class="row">
						<?php echo $form->labelEx($model,'adminPass'); ?>
						<?php echo $form->textField($model,'adminPass'); ?>
						<?php echo $form->error($model,'adminPass'); ?>
					</div>
				</fieldset>

				<fieldset>
					<legend><?php echo tFile::getT('module_install', 'SEO settings'); ?></legend>
					<div class="row">
						<?php echo $form->labelEx($model,'siteName'); ?>
						<?php echo '<div style="padding: 0px; line-height: 15px;"><small>'.tFile::getT('module_install', 'Site name help').'</small></div>';?>
						<?php echo $form->textField($model,'siteName'); ?>
						<?php echo $form->error($model,'siteName'); ?>
					</div>

					<div class="row">
						<?php echo $form->labelEx($model,'siteKeywords'); ?>
						<?php echo $form->textField($model,'siteKeywords'); ?>
						<?php echo $form->error($model,'siteKeywords'); ?>
					</div>

					<div class="row site-description">
						<?php echo $form->labelEx($model,'siteDescription'); ?>
						<?php echo $form->textArea($model,'siteDescription'); ?>
						<?php echo $form->error($model,'siteDescription'); ?>
					</div>
				</fieldset>
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="row license-block">
			<?php echo CHtml::activeCheckBox($model,'agreeLicense'); ?>
			<?php echo CHtml::activeLabel($model,'agreeLicense', array('style'=>'display:inline;')); ?>
			<?php echo $form->error($model,'agreeLicense'); ?>
		</div>

		<div class="row buttons">
			<?php
				echo CHtml::submitButton(tFile::getT('module_install', 'Install'), array('style' => 'width: 100px; padding: 5px; font-size: 1.1em; font-weight: bold;'));
			?>
		</div>

	<?php $this->endWidget(); ?>

	<div class="hidden">
		<div id="licensewidget" class="form min-fancy-width white-popup-block">
			<?php $this->renderPartial('license');?>
		</div>
	</div>
</div>