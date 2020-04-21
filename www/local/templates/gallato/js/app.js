jQuery(document).ready(function($) {

	// правки по верстке
	if (location.pathname == '/search/') {
		$('.no_products').css('display', 'none');
	}
	if (location.pathname == '/policy/') {
		$('.section_top_block h1').css('top', '43%');
	}
	if (location.pathname == '/catalog/gift-cards/') {
		$('.catalog_sorting .filt').css('display', 'none');
		$('#sort_area_content .b-alphabet').css('display', 'none');
	}
	$('.no_products').parent().parent().find('.catalog_sorting').css('display', 'none');







	

	// СКРЫТА ВРЕМЕННО ФИЛЬТРАЦИЯ И СОРТИРОВКА В ИЗБРАННОМ

	// Перемещаем блок #elemTags в #filter_area_content
	$('#filter_area_content').append( $('#elemTags') );

	if (location.pathname == '/wish-list/') {
		$('#elemTags').css('display', 'none');

	} else {
		$('#elemTags').css('display', 'flex');
	}

	// if (location.pathname == '/sale/') {
	// 	$('.catalog_sorting .filt').css('display', 'none'); //!!!!!!!!
	// }

	// if (location.pathname == '/catalog/') {
	// 	$('.catalog_sorting .filt').css('display', 'none'); //!!!!!!!!
	// }

	//клик на тэг
	$('#elemTags li').click(function(event) {

		//$('.transition-loader').css('display', 'block');

		$(this).toggleClass('active'); 

		var activeElements = [];

		$('#elemTags li').each(function( index ) { 
			if($(this).hasClass('active')){
				activeElements.push($(this).attr('data-name'));
			}
		});

		var json = JSON.stringify(activeElements);
		
		
		if (location.pathname == '/catalog/') {

			var dataPost = { 
				tags: json,
			};

			//console.log('catalog dataPost - ', dataPost);

			$.post( "/include/filt_tags.php", dataPost,  function( data ) {

				$('.tags_content_box').html(' ');
				$('.tags_content_box').append(data);

				//console.log(data);
			});

		} else if (location.pathname == '/sale/') {

			var dataPost = { 
				tags: json,
			};

			//console.log('sale dataPost - ', dataPost);

			$.post( "/include/sale_tags.php", dataPost,  function( data ) {

				$('.tags_content_box').html(' ');
				$('.tags_content_box').append(data);
				//console.log(data);
			});

		} else {

			var dataPostSection = { 
				tags: json,
				currentSection: $('.hidden_current_section').html()
			};

			//console.log('section dataPostSection - ', dataPostSection);

			$.post( "/include/section_tags.php", dataPostSection,  function( data ) {

				$('.tags_content_box').html(' ');
				$('.tags_content_box').append(data);
				//console.log(data);

				//$('.transition-loader').css('display', 'none');
			});

		}

		event.preventDefault();

	});









	

	//табы в карточке товара (описание + отзывы)
	$('#tab_decription').click(function(event) {
		$('#comments_box').removeClass('show');
		$('#desription_box').addClass('show');

		$('#tab_comments').removeClass('active');
		$('#tab_decription').addClass('active');

	});
	$('#tab_comments').click(function(event) {
		$('#desription_box').removeClass('show');
		$('#comments_box').addClass('show');

		$('#tab_decription').removeClass('active');
		$('#tab_comments').addClass('active');
	});


	// стрелочки в моб. меню
	$('.mp-level').parent('li').addClass('icon-arrow-left');

	if (location.pathname == '/checkout/') {

		var listWidth = $('.checkout_page .bx-basket-item-list')[0].offsetWidth;

		$('.checkout_page .info').css('width', listWidth);
	}




	// клики на табы регистрации

	// определение высоты для содержания табов регистрации
	var optHeight = $('.opt--area').find('.auth-other-form').height() + 100;
	var roznHeight = $('.rozn--area').find('.auth-other-form').height() + 100;
	$('.areas').css('height', roznHeight); // по умолчанию высота розничной регистрации

	$('#rozn').click(function(event) {
		$(this).addClass('active');
		$('#opt').removeClass('active');

		$('.rozn--area').addClass('show');
		$('.opt--area').removeClass('show');

		$('.areas').css('height', roznHeight);

	});

	$('#opt').click(function(event) {
		$(this).addClass('active');
		$('#rozn').removeClass('active');

		$('.opt--area').addClass('show');
		$('.rozn--area').removeClass('show');

		$('.areas').css('height', optHeight);
	});






	// отправка комментария в карточке товара
	$('#addComment').submit(function (e) {
		event.preventDefault();
		var $form = $(this);

		$.ajax({
            url: "/include/addComment.php",
            data: $form.serialize(),
            method: "POST",
            success: function (data) {
                $('#addComment')[0].reset();
                $('#comment_modal').addClass('active');
            },
            error: function (er) {
                console.log("er",er);
            }
        });
	});



	//сердечки
	$('.item__like').click(function(event) {
		//$(this).toggleClass('active');

        // console.log($(this).attr("data-product-id"));
        var like = $(this);
        $.ajax({
            url: "/include/addToFavorites.php",
            data: "id=" + $(this).attr("data-product-id"),
            method: "POST",
            success: function (data) {
                var count = parseInt($('span#wish_count').html());
                if(data == "true"){
                    $('span#wish_count').html(++count);
                    $(like).addClass("active");
                }else{
                    $('span#wish_count').html(--count);
                    $(like).removeClass("active");

                    if (location.pathname == '/wish-list/') {
                    	location.href = "/wish-list/";
                    }
                }
            },
            error: function (er) {
                console.log("er",er);
            }
        });
	});

	//эффекты у сортировки и фильтра ПРИ НАВЕДЕНИИ
	if ($(window).width() >= 1000) {
		$('#sort_btn').mouseenter(function(event) {
				$(this).addClass('open');
				$('#filter_btn').removeClass('open');

				$('#filter_area_content').css('display', 'none');
				$('#sort_area_content').css('display', 'block');
		});
		$('#filter_btn').mouseenter(function(event) {
				$(this).addClass('open');
				$('#sort_btn').removeClass('open');

				$('#sort_area_content').css('display', 'none');
				$('#filter_area_content').css('display', 'block');
		});

		$('#filter_area_content').mouseleave(function(event) {
			$(this).css('display', 'none');
		});
		$('#sort_area_content').mouseleave(function(event) {
			$(this).css('display', 'none');
		});

		$('.section_top_block').mouseenter(function(event) {
			$('#sort_area_content').css('display', 'none');
			$('#filter_area_content').css('display', 'none');

			$('#sort_btn').removeClass('open');
			$('#filter_btn').removeClass('open');
		});
	} else if($(window).width() < 1000) {
		//эффекты у сортировки и фильтра ПРИ КЛИКЕ
		$('#sort_btn').click(function(event) {
			if ($(this).hasClass('open')) {
				$(this).removeClass('open');
				$('#filter_btn').removeClass('open');

				$('#sort_area_content').css('display', 'none');
			} else {
				$(this).addClass('open');
				$('#filter_btn').removeClass('open');

				$('#filter_area_content').css('display', 'none');
				$('#sort_area_content').css('display', 'block');
			}
		});
		$('#filter_btn').click(function(event) {
			if ($(this).hasClass('open')) {
				$(this).removeClass('open');
				$('#sort_btn').removeClass('open');

				$('#filter_area_content').css('display', 'none');
			} else {
				$(this).addClass('open');
				$('#sort_btn').removeClass('open');

				$('#sort_area_content').css('display', 'none');
				$('#filter_area_content').css('display', 'block');
			}
		});

	}

	






	// $('#sort_area_content').mouseleave(function(event) {
	// 	$('#sort_area_content').css('display', 'none');
	// 	$('#sort_btn').removeClass('open');
	// });
	// $('#filter_area_content').mouseleave(function(event) {
	// 	$('#filter_area_content').css('display', 'none');
	// });



	// $('.section_top_block').mouseover(function(event) {
	// 	$('#filter_area_content').css('display', 'none');
	// 	$('#sort_area_content').css('display', 'none');

	// 	$('#sort_btn').removeClass('open');
	// });

	//фикс для телефонной маски
	$.fn.setCursorPosition = function(pos) {
		if ($(this).get(0).setSelectionRange) {
			$(this).get(0).setSelectionRange(pos, pos);
		} else if ($(this).get(0).createTextRange) {
			var range = $(this).get(0).createTextRange();
			range.collapse(true);
			range.moveEnd('character', pos);
			range.moveStart('character', pos);
			range.select();
		}
	};
	$('input[name="phone"], .phone_field').click(function(){
	    $(this).setCursorPosition(4);  // set position number
	});

	$('input[name="phone"], .phone_field').mask('+7 (999) 999-9999');



	// SUBMIT FORM
	$('.wholesalers_form').submit(function (e) {
		event.preventDefault();
		var $form = $(this);
		$.ajax({
			type: 'post',
			url: '/include/send.php',
			data: $form.serialize()
		}).done(function (event) {
			if (event == 1) {
				//alert('форма точно работает!');
				$('.wholesalers_form')[0].reset();
				$('#success_modal').addClass('active');
			}
		}).fail(function () {
			alert('fail');
		});
	});

	$('.franchise_form').submit(function (e) {
		event.preventDefault();
		var $form = $(this);
		$.ajax({
			type: 'post',
			url: '/include/send.php',
			data: $form.serialize()
		}).done(function (event) {
			if (event == 1) {
				//alert('форма точно работает!');
				$('.franchise_form')[0].reset();
				$('#success_modal').addClass('active');
			}
		}).fail(function () {
			alert('fail');
		});
	});

	$('.modal_form').submit(function (e) {
		event.preventDefault();
		var $form = $(this);
		$.ajax({
			type: 'post',
			url: '/include/send.php',
			data: $form.serialize()
		}).done(function (event) {
			if (event == 1) {
				//alert('форма точно работает!');
				$('#request_modal').removeClass('active');
				$('.modal_form')[0].reset();
				$('#success_modal').addClass('active');
			}
		}).fail(function () {
			alert('fail');
		});
	});



	//показывает при наведение подменю
	$('.header_main_menu li').mouseover(function(event) {
		$('.header_sub_menu').removeClass('show');
		$(this).find('.header_sub_menu').addClass('show');
	});
	$('.header_sub_menu').mouseleave(function(event) {
		$(this).removeClass('show');
	});
	$('.header__menu').mouseleave(function(event) {
		$('.header_sub_menu').removeClass('show');
	});


	// моб. меню
	// $('.mobile_icon').on('click', function(event) {
	// 	event.preventDefault();
		
	// 	$(this).toggleClass('open');
	// });

	// $('.mobile_icon_box').on('click', function(event) {
	// 	event.preventDefault();
		
	// 	$('.burder_menu').toggleClass('active');
	// });

	$('.request').on('click', function(event) {
		event.preventDefault();
		
		$('#request_modal').addClass('active');
	});


	

	// setTimeout(function() {
	// 	$('.menuItem a').trigger('click');
	// 	console.log('click');
	// 	$('.menuItem:first a').trigger('click');
	// }, 5000);
	

	// $('.profile_form .btn_save').click(function(event) {
	// 	/* Act on the event */
	// });

	//close modals
	$('.modal_close, .modal--background').on('click', function(event) {
		event.preventDefault();
		
		$('.burder_menu').removeClass('active');
		$('.mobile_icon').removeClass('open');

		$('#request_modal').removeClass('active');
		$('#success_modal').removeClass('active');
		$('#comment_modal').removeClass('active');
		$('#profile_save_success').removeClass('active');
		$('#profile_save_error').removeClass('active');
	});





	//красная рамка при наведении на поиск в шапке
	$('#title-search-input, #search_page_input').focusin(  
		function(){  
			$(this).parent().addClass('focus');
	}).focusout(  
	function(){  
		$(this).parent().removeClass('focus');
	});

	
	//слайдеры
	$('#main_slider').slick({
		dots: false,
		arrows: true,
		infinite: false,
		speed: 700,
		slidesToShow: 1,
		slidesToScroll: 1,
		responsive: [
			{
				
			}
		]
	});

	$('.home_promo_slider').slick({
		dots: false,
		arrows: true,
		infinite: true,
		speed: 700,
		slidesToShow: 1,
		slidesToScroll: 1,
		responsive: [
			{
				
			}
		]
	});

	$('.home_news_slider').slick({
		dots: false,
		arrows: true,
		infinite: true,
		speed: 700,
		slidesToShow: 2,
		slidesToScroll: 1,
		responsive: [
			{
				breakpoint: 601,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
				}
			},
		]
	});

	$('.catalog_slider').slick({
		dots: false,
		arrows: true,
		infinite: true,
		speed: 700,
		slidesToShow: 4,
		slidesToScroll: 1,
		responsive: [
			{
				breakpoint: 1101,
				settings: {
					slidesToShow: 3,
					slidesToScroll: 1,
				}
			},

			{
				breakpoint: 901,
				settings: {
					slidesToShow: 2,
					slidesToScroll: 1,
				}
			},

			{
				breakpoint: 601,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
				}
			},
		]
	});


	// ини фото карточки товара
	// $('.product-item-detail-slider-controls-block').slick({
	// 	dots: false,
	// 	arrows: true,
	// 	infinite: false,
	// 	speed: 700,
	// 	slidesToShow: 2,
	// 	slidesToScroll: 1,
	// 	responsive: [
	// 		{
				
	// 		}
	// 	]
	// });

	ymaps.ready(init);



}); // end of ready function



