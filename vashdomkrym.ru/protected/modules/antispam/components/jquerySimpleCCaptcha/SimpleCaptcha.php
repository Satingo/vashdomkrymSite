<?php
class SimpleCaptcha {
	private $images = array(
	);

	private $salt = "$#!-j7%8W6JKG^d-";
	private $selectedLang = 'en';
	const MIN_IMAGES = 3;
	const DEFAULT_NUM_IMAGES = 5;
	private $sessionData = null;
	private $answer = null;

	public function __construct() {
		$this->fillImagesArray();
		$this->getDataFromSession();

		if (isset($_GET) && isset($_GET['lang'])) {
			$this->selectedLang = $_GET['lang'];
		}

		if (!$this->sessionData) {
			$this->resetSessionData();
		}
	}

	public function init() {
		$this->fillImagesArray();
		$this->getDataFromSession();

		if (isset($_GET) && isset($_GET['lang'])) {
			$this->selectedLang = $_GET['lang'];
		}

		if (!$this->sessionData) {
			$this->resetSessionData();
		}
	}

	public function fillImagesArray() {
		$this->images = array(
			'en' => array(
				'Gmail'        => Yii::getPathOfAlias('webroot.common.js.antispam.jquerySimpleCCaptcha.captchaImages').DIRECTORY_SEPARATOR.'001.png',
				'Youtube'          => Yii::getPathOfAlias('webroot.common.js.antispam.jquerySimpleCCaptcha.captchaImages').DIRECTORY_SEPARATOR.'002.png',
				'Twitter'         => Yii::getPathOfAlias('webroot.common.js.antispam.jquerySimpleCCaptcha.captchaImages').DIRECTORY_SEPARATOR.'003.png',
				'Google+'        => Yii::getPathOfAlias('webroot.common.js.antispam.jquerySimpleCCaptcha.captchaImages').DIRECTORY_SEPARATOR.'004.png',
				'Linkedin'          => Yii::getPathOfAlias('webroot.common.js.antispam.jquerySimpleCCaptcha.captchaImages').DIRECTORY_SEPARATOR.'005.png',
				'Chrome'          => Yii::getPathOfAlias('webroot.common.js.antispam.jquerySimpleCCaptcha.captchaImages').DIRECTORY_SEPARATOR.'006.png',
				'Skype'   => Yii::getPathOfAlias('webroot.common.js.antispam.jquerySimpleCCaptcha.captchaImages').DIRECTORY_SEPARATOR.'007.png',
				'Dropbox' => Yii::getPathOfAlias('webroot.common.js.antispam.jquerySimpleCCaptcha.captchaImages').DIRECTORY_SEPARATOR.'008.png',
				'Google Drive'        => Yii::getPathOfAlias('webroot.common.js.antispam.jquerySimpleCCaptcha.captchaImages').DIRECTORY_SEPARATOR.'009.png',
				'Github'        => Yii::getPathOfAlias('webroot.common.js.antispam.jquerySimpleCCaptcha.captchaImages').DIRECTORY_SEPARATOR.'010.png',
			),
			/*'ru' => array(
				'Gmail'        => Yii::getPathOfAlias('webroot.js.antispam.jquerySimpleCCaptcha.captchaImages').DIRECTORY_SEPARATOR.'001.png',
				'Youtube'          => Yii::getPathOfAlias('webroot.js.antispam.jquerySimpleCCaptcha.captchaImages').DIRECTORY_SEPARATOR.'002.png',
				'Twitter'         => Yii::getPathOfAlias('webroot.js.antispam.jquerySimpleCCaptcha.captchaImages').DIRECTORY_SEPARATOR.'003.png',
				'Google+'        => Yii::getPathOfAlias('webroot.js.antispam.jquerySimpleCCaptcha.captchaImages').DIRECTORY_SEPARATOR.'004.png',
				'Linkedin'          => Yii::getPathOfAlias('webroot.js.antispam.jquerySimpleCCaptcha.captchaImages').DIRECTORY_SEPARATOR.'005.png',
				'Chrome'          => Yii::getPathOfAlias('webroot.js.antispam.jquerySimpleCCaptcha.captchaImages').DIRECTORY_SEPARATOR.'006.png',
				'Skype'   => Yii::getPathOfAlias('webroot.js.antispam.jquerySimpleCCaptcha.captchaImages').DIRECTORY_SEPARATOR.'007.png',
				'Dropbox' => Yii::getPathOfAlias('webroot.js.antispam.jquerySimpleCCaptcha.captchaImages').DIRECTORY_SEPARATOR.'008.png',
				'Google Drive'        => Yii::getPathOfAlias('webroot.js.antispam.jquerySimpleCCaptcha.captchaImages').DIRECTORY_SEPARATOR.'009.png',
				'Github'        => Yii::getPathOfAlias('webroot.js.antispam.jquerySimpleCCaptcha.captchaImages').DIRECTORY_SEPARATOR.'010.png',
			)*/
		);
	}

