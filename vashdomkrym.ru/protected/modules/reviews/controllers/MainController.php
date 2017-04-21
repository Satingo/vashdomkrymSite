<?php
/**********************************************************************************************
 *                            CMS Open Business Card
 *                              -----------------
 *	version				:	V1.16.1
 *	copyright			:	(c) 2015 Monoray
 *	website				:	http://www.monoray.ru/
 *	contact us			:	http://www.monoray.ru/contact
 *
 * This file is part of CMS Open Business Card
 *
 * Open Business Card is free software. This work is licensed under a GNU GPL.
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Open Business Card is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * Without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 ***********************************************************************************************/


class MainController extends ModuleUserController {
	public $modelName = 'Reviews';
	public $showSearchForm = false;

	public function init() {
		parent::init();

		$reviewsPage = Menu::model()->findByPk(Menu::REVIEWS_ID);
		if ($reviewsPage) {
			if ($reviewsPage->active == 0) {
				throw404();
			}
		}
	}

	public function actions() {
		$return = array();
		if (param('useJQuerySimpleCaptcha', 0)) {
			$return['captcha'] = array(
				'class' => 'jQuerySimpleCCaptchaAction',
				'backColor' => 0xFFFFFF,
			);
		}
		else {
			$return['captcha'] = array(
				'class' => 'MathCCaptchaAction',
				'backColor' => 0xFFFFFF,
			);
		}

		return $return;
	}

	public function actionIndex(){
		$criteria=new CDbCriteria;
		//$criteria->order = 'sorter';
		$criteria->order = 'date_created DESC';
		$criteria->condition = 'active='.Reviews::STATUS_ACTIVE;

		$pages = new CPagination(Reviews::model()->count($criteria));
		$pages->pageSize = param('module_reviews_itemsPerPage', 10);
		$pages->applyLimit($criteria);

		$reviews = Reviews::model()->cache(param('cachingTime', 1209600), Reviews::getCacheDependency())->findAll($criteria);

		$this->render('index',array(
			'reviews' => $reviews, 'pages' => $pages
		));
	}

	public function actionAdd(){
		$model = new Reviews;

		if(isset($_POST[$this->modelName]) && BlockIp::checkAllowIp(Yii::app()->controller->currentUserIpLong)){
			$model->attributes = $_POST[$this->modelName];

			if($model->validate()){
				$model->user_ip = Yii::app()->controller->currentUserIp;
				$model->user_ip_ip2_long = Yii::app()->controller->currentUserIpLong;

				if ($model->save(false)) {
					$model->name = CHtml::encode($model->name);
					$model->body = CHtml::encode($model->body);

					$notifier = new Notifier;
					$notifier->raiseEvent('onNewReview', $model);

					if (Yii::app()->user->checkAccess('reviews_admin'))
						Yii::app()->user->setFlash('success', tt('success_send_not_moderation'));
					else
						Yii::app()->user->setFlash('success', tt('success_send'));
					$this->redirect(array('index'));
				}
				$model->unsetAttributes(array('name', 'body','verifyCode'));
			}
			else {
				Yii::app()->user->setFlash('error', tt('failed_send'));
			}
			$model->unsetAttributes(array('verifyCode'));
		}
		if(Yii::app()->request->isAjaxRequest){
			$this->excludeJs();
			$this->renderPartial('add', array('model'=>$model, 'isFancy' => true,), false, true);
		} else {
			$this->render('add', array('model'=>$model));
		}
	}
}