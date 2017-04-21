<?php $cats = EntriesCategory::getAllCategories(); ?>
<?php if($cats){ ?>
<div id="entries_filter" style="display: none;" class="well">
	<h4><?php echo tt('Articles filter', 'entries') ?></h4>

	<div class="rowold">
		<div class=""><?php echo tt('Category', 'entries') ?>:</div>
		<?php echo CHtml::dropDownList('filterEntries[category_id]', $this->getFilterEntriesValue('category_id'), $cats); ?>
	</div>
</div>
<?php } ?>