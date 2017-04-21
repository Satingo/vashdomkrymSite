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
	public $htmlPageId = 'usercpanel';
	public $modelName = 'User';
	
	public function init() {
		parent::init();

		// если админ - делаем редирект на просмотр в админку
		if(Yii::app()->user->checkAccess('apartments_admin')){
			$this->redirect($this->createAbsoluteUrl('/apartments/backend/main/admin'));
		}
	}

	public function filters(){
		return array(
			'accessControl',
			array(
				'ESetReturnUrlFilter + index, view, create, update, bookingform, complain, mainform, add, edit',
			),
		);
	}

	public function accessRules(){
		return array(
			array('allow',
				'roles'=>array('registered'),
			),
			array(
				'deny',
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex(){
		if (param("useUserads"))
			$this->setActiveMenu('my_listings');
		else
			$this->setActiveMenu('my_data');

		$model=$this->loadModel(Yii::app()->user->id);
		$from = Yii::app()->request->getParam('from');

		$socSuccess = Yii::app()->request->getQuery('soc_success');
		if ($socSuccess)
			Yii::app()->user->setFlash('error', tt('During export account data may be generate random email and password. Please change it.', 'socialauth'));

		if ($from != 'userads') {
			if(!$socSuccess && preg_match("/null\.io/i", $model->email))
				Yii::app()->user->setFlash('error', tt('Please change your email and password!', 'socialauth'));
		}

		if(isset($_POST[$this->modelName])){
			if(isset($_POST['changePassword']) && $_POST['changePassword']){
				$model->scenario = 'changePass';

				$model->attributes=$_POST[$this->modelName];

				if($model->validate()){
					$model->setPassword();
					$model->save(false);
					Yii::app()->user->setFlash('success', tt('Your password successfully changed.'));
					$this->redirect(array('index'));
				}
			}
			else{
				$model->scenario = 'usercpanel';
				$model->attributes=$_POST[$this->modelName];

				if($model->save()){
					if($model->scenario == 'usercpanel'){
						Yii::app()->user->setFlash('success', tt('Your details successfully changed.'));
					}
					$this->redirect(array('index'));
				}
			}
		}

		$this->render('index',array(
			'model' => $this->loadModel(Yii::app()->user->id),
			'from' => $from,
		));
	}

	public function actionData(){
		$this->setActiveMenu('my_data');

		$model=$this->loadModel(Yii::app()->user->id);

		$agencyUserIdOld = '';

		if($model->type == User::TYPE_AGENT){
			$agencyUserIdOld = $model->agency_user_id;
		}

		if(preg_match("/null\.io/i", $model->email))
			Yii::app()->user->setFlash('error', tt('Please change your email and password!', 'socialauth'));

		if(isset($_POST[$this->modelName])){
			$model->scenario = 'usercpanel';
			$model->attributes=$_POST[$this->modelName];

			if($agencyUserIdOld != $model->agency_user_id){
				if($model->agency_user_id){
					$agency = User::model()->findByPk($model->agency_user_id);

					if($agency){
						$notifier = new Notifier();
						$notifier->raiseEvent('onNewAgent', $model, array(
							'forceEmail' => $agency->email,
						));
					} else {
						$model->addError('agency_user_id', 'There is no Agency with such ID');
					}
				}

				$model->agent_status = User::AGENT_STATUS_AWAIT_VERIFY;
			}

			if($model->save()){
				if($model->scenario == 'usercpanel'){
					Yii::app()->user->setFlash('success', tt('Your details successfully changed.'));
				}
				$this->redirect(array('index'));
			}
		}

		$this->render('data',array(
			'model' => $model,
		));
	}

	public function actionChangepassword(){
		$this->setActiveMenu('my_changepassword');

		$model=$this->loadModel(Yii::app()->user->id);
		$from = Yii::app()->request->getParam('from');

		if(preg_match("/null\.io/i", $model->email))
			Yii::app()->user->setFlash('error', tt('Please change your email and password!', 'socialauth'));

		if(isset($_POST[$this->modelName])){
			$model->scenario = 'changePass';
			$model->attributes=$_POST[$this->modelName];

			if($model->validate()){
				$model->setPassword();
				$model->save(false);
				Yii::app()->user->setFlash('success', tt('Your password successfully changed.'));
				$this->redirect(array('index'));
			}
		}

		$this->render('changepassword', array(
			'model' => $model,
			'from' => $from,
		));
	}

	public function actionPayments(){
		$this->setActiveMenu('my_payments');

		if(Yii::app()->request->isAjaxRequest){
			$this->renderPartial('payments',array(
				'model' => User::model()->with('payments')->findByPk(Yii::app()->user->id)
			), false, true);
		} else {
			$this->render('payments',array(
				'model' => User::model()->with('payments')->findByPk(Yii::app()->user->id)
			));
		}
	}

	public function actionBalance(){
		$this->setActiveMenu('my_balance');

		$this->render('balance');
	}

	public function actionAgents(){
		$user = HUser::getModel();
		if($user->type != User::TYPE_AGENCY){
			throw404();
		}

		$this->setActiveMenu('my_agents');

		$model = new User('search');
		$model->myAgents();
		$model->with('countAdRel');

		$this->render('agents', array('model' => $model));
	}

	public function actionDeleteAgent($id){
		$user = HUser::getModel();
		if($user->type != User::TYPE_AGENCY){
			throw404();
		}

		$agent = User::model()->findByPk($id);
		$agent->agency_user_id = 0;
		$agent->update(array('agency_user_id'));

		Yii::app()->user->setFlash('success', Yii::t('common', 'This user "{name}" is not your agent anymore', array('{name}' => $agent->username)));

		$this->redirect(array('agents'));
	}

	public function actionAjaxSetAgentStatus(){
		if (Yii::app()->request->getParam('id') && (Yii::app()->request->getParam('value') != null)) {
			$status = Yii::app()->request->getParam('value', null);
			$id = Yii::app()->request->getParam('id', null);
			$user = User::model()->findByPk($id);

			$availableStatuses = User::getAgentStatusList();
			if (!array_key_exists($status, $availableStatuses) || !$user) {
				HAjax::jsonError();
			}

			$user->agent_status = $status;
			$user->update(array('agent_status'));
		}

		echo CHtml::link($availableStatuses[$status]);
	}
}