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
	<link href='https://fonts.googleapis.com/css?family=PT+Sans+Narrow:400,700&amp;subset=cyrillic-ext,latin,latin-ext,cyrillic' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/reset.css<?php echo (demo()) ? '?v='.ORE_VERSION: '';?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/style.css<?php echo (demo()) ? '?v='.ORE_VERSION: '';?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/media-queries.css<?php echo (demo()) ? '?v='.ORE_VERSION: '';?>" type="text/css" media="screen">

	<link rel="icon" href="<?php echo Yii::app()->request->getBaseUrl(true); ?>/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php echo Yii::app()->request->getBaseUrl(true); ?>/favicon.ico" type="image/x-icon" />

	<?php
	HSite::registerMainAssets();

	if(Yii::app()->user->checkAccess('backend_access')){
		?><link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/tooltip/tipTip.css" /><?php
	}
	?>

	<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/ie.css">
	<![endif]-->

	<!--mail.ru API-->
	<script type="text/javascript" src="http://cdn.connect.mail.ru/js/loader.js"></script>

	<!-- LeadoMed code -->
<script>
    (function() {
        var config = {
            API_BASE: 'http://connect.leadomed.ru',
            PROJECT_NAME: 'LeadoMed'
        };

        if (typeof window[config.PROJECT_NAME] === 'undefined' && typeof CallPluginInitObject === 'undefined') {
            window['CallPluginInitObject'] = config;

            var s = document.createElement('script');
            s.type = 'text/javascript';
            s.async = true;
            s.src = config.API_BASE + '/static/api.js';
            var x = document.getElementsByTagName('head')[0];
            x.appendChild(s);
        }
        else
            console.log(config.PROJECT_NAME + ' is already defined.');
    })();
</script>
<!-- LeadoMed code end -->
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
		@media screen and (max-width: 780px){
			#page { padding-top: 155px; }
		}
	</style>
	<?php $this->renderPartial('//site/ads-block', array()); ?>
	<div class="clear"></div>
<?php endif; ?>

<noscript><div class="noscript"><?php echo Yii::t('common', 'Allow javascript in your browser for comfortable use site.'); ?></div></noscript>

