<?php
if ($data->canShowInView('floor_all')) {
    if ($data->floor || $data->floor_total) {
        if ($data->floor && $data->floor_total) {
            $label = tc('Floor');
            $value = Yii::t('module_apartments', '{n} floor of {total} total', array($data->floor, '{total}' => $data->floor_total));
            HFormEditor::renderViewRow($label, $value);
        } else {
            if ($data->floor) {
                HFormEditor::renderViewRow(tc('Floor'), $data->floor);
            }
            if ($data->floor_total) {
                HFormEditor::renderViewRow(tt('Total number of floors', 'apartments'), $data->floor_total);
            }
        }
    }
}
?>