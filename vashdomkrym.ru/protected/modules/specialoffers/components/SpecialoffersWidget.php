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

class SpecialoffersWidget extends CWidget {
	public $usePagination = 1;
	public $criteria = null;
	public $count = null;
	public $showWidgetTitle = true;
	public $widgetTitle = null;
	public $breadcrumbs = null;
	public $numBlocks = 3;
	public $showSorter = 1;
	public $showSwitcher = 1;
	public $setLimit = 1;
	public $showIfNone = true;
	public $modeListShow = '';

	public function getViewPath($checkTheme=true){
		if($checkTheme && ($theme=Yii::app()->getTheme())!==null){
			if (is_dir($theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'apartments'.DIRECTORY_SEPARATOR.'views'))
				return $theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'apartments'.DIRECTORY_SEPARATOR.'views';
		}
		return Yii::getPathOfAlias('application.modules.apartments.views');
	}

	public function run() {
		Yii::import('application.modules.apartments.helpers.apartmentsHelper');
		$this->widgetTitle = tc('Special offers');

		$this->criteria = new CDbCriteria;
		$this->criteria->addCondition('is_special_offer = 1');

		$result = apartmentsHelper::getApartments(param('countListitng'.User::getModeListShow(), 6), $this->usePagination, 0, $this->criteria);

		if($this->count){
			$result['count'] = $this->count;
		}

		$this->render('widgetApartments_list', $result);
	}
}