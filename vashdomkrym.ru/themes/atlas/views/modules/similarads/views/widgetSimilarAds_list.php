<?php
if (is_array($ads) && count($ads) > 0) {
	echo '<div class="similar-ads">';
		echo '<span class="viewapartment-subheader">'.tt('Similar ads', 'similarads').'</span>';
		echo '<div id="owl-similar-ads" class="owl-carousel">';
			foreach ($ads as $item) {
				echo '<div class="item">';
					echo '<a href="'.$item->getUrl().'">';
						$res = Images::getMainThumb(150, 100, $item->images);
						echo CHtml::image($res['thumbUrl'], '', array(
							'title' => $item->{'title_'.Yii::app()->language},
							'width' => 150,
							'height' => 100,
						));
					echo '</a>';
					if($item->getStrByLang('title')){
						echo '<div class="similar-descr">'.truncateText(CHtml::encode($item->getStrByLang('title')), 6).'</div>';
					}
					echo '<div class="similar-price">'.tt('Price from', 'apartments').': '.$item->getPrettyPrice().'</div>';
				echo '</div>';
			}
		echo '</div>';

		/*echo '<div class="customNavigation">';
			echo '<a class="btn-similar-prev prev">Previous</a>';
			echo '<a class="btn-similar-next next">Next</a>';
		echo '</div>';*/
	echo '</div>';

	/*$countAll = count($ads) - 4;
	$countDesktop = floor($countAll / 2);
	$countDesktopSmall = floor($countAll / 3);
	$itemsTablet = floor($countAll / 5);

	Yii::app()->clientScript->registerScript('similar-ads-slider', '
		var owl = $("#owl-similar-ads");

		owl.owlCarousel({
			items : '.$countAll.', //items above 1000px browser width
			itemsDesktop : [1000,'.$countDesktop.'], //items between 1000px and 901px
			itemsDesktopSmall : [900,'.$countDesktopSmall.'], // items betweem 900px and 601px
			itemsTablet: [600,'.$itemsTablet.'], // items between 600 and 0;
			itemsMobile : false // itemsMobile disabled - inherit from itemsTablet option
		});

		$(".next").click(function(){
			owl.trigger("owl.next");
		})
		$(".prev").click(function(){
			owl.trigger("owl.prev");
		})
', CClientScript::POS_READY);*/

Yii::app()->clientScript->registerScript('similar-ads-slider', '
		var owl = $("#owl-similar-ads");

		owl.owlCarousel({
			items : 5,
			/* itemsCustom: [[1199, 5], [979, 4], [768, 3], [479, 2], [380, 1]], */
			itemsDesktop : [1199, 5],
			itemsDesktopSmall : [979, 4],
			itemsTablet : [768, 3],
			itemsTabletSmall : [569, 2],
			itemsMobile : [379, 1]
		});
', CClientScript::POS_READY);
}
?>