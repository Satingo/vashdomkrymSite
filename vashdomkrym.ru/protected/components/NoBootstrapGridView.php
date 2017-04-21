<?php
/* * ********************************************************************************************
 *								Open Real Estate
 *								----------------
 * 	version				:	V1.16.1
 * 	copyright			:	(c) 2015 Monoray
 * 							http://monoray.net
 *							http://monoray.ru
 *
 * 	website				:	http://open-real-estate.info/en
 *
 * 	contact us			:	http://open-real-estate.info/en/contact-us
 *
 * 	license:			:	http://open-real-estate.info/en/license
 * 							http://open-real-estate.info/ru/license
 *
 * This file is part of Open Real Estate
 *
 * ********************************************************************************************* */


Yii::import('zii.widgets.grid.CGridView');

class NoBootstrapGridView extends CGridView {
	public $template="{summary}\n{pager}\n{items}\n{pager}";

	public function init() {
		$this->pager = array(
			'class'=>'itemPaginator'
		);

		if(Yii::app()->theme->name == 'atlas'){
			$this->pager = array(
				'class'=>'itemPaginatorAtlas',
				'header' => '',
				'selectedPageCssClass' => 'current',
				'htmlOptions' => array(
					'class' => ''
				)
			);

			$this->pagerCssClass = 'pagination';
		}
		parent::init();
	}
}