<?php
$this->pageTitle .= ' - '. $model->getStrByLang('title');
$page = (isset($_GET) && isset($_GET['page'])) ? ($_GET['page']) : '';

if (isset($model->menuPageOne) && isset($model->menuPageOne->parent) && $model->menuPageOne->parent && $model->menuPageOne->parent->type == Menu::LINK_NEW_INFO) {
	$this->breadcrumbs = array(
		truncateText($model->menuPageOne->parent->getStrByLang('title'), 10) => $model->menuPageOne->parent->getUrl(),
		truncateText($model->getStrByLang('title'), 10),
	);
}
else {
	$this->breadcrumbs=array(
		truncateText($model->getStrByLang('title'), 10),
	);
}?>

<h1><?php echo $model->getStrByLang('title');?></h1>

<?php if ($model->widget && $model->widget_position == InfoPages::POSITION_TOP){
    $this->renderPartial('_view_widget', array('model' => $model));
    echo '<div class="clear"></div>';
}
?>

<?php if(!$page):?>
	<?php echo $model->body; ?>
	<div class="clear"></div>
<?php endif;?>

<?php
	/*if (isset($model->menuPage) && $model->menuPage) {
		foreach ($model->menuPage as $menuPage) {
			$levelItem = $menuPage->getItemLevel();

			if ($levelItem == 2 && isset($menuPage->activeChilds) && $menuPage->activeChilds) {
				echo '<div class="block-childs-links">';
					echo '<ul>';
						foreach($menuPage->activeChilds as $childs) {
							if ($childs->getTitle()) {
								echo '<li>'.CHtml::link($childs->getTitle(), $childs->getUrl()).'</li>';
							}
						}
						echo '</ul>';
					echo '</div>';
				echo '<div class="clear">&nbsp;</div>';
			}
		}
	}*/
?>

<?php
if ($model->widget && $model->widget_position == InfoPages::POSITION_BOTTOM){
    $this->renderPartial('_view_widget', array('model' => $model));
}

if(param('enableCommentsForPages', 1)) { 
	$this->widget('application.modules.comments.components.commentListWidget', array(
		'model' => $model,
		'url' => $model->getUrl(),
		'showRating' => false,
	));
}