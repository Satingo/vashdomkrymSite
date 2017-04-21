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
	public $modelName = 'User';
	public $scenario = 'backend';

	public function accessRules(){
		return array(
			array('allow',
				'expression'=> "Yii::app()->user->checkAccess('users_admin')",
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}

	public function actionAdmin(){
		$this->rememberPage();
		
		$model = new $this->modelName('search');
		$model->resetScope();

		if($this->scenario){
			$model->scenario = $this->scenario;
		}

		if($this->with){
			$model = $model->with($this->with);
		}

		$model->unsetAttributes();  // clear any default values
		if(isset($_GET[$this->modelName])){
			$model->attributes=$_GET[$this->modelName];
		}

		$model->setRememberScenario('users_list_remember');

		$this->render('admin',
			array_merge(array('model'=>$model), $this->params)
		);
	}

	public function actionCreate(){
		$model=new $this->modelName;
		if($this->scenario){
			$model->scenario = $this->scenario;
		}

		if(isset($_POST[$this->modelName])){
			$model->attributes=$_POST[$this->modelName];
			$model->active = 1;
			if($model->validate()){
				$model->setPassword();
				$model->save(false);
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('create',array_merge(
				array('model'=>$model),
				$this->params
		));
	}

	public function actionUpdate($id){
		$model = $this->loadModel($id);
		$model->scenario = 'update';

		if (issetModule('rbac')) {
			if (Yii::app()->user->role == User::ROLE_MODERATOR && $model->role == User::ROLE_ADMIN)
				throw404();
		}

		$this->performAjaxValidation($model);

		if(isset($_POST[$this->modelName])){
			$model->attributes=$_POST[$this->modelName];

			if (isset($_POST[$this->modelName]['password']) && $_POST[$this->modelName]['password'])
				if (demo()) {
					Yii::app()->user->setFlash('error', tc('Sorry, this action is not allowed on the demo server.'));
					unset($model->password, $model->salt);
					$this->redirect(array('update','id'=>$model->id));
				} else
					$model->scenario = 'changePass';
			else
				unset($model->password, $model->salt);

			if($model->validate()) {
				if ($model->scenario == 'changePass')
					$model->setPassword();

				if($model->save(false)){
					$this->redirect(array('view','id'=>$model->id));
				}
			}
		}
		$this->render('update', array('model'=>$model));
	}

	public function actionView($id){
		$model = $this->loadModel($id);
		if ($model->role == User::ROLE_ADMIN) {
			$this->redirect(array('admin'));
		}

		$this->render('view',array(
			'model'=>$model,
		));
	}

	public static function returnStatusHtml($data, $tableId, $onclick = 0, $ignore = 0){
		if($ignore && ((is_array($ignore) && in_array($data->id, $ignore)) || $data->id == $ignore)){
			return '<div align="center">'.
			$img = CHtml::image(
					Yii::app()->theme->baseUrl.'/images/'.($data->active?'':'in').'active_grey.png',
					Yii::t('common', $data->active?'Active':'Inactive')).
				'</div>';
		}

		$url = Yii::app()->controller->createUrl("activate", array("id" => $data->id, 'action' => ($data->active==1?'deactivate':'activate') ));

		$img = CHtml::image(
			Yii::app()->theme->baseUrl.'/images/'.($data->active?'':'in').'active.png',
			Yii::t('common', $data->active?'Active':'Inactive'),
			array('title' => Yii::t('common', $data->active?'Deactivate':'Activate'))
		);
		$options = array();
		if($onclick){
			$options = array(
				'onclick' => 'ajaxSetStatus(this, "'.$tableId.'"); return false;',
			);
		}
		return '<div align="center">'.CHtml::link($img,$url, $options).'</div>';
	}

	public function actionActivate(){
		if(demo()){
            throw new CException(tc('Sorry, this action is not allowed on the demo server.'));
        }
		
		$field = isset($_GET['field']) ? $_GET['field'] : 'active';

		$action = $_GET['action'];
		$id = $_GET['id'];

		if(!(!$id && $action === null)){
			$model = $this->loadModel($id);

			if($this->scenario){
				$model->scenario = $this->scenario;
			}

			if($model) {
				$model->$field = ($action == 'activate'?1:0);
				$model->update(array($field));

				User::destroyUserSession($model->id);
			}
		}

		if(!Yii::app()->request->isAjaxRequest){
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
	}

    public function actionRecover($id){
        if(demo()){
            HAjax::jsonError(tc('Sorry, this action is not allowed on the demo server.'));
        }

        $model = $this->loadModel($id);

        $tempRecoverPassword = $model->randomString();
        $recoverPasswordKey = User::generateActivateKey();

        $model->temprecoverpassword = $tempRecoverPassword;
        $model->recoverPasswordKey = $recoverPasswordKey;
        $model->update(array('temprecoverpassword', 'recoverPasswordKey'));

        $model->recoverPasswordLink = Yii::app()->createAbsoluteUrl('/site/recover?key='.$recoverPasswordKey);

        // send email
        $notifier = new Notifier;
        $notifier->raiseEvent('onRecoveryPassword', $model, array('user' => $model));

        HAjax::jsonOk(Yii::t('module_notifier', 'A new password has been created and sent to {email}.', array(
            '{email}' => $model->email
        )));
    }
	
	public function actionDelete($id){
		if(demo()){
            throw new CException(tc('Sorry, this action is not allowed on the demo server.'));
        }
		
		if(Yii::app()->request->isPostRequest){
			$model = $this->loadModel($id);
			if ($model->role == User::ROLE_ADMIN) {
				throw404();
			}
			$model->delete();
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

}