<?php

if($data->parent_id && $data->parent){
    HFormEditor::renderViewRow(tt('Is located', 'apartments'), CHtml::link($data->parent->getTitle(), $data->parent->getUrl()));
}

