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

class JquerysimpleccaptchaController extends CController {
	public function actionRenderImages() {
		$sc = new SimpleCaptcha();

		if (isset($_REQUEST['lang'])) {
			$sc->setSelectedLanguage($_REQUEST['lang']);
		}
		else {
			$sc->setSelectedLanguage = 'ru';
		}

		try {
			if (isset($_REQUEST['hash'])) {
				// Just getting one image file by it's hash
				$sc->getImageByHash($_REQUEST['hash']);

			} else {
				// Getting all image data and hashes
				$sc->resetSessionData();
				$imageData = $sc->getAllImageData();

				// Finish up by writing data to the session and the ouput buffer
				$sc->writeSessionData();
				header("Content-Type: application/json");
				echo json_encode($imageData);
			}

		} catch (InvalidArgumentException $iae) {
			$code = $iae->getCode();
			if (!$code) { $code = 400; }
			header(SimpleCaptcha::getProtocol()." {$code} ".$iae->getMessage());
			echo $iae->getMessage();

		} catch (Exception $e) {
			$code = $e->getCode();
			if (!$code) { $code = 500; }
			header(SimpleCaptcha::getProtocol()." {$code} ".$e->getMessage());
			echo $e->getMessage();
		}

		exit; // make sure we stop the script
	}
}