<div id="page">

	<?php
	$bgUrl = Themes::getBgUrl();
	if($bgUrl){
		?>
		<div id="bg">
			<img src="<?php echo $bgUrl; ?>" alt="">
		</div>
	<?php } ?>

	<div class="line_header">
		<div class="main_header">
			<div class="left">
				<nav class="switch-menu">
					<span><?php echo tc('Menu');?></span>

					<?php
						$this->widget('zii.widgets.CMenu',array(
							'id' => 'nav',
							'items'=>$this->aData['userCpanelItems'],
							'htmlOptions' => array('class' => 'sf-menu line_menu hide-780 dropDownNav'),
						));
					?>
				</nav>
			</div>

			<?php if(!isFree()): ?>
				<div class="right">
					<?php $languages = Lang::getActiveLangs(true);?>
					<?php if(count($languages) > 1):?>
						<ul class="languages">
							<?php $this->widget('application.modules.lang.components.langSelectorWidget', array( 'type' => 'li', 'languages' => $languages )); ?>
						</ul>
					<?php endif; ?>

					<?php if(count(Currency::getActiveCurrency()) > 1) : ?>
						<div class="dotted_currency"></div>
						<div class="new_select">
							<?php $this->widget('application.modules.currency.components.currencySelectorWidget'); ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<div class="body_background"></div>

	<div class="bg">
		<!-- header -->
		<div class="body_background"></div>
		<div class="header">
			<div class="logo">
				<a title="<?php echo Yii::t('common', 'Go to main page'); ?>" href="<?php echo Yii::app()->controller->createAbsoluteUrl('/'); ?>">
					<div class="logo-img"> <img alt="logo" src="<?php echo Yii::app()->theme->baseUrl; ?>/images/pages/ol3.png" /></div>
					<div class="logo-text">Агентство недвижимости</div>
				</a>
			</div>
			<div class="contacts-main">
			<a title="Связаться с нами" href="http://vashdomkrym.ru/page/6">
				<div class="contacts-text">Республика Крым, г. Алушта<br>
				ул. Партизанская, д.1<br> +7 978 741 7807</div>
			</a>
		</div>
		</div>

		<div class="menu">
			<div id="mobnav-btn"><?php echo tc('Menu');?></div>
			<?php
			$this->widget('ResponsiveMainCMenu',array(
				'id' => 'sf-menu-id',
				'items' => $this->aData['topMenuItems'],
				'htmlOptions' => array('class' => 'sf-menu header_menu'),
				'encodeLabel' => false,
				'activateParents' => true,
			));
			?>
		</div>

		<?php echo $content; ?>

		<?php if(issetModule('advertising')) :?>
			<?php $this->renderPartial('//modules/advertising/views/advert-bottom', array());?>
		<?php endif; ?>

		<div class="page-buffer">&nbsp;</div>

		<div id="footer">
			<?php echo getGA(); ?>
			<?php echo getJivo(); ?>
			<div id="footer-links">
				<div class="wrapper">
					<div class="footer_links_block">
						<a class="footer_add_ad" rel="nofollow" href="<?php echo Yii::app()->createUrl('guestad/main/create');?>" target="_blank"><?php echo tc('List your property');?></a>

						<div class="footer_request_block">
							<a class="link" rel="nofollow" href="<?php echo Yii::app()->createUrl('booking/main/mainform');?>"><?php echo tc('Reserve apartment');?></a>
						</div>

						<div class="footer_social_block">
							<?php
							if (param('useYandexShare', 0))
								$this->widget('application.extensions.YandexShareApi', array(
									'services' => param('yaShareServices', 'yazakladki,moikrug,linkedin,vkontakte,facebook,twitter,odnoklassniki')
								));
							if (param('useInternalShare', 1))
								$this->widget('ext.sharebox.EShareBox', array(
									'url' => Yii::app()->getRequest()->getHostInfo().Yii::app()->request->url,
									'title'=> CHtml::encode($this->seoTitle ? $this->seoTitle : $this->pageTitle),
									'iconSize' => 24,
									'include' => explode(',', param('intenalServices', 'vk,facebook,twitter,google-plus,stumbleupon,digg,delicious,linkedin,reddit,technorati,entriesvine')),
								));
							?>
						</div>
					</div>
				</div>
			</div>

			<div id="footer-two-links">
				<div class="wrapper">
					<div class="mailRUcounter">
						<!-- Rating@Mail.ru logo -->
						<a href="http://top.mail.ru/jump?from=2826897">
						<img src="//top-fwz1.mail.ru/counter?id=2826897;t=479;l=1" 
						style="border:0;" height="31" width="88" alt="Рейтинг@Mail.ru" /></a>
						<!-- //Rating@Mail.ru logo -->
					</div>
					<div class="copyright">&copy;&nbsp;<?php echo CHtml::encode(Yii::app()->name).', 2006-'.date('Y'); ?>
						<br/>
						<?php if (param('adminPhone') || param('adminEmail')) :?>
							<div class="tel">
								<?php if (param('adminPhone')):?>
									<span><?php echo param('adminPhone');?></span>
								<?php endif;?>
								<?php if (param('adminEmail')):?>
									<?php if (param('adminPhone')):?><br /><?php endif;?>
									<div class="mail">
										<?php if (IdnaConvert::check(param('adminEmail'))):?>
											<?php echo IdnaConvert::checkDecode(param('adminEmail')); ?>
										<?php else:?>
											<?php echo $this->protectEmail(param('adminEmail')); ?>
										<?php endif;?>
									</div>
								<?php endif;?>
							</div>
						<?php endif;?>
					</div>
				</div>
			</div>
			<!-- <?php echo param('version_name').' '.param('version'); ?> -->
		</div>

		<div id="loading" style="display:none;"><?php echo Yii::t('common', 'Loading content...'); ?></div>
		<div id="toTop">^ <?php echo tc('Go up');?></div>
		<?php
		$cs->registerScript('main-vars', '
		var BASE_URL = '.CJavaScript::encode(Yii::app()->baseUrl).';
		var CHANGE_SEARCH_URL = '.CJavaScript::encode(Yii::app()->createUrl('/quicksearch/main/mainsearch/countAjax/1')).';
		var params = {
			change_search_ajax: '.param("change_search_ajax", 1).'
		}
	', CClientScript::POS_BEGIN, array(), true);

		$this->renderPartial('//layouts/_common');

		$this->widget('application.modules.fancybox.EFancyBox', array(
				'target'=>'a.fancy',
				'config'=>array(
					'ajax' => array('data'=>"isFancy=true"),
					'titlePosition' => 'inside',
					'onClosed' => 'js:function(){
						var capClick = $(".get-new-ver-code");
						if(typeof capClick !== "undefined")	{ 
							capClick.click(); 
						}
					}'
				),
			)
		);

		/*$this->widget('ext.magnific-popup.EMagnificPopup', array(
			'target'=>'a.fancy',
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
				'target'=>'.mgp-open-inline',
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

		$this->widget('ext.magnific-popup.EMagnificPopup', array(
				'target'=>'.mgp-open-ajax',
				'type' => 'ajax',
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
		);*/


		if(Yii::app()->user->checkAccess('apartments_admin')){
			$cs->registerScriptFile(Yii::app()->theme->baseUrl.'/js/tooltip/jquery.tipTip.js', CClientScript::POS_END);
			$cs->registerScript('adminMenuToolTip', '
			$(function(){
				$(".adminMainNavItem").tipTip({maxWidth: "auto", edgeOffset: 10, delay: 200});
			});
		', CClientScript::POS_READY);
			?>

			<div class="admin-menu-small <?php echo demo() ? 'admin-menu-small-demo' : '';?> ">
				<a href="<?php echo Yii::app()->baseUrl; ?>/apartments/backend/main/admin">
					<img src="<?php echo Yii::app()->theme->baseUrl; ?>/images/adminmenu/administrator.png" alt="<?php echo Yii::t('common','Administration'); ?>" title="<?php echo Yii::t('common','Administration'); ?>" class="adminMainNavItem" />
				</a>
			</div>
		<?php } ?>
		
		<?php		
			if (param('useShowInfoUseCookie') && isset(Yii::app()->controller->privatePolicyPage) && !empty(Yii::app()->controller->privatePolicyPage)) {
				$privatePolicyPage = Yii::app()->controller->privatePolicyPage;
				$cs->registerScript('display-info-use-cookie-policy', '
					$.cookieBar({/*acceptOnContinue:false, */ fixed: true, bottom: true, message: "'.  CHtml::encode(Yii::app()->name).' '.CHtml::encode(tc('uses cookie')).', <a href=\"'.$privatePolicyPage->getUrl().'\" target=\'_blank\'>'.$privatePolicyPage->getStrByLang('title').'</a>", acceptText : "X"});
				', CClientScript::POS_READY);
			}
		?>
	</div>

		<!-- Rating@Mail.ru counter -->
<script type="text/javascript">
var _tmr = window._tmr || (window._tmr = []);
_tmr.push({id: "2826897", type: "pageView", start: (new Date()).getTime()});
(function (d, w, id) {
  if (d.getElementById(id)) return;
  var ts = d.createElement("script"); ts.type = "text/javascript"; ts.async = true; ts.id = id;
  ts.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//top-fwz1.mail.ru/js/code.js";
  var f = function () {var s = d.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ts, s);};
  if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); }
})(document, window, "topmailru-code");
</script><noscript><div style="position:absolute;left:-10000px;">
<img src="//top-fwz1.mail.ru/counter?id=2826897;js=na" style="border:0;" height="1" width="1" alt="Рейтинг@Mail.ru" />
</div></noscript>
		<!-- //Rating@Mail.ru counter -->


