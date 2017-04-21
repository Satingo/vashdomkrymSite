<?php
$isInner = isset($isInner) ? $isInner : 0;
$compact = param("useCompactInnerSearchForm", true);
$loc = (issetModule('location')) ? 1 : 0;
$enableMetro = (issetModule('metroStations')) ? 1 : 0;
$urlReloadForm = Yii::app()->createUrl('/quicksearch/main/loadForm', array('lang' => Yii::app()->language));

$beforeShow = 'function(input, inst) {
    $(".hasDatepicker.eval_period").each(function(index, elm){
        if (index == 0) from = elm;
        if (index == 1) to = elm;
    })
    
    if (to.id == input.id) to = null;
    if (from.id == input.id) from = null;
    if (to) {
        maxDate = $(to).val();
        if (maxDate)
            $(inst.input).datepicker("option", "maxDate", maxDate);
    } 
    if (from) {
        minDate = $(from).val();
        if (minDate)
            $(inst.input).datepicker("option", "minDate", minDate);
    }
}
';
?>

    var sliderRangeFields = <?php echo CJavaScript::encode(SearchForm::getSliderRangeFields());?>;
    var cityField = <?php echo CJavaScript::encode(SearchForm::getCityField());?>;
    var loc = <?php echo CJavaScript::encode($loc);?>;
	var enableMetro = <?php echo CJavaScript::encode($enableMetro);?>;
    var countFiled = <?php echo CJavaScript::encode(SearchForm::getCountFiled() + ($loc ? 2 : 0));?>;
	if (enableMetro)
		countFiled = countFiled + 1;
    var isInner = <?php echo CJavaScript::encode($isInner);?>;
    var heightField = 38;
    var advancedIsOpen = 0;
    var compact = <?php echo $compact ? 1 : 0;?>;
    var minHeight = isInner ? 80 : 260;
    var searchCache = [[]];
    var objType = <?php echo isset($this->objType) ? $this->objType : SearchFormModel::OBJ_TYPE_ID_DEFAULT;?>;
    var useSearchCache = false;
    var useDatePicker = false;

    var search = {
        init: function(){

            if(sliderRangeFields){
                $.each(sliderRangeFields, function() {
                    search.initSliderRange(this.params);
                });
            }

            if(countFiled <= 6){
                if(advancedIsOpen){
                    if(isInner){
                        search.innerSetAdvanced();
                    }else{
                        search.indexSetNormal();
                        $('#more-options-link').hide();
                    }
                } else if(!isInner){
                    $('#more-options-link').hide();
                }
            } else {
                if(!isInner){
                    $('#more-options-link').show();
                }

                if(advancedIsOpen){
                    if(isInner){
                        search.innerSetAdvanced();
                    } else {
                        search.indexSetAdvanced();
                    }
                }
            }


            if(useDatePicker){
                jQuery.each(useDatePicker, function(id, options) {
					options.beforeShow = <?php echo $beforeShow;?>;
                    jQuery('#'+id).datepicker(jQuery.extend({showMonthAfterYear:false},jQuery.datepicker.regional['<?php echo Yii::app()->controller->datePickerLang; ?>'], options));
                });
            }

            if($("#search_term_text").length){
                search.initTerm();
            }

        },

        initTerm: function(){
            $(".search-term input#search_term_text").keypress(function(e) {
                var code = (e.keyCode ? e.keyCode : e.which);
                if(code == 13) { // Enter keycode
                    prepareSearch();
                    return false;
                }
            });
        },

        initSliderRange: function(sliderParams){
            $( "#slider-range-"+sliderParams.field ).slider({
                range: true,
                min: sliderParams.min,
                max: sliderParams.max,
                values: [ sliderParams.min_sel , sliderParams.max_sel ],
                step: sliderParams.step,
                slide: function( e, ui ) {
                    $( "#"+sliderParams.field+"_min_val" ).html( ui.values[ 0 ] );
                    $( "#"+sliderParams.field+"_min" ).val( ui.values[ 0 ] );
                    $( "#"+sliderParams.field+"_max_val" ).html( ui.values[ 1 ] );
                    $( "#"+sliderParams.field+"_max" ).val( ui.values[ 1 ] );
                },
                stop: function(e, ui) {  changeSearch(); }
            });
        },

        indexSetNormal: function(){
            $("#homeintro").animate({"height" : "270"});
            $("div.index-header-form").animate({"height" : "234"});
            $("div.searchform-index").animate({"height" : "267"});
			$("div.index-header-form").removeClass("search-form-is-opened");
            $("#more-options-link").html("<?php echo tc("More options");?>");
            advancedIsOpen = 0;
        },

        indexSetAdvanced: function(){
            var height = search.getHeight();
            $("#homeintro").animate({"height" : height + 10});
            $("div.index-header-form").animate({"height" : height});
            $("div.searchform-index").animate({"height" : height + 10});
			$("div.index-header-form").addClass("search-form-is-opened");
            $("#more-options-link").html("<?php echo tc("Less options");?>");
            advancedIsOpen = 1;
        },

        innerSetNormal: function(){
            $("#searchform-block").addClass("compact");
            $("#search-more-fields").hide();
            $("#more-options-link-inner").show();
            $("#more-options-img").hide();
            advancedIsOpen = 0;
        },

        innerSetAdvanced: function(){
            var height = search.getHeight();
            $("#searchform-block").removeClass("compact").css({"height" : height + 20});
            $("#search_form").css({"height" : height});
            $("#btnleft").removeClass("btnsrch-compact");
            $("#search-more-fields").show();
            $("#more-options-link-inner").hide();
            $("#more-options-img").show();
            advancedIsOpen = 1;
        },

        getHeight: function(){
            var height = countFiled * heightField + 30;

            if(height < minHeight){
                return minHeight;
            }

            return isInner ? height/2 + 20 : height;
        },

        renderForm: function(obj_type_id, ap_type_id){
            $('#search_form').html(searchCache[obj_type_id][ap_type_id].html);
            sliderRangeFields = searchCache[obj_type_id][ap_type_id].sliderRangeFields;
            cityField = searchCache[obj_type_id][ap_type_id].cityField;
            countFiled = searchCache[obj_type_id][ap_type_id].countFiled + (loc ? 2 : 0) + (enableMetro ? 1 : 0);
            search.init();
            if(!useSearchCache){
                delete(searchCache[obj_type_id][ap_type_id]);
            }
            changeSearch();
        },

        reloadForm: function(){
            var obj_type_id = $('#objType').val();
            var ap_type_id = $('#apType').val();
            if(typeof searchCache[obj_type_id] == 'undefined' || typeof searchCache[obj_type_id][ap_type_id] == 'undefined'){
                $.ajax({
                    url: <?php echo CJavaScript::encode($urlReloadForm); ?> + '?' + $('#search-form').serialize(),
                    dataType: 'json',
                    type: 'GET',
                    data: { is_inner: <?php echo CJavaScript::encode($isInner);?>, compact: advancedIsOpen ? 0 : 1 },
                    success: function(data){
                        if(data.status == 'ok'){
                            searchCache[obj_type_id] = [];
                            searchCache[obj_type_id][ap_type_id] = [];
                            searchCache[obj_type_id][ap_type_id].html = data.html;
                            searchCache[obj_type_id][ap_type_id].sliderRangeFields = data.sliderRangeFields;
                            searchCache[obj_type_id][ap_type_id].cityField = data.cityField;
                            searchCache[obj_type_id][ap_type_id].countFiled = data.countFiled;
                            search.renderForm(obj_type_id, ap_type_id);
                        }
                    }
                })
            } else {
                search.renderForm(obj_type_id, ap_type_id);
            }
        }
    }

    $(function(){
        search.init();

        $('#search-form').on('change', '#objType', function() {search.reloadForm();});
        $('#search-form').on('change', '#apType', function() {search.reloadForm();});

        if(isInner){
            $("#search-form").on('click', '#more-options-link-inner, #more-options-img', function(){
                if (advancedIsOpen) {
                    search.innerSetNormal();
                } else {
                    search.innerSetAdvanced();
                }
            });
        } else {
            $("#search-form").on('click', '#more-options-link', function(){
                if(advancedIsOpen){
                    search.indexSetNormal();
                } else {
                    search.indexSetAdvanced();
                }
            });
        }

        if(isInner && !compact){
            search.innerSetAdvanced();
        }
    });


function prepareSearch() {
    var term = $(".search-term input#search_term_text").val();

    if (term != <?php echo CJavaScript::encode(tc("Search by description or address")) ?>) {
        if (term.length >= <?php echo (int) Yii::app()->controller->minLengthSearch ?>) {
            term = term.split(" ");
            term = term.join("+");
            $("#do-term-search").val(1);
                window.location.replace("<?php echo Yii::app()->createAbsoluteUrl('/quicksearch/main/mainsearch') ?>?term="+term+"&do-term-search=1");
            } else {
                alert(<?php echo CJavaScript::encode(Yii::t('common', 'Minimum {min} characters.', array('{min}' => Yii::app()->controller->minLengthSearch))) ?>);
        }
    }
}