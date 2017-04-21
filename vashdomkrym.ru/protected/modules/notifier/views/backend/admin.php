<?php
$this->pageTitle=Yii::app()->name . ' - ' . tc('Mail editor');
$this->adminTitle = tc('Mail editor');
?>

<?php $this->widget('CustomGridView', array(
    'id'=>'mail-editor-grid',
    'dataProvider'=>$model->active()->search(),
    'filter'=>$model,
    'afterAjaxUpdate' => 'function(){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove();}',
    'columns'=>array(
        array(
            'header' => tc('Subject'),
            'value' => '$data->subject',
        ),
        array(
            'header' => tc('Event (name in code)'),
            'value' => '$data->event',
        ),

        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>'{update}',
        ),
    ),
));