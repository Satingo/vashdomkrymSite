<?php
foreach ($images as $item) {
	if ($item->img) {
		$tmp = $item->getTitle();

		$title = (isset($tmp) && $tmp) ? CHtml::encode($tmp) : '';
		$link = isset($item->url) ? CHtml::encode($item->url) : '';

		$title = str_replace(array("\r", "\n"), '', $title);

		if ($link) { echo '<a href="'.$item->url.'">'; }
		$img = CHtml::image(
			Yii::app()->request->baseUrl."/". Slider::model()->sliderPath."/".$item->getThumb(500, 280),
			$title,
			array(
				'width' => 500,
				'height' => 310,
				'title' => $title,
			)
		);
		echo $img;
		if ($link) { echo '</a>'; }
	}
}
unset($tmp);
?>