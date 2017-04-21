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


class Controller extends CController {

	public $layout = '//layouts/index';
	public $infoPages = array();
	public $menuTitle;
	public $menu = array();
	public $breadcrumbs = array();
	public $pageKeywords;
	public $pageDescription;
	public $adminTitle = '';
	public $aData;
	public $modelName;
	public $newFields;

	public $seoTitle;
	public $seoDescription;
	public $seoKeywords;

	/* advertising */
	public $advertPos1 = array();
	public $advertPos2 = array();
	public $advertPos3 = array();
	public $advertPos4 = array();
	public $advertPos5 = array();
	public $advertPos6 = array();

	public $apInComparison = array();
	public $assetsGenPath;
	public $assetsGenUrl;

	public $defaultTheme;
	public $baseUrl;
	public $baseThemeUrl;

	public $maxLengthSearch = 15; // максимальное количество слов во фразе
	public $minLengthSearch = 4; // минимальная длина искомого слова - http://dev.mysql.com/doc/refman/5.0/en/fulltext-fine-tuning.html

    public $showSearchForm = true;
	public $htmlPageId = 'inner';

    public $searchShowLabel = false;

	#### start for search
	public $selectedCountry;
	public $selectedRegion;
	public $selectedCity;
	public $selectedMetroStations;
	public $apType;
	public $objType;
	public $roomsCount;
	public $ot; // owner type
	public $wp; // with photo
	public $floorCountMin;
	public $floorCountMax;
	public $squareCountMin;
	public $squareCountMax;
	#### end for search

	public $currentUserIp;
	public $currentUserIpLong;

	public $datePickerLang;
	public $imagesForIndexPageSlider = array();

	public $geo;
	public $privatePolicyPage;
	
	public $adsForIndexMap = array();
	public $useAdditionalView;

