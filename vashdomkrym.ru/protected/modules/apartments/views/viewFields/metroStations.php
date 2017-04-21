<?php
if (issetModule('metroStations') && $data->canShowInView('metroStations') && isset($data->metroStationsTitle) && $data->metroStationsTitle) {
    HFormEditor::renderViewRow($data->getAttributeLabel('metroStations'), $data->metroStationsTitle);
}
?>