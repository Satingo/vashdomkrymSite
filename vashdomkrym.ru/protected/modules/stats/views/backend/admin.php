<?php
$this->breadcrumbs=array(tt('View statistics', 'stats'));
$this->menu = array();

$this->adminTitle = tt('View statistics', 'stats');

echo '<br /><h3>'.Yii::t('module_stats', 'Statistics of last {n} days', 10).'</h3><br />';

if (isset($dataBookingRequests) && count($dataBookingRequests)) {
	$this->widget('ext.Hzl.google.HzlVisualizationChart', array('visualization' => 'ColumnChart',
		'data' => $dataBookingRequests,
		'options' => array(
			'legend' => array('position' => 'top'),
			'colors' => array('#f15a22'),
			'min' => 0,
			'max' => $maxValBookingRequests,
			//'bar' => array('groupWidth' => "60%"),
			//'isStacked' => true,
			//'height' => 450,
			//'width' => 1750,
			//'title' => '',
		),
	));
	echo '<br/>';
}

if (isset($dataListings) && count($dataListings)) {
	$this->widget('ext.Hzl.google.HzlVisualizationChart', array('visualization' => 'ColumnChart',
		'data' => $dataListings,
		'options' => array(
			'legend' => array('position' => 'top'),
			'colors' => array('#4285F4'),
			'min' => 0,
			'max' => $maxValListings,
			//'bar' => array('groupWidth' => "60%"),
			//'isStacked' => true,
			//'height' => 450,
			//'width' => 1750,
			//'title' => '',
		),
	));
	echo '<br/>';
}

if (isset($dataPayments) && count($dataPayments)) {
	$this->widget('ext.Hzl.google.HzlVisualizationChart', array('visualization' => 'ColumnChart',
		'data' => $dataPayments,
		'options' => array(
			'legend' => array('position' => 'top'),
			'colors' => array('#DB4437'),
			'min' => 0,
			'max' => $maxValPayments,
		),
	));
	echo '<br/>';
}

if (isset($dataUsers) && count($dataUsers)) {
	$this->widget('ext.Hzl.google.HzlVisualizationChart', array('visualization' => 'ColumnChart',
		'data' => $dataUsers,
		'options' => array(
			'legend' => array('position' => 'top'),
			'colors' => array('#F4B400'),
			'min' => 0,
			'max' => $maxValUsers,
		),
	));
	echo '<br/>';
}

if (isset($dataComments) && count($dataComments)) {
	$this->widget('ext.Hzl.google.HzlVisualizationChart', array('visualization' => 'ColumnChart',
		'data' => $dataComments,
		'options' => array(
			'legend' => array('position' => 'top'),
			'colors' => array('#109618'),
			'min' => 0,
			'max' => $maxValComments,
		),
	));
	echo '<br/>';
}

if (isset($dataReviews) && count($dataReviews)) {
	$this->widget('ext.Hzl.google.HzlVisualizationChart', array('visualization' => 'ColumnChart',
		'data' => $dataReviews,
		'options' => array(
			'legend' => array('position' => 'top'),
			'colors' => array('#990099'),
			'min' => 0,
			'max' => $maxValReviews,
		),
	));
	echo '<br/>';
}