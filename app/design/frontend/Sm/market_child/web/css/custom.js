require([
		'jquery'], function ($) {
			$(document).ready(function(){
					require([
						'jquery',
						"owlcarousel"
						], function ($) {
                            $('#carousel').carousel();
                              SwitchCityField();
						     
						     var city_pre_select = '';
					          var logos1 = $("#brand-logo");
						    logos1.owlCarousel({
						        loop: true,
						        slideSpeed: 1000,
						        autoplay: true,
						        dots: false,
						        navigation: false,
						        margin: 10,
						        items: 7,
						        responsive: {0: {items: 2}, loop: true, 600: {items: 4}, 960: {items: 6}, 1200: {items: 7}},
						        pagination: false
						    });

						    var logos4 = $("#brand-logo2");
						    logos4.owlCarousel({
						        // rtl: true,
						        loop: true,
						        slideSpeed: 1000,
						        autoplay: true,
						        dots: false,
						        navigation: false,
						        margin: 10,
						        items: 7,
						        responsive: {0: {items: 2}, loop: true, 600: {items: 4}, 960: {items: 6}, 1200: {items: 7}},
						        pagination: false
                             });

						     $(".products-cat").owlCarousel({
                               items: 5,
                               responsive: {0: {items: 2},  480: { items: 2 }, 768: { items: 3 }, 992: { items: 4}, loop: true, 600: {items: 3}, 960: {items: 4}, 1200: {items: 5}},
			                    autoplay: false,
			                    loop: true,
			                    slideSpeed: 1000,
			                    margin: 15,
			                    navigation: false,
			                    dots: false,
			                    autoplayHoverPause: true,
			                    margin: 30,
			                    pagination: false,
			                });

						    $('.pro-arrow .owl-prev-h').click(function (e) {
                              jQuery(this).parent().parent().next().find('.owl-prev').click();
                            });
						    $('.pro-arrow .owl-next-h').click(function (e) {
						        jQuery(this).parent().parent().next().find('.owl-next').click();
						    });

						    $('.b-arrow .owl-prev-h').click(function (e) {
						        jQuery('.b-p .owl-carousel .owl-nav .owl-prev').click();
						    });
						    $('.b-arrow .owl-next-h').click(function (e) {
						        jQuery('.b-p .owl-carousel .owl-nav .owl-next').click();
						    });

						      $('.slider-home .owl-dots').css('width', $('.slider-home .owl-dot').length * 28);
						    $('.pro-main-box ul li').click(function (e) {
						        $(this).addClass('active').siblings().removeClass('active');
						    });
						    $('.menu-cat ul li').click(function (e) {
						        $(this).addClass('active').siblings().removeClass('active');
						    });
							$(".main-megamenu-search .ves-megamenu li.subdynamic").hover(function () {
								$(".black-shadow").css({"display": "block"});
							}, function () {
								$(".black-shadow").css({"display": "none"});
							});
						     var logos2 = $("#products");
							    logos2.owlCarousel({
							        loop: true,
							        slideSpeed: 1000,
							        autoplay: false,
							        dots: false,
							        navigation: false,
							        margin: 15,
							        items: 5,
							        responsive: {0: {items: 2}, loop: true, 600: {items: 3}, 960: {items: 4}, 1200: {items: 5}},
							        pagination: false
							    });
							    var logos2 = $("#products2");
							    logos2.owlCarousel({
							        loop: true,
							        slideSpeed: 1000,
							        autoplay: false,
							        dots: false,
							        navigation: false,
							        margin: 15,
							        items: 6,
							        responsive: {0: {items: 2}, loop: true, 600: {items: 3}, 960: {items: 5}, 1200: {items: 6}},
							        pagination: false
							    });
							    $('.p-arrow .owl-prev-h').click(function (e) {
							        jQuery('.p-p .owl-carousel .owl-nav .owl-prev').click();
							    });
							    $('.p-arrow .owl-next-h').click(function (e) {
							        jQuery('.p-p .owl-carousel .owl-nav .owl-next').click();
							    });
							    var logos5 = $(".products-cat");
							    logos5.owlCarousel({
							        loop: true,
							        slideSpeed: 1000,
							        autoplay: false,
							        dots: false,
							        navigation: false,
							        margin: 15,
							        items: 5,
							        responsive: {0: {items: 2}, loop: true, 600: {items: 3}, 960: {items: 4}, 1200: {items: 5}},
							        pagination: false
							    });
							    var logos5 = $(".products-cat-single");
							    logos5.owlCarousel({
							        loop: true,
							        slideSpeed: 1000,
							        autoplay: false,
							        dots: false,
							        navigation: false,
							        margin: 15,
							        items: 6,
							        responsive: {0: {items: 2}, loop: true, 600: {items: 4}, 960: {items: 5}, 1200: {items: 6}},
							        pagination: false
							    });

							      $('body').bind('touchstart',function(){});$(".sticky-bottom .button-sticky-bottom").click(function(){
							      	$(".sticky-bottom .block-bottom").removeClass("active");
							      	$(".sticky-bottom-open .block-search-open").removeClass("active");
							      	if($(this).hasClass("active")){$("#"+$(this).attr("data-drop")).removeClass("active");$(this).removeClass("active");$("body").removeClass("overflow-hidden");
							      	return;}
							      	else{$(".sticky-bottom .button-sticky-bottom").removeClass("active");
							      	$(".sticky-bottom-open .button-sticky-bottom-open").removeClass("active");
							      	$("#"+$(this).attr("data-drop")).toggleClass("active");
							        $("#search-drop-open").toggleClass("active"); 
							        $(this).addClass("active");
							        if($("#"+$(this).attr("data-drop")).hasClass("active")){
							        $("body").addClass("overflow-hidden");;}else{$("body").removeClass("overflow-hidden");}}});

							      $(".sticky-bottom .close-sticky-bottom").click(function(){
							      	var el=$(this).attr("data-drop");
							      	$("#"+el).removeClass("active");
							      	 $("#search-drop-open").removeClass("active");
							      	$(".sticky-bottom .button-sticky-bottom").removeClass("active");
                                    $(".sticky-bottom-open .button-sticky-bottom-open").removeClass("active");
							      	$("body").removeClass("overflow-hidden");});

							      $(".btn-categories").click(function(){$(this).toggleClass('active');$(".dropdown-categories-header").toggleClass('active');});$('.block-menu ul > li.all-categories > a.show_more').click(function(){$('.block-menu ul > li.hidden-item').slideDown(200).addClass('active');$(this).css({'display':'none'});$('.block-menu ul > li.all-categories > a.close_more').css({'display':'block'})});$('.block-menu ul > li.all-categories > a.close_more').click(function(){$('.block-menu ul > li.hidden-item').slideUp(200).removeClass('active');$(this).css({'display':'none'});$('.block-menu ul > li.all-categories > a.show_more').css({'display':'block'})});$('.block-menu .btn-showsub').click(function(){$(this).parent().toggleClass('parent-active');if($(this).hasClass('active')){$(this).removeClass('active').prev('.submenu-wrap').slideUp(200);return;}else{$(this).addClass('active').prev('.submenu-wrap').slideDown(200);return;}});$('.navigation-mobile > ul li').has('ul').append('<span class="touch-button"><span>open</span></span>');$('.touch-button').click(function(){$(this).prev().slideToggle(200);$(this).toggleClass('active');$(this).parent().toggleClass('parent-active');});$(".title-footer").click(function(){$(this).parent(".block-footer").toggleClass('active');});$(".sidebar-trigger").click(function(){if($(this).hasClass('active')){$('body').removeClass('overflow-hidden overlay-sidebar')
								$(this).removeClass('active').parent().parent().parent().removeClass('active');return;}else{$('.sidebar-container').removeClass('active')
								$(".sidebar-trigger").removeClass('active')
								$('body').addClass('overflow-hidden overlay-sidebar')
								$(this).addClass('active').parent().parent().parent().addClass('active');}});

							    $(document).ready(function() {
							    if ($(window).width() > 767 ) {
                                    //var ulw = $(".ves-megamenu .dropdown-menu .content-wrap .megamenu-sidebar").width();
                                    //alert(ulw);
                                    //$(".header-style-3 .header-bottom .searchbox-header").css({"width": ulw});
							        $(window).scroll(function () {
							            if ($(this).scrollTop() > 100) {
							                $('.header-bottom').addClass('fix-menu');
                                            $('.pro-cart').addClass('fix-cart');
                                        } else {
							                $('.header-bottom').removeClass('fix-menu');
                                            $('.pro-cart').removeClass('fix-cart');
							            }
							        });
							    }
								else{
									  $('.header-bottom').removeClass('fix-menu');
									}
							   }); 

							    $('.content-cat').showMore({
							        minheight: 145,
							        buttontxtmore: 'Show More',
							        buttontxtless: 'Show Less',
							        animationspeed: 750
							    });

						        $(document).on('change',"[name='country_id']",function(){
						            SwitchCityField();
						        });
                                
                                city_pre_select = $('input[name="city"]').val();
                                if(city_pre_select || city_pre_select != '' || city_pre_select != null) {
						        $("select.select_city option").each(function(){
							        if($(this).val()==city_pre_select){ // EDITED THIS LINE
							            $(this).attr("selected","selected");    
							        }
							    });
						        }

							 function SwitchCityField(){
						        var selectVal = $('select[name="country_id"] option:selected').val();
						        if(selectVal === "AE"){
						        	if($(".select_city").length === 0) {
						        	$("[name=city]").parent(".control").append('<select id="' + 'city_selection_id' + '" class="' + 'select_city' +'" name="' + 'select_city' + '" onchange="jQuery(' + '\'input[name=city]\'' + ').val(this.value); jQuery(' + '\'input[name=' + 'city' + ']\'' + ').keyup()">' + 
						        		'<option value="">Please select a city, district or town. *</option>' + 
						        		'<option value="Abu Dhabi">Abu Dhabi</option>' + 
						        		'<option value="Ajman">Ajman</option>' + 
						        		'<option value="Al Ain">Al Ain</option>' + 
						        		 '<option value="Dubai">Dubai</option>' +
						        		 '<option value="Fujairah">Fujairah</option>' +
						        		 '<option value="Ras al Khaimah">Ras al Khaimah</option>' +
						        		 '<option value="Sharjah">Sharjah</option>' + 
						        		 '<option value="Umm al Quwain">Umm al Quwain</option>' +
						        		 '</select>');
                                    }
                                   else { }
						        }else{
						            // $("[name=city]").replaceWith('<input class="input-text" type="text" data-bind="value: value,valueUpdate: keyup,hasFocus: focused, attr: { name: inputName, placeholder: placeholder,aria-describedby: getDescriptionId(),aria-required: required, aria-invalid: error() ? true :false, id: uid }" name="city" placeholder="City *" aria-required="true" autocomplete="off" aria-invalid="false">');
						            $("[name=city]").show();
						            $("[name=select_city]").remove();
						            $("[name=select_city_checkout]").remove();
						            // $("[name=city] > select").remove();
						        }
						    }
						   
							// $("#accordion").accordion("destroy");    // Removes the accordion bits
							// $("#accordion").empty(); 
							
                           /* To show or hide map */
						   $('body').on("change",'#show_map_check',function() {
                           if(this.checked != true){
							  $('#map').hide();
						   }
						   else {
                              $('#map').show();
						   }
						   }); 
                           /* To show or hide map */

					    });
						    
					
				})
		     
});
	
// jQuery(document).ready(function() {
//     if ( jQuery(window).width() > 767 ) {
//         jQuery(window).scroll(function () {
//             if (jQuery(this).scrollTop() > 100) {
//                 jQuery('.header-bottom').addClass('fix-menu');
//             } else {
//                 jQuery('.header-bottom').removeClass('fix-menu');
//             }
//         });
//     }
// 	else{
// 		  jQuery('.header-bottom').removeClass('fix-menu');
// 		}
// });