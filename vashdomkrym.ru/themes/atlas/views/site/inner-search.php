<?php
$compact = param("useCompactInnerSearchForm", false);
$showHideFilter = (isset($showHideFilter)) ? $showHideFilter : true;

if (!isset($this->aData['searchOnMap'])) {
	?>
<form id="search-form" action="<?php echo Yii::app()->controller->createUrl('/quicksearch/main/mainsearch'); ?>"  method="get">
	<?php
	if (isset($this->userListingId) && $this->userListingId) {
		echo CHtml::hiddenField('userListingId', $this->userListingId);
	}

	$loc = (issetModule('location')) ? "-loc" : "";
	?>
	<div class="filtr<?php if($compact) echo ' collapsed'?>">
		<div id="search_form" class="inner_form">
			<?php $this->renderPartial('//site/_search_form', array(
				'isInner' => 1,
				'compact' => $compact,
			));
			?>
		</div>
		<!--<div class="clear"></div>-->

        <div class="inner_search_button_row">
            <a href="javascript: void(0);" onclick="doSearchAction();" id="btnleft" class="link_blue inner_search_button"><?php echo Yii::t('common', 'Search'); ?></a>
        </div>

        <div class="clear"></div>

		<?php if ($showHideFilter):?>
			<a href="javascript: void(0);">
				<div class="hide_filtr"></div>
			</a>
		<?php endif;?>
	</div>
</form>

	<?php
	$content = $this->renderPartial('//site/_search_js', array(
			'isInner' => 1
		),
		true,
		false
	);
	//Yii::app()->clientScript->registerScript('search-params-inner-search', $content, CClientScript::POS_HEAD, array(), true);
	Yii::app()->clientScript->registerScript('search-params-inner-search', $content, CClientScript::POS_END);

} else {
	//echo '<br />';
}

Yii::app()->clientScript->registerScript('doSearchActionInner', '
		function doSearchAction() {
			if($("#search_term_text").length){
				var term = $(".search-term input#search_term_text").val();
				if (term.length < ' . Yii::app()->controller->minLengthSearch . ' || term == "' . tc("Search by description or address") . '") {
					$(".search-term input#search_term_text").attr("disabled", "disabled");
				}
			}

			$("#search-form").submit();
		}
', CClientScript::POS_END);
?>