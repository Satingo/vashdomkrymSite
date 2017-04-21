<?php
$modeListShow = $this->modeListShow ? $this->modeListShow : User::getModeListShow();

$route = Controller::getCurrentRoute();

$getForMapSwitch = $_GET;
if(isset($getForMapSwitch['is_ajax'])){
    unset($getForMapSwitch['is_ajax']);
}

$urlsSwitching = array(
    'block' => Yii::app()->createUrl($route, array('ls'=>'block') + $_GET, '&'),
    'table' => Yii::app()->createUrl($route, array('ls'=>'table') + $_GET, '&'),
    'map' => Yii::app()->createUrl($route, array('ls'=>'map') + $getForMapSwitch, '&'),
);

if (!param('useGoogleMap', 0) && !param('useYandexMap', 0) && !param('useOSMMap', 0))
    unset($urlsSwitching['map']);

Yii::app()->clientScript->registerScript('search-vars', "
	var urlsSwitching = ".CJavaScript::encode($urlsSwitching).";
",
	CClientScript::POS_HEAD, array(), true);

if(!Yii::app()->request->isAjaxRequest){
	Yii::app()->clientScript->registerScript('search-params', "
        var updateText = '" . Yii::t('common', 'Loading ...') . "';
        var resultBlock = 'appartment_box';
        var indicator = '" . Yii::app()->theme->baseUrl . "/images/pages/indicator.gif';
        var bg_img = '" . Yii::app()->theme->baseUrl . "/images/pages/opacity.png';

        var useGoogleMap = ".param('useGoogleMap', 0).";
        var useYandexMap = ".param('useYandexMap', 0).";
        var useOSMap = ".param('useOSMMap', 0).";

        var modeListShow = ".CJavaScript::encode($modeListShow).";

		$(function () {
			$('div#appartment_box').on('mouseover mouseout', 'div.appartment_item', function(event){
				if (event.type == 'mouseover') {
				 $(this).find('div.apartment_item_edit').show();
				} else {
				 $(this).find('div.apartment_item_edit').hide();
				}
			});
		});

        function setListShow(mode){
            modeListShow = mode;
            reloadApartmentList(urlsSwitching[mode]);
        };


        $(function () {
            if(modeListShow == 'map'){
                list.apply();
            }
        });
    ",
		CClientScript::POS_HEAD, array(), true);
}
?>

<?php if (Yii::app()->request->isAjaxRequest && $route != 'site/index') : ?>
	<?php if(isset($this->breadcrumbs) && $this->breadcrumbs):?>
		<div class="clear"></div>
		<?php
		$this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
			'separator' => ' &#8594; ',
		));
		?>
		<div class="clear"></div>
	<?php endif?>
<?php endif?>

<div class="title_list">
	<?php if ($this->showWidgetTitle) : ?>
		<h2>
			<?php
			if ($this->widgetTitle !== null) {
				echo $this->widgetTitle . (isset($count) && $count ? ' (' . $count . ')' : '');
			} else {
				echo tt('Apartments list', 'apartments') . (isset($count) && $count ? ' (' . $count . ')' : '');
			}
			?>
		</h2>
	<?php endif;?>

	<?php if($this->showSwitcher){ ?>
	<div class="change_list_show">
		<a href="<?php echo $urlsSwitching['block']; ?>" <?php if ($modeListShow == 'block') {
			echo 'class="active_ls"';
		} ?>
		   onclick="setListShow('block'); return false;">
			<img src="<?php echo Yii::app()->theme->baseUrl; ?>/images/pages/block.png" alt="block" />
		</a>

		<a href="<?php echo $urlsSwitching['table']; ?>" <?php if ($modeListShow == 'table') {
			echo 'class="active_ls"';
		} ?>
		   onclick="setListShow('table'); return false;">
			<img src="<?php echo Yii::app()->theme->baseUrl; ?>/images/pages/table.png" alt="table" />
		</a>

		<?php if (array_key_exists('map', $urlsSwitching)) : ?>
			<a href="<?php echo $urlsSwitching['map']; ?>" <?php if ($modeListShow == 'map') {
				echo 'class="active_ls"';
			} ?> >
				<img src="<?php echo Yii::app()->theme->baseUrl; ?>/images/pages/map.png" alt="<?php echo tc('Map');?>" />
			</a>
		<?php endif; ?>
	</div>
	<?php } ?>
