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

class MainController extends CController {
	public $modelName = 'installForm';
	public $layout = '/layouts/main';

	public function getViewPath($checkTheme=true){
		if($checkTheme && ($theme=Yii::app()->getTheme())!==null){
			if (is_dir($theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'views'))
				return $theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'views';
		}
		return Yii::getPathOfAlias('application.modules.install.views');
	}

	protected function beforeAction($action){
		if(oreInstall::isInstalled()) {
			throw404();
		}

		$this->setLangInstall();

		return parent::beforeAction($action);
	}

	public function actionIndex(){
		if(isFree()){
			$this->redirect(array('config', 'lang' => param('langToInstall', 'en')));
		}
		$this->render('index');
	}

	public function actionConfig(){
		Controller::disableProfiler();
		$model = new InstallForm;

		if(isset($_POST['ajax']) && $_POST['ajax'] === 'install-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		$this->checkRequirements();
		$this->checkRightFile();

		if(isset($_POST['InstallForm'])) {
			$model->attributes = $_POST['InstallForm'];
			if($model->validate()) {
				// form inputs are valid, do something here
				try {
					$ds = DIRECTORY_SEPARATOR;
					$dbConfFile = Yii::app()->basePath . "{$ds}config{$ds}db.php";

					/*if(isFree()) {
						$sqlFile = $this->module->basePath . "{$ds}data{$ds}open-re.sql";
					} else {
						$sqlFile = $this->module->basePath . "{$ds}data{$ds}open-re-full.sql";
					}*/


					$connectionString = "mysql:host={$model->dbHost};dbname={$model->dbName};port={$model->dbPort}";
					$connection = new CDbConnection($connectionString, $model->dbUser, $model->dbPass);
					$connection->connectionString = $connectionString;
					$connection->username = $model->dbUser;
					$connection->password = $model->dbPass;
					$connection->emulatePrepare = true;
					$connection->charset = 'utf8';
					$connection->tablePrefix = $model->dbPrefix;
					$connection->active = true;

					Yii::app()->setComponent('db', $connection);

					$params = array(
						'components' => array(
							'db' => array(
								'class' => 'CDbConnection',
								'connectionString' => $connectionString,
								'username' => $model->dbUser,
								'password' => $model->dbPass,
								'emulatePrepare' => true,
								'charset' => 'utf8',
								'enableParamLogging' => false,
								'enableProfiling' => false,
								'schemaCachingDuration' => 7200,
								'tablePrefix' => $model->dbPrefix
							),
						),
						'language' => $model->language,
					);

					$dbConfString = "<?php\n return " . var_export($params, true) . " ;\n?>";

					$fh = fopen($dbConfFile, 'w+');

					if(!$fh) {
						$model->addError('', tFile::getT('module_install', 'Can not open config/db.php file for record!'));
					} else {

						fwrite($fh, $dbConfString);

						fclose($fh);

						@chmod($dbConfFile, 0666);

						$adminSalt = User::generateSalt();
						$adminPass = User::hashPassword($model->adminPass, $adminSalt);

						Yii::app()->user->setState('adminName', $model->adminName);
						Yii::app()->user->setState('adminPass', $adminPass);
						Yii::app()->user->setState('adminSalt', $adminSalt);
						Yii::app()->user->setState('adminEmail', $model->adminEmail);
						Yii::app()->user->setState('dbPrefix', $model->dbPrefix);
						Yii::app()->user->setState('siteName', $model->siteName);
						Yii::app()->user->setState('siteKeywords', $model->siteKeywords);
						Yii::app()->user->setState('siteDescription', $model->siteDescription);
						if(!isFree()) {
							Yii::app()->user->setState('installLang', $model->language);
						}

						$this->redirect(array('/install/main/install'));
					}

				} catch(Exception $e) {
					$model->addError('', $e->getMessage());
				}
			}
		}

		$this->render('install', array('model' => $model));
	}

	public function getFilePath(){
		if(isFree()) {
			return $this->module->basePath . DIRECTORY_SEPARATOR."data".DIRECTORY_SEPARATOR."open-re.sql";
		} else {
			return $this->module->basePath . DIRECTORY_SEPARATOR."data".DIRECTORY_SEPARATOR."open-re-full.sql";
		}
	}

	public function actionInstall(){
		if(!Yii::app()->user->hasState('adminName')){
			$this->redirect(array('/install/main/config'));
		}

		$parser = new MySQLParser();

		if (!file_exists($this->getFilePath()) || !filesize($this->getFilePath())) {
			Yii::app()->user->setFlash('error', 'no sql file');
			$this->redirect(array('/install/main/config'));
		}

		$parser->fileName = $this->getFilePath();
		$slices = $parser->getSliceCount();

		$this->render('install-db', array(
			'slices' => $slices,
		));
	}

	public function actionGetSlice($num){
		$parser = new MySQLParser();
		$parser->fileName = $this->getFilePath();

		$sql = $parser->getSlice($num);

		if($sql){
			$arrReplace = array(
				'{adminName}',
				'{adminPass}',
				'{adminSalt}',
				'{adminEmail}',
				'{dbPrefix}',
				'{siteName}',
				'{siteKeywords}',
				'{siteDescription}',
			);

			$arrReplaceVal = array(
				Yii::app()->user->getState('adminName'),
				Yii::app()->user->getState('adminPass'),
				Yii::app()->user->getState('adminSalt'),
				Yii::app()->user->getState('adminEmail'),
				Yii::app()->user->getState('dbPrefix'),
				Yii::app()->user->getState('siteName'),
				Yii::app()->user->getState('siteKeywords'),
				Yii::app()->user->getState('siteDescription'),
			);

			$sql = str_replace($arrReplace, $arrReplaceVal, $sql);

			$matched = preg_match("/CREATE TABLE IF NOT EXISTS `([^`]+)`/", $sql, $matches);
			if(!$matched){
				$matched = preg_match("/CREATE TABLE `([^`]+)`/", $sql, $matches);
			}

			if($matched){
				echo 'Creating table `'.$matches[1].'` ... ';
			}

			$command = Yii::app()->db->createCommand($sql);
			$command->execute();
			$command->reset();

			if($matched){
				echo 'OK<br/>';
			}
		}
	}

	public function actionFinalRequest(){
		if(!isFree()) {
			$prefix = Yii::app()->user->getState('dbPrefix');

			$nameISO = Yii::app()->user->getState('installLang');

			$sql = PHP_EOL . 'UPDATE ' . $prefix . 'lang SET main=0, admin_mail=0 WHERE 1;';
			$sql .= PHP_EOL . 'UPDATE ' . $prefix . 'lang SET main=1, admin_mail=1 WHERE name_iso="' . $nameISO . '";';

			Yii::app()->db->createCommand($sql)->execute();
		}

		sleep(1);

		Yii::app()->user->setFlash('success', tFile::getT('module_install', 'Settings of a database are successfully kept, the database is initialized.'));

		if(!@file_put_contents(ALREADY_INSTALL_FILE, 'ready')) {
			Yii::app()->user->setFlash('notice', tFile::getT('module_install', 'It was not possible to create the "protected\runtime\already_install" file, for avoidance of repeated installation, please, create it independently or disconnect the "Install" module right after installation.'));
		}	

		if(!isFree()) {
			if (isset($nameISO) && $nameISO) {
				$sql = 'SELECT currency_id FROM ' . $prefix . 'lang WHERE name_iso="' . $nameISO . '";';
				$currency_id = Yii::app()->db->createCommand($sql)->queryScalar();
				if($currency_id) {
					$sql = PHP_EOL . 'UPDATE ' . $prefix . 'currency SET is_default=0 WHERE is_default=1;';
					$sql .= PHP_EOL . 'UPDATE ' . $prefix . 'currency SET is_default=1 WHERE id="' . $currency_id . '";';

					Yii::app()->db->createCommand($sql)->execute();
					
					# for update currency
					// Currency::model()->parseExchangeRates();
					$data = Yii::app()->statePersister->load();
					if (isset($data['next_check_status'])) {
						$data['next_check_status'] = time() - BeginRequest::TIME_UPDATE;
						Yii::app()->statePersister->save($data);
					}
				}

				# update {dbPrefix}menu - {baseUrl}
				$sql = 'SELECT id, name_iso FROM ' . $prefix . 'lang';
				$allLangs = Yii::app()->db->createCommand($sql)->queryAll();
				if (is_array($allLangs) && count($allLangs)) {
					$allLangs = CHtml::listData($allLangs, 'id', 'name_iso');

					$fields = $where  = array();
					foreach($allLangs as $lang) {
						$fields[] = 'href_'.$lang;
						$where[] = 'href_'.$lang.' LIKE "{baseUrl}%"';
					}

					if (count($fields)) {
						$sql = 'SELECT id, '.implode(', ', $fields).' FROM ' . $prefix . 'menu WHERE '.implode('OR ', $where).' GROUP BY id';
						$allRecords = Yii::app()->db->createCommand($sql)->queryAll();

						if (is_array($allRecords) && count($allRecords)) {
							foreach($allRecords as $record) {
								if (is_array($record) && isset($record['id'])) {
									$menuModel = Menu::model()->findByPk($record['id']);
									if ($menuModel) {
										$attr = $attrValues = array();
										foreach ($record as $itemKey => $itemValue) {
											if ($itemKey != 'id') {
												$attr[] = $itemKey;
												$attrValues[] = $itemValue;
											}
										}

										# delete all prefixes
										foreach($allLangs as $lang) {
											foreach($attrValues as &$attrVal) {
												$attrVal = preg_replace('/\/'.$lang.'\/\b/iU', '/', $attrVal);
											}
										}

										# add prefixes except new default lang
										$fullAttrToValues = array_combine($attr, $attrValues);

										$tmpAllLangs = $allLangs;
										if(($keyToDel = array_search($nameISO, $tmpAllLangs)) !== false) {
											unset($tmpAllLangs[$keyToDel]);
										}

										foreach($tmpAllLangs as $lang) {
											if (array_key_exists('href_'.$lang, $fullAttrToValues)) {
												$value = $fullAttrToValues['href_'.$lang];
												$value = preg_replace('/{baseUrl}\/\b/i', '{baseUrl}/'.$lang.'/', $value);
												$fullAttrToValues['href_'.$lang] = $value;
											}
										}

										if ($fullAttrToValues && count($fullAttrToValues)) {
											$setsArr = array();
											foreach($fullAttrToValues as $attr => $value) {
												$setsArr[] = $attr. ' = "'. $value.'" ';
											}
											$sql = 'UPDATE '. $prefix . 'menu SET '.implode(', ', $setsArr) .' WHERE id = '.$menuModel->id;
											Yii::app()->db->createCommand($sql)->execute();
										}
									}
								}
							}
						}
					}
				}
			}
		}

		if (isset($_SERVER['HTTP_HOST']) || isset($_SERVER['REQUEST_URI'])) {
			if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['REQUEST_URI']))
				$license = @file_get_contents('http://re.monoray.ru/license.php?host='.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			elseif (isset($_SERVER['HTTP_HOST']))
				$license = @file_get_contents('http://re.monoray.ru/license.php?host='.$_SERVER['HTTP_HOST']);
		}
				
		Yii::app()->cache->flush();

		echo 'Completed.<br/>Redirecting...';
	}

