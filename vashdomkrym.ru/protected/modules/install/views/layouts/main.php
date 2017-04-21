<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
	<link href='http://fonts.googleapis.com/css?family=Roboto:400,300,700,500&subset=latin,cyrillic-ext,greek-ext,greek,vietnamese,latin-ext,cyrillic' rel='stylesheet' type='text/css'>

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/screen.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/print.css" media="print" />

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/form.css" />
	<link media="screen" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/styles.css" rel="stylesheet" />

	<link rel="icon" href="<?php echo Yii::app()->request->baseUrl; ?>/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/favicon.ico" type="image/x-icon" />

	<title><?php echo tFile::getT('module_install', 'Open Real Estate').' - '.tFile::getT('module_install', 'Installation in 1 step'); ?></title>

    <style type="text/css">
        body {font-family: 'Roboto', Arial, sans-serif; font-size: 14px; font-weight: normal; }

        #page { width: 800px; margin: 0 auto; }
        div.logo {float: left; margin-left: -20px;}
        div.content {padding-top: 10px;}
        #footer { color: gray; font-size: 10px; border-top: 1px solid #aaa; margin-top: 10px; }

        h1 {color: black; font-size: 1.6em; font-weight: bold; margin: 0.5em 0; }
        h2 { color: black; font-size: 1.25em; font-weight: bold;  margin: 0.3em 0; }
        h3 {color: black; font-size: 1.1em; font-weight: bold; margin: 0.2em 0; }

        table.result { background: #E6ECFF none repeat scroll 0 0; border-collapse: collapse; width: 100%; }
        table.result th { background: #CCD9FF none repeat scroll 0 0; text-align: left; }
        table.result th, table.result td { border: 1px solid #BFCFFF; padding: 0.2em; }
        td.passed {background-color: #60BF60; border: 1px solid silver; padding: 2px; }
        td.warning {background-color: #FFFFBF; border: 1px solid silver; padding: 2px; }
        td.failed {background-color: #FF8080; border: 1px solid silver; padding: 2px; }
        .install_box {background-color: #EDF4FF; margin: 5px 0; padding: 5px; border: 1px solid #CCCCCC;}
		.install_color {background-color: #EDF4FF; border: 1px solid #CCCCCC; padding-left: 20px;}
		.padding-left5 {padding-left: 5px;}
        .install .install_color input, .install .install_color textarea {width: 200px; height: 20px;}
		div.form .row.buttons {margin: 15px 0 0 0;}
		div.form .license-block {margin: 10px 0;}
		div.form .site-description textarea {/*width: 465px;*/ width: 100%; height: 50px; resize: none;}
		div.form fieldset {
			border: 1px solid #ccc;
			margin: 10px 0 15px 5px;
			padding: 10px;
		}
		.span-12 {width: 450px;}
		div.form legend {font-size: 24px; padding: 0 5px;}
		div.form label {font-size: 14px; font-weight: normal;}
		div.content { width: 100% !important; }
		.install_color .row {padding-left: 5px;}
    </style>
</head>

<body>
	<div id="container">
		<div class="logo">
			<a title="" href="<?php echo Yii::app()->controller->createAbsoluteUrl('/'); ?>">
				<div class="logo-img"> <img width="77" height="70" alt="" src="<?php echo Yii::app()->theme->baseUrl; ?>/images/pages/logo-open-ore.png" /></div>
				<div class="logo-text"><?php echo CHtml::encode(Yii::app()->name);?></div>
			</a>
		</div>

        <?php
			if(!isFree() && Yii::app()->controller->action->id != 'index'){
				$this->widget('application.modules.lang.components.langSelectorWidget', array(
					'type' => 'links',
					'languages' => array(
						'en' => array(
							'name_iso' => 'en',
							'name' => 'English',
							'flag_img' => 'us.png'
						),
						'ru' => array(
							'name_iso' => 'ru',
							'name' => 'Русский',
							'flag_img' => 'ru.png'
						),
						'de' => array(
							'name_iso' => 'de',
							'name' => 'Deutsch',
							'flag_img' => 'de.png'
						)
					)
				));
			}
		?>
		<div class="content install">
			<?php echo $content; ?>
			<div class="clear"></div>
		</div>

		<div class="footer">
			<p class="slogan">&copy;&nbsp;<?php echo tFile::getT('module_install', 'Open Real Estate').', '.date('Y'); ?>&nbsp;v.&nbsp;<?php echo ORE_VERSION;?></p>
		</div>

		<?php
		if($this->isAssetsIsWritable()) {
			$this->widget('ext.magnific-popup.EMagnificPopup', array(
				'target' => 'a.fancy',
				'type' => 'image',
				'options' => array(
					'closeOnContentClick' => true,
					'mainClass' => 'mfp-img-mobile',
					'callbacks' => array(
						'close' => 'js:function(){
							var capClick = $(".get-new-ver-code");
							if(typeof capClick !== "undefined")	capClick.click();
						}
						',
					),
				),
			));

			$this->widget('ext.magnific-popup.EMagnificPopup', array(
					'target' => '.mgp-open-inline',
					'type' => 'inline',
					'options' => array(
						'preloader' => false,
						'focus' => '#name',
						'callbacks' => array(
							'beforeOpen' => 'js:function() {
								if($(window).width() < 700) {
								  this.st.focus = false;
								} else {
								  this.st.focus = "#name";
								}
							  }
							',
							'close' => 'js:function(){
								var capClick = $(".get-new-ver-code");
								if(typeof capClick !== "undefined")	capClick.click();
							}
							',
						),
					),
				)
			);
		}
		?>
    </div>
</body>
</html>