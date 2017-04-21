<div class="form">

<?php $form=$this->beginWidget('CustomForm', array(
	'id'=>'Service-form',
	'enableClientValidation'=>false,
)); ?>
	<p class="note">
		<?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?>
	</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="rowold padding-bottom10">
		<div class="rowold">
			<?php echo $form->checkboxRow($model,'is_offline'); ?>
			<?php echo $form->error($model,'is_offline'); ?>
		</div>
	</div>

	<div class="rowold">
		<?php echo $form->labelEx($model,'allow_ip'); ?>
		<?php echo '<div class="padding-bottom10"><sub>'.tt("Through_comma").'</sub></div>';?>
		<?php echo $form->textField($model,'allow_ip', array('size' => 100)); ?>
		<?php echo $form->error($model,'allow_ip'); ?>
	</div>

    <div class="rowold">
		<?php echo $form->labelEx($model,'page'); ?>
		<?php
			$filebrowserImageUploadUrl = '';
            $allowedContent = false;

            if (Yii::app()->user->checkAccess('upload_from_wysiwyg')) { // if admin - enable upload image
                $filebrowserImageUploadUrl = Yii::app()->createAbsoluteUrl('/site/uploadimage', array('type' => 'imageUpload', Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken));
                $allowedContent = true;
            }

			$this->widget('application.extensions.editMe.widgets.ExtEditMe',array(
				'model'=>$model,
				'attribute'=>'page',
				'toolbar'=>array(
					array('Source', '-', 'Bold','Italic','Underline','Strike'),
					array('Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'),
					array('NumberedList','BulletedList','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'),
					array('Styles','Format','Font','FontSize','TextColor','BGColor'),
					array('Image', 'Link', 'Unlink', 'SpecialChar'),
				),
                'allowedContent' => $allowedContent,
				'filebrowserImageUploadUrl' => $filebrowserImageUploadUrl,
				'htmlOptions' => array('id' => 'page')
			));
		?>
		<?php echo $form->error($model,'page'); ?>
	</div>

	<div class="rowold buttons">
		<?php $this->widget('bootstrap.widgets.TbButton',
					array('buttonType'=>'submit',
						'type'=>'primary',
						'icon'=>'ok white',
						'label'=> tc('Save'),
					)
		); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->