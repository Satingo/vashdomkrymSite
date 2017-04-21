<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo Yii::app()->language;?>" lang="<?php echo Yii::app()->language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title><?php echo CHtml::encode($this->seoTitle ? $this->seoTitle : $this->pageTitle); ?></title>
	<meta name="description" content="<?php echo CHtml::encode($this->seoDescription ? $this->seoDescription : $this->pageDescription); ?>" />
	<meta name="keywords" content="<?php echo CHtml::encode($this->seoKeywords ? $this->seoKeywords : $this->pageKeywords); ?>" />

    <?php Yii::app()->clientScript->registerCssFile(Yii::app()->theme->baseUrl.'/css/form.css', 'screen'); ?>


	<!--<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/form.css" />-->
	<link href='https://fonts.googleapis.com/css?family=Roboto:400,300,700,500&subset=latin,cyrillic-ext,greek-ext,greek,vietnamese,latin-ext,cyrillic' rel='stylesheet' type='text/css'>
	<link media="screen" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/styles.css<?php echo (demo()) ? '?v='.ORE_VERSION: '';?>" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/print.css<?php echo (demo()) ? '?v='.ORE_VERSION: '';?>" media="screen, print" />

	<!--[if IE]> <link href="<?php echo Yii::app()->theme->baseUrl; ?>/css/ie.css" rel="stylesheet" type="text/css"> <![endif]-->

	<?php HSite::registerMainAssets();?>
	
	<link rel="icon" href="<?php echo Yii::app()->request->getBaseUrl(true); ?>/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php echo Yii::app()->request->getBaseUrl(true); ?>/favicon.ico" type="image/x-icon" />
</head>

<body>
	<div id="container">

		<div class="content print-version">
			<?php echo $content; ?>
			<div class="clear"></div>
		</div>
	</div>


</body>
</html>