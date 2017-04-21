<?php
$this->pageTitle .= ' - '.  tc('News product');
$this->breadcrumbs=array(
	tc('News product'),
);
$this->menu = array(
	array(),
);
$this->adminTitle = tc('News product');

if ($items) {
	if($pages){
		echo '<div class="clear">&nbsp;</div>';
			echo '<div class="pagination">';
			$this->widget('itemPaginator',array('pages' => $pages, 'header' => '', 'showHidden' => true));
			echo '</div>';
		echo '<div class="clear">&nbsp;</div>';
	}

	foreach ($items as $item){
		echo '<div class="news-product-items">';
			echo '<p><font class="date">'.$item->pubDate.'</font></p>';
			//echo CHtml::link($item->title, $item->getUrl(), array('class'=>'title'));
			echo '<p><font class="title">'.$item->title.'</font></p>';
			echo '<p class="desc">';
			echo $item->description;
			echo '</p>';
			echo '<p>';
			echo CHtml::link(EntriesModule::t('Read more &raquo;'), $item->link);
			echo '</p>';
		echo '</div>';
	}
}

if(!$items){
	echo EntriesModule::t('News list is empty.');
}

if($pages){
	echo '<div class="clear">&nbsp;</div>';
		echo '<div class="pagination">';
			$this->widget('itemPaginator',array('pages' => $pages, 'header' => '', 'showHidden' => true));
		echo '</div>';
	echo '<div class="clear">&nbsp;</div>';
}
?>