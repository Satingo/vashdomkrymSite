<?php
$this->adminTitle = tc('The forms designer');

if(issetModule('formeditor')){
    $this->menu = array(
        array('label'=>tt('Add field', 'formeditor'), 'url'=>array('/formeditor/backend/main/create')),
        array('label'=>tt('Edit search form', 'formeditor'), 'url'=>array('/formeditor/backend/search/editSearchForm')),
    );
}

Yii::app()->clientScript->registerScript('search', "
$('#form-designer-filter').submit(function(){
    $('#form-designer-grid').yiiGridView('update', {
        data: $(this).serialize()
    });
    return false;
});

function ajaxSetVisible(elem){
	$.ajax({
		url: $(elem).attr('href'),
		success: function(){
			$('#form-designer-grid').yiiGridView.update('form-designer-grid');
		}
	});
}
");

$this->widget('CustomGridView', array(
    'id'=>'form-designer-grid',
    'dataProvider'=>$model->search(),
    'afterAjaxUpdate' => 'function(id, data){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove(); reInstallSortable(id, data);}',
	'rowCssClassExpression'=>'"items[]_{$data->id}"',
	'rowHtmlOptionsExpression' => 'array("data-bid"=>"items[]_{$data->id}")',
    'filter'=>$model,
    'columns'=>array(
        array(
            'name' => 'field',
            'value' => '$data->getLabel()',
            'filter' => false,
        ),

        array(
            'name' => 'view_in',
            'value' => '$data->getViewInName()',
            'filter' => FormDesigner::getViewInList(),
        ),

        array(
            'header' => tt('Show for property types', 'formdesigner'),
            'value' => '$data->getTypesHtml()',
            'type' => 'raw',
            'sortable' => false,
        ),

        array(
            'name' => 'tip',
            'filter' => false,
        ),

        array(
            'name' => 'visible',
            'value' => '$data->getVisibleName()',
            'type' => 'raw',
            'sortable' => false,
            'filter' => FormDesigner::getVisibleList(),
        ),

        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template' => '{up} {down} {fast_up} {fast_down} {update} {delete}',
            'htmlOptions' => array('class'=>'infopages_buttons_column', 'style'=>'width:160px;'),
            'buttons' => array(
                'up' => array(
                    'label' => tc('Move an item up'),
                    'imageUrl' => $url = Yii::app()->assetManager->publish(
                            Yii::getPathOfAlias('zii.widgets.assets.gridview').'/up.gif'
                        ),
                    'url'=>'Yii::app()->createUrl("/formeditor/backend/main/move", array("id"=>$data->id, "direction" => "up", "view_in"=>$data->view_in))',
                    'options' => array('class'=>'infopages_arrow_image_up'),
                    'visible' => '($data->sorter > "'.$model->minSorter.'") && '.intval($model->view_in),
                    'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'form-designer-grid'); return false;}",
                ),
                'down' => array(
                    'label' => tc('Move an item down'),
                    'imageUrl' => $url = Yii::app()->assetManager->publish(
                            Yii::getPathOfAlias('zii.widgets.assets.gridview').'/down.gif'
                        ),
                    'url'=>'Yii::app()->createUrl("/formeditor/backend/main/move", array("id"=>$data->id, "direction" => "down", "view_in"=>$data->view_in))',
                    'options' => array('class'=>'infopages_arrow_image_down'),
                    'visible' => '($data->sorter < "'.$model->maxSorter.'") && '.intval($model->view_in),
                    'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'form-designer-grid'); return false;}",
                ),
                'fast_up' => array(
                    'label' => tc('Move to the beginning of the list'),
                    'imageUrl' => Yii::app()->theme->baseUrl.'/images/default/fast_top_arrow.gif',
                    'url'=>'Yii::app()->createUrl("/formeditor/backend/main/move", array("id"=>$data->id, "direction" => "fast_up", "view_in"=>$data->view_in))',
                    'options' => array('class'=>'infopages_arrow_image_fast_up'),
                    'visible' => '($data->sorter > "'.$model->minSorter.'") && '.intval($model->view_in),
                    'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'form-designer-grid'); return false;}",
                ),
                'fast_down' => array(
                    'label' => tc('Move to end of list'),
                    'imageUrl' => Yii::app()->theme->baseUrl.'/images/default/fast_bottom_arrow.gif',
                    'url'=>'Yii::app()->createUrl("/formeditor/backend/main/move", array("id"=>$data->id, "direction" => "fast_down", "view_in"=>$data->view_in))',
                    'options' => array('class'=>'infopages_arrow_image_fast_down'),
                    'visible' => '($data->sorter < "'.$model->maxSorter.'") && '.intval($model->view_in),
                    'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'form-designer-grid'); return false;}",
                ),

                'update' => array(
                    'url' => '$data->getUpdateUrl()',
                ),
                'delete' => array(
                    'visible' => '$data->standard_type == 0',
                    'url' => 'Yii::app()->createUrl("/formeditor/backend/main/delete", array("id" => $data->id))'
                ),
            )
        ),
    ),
));
?>

<?php

$csrf_token_name = Yii::app()->request->csrfTokenName;
$csrf_token = Yii::app()->request->csrfToken;

$cs = Yii::app()->getClientScript();
$cs->registerCoreScript('jquery.ui');

$str_js = "
		var fixHelper = function(e, ui) {
			ui.children().each(function() {
				$(this).width($(this).width());
			});
			return ui;
		};

		function reInstallSortable(id, data) {
			installSortable($(data).find('select[name=\"FormDesigner[view_in]\"] option:selected').val());
		}

		function updateGrid() {
			$.fn.yiiGridView.update('form-designer-grid');
		}

		function installSortable(areaIdSel) {
			if (areaIdSel > 0) {
				$('#form-designer-grid table.items tbody').sortable({
					forcePlaceholderSize: true,
					forceHelperSize: true,
					items: 'tr',
					update : function () {
						serial = $('#form-designer-grid table.items tbody').sortable('serialize', {key: 'items[]', attribute: 'data-bid'}) + '&{$csrf_token_name}={$csrf_token}&area_id=' + areaIdSel;
						$.ajax({
							'url': '" . $this->createUrl('/formeditor/backend/main/sortitems') . "',
							'type': 'post',
							'data': serial,
							'success': function(data){
								updateGrid();
							},
							'error': function(request, status, error){
								alert('We are unable to set the sort order at this time.  Please try again in a few minutes.');
							}
						});
					},
					helper: fixHelper
				}).disableSelection();
			}
		}

		installSortable('".intval($model->view_in)."');
";

$cs->registerScript('sortable-project', $str_js);
