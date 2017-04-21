<p>
    <strong><?php echo tt('Settings for the field', 'formdesigner'); ?></strong>: <?php echo Apartment::model()->getAttributeLabel($model->field); ?>
</p>

<?php
/** @var CustomForm $form */
$form=$this->beginWidget('CustomForm', array(
    'id'=>'form-designer-filter'
));

echo $form->errorSummary($model);

echo CHtml::hiddenField('id', $model->id);

echo CHtml::hiddenField('FormDesigner[save]', $model->id);

    echo $form->dropDownListRow($model, 'view_in', FormDesigner::getViewInList(), array('class' => 'span3'));
    echo '<br>';

if($model->not_hide == 0){
    echo $form->dropDownListRow($model, 'visible', FormDesigner::getVisibleList(), array('class' => 'span3'));
    echo '<br>';

    echo $form->checkBoxListRow($model, 'apTypesArray', HApartment::getTypesArray());
    echo $form->checkBoxListRow($model, 'objTypesArray', ApartmentObjType::getList());
    echo '<br>';
}

$withoutTip = FormDesigner::getFieldsWithoutTip();

if(!in_array($model->field, $withoutTip)){
    $this->widget('application.modules.lang.components.langFieldWidget', array(
        'model' => $model,
        'field' => 'tip',
        'type' => 'string',
    ));
}

echo '<div class="clear"></div>';
echo '<br>';

$this->widget('bootstrap.widgets.TbButton',
    array('buttonType'=>'submit',
        'type'=>'primary',
        'icon'=>'ok white',
        'label'=> $model->isNewRecord ? tc('Add') : tc('Save'),
    )
);

$this->endWidget();
?>