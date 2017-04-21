<?php
if ($data->canShowInView('window_to') && $data->windowTo->getTitle()) {
    HFormEditor::renderViewRow(tt('window to'), CHtml::encode($data->windowTo->getTitle()));
}
?>