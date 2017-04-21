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
	public $modelName = 'FormDesigner';

	public function accessRules(){
		return array(
			array('allow',
				'expression'=> "Yii::app()->user->checkAccess('all_modules_admin')",
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}

    public function actionAdmin() {
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
        $this->render('admin',
            array_merge(array('model'=>$model), $this->params)
        );
    }

    public function actionVisible() {
        $id = Yii::app()->request->getParam('id', null);

        $model = $this->loadModel($id);

        $model->visible = $model->visible ? 0 : 1;
        $model->update('visible');

        if(!Yii::app()->request->isAjaxRequest){
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
        }
    }

    public function actionUpdate($id){
        $model = $this->loadModel($id);

        $this->performAjaxValidation($model);

        if(isset($_POST[$this->modelName])){
            $model->attributes=$_POST[$this->modelName];

            $model->scenario = 'save_types';

            if($model->save()){
				// delete assets js cache
				ConfigurationModel::clearGenerateJSAssets();
                Yii::app()->user->setFlash('success', tc('Success'));

                $this->redirect(array('admin'));
            }
        }

        $this->render('_setup_form',
            array('model'=>$model)
        );
    }
}