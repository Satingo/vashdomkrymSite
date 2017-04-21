<?php
$count = $this->imgCount + 1;
foreach ($images as $item) {
	if ($item->img) {
		$tmp = $item->getTitle();

		$title = (isset($tmp) && $tmp) ? CHtml::encode($tmp) : '';
		$link = isset($item->url) ? CHtml::encode($item->url) : '';

		$title = str_replace(array("\r", "\n"), '', $title);

		$img = CHtml::image(
			Yii::app()->request->baseUrl."/". Slider::model()->sliderPath."/".$item->getThumb(663, 380),
			'',
			array(
				'width' => 663,
				'height' => 380,
			)
		);


		echo '<div id="slide'.$count.'" class="slide">';
		if ($link)
			echo '<a href="'.$link.'" target="_blank">';
			echo $img;
			if ($title) {
				echo '<div class="slideContent">';
					echo '<div class="title_block2">';
						echo $title;
					echo '</div>';
				echo '</div>';
			}
		if ($link)
			echo '</a>';
		echo '</div>';


	}
$count++;
}
unset($tmp);
?>