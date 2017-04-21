<div id="ore-ads-block">
    <div>
        <ul>
			<li>
				<?php
				$linkTitle = Yii::t('module_install', 'Buy', array(), 'messagesInFile', Yii::app()->language);
				$linkHref = (Yii::app()->language == 'ru') ? 'http://open-real-estate.info/ru/download-open-real-estate' : 'http://open-real-estate.info/en/download-open-real-estate';

				echo CHtml::link(
					'<span class="download"></span>' . $linkTitle,
					$linkHref,
					array(
						'class' => 'button green'
					)
				);
				?>
			</li>
			<?php if (isFree()):?>
				<li>
					<?php
					echo CHtml::link(
						Yii::t('module_install', 'PRO version demo', array(), 'messagesInFile', Yii::app()->language),
						'http://re-pro.monoray.net/',
						array(
							'class' => 'button green'
						)
					);
					?>
				</li>

				<li>
					<?php
					echo CHtml::link(
						Yii::t('module_install', 'Add-ons', array(), 'messagesInFile', Yii::app()->language),
						(Yii::app()->language == 'ru') ? 'http://open-real-estate.info/ru/open-real-estate-modules' : 'http://open-real-estate.info/en/open-real-estate-modules',
						array(
							'class' => 'button cyan'
						)
					);
					?>
				</li>
			<?php endif;?>
            <li>
                <?php
                echo CHtml::link(
                    Yii::t('module_install', 'About product', array(), 'messagesInFile', Yii::app()->language),
                    (Yii::app()->language == 'ru') ? 'http://open-real-estate.info/ru/about-open-real-estate' : 'http://open-real-estate.info/en/about-open-real-estate',
                    array(
                        'class' => 'button cyan'
                    )
                );
                ?>
            </li>
            <li>
                <?php
                echo CHtml::link(
                    Yii::t('module_install', 'Contact us', array(), 'messagesInFile', Yii::app()->language),
                    (Yii::app()->language == 'ru') ? 'http://open-real-estate.info/ru/contact-us' : 'http://open-real-estate.info/en/contact-us',
                    array(
                        'class' => 'button cyan'
                    )
                );
                ?>
            </li>

			<?php if(Yii::app()->user->isGuest){ ?>
			<li class="item-login">
				<?php
				echo CHtml::link(
					Yii::t('module_install', 'Log in', array(), 'messagesInFile', Yii::app()->language),
					Yii::app()->createUrl('/login'),
					array(
						'class' => 'button orange'
					)
				);
				?>
			</li>
			<?php } ?>

			<?php if (!isFree()):?>
				<li>
					<?php
					$themeList = Themes::getColorThemesList();
					//deb($themeList);

					echo CHtml::dropDownList('theme', Themes::getParam('color_theme'), $themeList, array(
						'onchange' => 'js: changeTheme(this.value);',
						'empty' => Yii::t('module_install', 'Color theme', array(), 'messagesInFile', Yii::app()->language)
					));
					?>
				</li>
				<li>
					<?php
					$themeList = Themes::getAdditionalViewList(true);

					echo CHtml::dropDownList('additional_view', Yii::app()->controller->useAdditionalView, $themeList, array(
						'empty' => Yii::t('module_install', 'Additionally', array(), 'messagesInFile', Yii::app()->language),
						'onchange' => 'js: changeAdditionalView(this.value);',
						'style' => 'width: 200px;',
					));
					?>
				</li>
			<?php endif;?>
        </ul>
    </div>
</div>

<script type="text/javascript">
    function changeTheme(theme){
        location.href = URL_add_parameter(location.href, 'theme', theme);
    }
	
	function changeAdditionalView(additional_view){
        location.href = URL_add_parameter(location.href, 'additional_view', additional_view);
    }

    function URL_add_parameter(url, param, value){
        var hash       = {};
        var parser     = document.createElement('a');

        parser.href    = url;

        var parameters = parser.search.split(/\?|&/);

        for(var i=0; i < parameters.length; i++) {
            if(!parameters[i])
                continue;

            var ary      = parameters[i].split('=');
            hash[ary[0]] = ary[1];
        }

        hash[param] = value;

        var list = [];
        Object.keys(hash).forEach(function (key) {
            list.push(key + '=' + hash[key]);
        });

        parser.search = '?' + list.join('&');
        return parser.href;
    }
</script>