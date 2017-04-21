<?php
if(is_object($model)) {
	$id = $model->id;
	$title = $model->getStrByLang('title');
	$address = $model->getStrByLang('address');
	$url = $model->getUrl();
	$images = $model->images;
}
elseif(is_array($model)) {
	$id = $model['id'];
	$title = $model['title_'.Yii::app()->language];
	$address = $model['address_'.Yii::app()->language];
	$url = (isset($model['seoUrl']) && $model['seoUrl']) ? Yii::app()->createAbsoluteUrl('/apartments/main/view', array('url' => $model['seoUrl'] . (param('urlExtension') ? '.html' : ''))) : Yii::app()->createAbsoluteUrl('/apartments/main/view', array('id' => $id));
	$images = (isset($model['images'])) ? $model['images'] : null;
}
?>
<div class="gmap-marker">
	<div align="center" class="gmap-marker-adlink">
		<?php
		echo CHtml::link('<strong>'.tt('ID', 'apartments').': '.$id.'</strong>, '.
		CHtml::encode($title), $url);
		?>
	</div>
	<?php
	$res = Images::getMainThumb(150, 100, $images);
	?>
	<div align="center" class="gmap-marker-img">
		<?php
		echo CHtml::image($res['thumbUrl'], $title, array(
			'title' => $title,
		));
		?>
	</div>
	<?php
	?>
	<div align="center" class="gmap-marker-adress">
		<?php echo CHtml::encode($address); ?>
	</div>
</div>