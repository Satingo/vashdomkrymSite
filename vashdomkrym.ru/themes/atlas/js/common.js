var clickedImgIndex = 1;

function showGallery(){
    var index = clickedImgIndex;
    index = index - 1;

    $("a[data-gal^='prettyPhoto']:eq(" + index + ")").click();
}

function setImgGalleryIndex(index){
    if(index){
        clickedImgIndex = index;
        var tmp = clickedImgIndex - 1;
        $("#jcarousel .active").removeClass("active");
        $("#jcarousel li:eq(" + tmp + ")").addClass("active");


    }
}

jQuery(function ($) {    
    $(document).ajaxSend(function(){
        $("#loading").show();
    }).ajaxComplete(function(){
        $("#loading").hide();
    });
    
    $('#search-form').on('change', '.searchField', function (event) {
        changeSearch();
    });

    $(".switch-menu span").click(function(){
        if ($(window).width() <= 960)  {
            $(".hide-780").slideToggle("fast");
            return false;
        }
    });

    $(".home").hover(
        function () {
            $(this).find('ul').show();
            $(this).find('a').addClass("active");
        },
        function () {
            $(this).find('ul').hide();
            $(this).find('a').removeClass("active");
        }
    );

    $('#mobnav-btn').click(
        function () {
            $('#sf-menu-id').toggleClass("xactive");
            $('#sf-menu-id').find('li').removeClass("sfHover");
            $('#sf-menu-id').find('li').removeClass("xpopdrop");
            $('#sf-menu-id').find('li').removeClass("xpopdroplevelsvisible");
        });

    $('.mobnav-subarrow').click(
        function () {
            $(this).parent().removeClass("sfHover");
            $(this).parent().toggleClass("xpopdrop");            
        });

    $('.mobnav-subarrow-levels').click(
        function () {
            $(this).parent().removeClass("sfHover");
            $(this).parent().toggleClass("xpopdroplevelsvisible");
        });

    $( '.hide_filtr, .hide_filtr_collapsed').click(function() {
        if($(this).hasClass('collapsed')) {
            //$(this).removeClass('collapsed');
            $('.filtr').removeClass('collapsed');
            $('#inner_open_button').hide();
        } else {
            //$(this).addClass('collapsed');
            $('.filtr').addClass('collapsed');
            $('#inner_open_button').show();
        }
    });

    $('.mini_gallery ul img').click(function(){
        var path = $(this).parent().attr('href');
        var alt = $(this).attr('alt');
        //var relImg = $(this).parent().attr('rel');

        //var relImg = 'prettyPhoto[img-gallery]';
        var relImg = 'ppprettyPhoto';

        $('#imgHolder').animate({opacity: 0},300,function(){
            if (relImg) {
                $(this).html('<a onclick="showGallery(this); return false;" id="main-img" href='+ path +' data-gal=' + relImg + '><img src=' + path + ' /></a>').find('img').bind('load',function(){
                    $(this).parent().parent().append('').animate({opacity: 1},300);
                });
            }
            else {
                $(this).html('<img src=' + path + ' />').find('img').bind('load',function(){
                    $(this).parent().append('').animate({opacity: 1},300);
                });
            }
        });
        return false;
    });

    $('.mini_gallery ul img:first').click();

    var width = $(window).width() > 1024 ? '607px' : ($(window).width()-150);

    if(parseInt(width) <= 1024) {
        $('.jcarousel').attr({'style':'width:'+parseInt(width)+'px'});
    }

    $( ".tabs_1 li a" ).click(function() {
        $( ".tabs_1 li a" ).removeClass('active_tabs');
        $(this).addClass('active_tabs');
        $('.tab_bl_1').hide();
        $('.'+$(this).parent().attr('id')).show();
    });

    $( ".tabs_2 li a" ).click(function() {
        $( ".tabs_2 li a" ).removeClass('active_tabs');
        $(this).addClass('active_tabs');
        $('.tab_bl_2').hide();
        $('.'+$(this).parent().attr('id')).show();
    });

    $(window).scroll(function() {
        if($(this).scrollTop() != 0) {
            $('#toTop').fadeIn();
        } else {
            $('#toTop').fadeOut();
        }
    });

    $('#toTop').click(function() {
        $('body,html').animate({scrollTop:0},500);
    });
});

function focusSubmit(elem) {
    elem.keypress(function (e) {
        if (e.which == 13) {
            $(this).blur();
            $("#btnleft").focus().click();
        }
    });
}

$(window).bind('popstate', function(event) {
    var state = event.originalEvent.state;
    if (state) {
        if (state.callFrom == 'reloadApartmentList') {
            //$('div.main-content-wrapper').html(state.response);
            window.location.href = state.path;
        }
    }
    /*else {
        window.location.reload();
    }*/
});

