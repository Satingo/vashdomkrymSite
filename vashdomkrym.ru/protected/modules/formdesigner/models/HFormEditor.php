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
class HFormEditor
{
    private static $_cache;

    public static function getReferencesList($withNew = false)
    {
        Yii::import('application.modules.referencecategories.models.ReferenceCategories');
        $ref = ReferenceCategories::model()->findAllByAttributes(array(
            'type' => ReferenceCategories::TYPE_FOR_EDITOR,
        ));

		$ref = CHtml::listData($ref, 'id', 'title');

		if($withNew)
			$ref = CArray::merge(array(0 => tt('Create new category', 'formeditor')), $ref);

        return $ref;
    }

    public static function getGeneralFields()
    {
        $cache = FormDesigner::getCacheByView();

        return isset($cache[FormDesigner::VIEW_IN_GENERAL]) ? $cache[FormDesigner::VIEW_IN_GENERAL] : array();
    }

    public static function getExtendedFields()
    {
        $cache = FormDesigner::getCacheByView();

        return isset($cache[FormDesigner::VIEW_IN_EXTENDED]) ? $cache[FormDesigner::VIEW_IN_EXTENDED] : array();
    }

    public static function getRulesForModel()
    {
        $all = self::getAllFields();

        $rules = array();
        $fieldsSafe = array();
        foreach ($all as $row) {
            if ($row['rules'] == FormDesigner::RULE_REQUIRED || $row['rules'] == FormDesigner::RULE_REQUIRED_NUMERICAL) {
                $rules[] = array($row['field'], 'requiredAdvanced');
            }

            if ($row['rules'] == FormDesigner::RULE_NUMERICAL || $row['rules'] == FormDesigner::RULE_REQUIRED_NUMERICAL) {
                $rules[] = array($row['field'], 'numerical');
            }

            $fieldsSafe[] = $row['field'];
        }

        if ($fieldsSafe) {
            $rules[] = array(implode(', ', $fieldsSafe), 'safe');
        }

        return $rules;
    }

    public static function getAllFields()
    {
        if (!isset(self::$_cache['all'])) {
            $general = self::getGeneralFields();
            $extended = self::getExtendedFields();

            self::$_cache['all'] = CMap::mergeArray($general, $extended);
        }

        return self::$_cache['all'];
    }

    public static $viewRowTemplate = '<dt>{label}:</dt><dd>{value}</dd>';

	public static function renderViewRows($rows, Apartment $model, $isPrintable = false) {
		if (!$rows) {
			return '';
		}

        Yii::import('application.modules.referencevalues.models.ReferenceValues');

		foreach ($rows as $row) {
            $is_multy = ($row->type == FormDesigner::TYPE_MULTY) ? 1 : 0;

			if (!$model->canShowInView($row['field'], $is_multy)) {
				continue;
			}

            $fieldViewPath = Yii::getPathOfAlias('webroot.protected.modules.apartments.views.viewFields').DIRECTORY_SEPARATOR;

			if ($row['standard_type'] != FormDesigner::STANDARD_TYPE_NEW && file_exists($fieldViewPath . $row['field'] . '.php')) { //
				Yii::app()->controller->renderPartial('//../modules/apartments/views/viewFields/' . $row['field'], array('data' => $model, 'isPrintable' => $isPrintable));
				continue;
			}

			if ($row->type == FormDesigner::TYPE_REFERENCE) {
				$value = ReferenceValues::getTitleById($model->$row['field']);
			} elseif ($row->type == FormDesigner::TYPE_MULTY) {
                $value = '';
                if(isset($model->references[$row->reference_id])) {
                    $item = $model->references[$row->reference_id];
                    $value = '<ul class="apartment-description-ul">';
                    foreach ($item['values'] as $key => $val) {
                        if ($val) {
                            $value .= '<li><span>' . CHtml::encode($val) . '</span></li>';
                        }
                    }
                    $value .= '</ul>';
                }
            } else {
				$fieldName = $row['field'];
				$value = is_string($model->$fieldName) ? CHtml::encode($model->$fieldName) : '???';
				if($row->type == FormDesigner::TYPE_TEXT_AREA_WS){
					$value = purify($model->$fieldName);
				}
			}

            if ($row->type == FormDesigner::TYPE_MULTY) {
                $value = '';
                if(isset($model->references[$row->reference_id])) {
                    $item = $model->references[$row->reference_id];
                    $value = '<ul class="apartment-description-ul">';
                    foreach ($item['values'] as $key => $val) {
                        if ($val) {
                            $value .= '<li><span>' . CHtml::encode($val) . '</span></li>';
                        }
                    }
                    $value .= '</ul>';
                }
            }

			if ($row->type == FormDesigner::TYPE_INT && $row->measure_unit) {
				$value .= '&nbsp;' . CHtml::encode($row->measure_unit);
			}

			if ($value) {
				if ($row['standard_type'] > 0) {
					$label = CHtml::encode($model->getAttributeLabel($row['field']));
				}
				else {
					$label = CHtml::encode($row['label_' . Yii::app()->language]);
				}

				if ($row->field == 'phone') {
                    if (issetModule('tariffPlans') && issetModule('paidservices') && (!$model->isOwner())) {
                        if (Yii::app()->user->isGuest) {
                            $defaultTariffInfo = TariffPlans::getFullTariffInfoById(TariffPlans::DEFAULT_TARIFF_PLAN_ID);

                            if (!$defaultTariffInfo['showPhones']) {
                                $valueRow = Yii::t('module_tariffPlans', 'Please <a href="{n}">login</a> to view', Yii::app()->controller->createUrl('/site/login'));
                            }
                            else {
                                $valueRow =  '<span id="owner-phone">' . CHtml::link(tc('Show phone'), 'javascript: void(0);', array('onclick' => 'generatePhone();')) . '</span>';
                            }
                        }
                        else {
                            if (TariffPlans::checkAllowShowPhone())
                                $valueRow =  $value;
                            else
                                $valueRow = Yii::t('module_tariffPlans', 'Please <a href="{n}">change the tariff plan</a> to view', Yii::app()->controller->createUrl('/tariffPlans/main/index'));
                        }
                    }
                    else {
                        $valueRow = '<span id="owner-phone">' . CHtml::link(tc('Show phone'), 'javascript: void(0);', array('onclick' => 'generatePhone();')) . '</span>';
                    }
				}
				else {
                    $valueRow = $value;
				}

                self::renderViewRow($label, $valueRow);
            }
        }
	}

