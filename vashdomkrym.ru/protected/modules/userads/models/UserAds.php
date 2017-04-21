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

class UserAds extends Apartment {
	public function search() {
		$this->owner_id = Yii::app()->user->id;

		return parent::search();
	}

	public static function returnStatusHtml($data, $tableId, $onclick = 0, $ignore = 0){
		if($ignore && $data->id == $ignore){
			return '';
		}

		$name = tc('Inactive');
		if ($data->active == Apartment::STATUS_MODERATION) {
			$name = tc('Awaiting moderation');
		}
		elseif ($data->active == Apartment::STATUS_ACTIVE) {
			$name = tc('Active');
		}
		return '<div align="center">'.$name.'</div>';
	}

	public static function returnStatusOwnerActiveHtml($data, $tableId, $onclick = 0, $ignore = 0){
		if($ignore && $data->id == $ignore){
			return '';
		}
		$url = Yii::app()->controller->createUrl("/userads/main/activate", array("id" => $data->id, 'action' => ($data->owner_active==1?'deactivate':'activate') ));
		$img = CHtml::image(
			Yii::app()->theme->baseUrl.'/images/'.($data->owner_active?'':'in').'active.png',
			Yii::t('common', $data->owner_active?'Inactive':'Active'),
			array('title' => Yii::t('common', $data->owner_active?'Deactivate':'Activate'))
		);
		$options = array();
		if($onclick){

			if ($data->owner_active) {
				$options = array(
					'onclick' => 'ajaxSetStatus(this, "'.$tableId.'", 1); return false;',
				);
			}
			else {
				$options = array(
					'onclick' => 'ajaxSetStatus(this, "'.$tableId.'", 2); return false;',
				);
			}
		}

		$return = '<div align="center">'.CHtml::link($img,$url, $options).'</div>';

		return $return;
	}

	public function beforeSave(){
		if(!$this->isNewRecord && !$this->isOwner()){
			throw404();
		}

		return parent::beforeSave();
	}
}