	public function resetSessionData() {
		$this->sessionData = array(
			'time'   => time(),
			'images' => array(),
			'salt'   => null
		);
		$this->sessionData['salt'] = $this->salt . $this->sessionData['time'];
	}

	public function getImageByHash($hash) {
		if (isset($this->sessionData['images'][$hash])) {
			$fn = $this->sessionData['images'][$hash];


			if (file_exists($fn)) {
				$mime = null;
				if (function_exists("finfo_open")) {
					// PHP 5.3
					$finfo = finfo_open(FILEINFO_MIME_TYPE);
					$mime = finfo_file($finfo, $fn);

				} else if (function_exists("mime_content_type")) {
					// PHP 5.2
					$mime = mime_content_type($fn);
				}

				if (!$mime) { $mime = "image/png"; }

				header("Content-Type: {$mime}");
				readfile($fn);
				exit;

			} else {
				throw new InvalidArgumentException("That captcha image file does not exist", 404);
			}
		} else {
			throw new InvalidArgumentException("No captcha image exists with that hash: ".json_encode($this->sessionData->images), 404);
		}
	}

	public function getAllImageData() {
		$iData = array(
			'text'   => '',
			'images' => array()
		);

		if (!isset($this->images[$this->selectedLang]) || !is_array($this->images[$this->selectedLang]) || sizeof($this->images[$this->selectedLang]) < SimpleCaptcha::MIN_IMAGES) {
			throw new InvalidArgumentException("There aren\'t enough images on the server!", 400);
		}

		$numImages = SimpleCaptcha::DEFAULT_NUM_IMAGES;
		if (isset($_REQUEST['numImages']) &&
			intval($_REQUEST['numImages']) &&
			intval($_REQUEST['numImages']) > SimpleCaptcha::MIN_IMAGES) {
			$numImages = intval($_REQUEST['numImages']);
		}
		$totalSize = sizeof($this->images[$this->selectedLang]);
		$numImages = min(array($totalSize, $numImages));

		$keys = array_keys($this->images[$this->selectedLang]);
		$used = array();

		mt_srand(((float) microtime() * 587) / 33); // add some randomness

		for ($i=0; $i<$numImages; ++$i) {
			$r = rand(0, $totalSize-1);
			while (array_search($keys[$r], $used) !== false) {
				$r = rand(0, $totalSize-1);
			}
			array_push($used, $keys[$r]);
		}

		$iData['text'] = $used[rand(0, $numImages-1)];
		$this->answer = sha1($iData['text'] . $this->sessionData['salt']);

		shuffle($used);

		for ($i=0; $i<sizeof($used); ++$i) {
			$hash = sha1($used[$i] . $this->sessionData['salt']);
			array_push($iData['images'], $hash);
			$this->sessionData['images'][$hash] = $this->images[$this->selectedLang][$used[$i]];
		}

		return $iData;
	}


	public function writeSessionData() {
		Yii::app()->user->setState('simpleCaptchaAnswer', $this->answer);
		Yii::app()->user->setState('simpleCaptchaData', json_encode($this->sessionData));
		Yii::app()->user->setState('simpleCaptchaTimestamp', $this->sessionData['time']);
	}

	public function getDataFromSession() {
		if (Yii::app()->user->hasState("simpleCaptchaData")) {
			$this->sessionData = json_decode(Yii::app()->user->getState('simpleCaptchaData'), true);
		}

		if (Yii::app()->user->hasState("simpleCaptchaAnswer")) {
			$this->answer = Yii::app()->user->getState('simpleCaptchaAnswer');
		}
	}


	// Static helper methods

	public static function getProtocol() {
		$protocol = "HTTP/1.1";
		if(isset($_SERVER['SERVER_PROTOCOL'])) {
			$protocol = $_SERVER['SERVER_PROTOCOL'];
		}
		return $protocol;
	}

	public function setSelectedLanguage($lang = 'en') {
		if ($lang && isset($this->images[$lang])) {
			$this->selectedLang = $lang;
		}
		else
			$this->selectedLang = 'en';
	}

}