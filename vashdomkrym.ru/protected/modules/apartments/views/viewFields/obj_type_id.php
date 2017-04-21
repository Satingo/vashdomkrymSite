<?php

if ($data->canShowInView('obj_type_id')) {
    HFormEditor::renderViewRow($data->getAttributeLabel('obj_type_id'), $data->objType->name);
}