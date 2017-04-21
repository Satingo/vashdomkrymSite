<?php
if ($model->canShowInForm('description_near')) {
    $this->widget('application.modules.lang.components.langFieldWidget', array(
        'model' => $model,
        'field' => 'description_near',
        'type' => 'text'
    ));
    echo '<div class="clear">&nbsp;</div>';
}
?>