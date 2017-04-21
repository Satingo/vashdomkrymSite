<?php
if ($data->canShowInView('square')) {
    HFormEditor::renderViewRow(Yii::t('module_apartments', 'Total square'), $data->square . ' ' . tc('site_square'));
}
?>