    protected function beforeAction($action) {
		if(Yii::app()->request->enableCsrfValidation) {
			Yii::app()->clientScript->registerScript('ajax-csrf', '
				if(typeof jQuery != "undefined"){
					$.ajaxPrefilter(function(options, originalOptions, jqXHR){
						if(originalOptions.type){
							var type = originalOptions.type.toLowerCase();
						} else {
							var type = "";
						}

						if(type == "post" && typeof originalOptions.data === "object"){
							options.data = $.extend(originalOptions.data, { "' . Yii::app()->request->csrfTokenName . '": "' . Yii::app()->request->csrfToken . '" });
							options.data = $.param(options.data);
						}
					});
				}
			', CClientScript::POS_END, array());
		}

		if (!Yii::app()->user->checkAccess('backend_access')) {
			$currentController = Yii::app()->controller->id;
			$currentAction = Yii::app()->controller->action->id;

			if (!($currentController == 'site' && ($currentAction == 'login' || $currentAction == 'logout' || $currentAction == 'captcha'))) {
				if (issetModule('service')){
					$serviceInfo = Service::model()->findByPk(Service::SERVICE_ID);
					if ($serviceInfo && $serviceInfo->is_offline == 1) {
						$allowIps = explode(',', $serviceInfo->allow_ip);
						$allowIps = array_map("trim", $allowIps);

						if (!in_array(Yii::app()->request->userHostAddress, $allowIps)) {
							$this->renderPartial('//modules/service/views/index', array('page' => $serviceInfo->page), false, true);
							Yii::app()->end();
						}
					}
				}
			}
		}

		/* start  get page banners */
		if (issetModule('advertising') && !param('useBootstrap')) {
			$advert = new Advert;
			$advert->getAdvertContent();
		}
		/* end  get page banners */


		$this->checkCookieEnabled();

		return parent::beforeAction($action);
	}

	function init() {
		if (!oreInstall::isInstalled() && !(Yii::app()->controller->module && Yii::app()->controller->module->id == 'install')) {
			$this->redirect(array('/install'));
		}
		
		#Hsite::setSettingTimeZone();
		#Hsite::setSiteTimeZone();
		
		setLang();
        setCurrency();

		HGeo::init();

		$modulesToCheck = ConfigurationModel::getModulesList();
		foreach($modulesToCheck as $module){
			if(param('module_enabled_'.$module) === null){
				ConfigurationModel::createValue('module_enabled_'.$module, 0);
				Yii::app()->params['module_enabled_'.$module] = 0;
			}
		}
		unset($modulesToCheck);

		$this->assetsGenPath = Yii::getPathOfAlias('webroot.assets');
		$this->assetsGenUrl = Yii::app()->getBaseUrl(true).'/assets/';

		Yii::app()->user->setState('menu_active', '');

        $this->pageTitle = tt('siteName', 'seo');
        $this->pageKeywords = tt('siteKeywords', 'seo');
        $this->pageDescription = tt('siteDescription', 'seo');

		Yii::app()->name = $this->pageTitle;

		$this->defaultTheme = Themes::getDefaultTheme();
		if (!$this->defaultTheme)
			$this->defaultTheme = 'classic';

		Yii::app()->theme = $this->defaultTheme;
		
		$this->baseUrl = Yii::app()->baseUrl;
		$this->baseThemeUrl = Yii::app()->theme->baseUrl;

		if(Yii::app()->getModule('menumanager')){
			if(!(Yii::app()->controller->module && Yii::app()->controller->module->id == 'install')){
				$this->infoPages = Menu::getMenuItems(true, 2);
			}
		}

		$subItems = array();

		if(!Yii::app()->user->isGuest){
			$subItems = HUser::getMenu();
		}
		else {
			$subItems[] = array(
				'label' => tc('Login'),
				'url' => Yii::app()->createUrl('/site/login'),
				//'active' => Yii::app()->controller->menuIsActive('my_balance'),
			);
			if(param('useUserRegistration')) {
				$subItems[] = array(
					'label' => tc("Join now"),
					'url' => Yii::app()->createUrl('/site/register'),
				);
			}
			$subItems[] = array(
				'label' => tc('Forgot password?'),
				'url' => Yii::app()->createUrl('/site/recover'),
				//'active' => Yii::app()->controller->menuIsActive('my_balance'),
			);
		}

        $this->aData['userCpanelItems'] = Menu::getMenuItems(true, 1);

        $this->aData['userCpanelItems'][] = array(
            'label' => tt('Reserve apartment', 'common'),
            'url' => array('/booking/main/mainform'),
            'visible' => Yii::app()->user->checkAccess('backend_access') === false,
            'linkOptions' => array('class' => 'fancy mgp-open-ajax'),
            'itemOptions' => array('class' => 'depth_zero'),
        );

        $this->aData['userCpanelItems'][] = array(
				'label' => Yii::t('common', 'Control panel'),
				'url' => array('/usercpanel/main/index'),
				'visible' => Yii::app()->user->checkAccess('backend_access') === false,
				'items' => $subItems,
                'itemOptions' => array('class' => 'depth_zero'),
				'submenuOptions'=>array(
					'class'=>'sub_menu_dropdown'
				),
		);

		if(!Yii::app()->user->isGuest){
			$user = HUser::getModel();
			
			$this->aData['userCpanelItems'][] = array(
				'visible' => Yii::app()->user->checkAccess('apartments_admin') === true,
                'label' => Yii::t('common','Administration'),
                'url' => array('/apartments/backend/main/admin'),
                'itemOptions' => array('class' => 'depth_zero'),
            );
			
			$this->aData['userCpanelItems'][] = array(
                'label' => '(' . $user->username . ') ' . tt('Logout', 'common'),
                'url' => array('/site/logout'),
                'itemOptions' => array('class' => 'depth_zero'),
            );
		}

		$this->aData['topMenuItems'] = $this->infoPages;

		// comparison list
		if (issetModule('comparisonList')) {
			if (!Yii::app()->user->isGuest) {
				$resultCompare = ComparisonList::model()->findAllByAttributes(
					array(
						'user_id' => Yii::app()->user->id,
					)
				);
			}
			else {
				$resultCompare = ComparisonList::model()->findAllByAttributes(
					array(
						'session_id' => Yii::app()->session->sessionId,
					)
				);
			}

			if ($resultCompare) {
				foreach($resultCompare as $item) {
					$this->apInComparison[] = $item->apartment_id;
				}
			}
		}

		$this->currentUserIp = Yii::app()->request->getUserHostAddress();
		$this->currentUserIpLong = ip2long($this->currentUserIp);

		$this->datePickerLang = Yii::app()->language;
		if ($this->datePickerLang == 'en')
			$this->datePickerLang = 'en-GB';

        if(demo()){
            if(isset($_GET['theme']) && array_key_exists($_GET['theme'], Themes::getColorThemesList())){
                $theme = CHtml::encode($_GET['theme']);

                $cookie = new CHttpCookie('theme', $theme);
                $cookie->expire = time()+86400;
                Yii::app()->request->cookies['theme'] = $cookie;
            }
        }
				
		if (param('useShowInfoUseCookie'))
			$this->privatePolicyPage = InfoPages::model()->findByPk(InfoPages::PRIVATE_POLICY_PAGE_ID);

		$this->useAdditionalView = Themes::getParam('additional_view');
		
		if(demo()){
            if(isset($_GET['additional_view']) && array_key_exists($_GET['additional_view'], Themes::getAdditionalViewList())){
                $additionalView = (int) $_GET['additional_view'];

				$this->useAdditionalView = $additionalView;
				
                $cookie = new CHttpCookie('additional_view', $additionalView);
                $cookie->expire = time()+86400;
                Yii::app()->request->cookies['additional_view'] = $cookie;
            }
        }
		
		parent::init();
	}

	public function render($view,$data=null,$return=false) {
		if($this->beforeRender($view))
		{
			$output=$this->renderPartial($view,$data,true);
			if(($layoutFile=$this->getLayoutFile($this->layout))!==false) {
				$output=$this->renderFile($layoutFile,array('content'=>$output),true);
			}
			$this->afterRender($view,$output);
			$output=$this->processOutput($output);
			$output=call_user_func("processExecutableOutput", $output);
			if($return) {
				return $output;
			}
			else {
				echo $output;
			}
		}
	}
	
	public static function disableProfiler() {
		if (Yii::app()->getComponent('log')) {
			foreach (Yii::app()->getComponent('log')->routes as $route) {
				if (in_array(get_class($route), array('CProfileLogRoute', 'CWebLogRoute', 'YiiDebugToolbarRoute'))) {
					$route->enabled = false;
				}
			}
		}
	}

	public function createLangUrl($lang='en', $params = array()){
		$langs = Lang::getActiveLangs();

		if(count($langs) > 1 && issetModule('seo') && isset(SeoFriendlyUrl::$seoLangUrls[$lang])){
			if (count($params))
				return SeoFriendlyUrl::$seoLangUrls[$lang].'?'.http_build_query($params);

			return SeoFriendlyUrl::$seoLangUrls[$lang];
		}

		$route = Yii::app()->urlManager->parseUrl(Yii::app()->getRequest());
		$params = array_merge($_GET, $params);
		$params['lang'] = $lang;
		return $this->createUrl('/'.$route, $params);
	}

	public function excludeJs(){
		//Yii::app()->clientscript->scriptMap['*.js'] = false;
		Yii::app()->clientscript->scriptMap['jquery.js'] = false;
		Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
		Yii::app()->clientscript->scriptMap['jquery-ui.min.js'] = false;
		Yii::app()->clientscript->scriptMap['bootstrap.min.js'] = false;
		Yii::app()->clientscript->scriptMap['jquery-ui-i18n.min.js'] = false;
	}

	public static function getCurrentRoute(){
		$moduleId = isset(Yii::app()->controller->module) ? Yii::app()->controller->module->id.'/' : '';
		return trim($moduleId.Yii::app()->controller->getId().'/'.Yii::app()->controller->getAction()->getId());
	}

	public function setSeo(SeoFriendlyUrl $seo){
		$this->seoTitle = $seo->getStrByLang('title');
		if ($page = Yii::app()->request->getParam('page')) {
			$this->seoTitle .= ' - '. tt('Page', 'service'). ' '. (int) $page;
		}

		$this->seoDescription = $seo->getStrByLang('description');
		$this->seoKeywords = $seo->getStrByLang('keywords');
	}

	public function actionDeleteVideo($id = null, $apId = null) {
		if (Yii::app()->user->isGuest)
			throw404();

		if (!$id && !$apId)
			throw404();

		if (Yii::app()->user->checkAccess('backend_access')) {
			$modelVideo = ApartmentVideo::model()->findByPk($id);
			$modelVideo->delete();

			if (issetModule('historyChanges')) {
				HistoryChanges::addApartmentInfoToHistory('delete_video', (int) $apId, 'delete');
			}

			$this->redirect(array('/apartments/backend/main/update', 'id' => $apId));
		}
		else {
			$modelApartment = Apartment::model()->findByPk($apId);
			if(!$modelApartment->isOwner()){
				throw404();
			}

			$modelVideo = ApartmentVideo::model()->findByPk($id);
			$modelVideo->delete();

			if (issetModule('historyChanges')) {
				HistoryChanges::addApartmentInfoToHistory('delete_video', (int) $apId, 'delete');
			}

			$this->redirect(array('/userads/main/update', 'id' => $apId));
		}
	}
	public function actionDeletePanorama($id = null, $apId = null) {
		if (Yii::app()->user->isGuest)
			throw404();

		if (!$id && !$apId)
			throw404();

		if (Yii::app()->user->checkAccess('backend_access')) {
			$modelPanorama = ApartmentPanorama::model()->findByPk($id);
			$modelPanorama->delete();

			if (issetModule('historyChanges')) {
				HistoryChanges::addApartmentInfoToHistory('delete_panorama', (int) $apId, 'delete');
			}

			$this->redirect(array('/apartments/backend/main/update', 'id' => $apId));
		}
		else {
			$modelApartment = Apartment::model()->findByPk($apId);
			if(!$modelApartment->isOwner()){
				throw404();
			}

			$modelPanorama = ApartmentPanorama::model()->findByPk($id);
			$modelPanorama->delete();

			if (issetModule('historyChanges')) {
				HistoryChanges::addApartmentInfoToHistory('delete_panorama', (int) $apId, 'delete');
			}

			$this->redirect(array('/userads/main/update', 'id' => $apId));
		}
	}

	public static function returnBookingTableStatusHtml($data, $tableId, $onclick = 0, $ignore = 0){
		$statuses = Bookingtable::getAllStatuses();


		$user = HUser::getModel();
		// если это обычный пользователь возвращаем текстовый статус
		if(!in_array($user->role, array(User::ROLE_MODERATOR, User::ROLE_ADMIN)) && $data->active == Bookingtable::STATUS_NEED_PAY){
			return $statuses[$data->active];
		}

		$statuses = Bookingtable::getAllStatuses(true, true);
		$items = CJavaScript::encode($statuses);

		$options = array(
			'onclick' => 'ajaxSetBookingTableStatus(this, "'.$tableId.'", "'.$data->id.'", "'.$items.'"); return false;',
		);

		return '<div align="center" class="editable_select" id="editable_select-'.$data->id.'">'.CHtml::link($statuses[$data->active], '#' , $options).'</div>';
	}

	public function actionBookingTableActivate(){
		$field = isset($_GET['field']) ? $_GET['field'] : 'active';

		if (Yii::app()->request->getParam('id') && (Yii::app()->request->getParam('value') != null)) {
			$this->scenario = 'update_status';
			$action = Yii::app()->request->getParam('value', null);
			$id = Yii::app()->request->getParam('id', null);
			$availableStatuses = Bookingtable::getAllStatuses(true, true);

			if (!array_key_exists($action, $availableStatuses)) {
				$action = 0;
			}
		}

		if(!(!$id && $action === null)){
			$model = $this->loadModelUserBookingTable($id);

			if($this->scenario){
				$model->scenario = $this->scenario;
			}

			if($model){
				$oldStatus = $model->$field;

				$model->$field = $action;
				$model->save(false);

				if($field == 'active' && $oldStatus != $model->$field){
					$notifier = new Notifier();
					if($model->active == Bookingtable::STATUS_CONFIRM){
						$notifier->raiseEvent('onBookingConfirm', $model, array('user' => $model->sender));
					}elseif(isset($model->apartment->user)){
						$notifier->raiseEvent('onBookingChangeStatus', $model, array('user' => $model->apartment->user));
					}
				}

				if (issetModule('bookingcalendar')) {
					if ($field == 'active' && $action == Bookingtable::STATUS_CONFIRM) {
						Bookingcalendar::addRecord($model);
					}
				}
			}
		}

		echo CHtml::link($availableStatuses[$action]);
	}

	public function loadModelUserBookingTable($id) {
		$model = $this->loadModel($id);

		if (!Yii::app()->user->checkAccess('backend_access')) {
			$sql = 'SELECT id FROM {{apartment}} WHERE owner_id = "'.Yii::app()->user->id.'" ';
			$apIds = Yii::app()->db->createCommand($sql)->queryColumn();

			if(!in_array($model->apartment_id, $apIds)) {
				throw404();
			}
		}

		return $model;
	}

	public function setActiveMenu($key, $pos = 'cpanel'){
		$this->aData[$pos] = array();
		$this->aData[$pos][$key] = true;
	}

	public function menuIsActive($key, $pos = 'cpanel'){
		return isset($this->aData[$pos][$key]) && $this->aData[$pos][$key] === true;
	}

	public function protectEmail($email = null) {
		if ($email) {
			$characterSet = '+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz';
			$key = str_shuffle($characterSet);
			$cipherText = '';
			$id = 'e'.rand(1,999999999);

			for ($i=0; $i<strlen($email); $i+=1) {
				$cipherText.= $key[strpos($characterSet, $email[$i])];
			}

			$script = 'var a="'.$key.'";var b=a.split("").sort().join("");var c="'.$cipherText.'";var d="";';
			$script.= 'for(var e=0;e<c.length;e++)d+=b.charAt(a.indexOf(c.charAt(e)));';
			$script.= 'document.getElementById("'.$id.'").innerHTML="<a href=\\"mailto:"+d+"\\">"+d+"</a>"';

			$script = "eval(\"".str_replace(array("\\",'"'),array("\\\\",'\"'), $script)."\")";
			$script = '<script type="text/javascript">/*<![CDATA[*/'.$script.'/*]]>*/</script>';

			return '<span class="email" id="'.$id.'">[javascript protected email address]</span>'.$script;
		}
		return false;
	}

	public function checkCookieEnabled() {
		$templateNoCookie = $this->renderPartial(
			'//site/cookie-disabled',
			array(),
			true
		);

		//$.cookie("test_cookie", "cookie_value", { domain: "'.Yii::app()->request->serverName.'" });
		Yii::app()->clientScript->registerScript('checkCookieEnabled', '
			$.cookie("test_cookie", "cookie_value");

			if ($.cookie("test_cookie") != "cookie_value") {
				$.magnificPopup.open({
					items: {
						src: '.CJavaScript::encode($templateNoCookie).',
						type: "inline"
					}
				});
			}
		', CClientScript::POS_READY, array(), true);
	}

	public function getImagesForIndexSlider() {
		$sliderImgs = array();
		$paidImgs = (issetModule('paidservices')) ? PaidServices::getImgForSlider() : array();

		if (Yii::app()->theme->name == 'classic') {
			$width = param('slider_img_width', 500);
			$height = param('slider_img_height', 280);
		}
		else {			
			$width = 663;
			$height = 380;
			
			if ($this->useAdditionalView == Themes::ADDITIONAL_VIEW_FULL_WIDTH_SLIDER) {
				$width = 1300;
			}
			if ($this->useAdditionalView == Themes::ADDITIONAL_VIEW_FULL_WIDTH_SLIDER) {
				$height = 410;
			}
		}	

		if(issetModule('slider')) {
			$imagesSlider = Slider::model()->getActiveImages();

			if ($imagesSlider && count($imagesSlider)) {
				$randArr = array();
				foreach($imagesSlider as $imgSlider) {
					if ($imgSlider->img) {
						$rand = getRandomNumber(1, 17, $randArr);
						$randArr[] = $rand;

						$tmp = $imgSlider->getTitle();
						$title = (isset($tmp) && $tmp) ? $tmp : '';
						$link = isset($imgSlider->url) ? $imgSlider->url : '';
						$title = str_replace(array("\r", "\n"), '', $title);

						$sliderImgs[$rand]['url'] = $link;
						$sliderImgs[$rand]['title'] = $title;
						$sliderImgs[$rand]['src'] = Yii::app()->request->baseUrl."/". Slider::model()->sliderPath."/".$imgSlider->getThumb($width, $height);
						$sliderImgs[$rand]['width'] = $width;
						$sliderImgs[$rand]['height'] = $height;

						unset($tmp);
					}
				}
			}
		}
		else {
			$randArr = array();
			for($i=1; $i<=3; $i++) {
				$rand = getRandomNumber(1, 17, $randArr);
				$randArr[] = $rand;

				$sliderImgs[$rand]['url'] = '';
				$sliderImgs[$rand]['title'] = '';
				$sliderImgs[$rand]['src'] = Yii::app()->theme->baseUrl.'/images/slider/'.$i.'.jpg';
				$sliderImgs[$rand]['width'] = $width;
				$sliderImgs[$rand]['height'] = $height;
			}
		}

		$fullImgsSliderArr = CMap::mergeArray($paidImgs, $sliderImgs);
		shuffle($fullImgsSliderArr);

		return $fullImgsSliderArr;
	}
}