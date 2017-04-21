<?php echo CHtml::form(Yii::app()->createUrl('/seo/main/ajaxSave'), 'post', array('id'=>'seo_url_form')); ?>

<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

<?php echo CHtml::errorSummary($friendlyUrl); ?>

<?php if($this->canUseDirectUrl){ ?>
    <div class="rowold no-mrg">
        <?php
        echo CHtml::activeCheckBox($friendlyUrl, 'direct_url');
        echo '&nbsp;'.CHtml::activeLabelEx($friendlyUrl, 'direct_url', array('class' => 'noblock'));;
        ?>
    </div>
<?php } ?>

<?php
echo CHtml::hiddenField('canUseDirectUrl', $this->canUseDirectUrl ? 1 : 0);

$this->widget('application.modules.lang.components.langFieldWidget', array(
	'model' => $friendlyUrl,
	'field' => 'url',
	'type' => 'string',
	'note' => $friendlyUrl->prefixUrl,
));
?>
<br/>

<?php
$this->widget('application.modules.lang.components.langFieldWidget', array(
	'model' => $friendlyUrl,
	'field' => 'title',
	'type' => 'string',
));
?>
<br/>

<?php
$this->widget('application.modules.lang.components.langFieldWidget', array(
	'model' => $friendlyUrl,
	'field' => 'description',
	'type' => 'text'
));
?>

<div class="clear"></div>
<br>

<?php
$this->widget('application.modules.lang.components.langFieldWidget', array(
	'model' => $friendlyUrl,
	'field' => 'keywords',
	'type' => 'string',
));
?>
<br/>

<?php echo CHtml::hiddenField('SeoFriendlyUrl[model_name]', $friendlyUrl->model_name); ?>
<?php echo CHtml::hiddenField('SeoFriendlyUrl[model_id]', $friendlyUrl->model_id); ?>
<?php echo CHtml::hiddenField('SeoFriendlyUrl[id]', $friendlyUrl->id); ?>

<?php echo CHtml::submitButton(tc('Save'), array('onclick' => 'js:saveSeoUrl(); return false;')); ?>
&nbsp;<?php echo CHtml::button(tc('Close'), array('onclick' => 'js:$("#seo_dialog").dialog("close"); return false;', 'class' => 'button-blue button-gray')); ?>

<?php echo CHtml::endForm(); ?>