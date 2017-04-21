<?php
$this->pageTitle .= ' - '.$categoryTitle;
if (isset($tagName) && $tagName) {
	$this->pageTitle .= ' - '.tt('by tag', 'entries').' '.$tagName;
	$this->seoTitle .= ' - '.tt('by tag', 'entries').' '.$tagName;
}

$this->breadcrumbs=array(
    $categoryTitle,
);
?>

<h1>
	<?php echo $categoryTitle; ?>
	<?php if (isset($tagName) && $tagName):?>
		<?php echo ' '.tt('by tag', 'entries').' '.$tagName;?>
	<?php endif; ?>
</h1>

<?php
$this->renderPartial('widgetEntries_list', array(
	'entries' => $items,
	'pages' => $pages,
));