<?php
$apartments = HApartment::findAllWithCache($criteria);

$ids = array();
foreach($apartments as $apartment){
	$ids[] = $apartment->id;
}
?>

<div class="apartment_list_map" id="list_map_block" data-exist="1">
	<?php $this->widget('application.modules.viewallonmap.components.ViewallonmapWidget', array('selectedIds' => $ids, 'filterOn' => false, 'withCluster' => false)); ?>
</div>

<?php $this->render('widgetApartments_list_item', array('apartments' => $apartments)); ?>