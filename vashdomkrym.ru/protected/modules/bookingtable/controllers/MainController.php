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

class MainController extends ModuleUserController {
    public $layout='//layouts/usercpanel';

	public $modelName = 'Bookingtable';
	public $scenario = null;

	public function init() {
		// если админ - делаем редирект на просмотр в админку
		if(Yii::app()->user->checkAccess('backend_access')){
			$this->redirect($this->createAbsoluteUrl('/bookingtable/backend/main/admin'));
		}
		/*if (!param('useUserads')) {
			throw404();
		}*/
		parent::init();
	}

	public function accessRules(){
		return array(
			array(
				'allow',
				//'expression' => 'param("useUserads") && !Yii::app()->user->isGuest',
				'expression' => '!Yii::app()->user->isGuest',
			),
			array(
				'deny',
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex() {
        $this->setActiveMenu('booking_applications');

		/*$sql = 'SELECT id FROM {{apartment}} WHERE owner_id = "'.Yii::app()->user->id.'" ';
		$apIds = Yii::app()->db->createCommand($sql)->queryColumn();

		$sql = 'UPDATE {{booking_table}} SET active = "'.Bookingtable::STATUS_VIEWED.'" WHERE active = "'.Bookingtable::STATUS_NEW.'" AND apartment_id IN ('.implode(',', $apIds).')';
		Yii::app()->db->createCommand($sql)->execute();
*/
		$model = new $this->modelName('search');

		Yii::app()->user->setState('searchUrl', NULL);

		$model->unsetAttributes();  // clear any default values
		if(isset($_GET[$this->modelName])){
			$model->attributes = $_GET[$this->modelName];
		}

		if(Yii::app()->request->isAjaxRequest){
			$this->renderPartial('index',array(
				'model'=>$model,
			), false, true);
		} else {
			$this->render('index',array(
				'model'=>$model,
			));
		}
	}

	public function actionMy(){
		$this->setActiveMenu('my_bookings');

		$model = new $this->modelName('search');

		$model->scopeMy();

		$model->unsetAttributes();  // clear any default values
		if(isset($_GET[$this->modelName])){
			$model->attributes = $_GET[$this->modelName];
		}

		$this->render('booking', array(
			'model'=>$model,
		));
	}

	public function actionDetails($id)
	{
		$model = $this->loadModel($id);
		HBooking::renderDetails($model);
	}
}