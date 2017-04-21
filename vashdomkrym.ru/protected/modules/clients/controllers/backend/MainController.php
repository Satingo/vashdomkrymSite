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

class MainController extends ModuleAdminController {
	public $modelName = 'Clients';

	public function accessRules(){
		return array(
			array('allow',
				'expression'=> "Yii::app()->user->checkAccess('clients_admin')",
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}

    public function actionAdmin(){
		$this->rememberPage();

		$model = new Clients('search');

		$this->render('admin',array_merge(array('model'=>$model), $this->params));
    }

	public function actionUpdate($id){
		$model = $this->loadModel($id);

		if (!$model)
			throw404();

		//Yii::app()->user->setState('menu_active', 'clients.update');
		Yii::app()->user->setState('menu_active', 'clients');

		$this->performAjaxValidation($model);

		if(isset($_POST[$this->modelName])){
			$model->attributes=$_POST[$this->modelName];
			if($model->save()){
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('update', array('model'=>$model));
	}

	public function actionCreate(){
		$model = new $this->modelName;
		//Yii::app()->user->setState('menu_active', 'clients.create');
		Yii::app()->user->setState('menu_active', 'clients');

		$this->performAjaxValidation($model);

		if(isset($_POST[$this->modelName])){
			$model->attributes=$_POST[$this->modelName];
			if($model->save()){
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('create', array('model'=>$model));
	}


	public function returnControllerClientStateHtml($data, $tableId) {
		$states = Clients::getClientsStatesArray();

		$items = CJavaScript::encode($states);

		$options = array(
			'onclick' => 'ajaxSetModerationClientState(this, "'.$tableId.'", "'.$data->id.'", "'.$items.'"); return false;',
		);

		return '<div align="center" class="editable_select" id="editable_select_state-'.$data->id.'">'.CHtml::link($states[$data->state], '#' , $options).'</div>';

	}

	public function actionActivateClientState(){
		$field = isset($_GET['field']) ? $_GET['field'] : 'state';

		$this->scenario = 'update_client_state';
		$action = Yii::app()->request->getParam('value', null);
		$id = Yii::app()->request->getParam('id', null);
		$availableStates = Clients::getClientsStatesArray();

		if (!array_key_exists($action, $availableStates)) {
			$action = 0;
		}

		if(!(!$id && $action === null)){
			$model = $this->loadModel($id);

			if($this->scenario){
				$model->scenario = $this->scenario;
			}

			if($model){
				$model->$field = $action;
				$model->save(false);
			}
		}

		echo CHtml::link($availableStates[$action]);
	}
}