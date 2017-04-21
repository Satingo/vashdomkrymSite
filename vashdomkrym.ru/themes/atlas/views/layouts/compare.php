<!DOCTYPE html>
<?php
$cs = Yii::app()->clientScript;
?>
<html lang="<?php echo Yii::app()->language;?>">
<head>
	<title><?php echo CHtml::encode($this->seoTitle ? $this->seoTitle : $this->pageTitle); ?></title>
	<meta name="description" content="<?php echo CHtml::encode($this->seoDescription ? $this->seoDescription : $this->pageDescription); ?>" />
	<meta name="keywords" content="<?php echo CHtml::encode($this->seoKeywords ? $this->seoKeywords : $this->pageKeywords); ?>" />

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="format-detection" content="telephone=no">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<link href='https://fonts.googleapis.com/css?family=PT+Sans+Narrow:400,700&subset=cyrillic-ext,latin,latin-ext,cyrillic' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/reset.css<?php echo (demo()) ? '?v='.ORE_VERSION: '';?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/style.css<?php echo (demo()) ? '?v='.ORE_VERSION: '';?>" type="text/css" media="screen">

	<?php HSite::registerMainAssets();?>
	
	<link rel="icon" href="<?php echo Yii::app()->request->getBaseUrl(true); ?>/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php echo Yii::app()->request->getBaseUrl(true); ?>/favicon.ico" type="image/x-icon" />
</head>

<body class="<?php echo ($this->htmlPageId == 'index') ? 'b_mainpage' : $this->htmlPageId;?>">
<?php if (CheckBrowser::isOld()) : ?>
	<link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/oldbrowsers/check_browser.css" type="text/css" media="screen">
	<?php
	echo CheckBrowser::warning(null, true);
	echo '</body></html>';
	exit;
	?>
<?php endif;?>

<?php if (demo()) :?>
	<style>
		#page { padding-top: 40px; }
	</style>
	<?php $this->renderPartial('//site/ads-block', array()); ?>
	<div class="clear"></div>
<?php endif; ?>

<div id="page" class="compare-main" <?php echo (demo()) ? 'style="padding-top: 40px;"' : '';?> >
	<div class="header">
		<div class="logo">
			<a title="<?php echo Yii::t('common', 'Go to main page'); ?>" href="<?php echo Yii::app()->controller->createAbsoluteUrl('/'); ?>">
				<div class="logo-img"> <img alt="logo" src="<?php echo Yii::app()->theme->baseUrl; ?>/images/pages/ol3.png" /></div>
				<div class="logo-text">Агентство недвижимости</div>
			</a>
		</div>
	</div>
	<div class="clear"></div>

	<div class="contentCompare">
		<?php echo $content; ?>
	</div>
	<div class="clear"></div>
	<div class="page-buffer">&nbsp;</div>
	<div id="footer">
		<?php echo getGA(); ?>
		<?php echo getJivo(); ?>
		<div class="wrapper">
			<div class="copyright">&copy;&nbsp;<?php echo CHtml::encode(Yii::app()->name).', '.date('Y'); ?></div>
			<!-- <?php echo param('version_name').' '.param('version'); ?> -->
		</div>
	</div>
</div>
<?php Yii::app()->clientScript->registerCoreScript('jquery');?>
</body>
</html>