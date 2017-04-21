
		var updateText = 'Загрузка ...';
		var resultBlock = 'appartment_box';
		var indicator = '/themes/atlas/images/pages/indicator.gif';
		var bg_img = '/themes/atlas/images/pages/opacity.png';

		var useGoogleMap = 0;
		var useYandexMap = 1;
		var useOSMap = 0;
			
		var modeListShow = 'block';

		$(function () {
			$('div#appartment_box').on('mouseover mouseout', 'div.appartment_item', function(event){
				if (event.type == 'mouseover') {
				 $(this).find('div.apartment_item_edit').show();
				} else {
				 $(this).find('div.apartment_item_edit').hide();
				}
			});
		});
		
		function setListShow(mode){
            modeListShow = mode;
            reloadApartmentList(urlsSwitching[mode]);
        };


        $(function () {
            if(modeListShow == 'map'){
                list.apply();
            }
        });
	