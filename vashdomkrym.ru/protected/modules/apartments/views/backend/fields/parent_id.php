<?php
if(Yii::app()->user->id && $model->objType && $model->objType->parent_id){
    $list = HApartment::getParentList($model->objType->parent_id);

    if($list){
        $list = CMap::mergeArray(array(0 => ''), $list);
        echo $form->labelEx($model, 'parent_id');
        echo $form->dropDownList($model, 'parent_id', $list, array('class' => 'span3'));
        echo $form->error($model, 'parent_id');
    }
}
?>