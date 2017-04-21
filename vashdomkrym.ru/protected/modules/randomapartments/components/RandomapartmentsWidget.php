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

class RandomapartmentsWidget extends CWidget {
	public $usePagination = 1;
	public $criteria = null;
	public $count = null;
	public $widgetTitle = null;
	public $showWidgetTitle = true;
	public $numBlocks = 3;
	public $showSorter = 0;

	public function getViewPath($checkTheme=true){
		if($checkTheme && ($theme=Yii::app()->getTheme())!==null){
			if (is_dir($theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'apartments'.DIRECTORY_SEPARATOR.'views'))
				return $theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'apartments'.DIRECTORY_SEPARATOR.'views';
		}
		return Yii::getPathOfAlias('application.modules.apartments.views');
	}

	public function run() {
		Yii::import('application.modules.apartments.helpers.apartmentsHelper');

		$dependency = new CDbCacheDependency('SELECT MAX(date_updated) FROM {{apartment}}');
		$sql = 'SELECT id FROM {{apartment}} WHERE active="'.Apartment::STATUS_ACTIVE.'" ';

		if (param('useUserads'))
			$sql .= ' AND owner_active = '.Apartment::STATUS_ACTIVE;

		$results = Yii::app()->db->cache(param('cachingTime', 1209600), $dependency)->createCommand($sql)->queryColumn();
		shuffle($results);

		$this->criteria = new CDbCriteria;
		$this->criteria->addInCondition('t.id', array_slice($results, 0, param('countListitng'.User::getModeListShow(), 6)));

		$result = apartmentsHelper::getApartments(param('countListitng'.User::getModeListShow(), 6), $this->usePagination, 0, $this->criteria);

		if($this->count){
			$result['count'] = $this->count;
		}

		$this->render('widgetApartments_list', $result);
	}
}