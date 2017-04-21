<div class="map_full_with_homepage">
	<?php $withCluster = ((count($selectedIds)) > 15) ? true : false;?>
	<?php $this->widget(
		'application.modules.viewallonmap.components.ViewallonmapWidget', 
			array(
				'selectedIds' => $selectedIds, 
				'filterOn' => false, 
				'withCluster' => $withCluster,
				'scrollWheel' => false,
				'draggable' => true,
			)
		); 
	?>
</div>