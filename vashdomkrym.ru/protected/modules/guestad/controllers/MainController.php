<?php

class MainController extends ModuleUserController {
    public function getViewPath($checkTheme=true){
        return Yii::getPathOfAlias('application.modules.'.$this->getModule($this->id)->getName().'.views');
    }

	public $htmlPageId = 'guestad';

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

	public function actionCreate(){
		if(!Yii::app()->user->isGuest){
			if(Yii::app()->user->checkAccess('backend_access')){
				$this->redirect(Yii::app()->createUrl('/apartments/backend/main/create'));
			}else{
				$this->redirect(Yii::app()->createUrl('/userads/main/create'));
			}
		}

		if (param('user_registrationMode') == 'without_confirm')
			$user = new User('register_without_confirm');
		else
			$user = new User('register');

		$login = new LoginForm();
		$model = new Apartment();
		$model->active = Apartment::STATUS_DRAFT;
		$model->period_activity = param('apartment_periodActivityDefault', 'always');
		$model->references = HApartment::getFullInformation($model->id, $model->type);
		$model = HGeo::setForAd($model);

		$seasonalPricesModel = null;
		$isAdmin = false;
		$activeTab = 'tab_register';
		$isUpdate = Yii::app()->request->getPost('is_update');

		if (!$isUpdate && isset($_POST['LoginForm']) && ( $_POST['LoginForm']['username'] || $_POST['LoginForm']['password'] )) {
			if (Yii::app()->user->getState('attempts-login') >= LoginForm::ATTEMPTSLOGIN) {
				$login->scenario = 'withCaptcha';
			}

			$activeTab = 'tab_login';
			$login->attributes = $_POST['LoginForm'];

			if ($login->validate() && $login->login()) {
				Yii::app()->user->setState('attempts-login', 0);

				User::updateUserSession();
				$isAdmin = Yii::app()->user->checkAccess('backend_access');
				$user = User::model()->findByPk(Yii::app()->user->id);
			}
			else {
				Yii::app()->user->setState('attempts-login', Yii::app()->user->getState('attempts-login', 0) + 1);

				if (Yii::app()->user->getState('attempts-login') >= LoginForm::ATTEMPTSLOGIN) {
					$login->scenario = 'withCaptcha';
				}
			}
		}

		if(isset($_POST['Apartment'])){
			$model->attributes = $_POST['Apartment'];

			if(!$isUpdate){
				$adValid = $model->validate();
				$userValid = false;

				if($activeTab == 'tab_register' && param('useUserRegistration')){
					$user->attributes = $_POST['User'];

					$userValid = $user->validate();
					if($adValid && $userValid){
						$user->activatekey = User::generateActivateKey();
						$userData = User::createUser($user->attributes);

						if ($userData) {
							$user = $userData['userModel'];

							$user->password = $userData['password'];
							$user->activatekey = $userData['activatekey'];
							$user->activateLink = $userData['activateLink'];

							$notifier = new Notifier;
							$notifier->raiseEvent('onNewUser_'.param('user_registrationMode'), $user, array('forceEmail'=>$user->email));

							if (param('user_registrationMode') == 'without_confirm') {

								if ($user->type == User::TYPE_AGENT && $user->agency_user_id) {
									$agency = User::model()->findByPk($user->agency_user_id);

									if ($agency) {
										$notifier = new Notifier();
										$notifier->raiseEvent('onNewAgent', $user, array(
											'forceEmail' => $agency->email,
										));
									}
								}
							}
						}
					}
				}

				if($user->id && (($activeTab == 'tab_login' && $adValid) || ($activeTab == 'tab_register' && param('useUserRegistration') && $adValid && $userValid))){
					if(param('useUseradsModeration', 1)){
						$model->active = Apartment::STATUS_MODERATION;
					} else {
						$model->active = Apartment::STATUS_ACTIVE;
					}
					$model->owner_active = Apartment::STATUS_ACTIVE;
					$model->owner_id = $user->id;

					if($model->save(false)){
						if(!$isAdmin && param('useUseradsModeration', 1)){
							Yii::app()->user->setFlash('success', tc('The listing is succesfullty added and is awaiting moderation'));
						} else {
							Yii::app()->user->setFlash('success', tc('The listing is succesfullty added'));
						}

						if($activeTab == 'tab_register'){
							if (param('user_registrationMode') == 'without_confirm') {
								$login = new LoginForm;
								$login->setAttributes(array('username' => $user['email'], 'password' => $user['password']));

								if ($login->validate() && $login->login()) {
									User::updateUserSession();
									User::updateLatestInfo(Yii::app()->user->id, Yii::app()->controller->currentUserIp);

									$this->redirect(array('/usercpanel/main/index'));
								}
								else {
									/*echo 'getErrors=<pre>';
									print_r($login->getErrors());
									echo '</pre>';
									exit;*/
									showMessage(Yii::t('common', 'Registration'), Yii::t('common', 'You were successfully registered.'));
								}
							}
							else {
								showMessage(Yii::t('common', 'Registration'), Yii::t('common', 'You were successfully registered. The letter for account activation has been sent on {useremail}', array('{useremail}' => $user['email'])));
							}
						}
						else {
							if ($isAdmin) {
								NewsProduct::getProductNews();
								$this->redirect(array('/apartments/backend/main/update', 'id' => $model->id));
								Yii::app()->end();
							} else {
								if (issetModule('seasonalprices') && $model->type == Apartment::TYPE_RENT) {
									# копирование информации в таблицу сезонных цен
									$seasonalPricesModel = new Seasonalprices;
									$seasonalPricesModel->apartment_id = $model->id;
									$seasonalPricesModel->price = $model->price;
									$seasonalPricesModel->price_type = $model->price_type;
									$seasonalPricesModel->date_start = 1;
									$seasonalPricesModel->month_start = 1;
									$seasonalPricesModel->date_end = 31;
									$seasonalPricesModel->month_end = 12;
									$seasonalPricesModel->date_created = new CDbExpression('NOW()');
									if(issetModule('currency')){
										$seasonalPricesModel->in_currency = Currency::getDefaultCurrencyModel()->char_code;
									}
									$seasonalPricesModel->save(false);
								}

								$this->redirect(array('/userads/main/update', 'id' => $model->id));
							}
						}
					}
				}

			}

		} else {
			$objTypes = array_keys(Apartment::getObjTypesArray());

			$model->setDefaultType();
			$model->obj_type_id = reset($objTypes);

			$user->unsetAttributes(array('verifyCode'));
		}

		HApartment::getCategoriesForUpdate($model);

		$user->unsetAttributes(array('verifyCode'));

		$this->render('create', array(
			'model' => $model,
			'user' => $user,
			'login' => $login,
			'activeTab' => $activeTab,
			'seasonalPricesModel' => $seasonalPricesModel,
		));
	}
}