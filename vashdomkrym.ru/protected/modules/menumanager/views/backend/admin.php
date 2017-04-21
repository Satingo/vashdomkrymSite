<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/jquery.jstree/apple/style.css" />
<?php
Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl.'/js/jquery.jstree.js', CClientScript::POS_HEAD);
Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl.'/js/jquery.dialog-plugin.js', CClientScript::POS_HEAD);
Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl.'/js/jquery.blockUI.js', CClientScript::POS_HEAD);

$this->breadcrumbs=array(
	tt('Manage menu items'),
);

$this->menu = array(
	array(),
);

$this->adminTitle = tt('Manage menu items');
?>

<div class="flash-notice"><?php echo tt('help_menumanager_backend_main_admin'); ?></div>



<?php

if($position == 2) {
    ?>
    <ul class="nav nav-tabs" id="myTab">
        <li><a href="<?php echo Yii::app()->createUrl('/menumanager/backend/main/admin', array('position' => 1)) ?>"><?php echo Yii::t('common', '1 menu') ?></a></li>
        <li class="active"><a href="#menu2"><?php echo Yii::t('common', '2 menu') ?></a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane" id="menu1"></div>
        <div class="tab-pane active" id="menu2"><?php $this->renderPartial('_menu_form', array('position' => 2)) ?></div>
    </div>
<?php
} else {
?>
    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a href="#menu1"><?php echo Yii::t('common', '1 menu') ?></a></li>
        <li><a href="<?php echo Yii::app()->createUrl('/menumanager/backend/main/admin', array('position' => 2)) ?>"><?php echo Yii::t('common', '2 menu') ?></a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="menu1"><?php $this->renderPartial('_menu_form', array('position' => 1)) ?></div>
        <div class="tab-pane" id="menu2">...</div>
    </div>
<?php
			}

?>


<div id="pageList" style="height: 400px; overflow: auto"></div>