</div>

<div class="clear"></div>

<?php
	if ($this->showSorter && $modeListShow != 'map' && $sorterLinks) {
		foreach ($sorterLinks as $link) {
			echo '<div class="sorting">' . $link . '</div>';
		}
	}
?>

<div class="appartment_box" id="appartment_box">
	<?php
	if ($apCount) {

		if ($modeListShow == 'block') {
			$this->render('widgetApartments_list_item', array('criteria' => $criteria));
		} elseif ($modeListShow == 'map' && (param('useGoogleMap', 0) || param('useYandexMap', 0) || param('useOSMMap', 0))) {
			$this->render('widgetApartments_list_map', array('criteria' => $criteria));
		} else {

			$dataProvider = new CActiveDataProvider('Apartment', array(
				'criteria'=>$criteria,
				'pagination'=>false,
			));

			$canShowAddress = isset($dataProvider->data[0]) ? $dataProvider->data[0]->canShowInView("address") : false;

			$this->widget('zii.widgets.grid.CGridView', array(
					'id' => 'ap-view-table-list',
					'dataProvider' => $dataProvider,
					'rowCssClassExpression' => '$data->getRowCssClass()',
					'enablePagination'=>false,
					'selectionChanged'=>'js:function(id) {
						$currentGrid = $("#"+id);
						$rows = $currentGrid.find(".items").children("tbody").children();
						$selKey = $.fn.yiiGridView.getSelection(id);

						if ($selKey.length > 0) {
							$.each($currentGrid.find(".keys").children("span"), function(i,el){
								if ($(this).text() == $selKey) {
									$(this).attr("data-rel", "selected");
								}
								else {
									$(this).removeAttr("data-rel");
								}
							});
						}

						$.each($currentGrid.find(".keys").children("span"), function(i,el){
							var attr = $(this).attr("data-rel");
							if (typeof attr !== "undefined" && attr !== false) {
								$currentGrid.find(".items").children("tbody").children("tr").eq(i).addClass("selected");
							}
							else {
								$currentGrid.find(".items").children("tbody").children("tr").eq(i).removeClass("selected");
							}
						});

						return false;
					}',
					'template' => '{items}{pager}',
					'columns' => array(
						/*array(
							'name' => 'id',
						),
						array(
							'header' => tc('Photo'),
							'value' => '(isset($data->images) && count($data->images) > 0) ? \'<img alt="'.tc('With photo').'" src="'.Yii::app()->theme->baseUrl.'/images/with-photo.png">\' : tc("No")',
							'type' => 'raw'
						),*/
						array(
							'header' => '',
							'type' => 'raw',
							'value' => 'Apartment::returnMainThumbForGrid($data)'
						),
						array(
							'header' => tt('Type', 'apartments'),
							'value' => 'HApartment::getNameByType($data->type)'
						),
						array(
							'header' => tt('Apartment title', 'apartments'),
							'value' => 'CHtml::link($data->getTitle(), $data->url)',
							'type' => 'raw'
						),
						array(
							'header' => tt('Address', 'apartments'),
							'value' => '$data->getStrByLang("address")',
                            'visible' => $canShowAddress,
						),
						array(
							'header' => tt('Object type', 'apartments'),
							'type' => 'raw',
							'value' => '$data->getObjType4table()'
						),
						array(
							'header' => tt('Square', 'apartments'),
							'type' => 'raw',
							'value' => '$data->getSquareString()',
							//'value' => 'Yii::t("module_apartments", "total square: {n}", $data->square)'
						),
						array(
							'header' => tt('Price', 'apartments'),
							'value' => '$data->getPrettyPrice()'
						),
						array(
							'header' => tt('Floor', 'apartments'),
							'type' => 'raw',
							'value' => '$data->floor == 0 ? tc("floors").":&nbsp;".$data->floor_total : $data->floor."/".$data->floor_total ;',
						),
					)
				)
			);
		}
	}
	?>

</div>


<?php
if (!$apCount) {
	echo Yii::t('module_apartments', 'Apartments list is empty.');
}

if ($pages) {
	$this->widget('itemPaginator', array('pages' => $pages, 'header' => '', 'htmlOption' => array('onClick' => 'reloadApartmentList(this.href); list.apply(); return false;')));
}
?>