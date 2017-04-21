<?php
$isPrintable = (isset($isPrintable)) ? $isPrintable : false;

if($additionFields){
    echo '<dl class="ap-descr">';
    HFormEditor::renderViewRows($additionFields, $data, $isPrintable);
    echo '</dl>';
    echo '<div class="clear"></div>';
}

