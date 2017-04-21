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
	public $modelName = 'BlockIp';
	public $defaultAction = 'admin';
	public $redirectTo = array('admin');

	public function filters(){
		return array(
			'accessControl',
			array(
				'ESetReturnUrlFilter + index, view, admin, update, create',
			),
		);
	}

	public function accessRules(){
		return array(
			array('allow',
				'expression'=> "Yii::app()->user->checkAccess('blockip_admin')",
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}

	public function actionAdmin() {
		$model = new $this->modelName('search');
		$model->resetScope();

		$model->unsetAttributes();  // clear any default values
		if(isset($_GET[$this->modelName])){
			$model->attributes=$_GET[$this->modelName];
		}

		$model->deleteIpAfterDays = param('delete_ip_after_days');

		if(isset($_POST[$this->modelName]) && isset($_POST[$this->modelName]['deleteIpAfterDays'])){
			$model->scenario = 'upd_settings_day';
			$model->attributes = $_POST[$this->modelName];
			$model->setAttribute('deleteIpAfterDays', $_POST[$this->modelName]['deleteIpAfterDays']);

			if (!$model->deleteIpAfterDays)
				$model->setAttribute('deleteIpAfterDays', 1);

			if ($model->validate()) {
				ConfigurationModel::updateValue('delete_ip_after_days', $model->deleteIpAfterDays);

				Yii::app()->user->setFlash('success', tt('success_saved', 'service'));
			}
		}

		$this->render('admin',
			array_merge(array('model'=>$model), $this->params)
		);
	}

	public function actionView($id) {
		$this->redirect('admin');
	}

	public function actionAjaxAdd() {
		if (Yii::app()->request->isAjaxRequest && Yii::app()->user->checkAccess('blockip_admin')) {
			$postValue = CHtml::encode(strip_tags(Yii::app()->request->getParam('value')));
			$pk = CHtml::encode(strip_tags(Yii::app()->request->getParam('pk')));

			if ($postValue) {
				$blockIpModel = BlockIp::model()->find(
					'ip = :ip',
					array(':ip' => $postValue)
				);

				if ($blockIpModel) { # IP уже есть
					$msg = 'already_exists';
				}
				else {
					$blockIpModel = new BlockIp;
					$blockIpModel->ip = $postValue;
					$blockIpModel->ip_long = ip2long($postValue);
					$blockIpModel->date_created = new CDbExpression('NOW()');

					if ($blockIpModel->save()) {
						$msg = 'ok';
					}
					else {
						$msg = 'save_error';
					}
				}
			}
			else {
				$msg = 'no_value';
			}

			echo CJSON::encode(array('msg' => $msg, 'value' => $postValue, 'pk' => $pk));
			Yii::app()->end();
		}
	}
}