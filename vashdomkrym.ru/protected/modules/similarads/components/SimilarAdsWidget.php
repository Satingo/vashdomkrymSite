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

class SimilarAdsWidget extends CWidget {

	public function getViewPath($checkTheme=true){
		if($checkTheme && ($theme=Yii::app()->getTheme())!==null){
			if (is_dir($theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'similarads'.DIRECTORY_SEPARATOR.'views'))
				return $theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'similarads'.DIRECTORY_SEPARATOR.'views';
		}
		return Yii::getPathOfAlias('application.modules.similarads.views');
	}

	public function viewSimilarAds($data = null) {
		$similarAds = new SimilarAds;

		$criteria = new CDbCriteria;
		$criteria->addCondition('active = '.Apartment::STATUS_ACTIVE);
		$criteria->addCondition('deleted = 0');
		if (param('useUserads'))
			$criteria->addCondition('owner_active = '.Apartment::STATUS_ACTIVE);

		if ($data->id) {
			$criteria->addCondition('t.id != :id');
			$criteria->params[':id'] = $data->id;
		}

		if (issetModule('location')) {
			if ($data->loc_city) {
				$criteria->addCondition('loc_city = :loc_city');
				$criteria->params[':loc_city'] = $data->loc_city;
			}
		}
		else {
			if ($data->city_id) {
				$criteria->addCondition('city_id = :city_id');
				$criteria->params[':city_id'] = $data->city_id;
			}
		}

		if ($data->obj_type_id) {
			$criteria->addCondition('obj_type_id = :obj_type_id');
			$criteria->params[':obj_type_id'] = $data->obj_type_id;
		}
		if ($data->type) {
			$criteria->addCondition('type = :type');
			$criteria->params[':type'] = $data->type;
		}
		if ($data->price_type) {
			$criteria->addCondition('price_type = :price_type');
			$criteria->params[':price_type'] = $data->price_type;
		}

		/*$criteria->limit = param('countListitng'.User::getModeListShow(), 10);*/
		$criteria->limit = 8;
		$criteria->order = 't.id ASC';

		$ads = $similarAds->getSimilarAds($criteria);

		if($ads){
			$similarAds->publishAssets();
		}

		$this->render('widgetSimilarAds_list', array(
			'ads' => $ads,
		));
	}
}