<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter40377160 = new Ya.Metrika({
                    id:40377160,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    webvisor:true
                });
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/40377160" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

		<!-- Mail.ru API -->
	 <script type="text/javascript">
   //<![CDATA[
    // этот вызов обязателен, он осуществляет непосредственную загрузку
    // кода библиотеки; рекомендуем всю работу с API вести внутри callback'а
    mailru.loader.require('api', function() {
     // инициализируем внутренние переменные
     // не забудьте поменять на ваши значения app_id и private_key
     mailru.connect.init(__app_id__, __private_key__);
     // регистрируем обработчики событий,
     // которые будут вызываться при логине и логауте
     mailru.events.listen(mailru.connect.events.login, function(session){
      window.location.reload();
     });
     mailru.events.listen(mailru.connect.events.logout, function(){
      window.location.reload();
     });
     // проверка статуса логина, в result callback'a приходит
     // вся информация о сессии (см. следующий раздел)
     mailru.connect.getLoginStatus(function(result) {
      if (result.is_app_user != 1) {
       // пользователь не залогинен, надо показать ему кнопку логина
 
       // вешаем кнопку логина (пример для jquery)
       $('<a class="mrc__connectButton">вход@mail.ru</a>').appendTo('body');
       // эта функция превращает только что вставленный элемент в
       // стандартную кнопку Mail.Ru
       mailru.connect.initButton();
      } else {
       // все ок, можно работать
 
       // получаем полную информацию о текущем пользователе
       mailru.common.users.getInfo(function(result){console.log(result[0].uid)});
      }
     });
    });
   //]]>
  </script>

  <!--Facebook API-->
  <script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '143651796044738',
      xfbml      : true,
      version    : 'v2.6'
    });
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>
<script>
  // This is called with the results from from FB.getLoginStatus().
  function statusChangeCallback(response) {
    console.log('statusChangeCallback');
    console.log(response);
    // The response object is returned with a status field that lets the
    // app know the current login status of the person.
    // Full docs on the response object can be found in the documentation
    // for FB.getLoginStatus().
    if (response.status === 'connected') {
      // Logged into your app and Facebook.
      testAPI();
    } else if (response.status === 'not_authorized') {
      // The person is logged into Facebook, but not your app.
      document.getElementById('status').innerHTML = 'Please log ' +
        'into this app.';
    } else {
      // The person is not logged into Facebook, so we're not sure if
      // they are logged into this app or not.
      document.getElementById('status').innerHTML = 'Please log ' +
        'into Facebook.';
    }
  }

  // This function is called when someone finishes with the Login
  // Button.  See the onlogin handler attached to it in the sample
  // code below.
  function checkLoginState() {
    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    });
  }

  window.fbAsyncInit = function() {
  FB.init({
    appId      : '143651796044738',
    cookie     : true,  // enable cookies to allow the server to access 
                        // the session
    xfbml      : true,  // parse social plugins on this page
    version    : 'v2.2' // use version 2.2
  });

  // Now that we've initialized the JavaScript SDK, we call 
  // FB.getLoginStatus().  This function gets the state of the
  // person visiting this page and can return one of three states to
  // the callback you provide.  They can be:
  //
  // 1. Logged into your app ('connected')
  // 2. Logged into Facebook, but not your app ('not_authorized')
  // 3. Not logged into Facebook and can't tell if they are logged into
  //    your app or not.
  //
  // These three cases are handled in the callback function.

  FB.getLoginStatus(function(response) {
    statusChangeCallback(response);
  });

  };

  // Load the SDK asynchronously
  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));

  // Here we run a very simple test of the Graph API after login is
  // successful.  See statusChangeCallback() for when this call is made.
  function testAPI() {
    console.log('Welcome!  Fetching your information.... ');
    FB.api('/me', function(response) {
      console.log('Successful login for: ' + response.name);
      document.getElementById('status').innerHTML =
        'Thanks for logging in, ' + response.name + '!';
    });
  }
