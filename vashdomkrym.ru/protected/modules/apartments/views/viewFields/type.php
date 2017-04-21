<?php
if ($data->canShowInView('type')) {
    HFormEditor::renderViewRow($data->getAttributeLabel('type'), HApartment::getNameByType($data->type));
}
?>