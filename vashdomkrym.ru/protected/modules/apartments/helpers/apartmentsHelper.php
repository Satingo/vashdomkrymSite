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

class apartmentsHelper {
	public static function getApartments($limit = 10, $usePagination = 1, $all = 1, $criteria = null){
		$pages = array();

		Yii::app()->getModule('apartments');

		if($criteria === null)
			$criteria = new CDbCriteria;

		if(!$all){
			$criteria->addCondition('t.deleted = 0');
			$criteria->addCondition('t.active = '.Apartment::STATUS_ACTIVE);
			if (param('useUserads'))
				$criteria->addCondition('owner_active = '.Apartment::STATUS_ACTIVE);
		}

		$sort = new CSort('Apartment');
		$sort->attributes = array(
			'price' => 'price',
			'date_created' => 'date_created',
		);

		if(!$criteria->order)
			$sort->defaultOrder = 't.date_up_search DESC, t.sorter DESC';
		$sort->applyOrder($criteria);

		if (issetModule('seasonalprices')) {
			$criteria->with = CMap::mergeArray($criteria->with, array('seasonalPrices'));

			if($criteria->order){
				if($criteria->order == '`t`.`price`'){
					$criteria->order = 't.price, seasonalPrices_sort_asc.price';
					$criteria->with = CMap::mergeArray($criteria->with, array('seasonalPrices_sort_asc'));
				}
				if($criteria->order == '`t`.`price` DESC'){
					$criteria->order = 't.price DESC, seasonalPrices_sort_desc.price DESC';
					$criteria->with = CMap::mergeArray($criteria->with, array('seasonalPrices_sort_desc'));
				}
			}
		}

		$sorterLinks = self::getSorterLinks($sort);

		$criteria->addCondition('t.owner_id = 1 OR t.owner_active = 1');
		$criteria->addInCondition('t.type', HApartment::availableApTypesIds());
		$criteria->addInCondition('t.price_type', array_keys(HApartment::getPriceArray(Apartment::PRICE_SALE, true)));

		// find count
		$apCount = Apartment::model()->count($criteria);

		if($usePagination){
			$pages = new CPagination($apCount);
			$pages->pageSize = $limit;
			$pages->applyLimit($criteria);
		}
		else{
			if($limit)
				$criteria->limit = $limit;
		}

		if(issetModule('seo'))
			$criteria->with = CMap::mergeArray($criteria->with, array('seo'));


		return array(
			'pages' => $pages,
			'sorterLinks' => $sorterLinks,
			'apCount' => $apCount,
			'criteria' => $criteria
		);
	}

	public static function getSorterLinks($sort){
        $HtmlOption = array('onClick'=>'reloadApartmentList(this.href); return false;');
		$return = array(
			$sort->link('price', tt('Sorting by price', 'quicksearch'), $HtmlOption),
			$sort->link('date_created', tt('Sorting by date created', 'quicksearch'), $HtmlOption),
		);
		return $return;
	}
}