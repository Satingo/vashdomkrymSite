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

class MainController extends ModuleAdminController{
	public $modelName = 'Bookingtable';

	public function accessRules(){
		return array(
			array('allow',
				'expression'=> "Yii::app()->user->checkAccess('bookingtable_admin')",
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}

	public function actionChangeStatus()
	{
		$id = Yii::app()->request->getParam('id', 0);
		$model = Bookingtable::model()->findByPk($id);
		if(!$model){
			throw404();
		}
		$oldStatus = $model->active;

		if(isset($_POST['Bookingtable'])){
			$model->scenario = 'change_status';
			$model->attributes = $_POST['Bookingtable'];

			if($model->validate(array('active', 'amount')) && $model->update(array('active', 'amount'))){
				$notifier = new Notifier();
				if($model->active == Bookingtable::STATUS_NEED_PAY){
					$notifier->raiseEvent('onBookingNeedPay', $model, array('user' => $model->sender));
				}else{
					if($oldStatus != $model->active){
						if($model->active == Bookingtable::STATUS_CONFIRM && $model->sender){
							if (issetModule('bookingcalendar')) {
								Bookingcalendar::addRecord($model);
							}
							$notifier->raiseEvent('onBookingConfirm', $model, array('user' => $model->sender));
						}elseif($model->sender){
							$notifier->raiseEvent('onBookingChangeStatus', $model, array('user' => $model->sender));
						}
					}
				}
				HAjax::jsonOk(tt('Success change status'), array(
					'id' => $model->id,
					'html' => HBooking::getChangeBookingStatus($model)
				));
			} else {
				HAjax::jsonError(tt('Error change status'), array(
					'html' => $this->renderPartial('_changeStatus_form', array(
						'model' => $model,
					), true)
				));
			}
		}elseif(issetModule('paidservices') && !$model->amount){
			$model->amount = HBooking::calculateAdvancePayment($model);
		}

		$this->renderPartial('changeStatus', array(
			'model' => $model,
		));
	}

	public function actionDetails($id)
	{
		$model = $this->loadModel($id);
		HBooking::renderDetails($model, false);
	}
}