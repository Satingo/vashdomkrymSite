<?php
if (param("useUserads")) {
	$this->pageTitle .= ' - '.tc('My listings');
	$this->breadcrumbs = array(
		tc('Control panel') => Yii::app()->createUrl('/usercpanel'),
		tc('My listings'),
	);

	// Управление объявлениями
	$modelAds = new Apartment('search');

	Yii::app()->user->setState('searchUrl', NULL);

	$modelAds->unsetAttributes();  // clear any default values
	if(isset($_GET['Apartment'])){
		$modelAds->attributes = $_GET['Apartment'];
	}
	?>

	<h1 class="title highlight-left-right">
	<span>
		<?php echo tc('My listings'); ?>
	</span>
	</h1>
	<div class="clear"></div><br />

	<?php $this->renderPartial('//modules/userads/views/index', array('model' => $modelAds->onlyAuthOwner()->notDeleted()));
}
else {
	$this->pageTitle .= ' - '.tc('My data');
	$this->breadcrumbs = array(
		tc('Control panel') => Yii::app()->createUrl('/usercpanel'),
		tc('My data'),
	);
	?>
	<div class="clear"></div>

	<?php $this->renderPartial('//modules/usercpanel/views/data', array('model' => $model));
}
?>