	public function actionSuccess(){
		$this->render('success');
	}

	public function checkRequirements(){

		$requirements = array(
			array(
				Yii::t('yii', 'PHP version'),
				true,
				version_compare(PHP_VERSION, "5.3.0", ">="),
				'<a href="http://www.yiiframework.com">Yii Framework</a>',
				Yii::t('yii', 'PHP 5.3.0 or higher is required.')),
			array(
				Yii::t('yii', 'Reflection extension'),
				true,
				class_exists('Reflection', false),
				'<a href="http://www.yiiframework.com">Yii Framework</a>',
				''),
			array(
				Yii::t('yii', 'PCRE extension'),
				true,
				extension_loaded("pcre"),
				'<a href="http://www.yiiframework.com">Yii Framework</a>',
				''),
			array(
				Yii::t('yii', 'SPL extension'),
				true,
				extension_loaded("SPL"),
				'<a href="http://www.yiiframework.com">Yii Framework</a>',
				''),
			array(
				Yii::t('yii', 'DOM extension'),
				false,
				class_exists("DOMDocument", false),
				'<a href="http://www.yiiframework.com/doc/api/CHtmlPurifier">CHtmlPurifier</a>, <a href="http://www.yiiframework.com/doc/api/CWsdlGenerator">CWsdlGenerator</a>',
				''),
			array(
				Yii::t('yii', 'cURL extension'),
				true,
				extension_loaded('curl'),
				'<a href="http://www.php.net/manual/en/book.curl.php">cURL</a>',
				'This is required for geocoding and automatic translate'),
			array(
				Yii::t('yii', 'PDO extension'),
				true,
				extension_loaded('pdo'),
				Yii::t('yii', 'All <a href="http://www.yiiframework.com/doc/api/#system.db">DB-related classes</a>'),
				''),
			array(
				Yii::t('yii', 'PDO MySQL extension'),
				true,
				extension_loaded('pdo_mysql'),
				Yii::t('yii', 'All <a href="http://www.yiiframework.com/doc/api/#system.db">DB-related classes</a>'),
				Yii::t('yii', 'This is required if you are using MySQL database.')),
			array(
				Yii::t('yii', 'PDO PostgreSQL extension'),
				false,
				extension_loaded('pdo_pgsql'),
				Yii::t('yii', 'All <a href="http://www.yiiframework.com/doc/api/#system.db">DB-related classes</a>'),
				Yii::t('yii', 'This is required if you are using PostgreSQL database.')),
			array(
				Yii::t('yii', 'Memcache extension'),
				false,
				extension_loaded("memcache"),
				'<a href="http://www.yiiframework.com/doc/api/CMemCache">CMemCache</a>',
				''),
			array(
				Yii::t('yii', 'APC extension'),
				false,
				extension_loaded("apc"),
				'<a href="http://www.yiiframework.com/doc/api/CApcCache">CApcCache</a>',
				''),
			array(
				Yii::t('yii', 'Mcrypt extension'),
				false,
				extension_loaded("mcrypt"),
				'<a href="http://www.yiiframework.com/doc/api/CSecurityManager">CSecurityManager</a>',
				Yii::t('yii', 'This is required by encrypt and decrypt methods.')),
			array(
				Yii::t('yii', 'SOAP extension'),
				false,
				extension_loaded("soap"),
				'<a href="http://www.yiiframework.com/doc/api/CWebService">CWebService</a>, <a href="http://www.yiiframework.com/doc/api/CWebServiceAction">CWebServiceAction</a>',
				'')
		);

		$result = 1;

		foreach($requirements as $i => $requirement) {
			if($requirement[1] && !$requirement[2]) {
				$result = 0;
			} else {
				if($result > 0 && !$requirement[1] && !$requirement[2]) {
					$result = -1;
				}
			}
			if($requirement[4] === '') {
				$requirements[$i][4] = '&nbsp;';
			}
		}

		$arr = array(
			'result' => $result,
			'requirements' => $requirements
		);

		if($result == 0) {
			$this->render('requirements', array('req' => $arr));
			Yii::app()->end();
		} else {
			return $arr;
		}
	}

