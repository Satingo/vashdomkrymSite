<?php
$this->breadcrumbs=array(
	Yii::t('common', 'Apartment search') => array('/quicksearch/main/mainsearch'),
	truncateText(CHtml::encode($apartment->getStrByLang('title')), 8) => $apartment->getUrl(),
	tt('Booking apartment'),
);

$this->pageTitle = tt('Booking apartment').' - '.CHtml::encode($apartment->getStrByLang('title'));
?>

<div class="form min-fancy-width <?php echo (isset($isFancy) && $isFancy) ? 'max-fancy-width white-popup-block' : ''; ?>">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'action' => Yii::app()->controller->createUrl('/booking/main/bookingform', array('id' => $apartment->id)),
		'id'=>$this->modelName.'-form',
		'enableAjaxValidation'=>false,
	)); ?>
		<h2 class="title highlight-left-right">
			<span><?php echo tt('Booking apartment').': '.CHtml::encode($apartment->getStrByLang('title')); ?></span>
		</h2>
		<div class="clear"></div><br />

		<?php
			/*if(Yii::app()->user->isGuest && param('useUserRegistration')){
				echo Yii::t('module_booking', 'Already have site account? Please <a title="Login" href="{n}">login</a>',
					Yii::app()->controller->createUrl('/site/login')).'<br /><br />';
			}*/
		?>
		<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>
		<?php echo $form->errorSummary($model); ?>

		<?php
			$this->renderPartial('_form', array(
				'model' => $model,
				'form' => $form,
				'isGuest' => Yii::app()->user->isGuest,
				'isSimpleForm' => false,
				'apartment' => $apartment,
				'user' => $user,
			));
		?>

		<div class="row buttons">
			<?php echo CHtml::submitButton(Yii::t('common', 'Send'), array('class' => 'button-blue')); ?>
		</div>
	<?php $this->endWidget(); ?>
</div>