function reloadApartmentList(url) {
    $.ajax({
        type: 'GET',
        url: url,
        /*data: {is_ajax: 1},*/
        ajaxStart: UpdatingProcess(resultBlock, updateText),
        success: function (msg) {
            history.pushState( {callFrom: 'reloadApartmentList', path: url, response: msg}, null, url);

            $('div.main-content-wrapper').html(msg);
            $('div.rating > span > input').rating({'readOnly': true});

            list.apply();

            // smooth scroll to
            var dest=0;
            
            if($("#appartment_box").offset().top > $(document).height()-$(window).height()){
                dest=$(document).height()-$(window).height();
            }
            else{
                dest=$("#appartment_box").offset().top;
            }
            
            $("html,body").animate({scrollTop:dest}, 500,"swing");
            
            $('#update_div').remove();
            $('#update_text').remove();
            $('#update_img').remove();
        }
    });
}

function UpdatingProcess(resultBlock, updateText) {
    $('#update_div').remove();
    $('#update_text').remove();
    $('#update_img').remove();

    var opacityBlock = $('#' + resultBlock);

    if (opacityBlock.width() != null) {
        var width = opacityBlock.width();
        var height = opacityBlock.height();
        var left_pos = opacityBlock.offset().left;
        var top_pos = opacityBlock.offset().top;
        $('body').append('<div id=\"update_div\"></div>');

        var cssValues = {
            'z-index': '1005',
            'position': 'absolute',
            'left': left_pos,
            'top': top_pos,
            'width': width,
            'height': height,
            'border': '0px solid #FFFFFF',
            'background-image': 'url(' + bg_img + ')'
        }

        $('#update_div').css(cssValues);

        var left_img = left_pos + width / 2 - 16;
        var left_text = left_pos + width / 2 + 24;
        var top_img = top_pos + height / 2 - 16;
        var top_text = top_img + 8;

        $('body').append("<img id='update_img' src='" + indicator + "' style='position:absolute;z-index:1006; left: " + left_img + "px;top: " + top_img + "px;'>");
        $('body').append("<div id='update_text' style='position:absolute;z-index:6; left: " + left_text + "px;top: " + top_text + "px;'>" + updateText + "</div>");
    }
}

var searchLock = false;

function changeSearch() {
    if (params.change_search_ajax != 1) {
        return false;
    }

    if (!searchLock) {
        searchLock = true;

        $.ajax({
            url: CHANGE_SEARCH_URL,
            data: $('#search-form').serialize(),
            dataType: 'json',
            type: 'get',
            success: function (data) {
                $('#btnleft').html(data.string);
                searchLock = false;
            },
            error: function () {
                searchLock = false;
            }
        })
    }
}

var placemarksYmap = [];

var list = {
    lat: 0,
    lng: 0,
    ad_id : 0,

    apply: function () {
        $('div.appartment_item').each(function () {

            var existListMap = $('#list_map_block').attr('data-exist') == 1;
            if(!existListMap){
                return;
            }

            var item = $(this);

            item.mouseover(function () {

                var ad = $(this);
                var lat = ad.attr('data-lat') + 0;
                var lng = ad.attr('data-lng') + 0;
                var id = ad.attr('data-ap-id');

                if ((list.lat != lat || list.lng != lng || list.ad_id != id) && lat > 0 && lng > 0) {
                    list.lat = lat;
                    list.lng = lng;
                    list.ad_id = id;

                    if (useGoogleMap) {
                        if (typeof infoWindowsGMap !== 'undefined' && typeof infoWindowsGMap[id] !== 'undefined') {
                            for (var key in infoWindowsGMap) {
                                if (key == id) {
                                    infoWindowsGMap[key].open();
                                } else {
                                    infoWindowsGMap[key].close();
                                }
                            }
                            var latLng = new google.maps.LatLng(lat, lng);

                            mapGMap.panTo(latLng);
                            infoWindowsGMap[id].open(mapGMap, markersGMap[id]);
                        }
                    }

                    if (useYandexMap) {
                        if (typeof placemarksYMap[id] !== 'undefined') {
                            placemarksYMap[id].balloon.open();
                        }
                    }

                    if (useOSMap) {
                        if (typeof markersOSMap[id] !== 'undefined') {
                            markersOSMap[id].openPopup();
                            mapOSMap.panTo(new L.LatLng(lat, lng));
                        }
                    }
                }

            });

        });
    }
}

var scriptLoaded = [];

function loadScript(url, reload) {
    reload = reload || true;

    //if(typeof scriptLoaded[url] == 'undefined' || reload){
        var script = document.createElement("script");
        script.type = "text/javascript";
        script.src = url;
        document.body.appendChild(script);

        scriptLoaded[url] = 1;
    //}
}

function ajaxRequest(url, tableId){
	$.ajax({
		url: url,
		type: "get",
		success: function(){
			$("#"+tableId).yiiGridView.update(tableId);
		}
	});
}