<?php
$this->pageTitle=Yii::app()->name . ' - '.tc('Error');
$this->breadcrumbs=array(
	tc('Error'),
);
?>

<h2 class="title highlight-left-right">
	<span><?php echo tc('Error');?> <?php echo CHtml::encode($code); ?></span>
</h2>
<div class="clear"></div><br />

<div class="flash-error">
	<?php echo CHtml::encode($message); ?>
</div>