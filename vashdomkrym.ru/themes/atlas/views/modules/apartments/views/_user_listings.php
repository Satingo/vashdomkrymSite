<?php
$this->pageTitle .= ' - '.tt('all_member_listings', 'apartments') . ' '.$username;
$this->breadcrumbs=array(
	tt('all_member_listings', 'apartments').' '.$username,
);
?>

<?php $this->widget('application.modules.apartments.components.ApartmentsWidget', array(
	'criteria' => $criteria,
	'count' => $apCount,
	'widgetTitle' => tt('all_member_listings', 'apartments'). ' '.$username,
)); ?>