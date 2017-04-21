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

Yii::import('bootstrap.widgets.TbGridView');

class CustomGridView extends TbGridView {
	//public $pager = array('class'=>'itemPaginator');
	public $template = "{summary}\n{pager}\n{items}\n{pager}";

    public $type = 'striped bordered condensed';

	public $pager = array('class'=>'bootstrap.widgets.TbPager', 'displayFirstAndLast' => true);

}