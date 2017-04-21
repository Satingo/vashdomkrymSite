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
	public $modelName = 'Apartment';

	public function accessRules(){
		return array(
			array('allow',
				'expression'=> "Yii::app()->user->checkAccess('apartments_admin')",
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}

	public function actionView($id = 0) {
		//$this->layout='//layouts/inner';

		Yii::app()->bootstrap->plugins['tooltip'] = array(
			'selector'=>' ', // bind the plugin tooltip to anchor tags with the 'tooltip' class
			'options'=>array(
				'placement'=>'top', // place the tooltips below instead
			),
		);

		$model = $this->loadModelWith(array('windowTo', 'objType', 'city'));

		if (!in_array($model->type, HApartment::availableApTypesIds()) || !in_array($model->price_type, array_keys(HApartment::getPriceArray(Apartment::PRICE_SALE, true)))) {
			throw404();
		}

		$this->render('view', array(
			'model' => $model,
			'statistics' => Apartment::getApartmentVisitCount($model),
		));
	}

	public function actionAdmin(){
		$countNewsProduct = NewsProduct::getCountNoShow();
		if($countNewsProduct > 0) {
			Yii::app()->user->setFlash('info', Yii::t('common', 'There are new product news') . ': '
				. CHtml::link(Yii::t('common', '{n} news', $countNewsProduct), array('/entries/backend/main/product')));
		}

		$this->rememberPage();

		$this->getMaxSorter();
		$this->getMinSorter();

		$model = new Apartment('search');
		$model->resetScope();

		$model->unsetAttributes();  // clear any default values
		if(isset($_GET[$this->modelName])){
			$model->attributes=$_GET[$this->modelName];
		}

		if (isset($_GET['resetFilters']) && $_GET['resetFilters'] == 1) {
			unset($_GET['resetFilters']);
			
			$model->unsetFilters();
			
			$attributes = $model->getSafeAttributeNames();
			if (is_array($attributes) && !empty($attributes)) {
				$toUnsetArr = array();
				$modelName = get_class($model);
				$prefix = $modelName.'ads_remember';
				foreach ($attributes as $attribute) {
					if (null != ($value = Yii::app()->user->getState($prefix . $attribute, null))) {
						$toUnsetArr[$attribute] = Yii::app()->user->getState($prefix . $attribute, null);
					}
				}
						
				if(!empty($toUnsetArr)) {
					$_GET['Apartment'] = array();
					foreach ($toUnsetArr as $key => $value) {
						$_GET['Apartment'][$key] = '';
					}
				}
			}
												
			$model->setRememberScenario('ads_remember');
			$this->redirect(array('admin'));
		}
		
		$model->setRememberScenario('ads_remember');

		$loadModelWith = array('user', 'objType', 'city');
		if (issetModule('location'))
			$loadModelWith = array('user', 'locCity', 'locRegion', 'locCountry', 'objType');
		
		$model = $model->with($loadModelWith);

		$this->params['paidServicesArray'] = array();
		if(issetModule('paidservices')) {
			$paidServices = PaidServices::model()->findAll('id != '.PaidServices::ID_ADD_FUNDS);
			$this->params['paidServicesArray'] = CHtml::listData($paidServices, 'id', 'name');
		}

		$this->render('admin',array_merge(array('model'=>$model), $this->params));
	}

	public function getMaxSorter(){
		$model = new $this->modelName;
		$maxSorter = Yii::app()->db->createCommand()
			->select('MAX(sorter) as maxSorter')
			->from($model->tableName())
			->where('active <> '.Apartment::STATUS_DRAFT)
			->queryScalar();
		$this->params['maxSorter'] = $maxSorter;
		return $maxSorter;
	}

	public function getMinSorter(){
		$model = new $this->modelName;
		$minSorter = Yii::app()->db->createCommand()
			->select('MIN(sorter) as maxSorter')
			->from($model->tableName())
			->where('active <> '.Apartment::STATUS_DRAFT)
			->queryScalar();
		$this->params['minSorter'] = $minSorter;
		return $minSorter;
	}

	public function actionClone($id) {
		$model = $this->loadModel($id);
		if(!$model){
			throw404();
		}

		$model->makeClone();
		// if AJAX request (triggered by deletion via grid view), we should not redirect the browser
		if (!Yii::app()->request->isAjaxRequest) {
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}

	}

	public function actionRestore($id) {
		$model = $this->loadModel($id);

		if(!$model){
			throw404();
		}

		$model->deleted = 0;
		$model->update(array('deleted'));
		// if AJAX request (triggered by deletion via grid view), we should not redirect the browser
		if (!Yii::app()->request->isAjaxRequest) {
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}

	}

	public function actionUpdate($id){
        $this->_model = $this->loadModel($id);

        if(!$this->_model) { throw404(); }

        $oldStatus = $this->_model->active;
		$priceTypesArr = HApartment::getPriceArray($this->_model->type);

        if(issetModule('bookingcalendar')) {
			$this->_model = $this->_model->with(array('bookingCalendar'));
		}

        if(isset($_GET['type'])){
            $this->_model->type = HApartment::getRequestType();
			
			$priceTypesArr = HApartment::getPriceArray($this->_model->type);
			reset($priceTypesArr);
			$this->_model->price_type = current($priceTypesArr);		
        }

		$this->_model = HGeo::setForAd($this->_model);
		
		if (issetModule('metroStations')) {
			$this->_model->metroStations = MetroStations::getMetroStations($this->_model->id);
		}

		if(isset($_POST[$this->modelName])){
			$this->_model->attributes = $_POST[$this->modelName];

			if ($this->_model->type != Apartment::TYPE_BUY && $this->_model->type != Apartment::TYPE_RENTING) {
				// video, panorama, lat, lon
                HApartment::saveOther($this->_model);
			}

			$this->_model->scenario = 'savecat';

			$isUpdate = Yii::app()->request->getPost('is_update');
			$this->_model->isAjaxLoadOnUpdate = $isUpdate;
			$this->_model->date_manual_updated = new CDbExpression('NOW()');

			if($isUpdate){
				$this->_model->active = $oldStatus;

				reset($priceTypesArr);
				$this->_model->price_type = current($priceTypesArr);
				
				$this->_model->save(false);
			} 
			elseif($this->_model->validate()) {
				if (issetModule('metroStations')) {
					MetroStations::setMetroStations($this->_model->id, $this->_model->metroStations);
				}
				
				$this->_model->save(false);

				Yii::app()->user->setFlash('success', tc('Success'));
				$this->redirect(array('update','id'=>$this->_model->id));
			}
		}

        HApartment::getCategoriesForUpdate($this->_model);

		$seasonalPricesModel = null;
		if (issetModule('seasonalprices')) {
			$seasonalPricesModel = new Seasonalprices;
		}

        if($this->_model->active == Apartment::STATUS_DRAFT){
			Yii::app()->user->setState('menu_active', 'apartments.create');
			$this->render('create', array(
				'model' => $this->_model,
				'supportvideoext' => ApartmentVideo::model()->supportExt,
				'supportvideomaxsize' => ApartmentVideo::model()->fileMaxSize,
				'seasonalPricesModel' => $seasonalPricesModel,
			));
			return;
		}

		$this->render('update', array(
			'model' => $this->_model,
			'supportvideoext' => ApartmentVideo::model()->supportExt,
			'supportvideomaxsize' => ApartmentVideo::model()->fileMaxSize,
			'seasonalPricesModel' => $seasonalPricesModel,
		));
	}


	public function actionCreate(){
		$model = new $this->modelName;
		$model->active = Apartment::STATUS_DRAFT;
		$model->owner_active = Apartment::STATUS_ACTIVE;
        $model->setDefaultType();
		$model->date_manual_updated = new CDbExpression('NOW()');
		$model->save(false);

		$this->redirect(array('update', 'id' => $model->id));
	}

	public function getWindowTo(){
		$sql = 'SELECT id, title_'.Yii::app()->language.' as title FROM {{apartment_window_to}}';
		$results = Yii::app()->db->createCommand($sql)->queryAll();
		$return = array();
		$return[0] = '';
		if($results){
			foreach($results as $result){
				$return[$result['id']] = $result['title'];
			}
		}
		return $return;
	}

	public function actionSavecoords($id){
		if(param('useGoogleMap', 1) || param('useYandexMap', 1) || param('useOSMMap', 1)){
			$apartment = $this->loadModel($id);
			if(isset($_POST['lat']) && isset($_POST['lng'])){
				$apartment->lat = floatval($_POST['lat']);
				$apartment->lng = floatval($_POST['lng']);
				$apartment->update(array('lat', 'lng'));
			}
			Yii::app()->end();
		}
	}

	public function actionGmap($id, $model = null){
		if($model === null){
			$model = $this->loadModel($id);
		}
		$result = CustomGMap::actionGmap($id, $model, $this->renderPartial('_marker', array('model' => $model), true), true);

		if($result){
			return $this->renderPartial('_gmap', $result, true);
		}
		return '';
	}

	public function actionYmap($id, $model = null){

		if($model === null){
			$model = $this->loadModel($id);
		}

		$result = CustomYMap::init()->actionYmap($id, $model, $this->renderPartial('_marker', array('model' => $model), true));

		if($result){
			//return $this->renderPartial('backend/_ymap', $result, true);
		}
		return '';
	}

	public function actionOSmap($id, $model = null){
		if($model === null){
			$model = $this->loadModel($id);
		}
		$result = CustomOSMap::actionOSmap($id, $model, $this->renderPartial('_marker', array('model' => $model), true));

		if($result){
			return $this->renderPartial('_osmap', $result, true);
		}
		return '';
	}

	public function actionSortItems() {
		if (isset($_POST['items']) && is_array($_POST['items'])) {
			//$thisModel = call_user_func($this->modelName, 'model');

			//$cur_items = $thisModel::model()->findAllByPk($_POST['items'], array('order'=>'sorter'));
			$cur_items = CActiveRecord::model($this->modelName)->findAllByPk($_POST['items'], array('order'=>'sorter DESC'));

			for ($i = 0; $i < count($_POST['items']); $i++) {
				//$item = $thisModel::model()->findByPk($_POST['items'][$i]);

				$item = CActiveRecord::model($this->modelName)->findByPk($_POST['items'][$i]);

				if ($item->sorter != $cur_items[$i]->sorter) {
					$item->sorter = $cur_items[$i]->sorter;
					$item->save(false);
				}
			}
		}
	}

	public function actionChooseNewOwner() {
		$apId = Yii::app()->request->getParam('id');

		if (!$apId)
			throw404();

		$modelApartment = Apartment::model()->findByPk($apId);
		if (!$modelApartment)
			throw404();

		$this->modelName = 'ChangeOwner';
		$model = new ChangeOwner;

		$modelUser = new User('search');
		$modelUser->resetScope();
		$modelUser->unsetAttributes();  // clear any default values
		if(isset($_GET['User'])){
			$modelUser->attributes = $_GET['User'];
		}
		$modelUser->active = 1;

		if (Yii::app()->request->isPostRequest) {
			if(isset($_POST)) {
				$futureOwner = (isset($_POST['itemsSelected']) && isset($_POST['itemsSelected'][0])) ? $_POST['itemsSelected'][0] : '';
				$futureApartments = array($apId);

				$model->setAttributes(
					array(
						'futureOwner' => $futureOwner,
						'futureApartments' => array($apId)
					)
				);

				if($model->validate()){
					if ($futureOwner && is_array($futureApartments)) {
						$sql = 'UPDATE {{apartment}} SET owner_id = '.$futureOwner. ' WHERE id IN ('.implode(', ', $futureApartments).')';
						Yii::app()->db->createCommand($sql)->execute();

						$sql = 'UPDATE {{images}} SET id_owner = '.$futureOwner. ' WHERE id_object IN ('.implode(', ', $futureApartments).')';
						Yii::app()->db->createCommand($sql)->execute();

						Yii::app()->cache->flush();

						Yii::app()->user->setFlash('success', tc('Success'));

						Yii::app()->controller->redirect(array('admin'));
					}
				}
			}
		}

		$renderData = array(
			'apId' => $apId,
			'model' => $model,
			'modelUser' => $modelUser,
			'modelApartment' => $modelApartment,
		);

		if(Yii::app()->request->isAjaxRequest){
			$this->renderPartial('change_owner', $renderData, false, true);
		}
		else{
			$this->render('change_owner', $renderData);
		}
	}
}