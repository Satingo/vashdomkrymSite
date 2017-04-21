<?php
if ($data->canShowInView('land_square')) {
    HFormEditor::renderViewRow(Yii::t('module_apartments', 'Land square'), $data->land_square . ' ' . tc('site_land_square'));
}
?>