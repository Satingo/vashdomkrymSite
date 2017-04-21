<h1><?php echo tt('Update apartment', 'apartments'); ?></h1>

<?php
$this->pageTitle .= ' - '.tt('Update apartment', 'apartments');
$this->breadcrumbs = array(
	Yii::t('common', 'Control panel') => array('/usercpanel/main/index'),
	tt('Update apartment', 'apartments')
);

if(!Yii::app()->user->isGuest){
    $menuItems = array(
    		array('label' => tt('Manage apartments', 'apartments'), 'url'=>array('/usercpanel/main/index')),
    		array('label' => tt('Add apartment', 'apartments'), 'url'=>array('create')),
    		array(
    			'label' => tt('Delete apartment', 'apartments'),
    			'url'=>'#',
    			'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>tc('Are you sure you want to delete this item?'))),
			array('label' => tt('Set the owner of the listing', 'apartments'), 'url'=>array('/userads/main/choosenewowner', 'id' => $model->id)),
    );
} else {
    $menuItems = array();
}

$this->widget('CustomMenu', array(
	'items' => $menuItems
));

if(issetModule('paidservices')){
	echo '<div class="current_paid" id="current_paid_first">';
	echo '<h3>'.tc('Paid services').'</h3>';
	echo HApartment::getPaidHtml($model, true);
	echo '</div>';
}

if(isset($show) && $show){
	if ($show == 'paidservices')
		$show = 'current_paid_first';

	Yii::app()->clientScript->registerScript('scroll-to','
			scrollto("'.CHtml::encode($show).'");
		',CClientScript::POS_READY
	);
}

//$model->metroStations = $model->getMetroStations();
$this->renderPartial('_form',array(
	'model'=>$model,
	'supportvideoext' => $supportvideoext,
	'supportvideomaxsize' => $supportvideomaxsize,
	'seasonalPricesModel' => $seasonalPricesModel,
));

