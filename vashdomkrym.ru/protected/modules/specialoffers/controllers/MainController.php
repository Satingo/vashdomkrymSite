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

class MainController extends ModuleUserController{
	public function init() {
		parent::init();

		$specialOfferPage = Menu::model()->findByPk(Menu::SPECIALOFFERS_ID);
		if ($specialOfferPage) {
			if ($specialOfferPage->active == 0) {
				throw404();
			}
		}
	}

	public function actionIndex(){
		$this->showSearchForm = false;
		
        Yii::app()->user->setState('searchUrl', NULL);

		Yii::app()->getModule('apartments');

		$criteria = new CDbCriteria;
		$criteria->condition = 'is_special_offer = 1';

        if(Yii::app()->request->isAjaxRequest){
			$this->excludeJs();
            $this->renderPartial('index', array(
                'criteria' => $criteria,
            ), false, true);
        }else{
            $this->render('index', array(
                'criteria' => $criteria,
            ));
        }
	}
}