<?php
if ($data->canShowInView('price')) {
	$value = $data->getPrettyPrice();
	if (!$value) $value = '-';
	
    HFormEditor::renderViewRow($data->getAttributeLabel('price'), '<span class="price_row">' . $value . '</span>');
}
?>