function init() {

    // Создание экземпляра карты.
    var myMap = new ymaps.Map('map', {
            center: [54.97179156972096,73.28477649999996],
            zoom: 15
        }, {
            searchControlProvider: 'yandex#search'
        }),
        // Контейнер для меню.
        menu = $('<ul class="menu"/>');
        
    for (var i = 0, l = groups.length; i < l; i++) {
        createMenuGroup(groups[i]);
    }

    function createMenuGroup (group) {
        // Пункт меню.
        var menuItem = $('<li class="menuItem show" onclick="return false;"><a href="#">' + group.name + '</a></li>'),
        // Коллекция для геообъектов группы.
            collection = new ymaps.GeoObjectCollection(null, { preset: group.style }),
        // Контейнер для подменю.
            submenu = $('<ul class="submenu"/>');

        // Добавляем коллекцию на карту.
        myMap.geoObjects.add(collection);
        // Добавляем подменю.
        menuItem
            .append(submenu)
            // Добавляем пункт в меню.
            .appendTo(menu)
            // По клику удаляем/добавляем коллекцию на карту и скрываем/отображаем подменю.
            .find('a')
            .bind('click', function () {
                if (collection.getParent()) {
                    myMap.geoObjects.remove(collection);
                    //submenu.hide();
                    menuItem.removeClass('show');
                    submenu.slideUp(300);
                    //console.log(submenu);
                } else {
                    myMap.geoObjects.add(collection);
                    //submenu.show();
                    menuItem.addClass('show');
                	//console.log(submenu);
                	submenu.slideDown(300);
                }
            });
            
        for (var j = 0, m = group.items.length; j < m; j++) {
            createSubMenu(group.items[j], collection, submenu);
        }
    }

    function createSubMenu (item, collection, submenu) {
        // Пункт подменю.
        var submenuItem = $('<li class="submenuItem"><p>' + item.name + '</p><a href="#">показать</a></li>'),
        // Создаем метку.
            placemark = new ymaps.Placemark(item.center, { balloonContent: item.name });

        // Добавляем метку в коллекцию.
        collection.add(placemark);
        // Добавляем пункт в подменю.
        submenuItem
            .appendTo(submenu)
            // При клике по пункту подменю открываем/закрываем баллун у метки.
            .find('a')
            .bind('click', function () {
                if (!placemark.balloon.isOpen()) {
                    placemark.balloon.open();
                } else {
                    placemark.balloon.close();
                }
                return false;
            });
    }

    // Добавляем меню в тэг BODY.
    menu.appendTo($('.accordeon'));

    $('.accordeon .menu li:first').find('.submenu').addClass('show');

  //   $('.menuItem a').on('click', function(event) {

  //   	let target = event.target;
  //   	let parent = $(target).parent();

		// $(parent).trigger('click');
		// console.log('trigger');
		// $('.menuItem a').off('click');
		// console.log('off');
  //   });

    //console.log($('.menuItem'));
	//console.log($('.accordeon .menu li:first'));

    // Выставляем масштаб карты чтобы были видны все группы.
    //myMap.setBounds(myMap.geoObjects.getBounds());
}