</script>

<!--
  Below we include the Login Button social plugin. This button uses
  the JavaScript SDK to present a graphical Login button that triggers
  the FB.login() function when clicked.


<fb:login-button scope="public_profile,email" onlogin="checkLoginState();">
</fb:login-button>

<div id="status">
</div>
-->
<!--
<div id='Rambler-counter'>

<noscript>
<a href="http://top100.rambler.ru/navi/4433295/">
  <img src="http://counter.rambler.ru/top100.cnt?4433295" alt="Rambler's Top100" border="0" />
</a>
</noscript>
</div>


<script type="text/javascript">
var _top100q = _top100q || [];
_top100q.push(['setAccount', '4433295']);
_top100q.push(['trackPageviewByLogo', document.getElementById('Rambler-counter')]);

(function(){
  var pa = document.createElement("script"); 
  pa.type = "text/javascript"; 
  pa.async = true;
  pa.src = ("https:" == document.location.protocol ? "https:" : "http:") + "//st.top100.ru/top100/top100.js";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(pa, s);
})();
</script>
-->

<!-- код expecto.me -->
<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-P23G9N"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i,cid){w[l]=w[l]||[];w.pclick_client_id=cid;w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:''; j.async=true; j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-P23G9N', '48605');</script>
<!-- End Google Tag Manager -->

</body>
</html>