	function checkRightFile(){
		$ds = DIRECTORY_SEPARATOR;

		if(!isFree()) {
			$aCheckDir = array(
				ROOT_PATH . $ds . 'assets',
				ROOT_PATH . $ds . 'protected' . $ds . 'config' . $ds . 'db.php',
				ROOT_PATH . $ds . 'protected' . $ds . 'runtime',
				ROOT_PATH . $ds . 'uploads',
				ROOT_PATH . $ds . 'uploads' . $ds . 'ava',
				ROOT_PATH . $ds . 'uploads' . $ds . 'editor',
				ROOT_PATH . $ds . 'uploads' . $ds . 'iconsmap',
				ROOT_PATH . $ds . 'uploads' . $ds . 'iecsv',
				ROOT_PATH . $ds . 'uploads' . $ds . 'messages',
				ROOT_PATH . $ds . 'uploads' . $ds . 'entries',
				ROOT_PATH . $ds . 'uploads' . $ds . 'objects',
				ROOT_PATH . $ds . 'uploads' . $ds . 'qrcodes',
				ROOT_PATH . $ds . 'uploads' . $ds . 'rkl',
				ROOT_PATH . $ds . 'uploads' . $ds . 'slider',
				ROOT_PATH . $ds . 'uploads' . $ds . 'slider' . $ds . 'thumb',
				ROOT_PATH . $ds . 'uploads' . $ds . 'video',
			);
		} else {
			$aCheckDir = array(
				ROOT_PATH . $ds . 'assets',
				ROOT_PATH . $ds . 'protected' . $ds . 'config' . $ds . 'db.php',
				ROOT_PATH . $ds . 'protected' . $ds . 'runtime',
				ROOT_PATH . $ds . 'uploads',
				ROOT_PATH . $ds . 'uploads' . $ds . 'ava',
				ROOT_PATH . $ds . 'uploads' . $ds . 'editor',
				ROOT_PATH . $ds . 'uploads' . $ds . 'iconsmap',
				ROOT_PATH . $ds . 'uploads' . $ds . 'entries',
				ROOT_PATH . $ds . 'uploads' . $ds . 'objects',
				ROOT_PATH . $ds . 'uploads' . $ds . 'qrcodes',
				ROOT_PATH . $ds . 'uploads' . $ds . 'video',
			);
		}

		$aCheckDirErr = array(
			'err' => 0
		);
		foreach($aCheckDir as $sDirPath) {
			if(is_writable($sDirPath)) {
				$aCheckDirErr['dirs'][$sDirPath] = 'ok';
			} else {
				$aCheckDirErr['err']++;
				if(is_file($sDirPath)) {
					$aCheckDirErr['dirs'][$sDirPath] = tFile::getT('module_install', 'It is necessary to establish the rights') . ' 666';
				} else {
					$aCheckDirErr['dirs'][$sDirPath] = tFile::getT('module_install', 'It is necessary to establish the rights') . ' 777';
				}
			}
		}
		if($aCheckDirErr['err'] > 0) {
			$this->render('right_file', array('aCheckDirErr' => $aCheckDirErr));
			Yii::app()->end();
		} else {
			return $aCheckDirErr;
		}
	}