    public static function renderViewRow($label, $valueRow)
    {
        echo strtr(self::$viewRowTemplate, array('{label}' => $label, '{value}' => $valueRow));
    }

    public static function renderFormRows($rows, Apartment $model, $form = null, Seasonalprices $seasonalPricesModel = null, $callFrom = '') {
		if (!$rows) {
			return '';
		}


        foreach ($rows as $row) {
            $isShowTip = true;

            if (!$model->canShowInForm($row['field'])) {
				continue;
			}

            if ($row['type'] == FormDesigner::TYPE_REFERENCE) {
                $refs = FormDesigner::getListByCategoryID($row->reference_id, $model->type);
                if(!$refs)
                    continue;
            }

            if ($row['type'] == FormDesigner::TYPE_MULTY) {
                if(!isset($model->references[$row->reference_id]))
                    continue;
            }


            // если есть файл отображения для формы
			if ($row['standard_type'] == FormDesigner::STANDARD_TYPE_ORIGINAL_VIEW) {
				Yii::app()->controller->renderPartial('//../modules/apartments/views/backend/fields/' . $row['field'], array('model' => $model, 'seasonalPricesModel' => $seasonalPricesModel, 'form' => $form, 'callFrom' => $callFrom));
				continue;
			}

			$required = ($row->rules == FormDesigner::RULE_REQUIRED || $row->rules == FormDesigner::RULE_REQUIRED_NUMERICAL) ? array('required' => true) : array();

			echo '<div class="rowold">';
			if ($row['standard_type'] == FormDesigner::STANDARD_TYPE_NEW) {
				echo CHtml::label($row['label_' . Yii::app()->language], get_class($model) . '_' . $row['field'], $required);
			} else {
				echo $row['is_i18n'] ? '' : CHtml::activeLabel($model, $row['field']);
			}

			if ($row['is_i18n'])
				$isShowTip = false;

			if ($isShowTip)
				echo HApartment::getTip($row['field']);

			switch ($row['type']) {
				case FormDesigner::TYPE_TEXT:
					if ($row['is_i18n']) {
						Yii::app()->controller->widget('application.modules.lang.components.langFieldWidget', array(
							'model' => $model,
							'field' => $row['field'],
							'type' => 'string'
						));
					} else {
						echo CHtml::activeTextField($model, $row['field'], array('class' => 'width500', 'maxlength' => 255));
					}
					break;

				case FormDesigner::TYPE_INT:
					echo CHtml::activeTextField($model, $row['field'], array('class' => 'width70', 'maxlength' => 255));
					if ($row->measure_unit) {
						echo '&nbsp;' . $row->measure_unit;
					}
					break;

				case FormDesigner::TYPE_TEXT_AREA:
					if ($row['is_i18n']) {
						Yii::app()->controller->widget('application.modules.lang.components.langFieldWidget', array(
							'model' => $model,
							'field' => $row['field'],
							'type' => 'text'
						));
					} else {
						echo CHtml::activeTextArea($model, $row['field'], array('class' => 'width500 height200'));
					}
					break;

				case FormDesigner::TYPE_TEXT_AREA_WS:
                    if ($row['is_i18n']) {
                        Yii::app()->controller->widget('application.modules.lang.components.langFieldWidget', array(
                            'model' => $model,
                            'field' => $row['field'],
                            'type' => 'text-editor'
                        ));
                    } else {
                        // даем пользователям ограниченый набор форматирования
                        $toolbar = array(
                            array('Source', '-', 'Bold', 'Italic', 'Underline', 'Strike'),
                           // array('Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'),
                            array('NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'),
                            //array('Styles', 'Format', 'Font', 'FontSize', 'TextColor', 'BGColor'),
                            //array('Image', 'Link', 'Unlink', 'SpecialChar'),
                        );

						$filebrowserImageUploadUrl = '';

                        if (Yii::app()->user->checkAccess('backend_access')) { // if admin - enable upload image
                            $filebrowserImageUploadUrl = Yii::app()->createAbsoluteUrl('/site/uploadimage', array('type' => 'imageUpload', Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken));

                            $toolbar = array(
                                array('Source', '-', 'Bold', 'Italic', 'Underline', 'Strike'),
                                array('Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'),
                                array('NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'),
                                array('Styles', 'Format', 'Font', 'FontSize', 'TextColor', 'BGColor'),
                                array('Image', 'Link', 'Unlink', 'SpecialChar'),
                            );
                        }

                        Yii::app()->controller->widget('application.extensions.editMe.widgets.ExtEditMe', array(
                            'model' => $model,
                            'attribute' => $row['field'],
                            'toolbar' => $toolbar,
                            'filebrowserImageUploadUrl' => $filebrowserImageUploadUrl,
                            'htmlOptions' => array('id' => $model->id)
                        ));
                    }

					break;

				case FormDesigner::TYPE_REFERENCE:
					echo CHtml::activeDropDownList($model, $row['field'], CMap::mergeArray(array("" => Yii::t('common', 'Please select')), $refs), array('class' => 'span3'));
					break;


                case FormDesigner::TYPE_MULTY:

                    $refs = (isset($model->references[$row->reference_id]) && isset($model->references[$row->reference_id]['values'])) ? $model->references[$row->reference_id]['values'] : array();

					if (!empty($refs)) {
						echo '<div class="apartment-description-multy">';
							echo '<input type="checkbox" id="ref-check-all-'.$row->reference_id.'-'.$row->id.'" class="ref-check-all" title="' . CHtml::encode(tc('check all')) . '"/>';
							echo '<label for="ref-check-all-'.$row->reference_id.'-'.$row->id.'" class="viewapartment-subheader subheader-clickable">'. CHtml::encode(tc('check all')) . '</label>';
							echo '<ul class="no-disk">';
								foreach ($refs as $id => $ref) {
									echo '<li>';
									echo CHtml::checkBox('category['.$row->reference_id.']['.$id.']', (isset($model->references) && isset($model->references[$row->reference_id]['values'][$id]['selected'])) ?
										$model->references[$row->reference_id]['values'][$id]['selected'] : 0, array('class'=>'s-categorybox'));
									echo ' '.CHtml::label($ref['title'], 'category_'.$row->reference_id.'_'.$id);
									echo '</li>';
								}
							echo '</ul>';
						echo '</div>';
						
						Yii::app()->clientScript->registerScript('ref-check-all-multy', '
							$(".ref-check-all").on("click", function(){
								var elems = $(this).closest("div").find(".s-categorybox");
								if($(this).is(":checked")){
									elems.prop("checked", true);
								} else {
									elems.prop("checked", false);
								}
							});

							$(".ref-check-all").each(function(){
								var elems = $(this).closest("div").find(".s-categorybox");
								if($(this).closest("div").find(".s-categorybox:checked").length == elems.length){
									$(this).attr("checked", true);
								}
							});
						', CClientScript::POS_READY);
					}

                    break;
			}
			echo '</div>';
		}
    }

    public static function existValueInRows($rows, Apartment $model){
        foreach($rows as $row){
            if(!$model->canShowInView($row['field'])){
                continue;
            }
            return true;
        }
        return false;
    }

    public static function createCategoryFromField(FormDesigner $field) {
        $category = new ReferenceCategories();

        $category->type = ReferenceCategories::TYPE_FOR_EDITOR;

        $activeLangs = Lang::getActiveLangs();

        foreach($activeLangs as $lang){
            $title = 'title_' . $lang;
            $label = 'label_' . $lang;
            $category->$title = $field->$label;
        }

        return $category->save() ? $category->id : false;

    }
}