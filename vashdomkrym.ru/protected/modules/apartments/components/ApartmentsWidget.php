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

class ApartmentsWidget extends CWidget {
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
		$limit = $this->setLimit ? param('countListitng'.User::getModeListShow(), 10) : 0;
		Yii::import('application.modules.apartments.helpers.apartmentsHelper');
		$result = apartmentsHelper::getApartments($limit, $this->usePagination, 0, $this->criteria);

		if (!$this->breadcrumbs) {
			$this->breadcrumbs=array(
				Yii::t('common', 'Apartment search'),
			);
		}

		if($this->count){
			$result['count'] = $this->count;
		}
		else {
			$result['count'] = $result['apCount'];
		}

		if(!$result['count'] && !$this->showIfNone){
			return;
		}

		$this->render('widgetApartments_list', $result);
	}
}