	public function isAssetsIsWritable(){
		$path = ROOT_PATH . DIRECTORY_SEPARATOR . 'assets';
		return is_writable($path);
	}

	public function createLangUrl($lang = 'en', $params = array()){
		$route = Yii::app()->urlManager->parseUrl(Yii::app()->getRequest());
		$params = array_merge($_GET, $params);
		$params['lang'] = $lang;
		$url = $this->createUrl('/' . $route, $params);
		return $url;
	}

	public function setLangInstall(){
		$app = Yii::app();
		$user = $app->user;

		$lang = 'en';
		$activeLangs = array('en' => 'en', 'ru' => 'ru', 'de' => 'de');

		$app->setLanguage($lang);

		if(isset($_GET['lang'])) {
			$tmplang = $_GET['lang'];
			if(isset($activeLangs[$tmplang])) {
				$lang = $tmplang;
				$app->setLanguage($lang);
			}
			$this->setLangCookieInstall($lang);
		} else {
			if($user->hasState('_lang')) {
				$tmplang = $user->getState('_lang');

				if(isset($activeLangs[$tmplang])) {
					$lang = $tmplang;
					$app->setLanguage($lang);
				} else {
					$this->setLangCookieInstall($lang);
				}
			} else {
				if(isset($app->request->cookies['_lang'])) {
					$tmplang = $app->request->cookies['_lang']->value;
					if(isset($activeLangs[$tmplang])) {
						$lang = $tmplang;
						$app->setLanguage($lang);
					} else {
						$this->setLangCookieInstall($lang);
					}
				}
			}
		}
	}

	public function setLangCookieInstall($lang = 'en'){
		Yii::app()->user->setState('_lang', $lang);
		$cookie = new CHttpCookie('_lang', $lang);
		$cookie->expire = time() + (60 * 60 * 24 * 365); // (1 year)
		Yii::app()->request->cookies['_lang'] = $cookie;
	}
}