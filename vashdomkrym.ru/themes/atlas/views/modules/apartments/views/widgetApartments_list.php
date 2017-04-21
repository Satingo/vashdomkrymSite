<?php if($this->numBlocks == 2):?>
	<style>
		.block { margin: 32px 11px 5px 0 !important; }
	</style>
<?php endif;?>

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

if (!param('useGoogleMap', 0) && !param('useYandexMap', 0) && !param('useOSMMap', 0) || Yii::app()->controller->useAdditionalView == Themes::ADDITIONAL_VIEW_FULL_WIDTH_MAP)
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

<?php /*if (Yii::app()->request->isAjaxRequest && $route != 'site/index') : ?>
	<?php if(isset($this->breadcrumbs) && $this->breadcrumbs):?>
		<?php
			$this->widget('zii.widgets.CBreadcrumbs', array(
				'homeLink' => CHtml::link(Yii::t('zii','Home'),Yii::app()->homeUrl, array('class' => 'path')),
				'links'=>$this->breadcrumbs,
				'separator' => ' / ',
				'activeLinkTemplate' => '<a class="path" href="{url}">{label}</a>',
				'inactiveLinkTemplate' => '<a href="javascript: void(0);">{label}</a>',
			));
		?>
	<?php endif;?>
<?php endif; */?>

<div class="catalog" id="appartment_box">
	<?php if ($this->showWidgetTitle) : ?>
		<h2 class="title highlight-left-right">
			<span>
				<?php
				if ($this->widgetTitle !== null) {
					echo $this->widgetTitle . (isset($count) && $count ? ' (' . $count . ')' : '');
				} else {
					echo tt('Apartments list', 'apartments') . (isset($count) && $count ? ' (' . $count . ')' : '');
				}
			?>
			</span>
		</h2>
	<?php endif;?>
	<div class="clear"></div>
	
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
	<div class="clear"></div><br />
	<?php if ($this->showSorter && $sorterLinks && $apCount):?>
		<div class="sorting">
			<?php foreach ($sorterLinks as $link):?>
				<?php echo $link;?>
			<?php endforeach;?>
		</div>
		<div class="clear"></div><br />
	<?php endif;?>

	<?php if($pages):?>
		<div class="pagination">
			<?php
			$this->widget('itemPaginatorAtlas',
				array(
					'pages' => $pages,
					'maxButtonCount' => 6,
					'header' => '',
					'selectedPageCssClass' => 'current',
					'htmlOptions' => array(
						'class' => '',
					),
					'htmlOption' => array('onClick' => 'reloadApartmentList(this.href); return false;'),
				)
			);
			?>
		</div>
		<div class="clear"></div><br />
	<?php endif; ?>

	<?php if ($apCount):?>
		<?php		
		if ($modeListShow == 'block') {
			$this->render('widgetApartments_list_item', array('criteria' => $criteria));
		} elseif ($modeListShow == 'map' && (param('useGoogleMap', 0) || param('useYandexMap', 0) || param('useOSMMap', 0))) {
			$this->render('widgetApartments_list_map', array('criteria' => $criteria));
		} else {
			?>
			<style>
				@media screen and (max-width: 800px) {
					#ap-view-table-list td:nth-of-type(1):before { content: "<?php echo CHtml::encode(tc('Main photo'));?>"; }
					#ap-view-table-list td:nth-of-type(2):before { content: "<?php echo CHtml::encode(tt('Type', 'apartments'));?>"; }
					#ap-view-table-list td:nth-of-type(3):before { content: "<?php echo CHtml::encode(tt('Apartment title', 'apartments'));?>"; }
					#ap-view-table-list td:nth-of-type(4):before { content: "<?php echo CHtml::encode(tt('Address', 'apartments'));?>"; }
					#ap-view-table-list td:nth-of-type(5):before { content: "<?php echo CHtml::encode(tt('Object type', 'apartments'));?>"; }
					#ap-view-table-list td:nth-of-type(6):before { content: "<?php echo CHtml::encode(tt('Square', 'apartments'));?>"; }
					#ap-view-table-list td:nth-of-type(7):before { content: "<?php echo CHtml::encode(tt('Price', 'apartments'));?>"; }
					#ap-view-table-list td:nth-of-type(8):before { content: "<?php echo CHtml::encode(tt('Floor', 'apartments'));?>"; }
				}
			</style>
		
			<?php $dataProvider = new CActiveDataProvider('Apartment', array(
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
						array(
							'header' => '',
							'type' => 'raw',
							'value' => 'Apartment::returnMainThumbForGrid($data)',
							'htmlOptions' => array('class' => 'ap-view-table-photo'),
						),
						array(
							'header' => tt('Type', 'apartments'),
							'value' => 'HApartment::getNameByType($data->type)',
							'htmlOptions' => array('class' => 'ap-view-table-type'),
						),
						array(
							'header' => tt('Apartment title', 'apartments'),
							'type' => 'raw',
							'value' => 'CHtml::link($data->getTitle(), $data->url)',
							'htmlOptions' => array('class' => 'ap-view-table-title'),
						),
						array(
							'header' => tt('Address', 'apartments'),
							'value' => '$data->getStrByLang("address")',
                            'visible' => $canShowAddress,
							'htmlOptions' => array('class' => 'ap-view-table-address'),
						),
						array(
							'header' => tt('Object type', 'apartments'),
							'type' => 'raw',
							'value' => '$data->getObjType4table()',
							'htmlOptions' => array('class' => 'ap-view-table-obj-type'),
						),
						array(
							'header' => tt('Square', 'apartments'),
							'type' => 'raw',
							'value' => '$data->getSquareString()',
							'htmlOptions' => array('class' => 'ap-view-table-square'),
						),
						array(
							'header' => tt('Price', 'apartments'),
							'type' => 'raw',
							'value' => '$data->getPrettyPrice()',
							'htmlOptions' => array('class' => 'ap-view-table-price'),
						),
						array(
							'header' => tt('Floor', 'apartments'),
							'type' => 'raw',
							'value' => '$data->floor == 0 ? tc("floors").":&nbsp;".$data->floor_total : $data->floor."/".$data->floor_total ;',
							'htmlOptions' => array('class' => 'ap-view-table-floor'),
						),
					)
				)
			);
		}
		?>
		<div class="clear"></div>
	<?php endif; ?>
</div>

<?php if (!$apCount):?>
	<div class="empty"><?php echo Yii::t('module_apartments', 'Apartments list is empty.');?></div>
	<div class="clear"></div>
<?php endif;?>

<?php if($pages):?>
	<div class="pagination">
		<?php
		$this->widget('itemPaginatorAtlas',
			array(
				'pages' => $pages,
				'maxButtonCount' => 6,
				'header' => '',
				'selectedPageCssClass' => 'current',
				'htmlOptions' => array(
					'class' => '',
				),
				'htmlOption' => array('onClick' => 'reloadApartmentList(this.href); return false;'),
			)
		);
		?>
	</div>
	<div class="clear"></div>
<?php endif; ?>