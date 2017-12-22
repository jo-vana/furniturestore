(function($) {

	'use strict';

    // Function for tabs on furniture node page.
    Drupal.behaviors.tabLinks = {
        attach:function(context, settings) {

            $('body .node--type-furniture .second-block ul.tabs li').click(function(){
                var tab_id = $(this).attr('data-tab');

                $('body .node--type-furniture .second-block ul.tabs li').removeClass('current');
                $('body .node--type-furniture .second-block .tab-content').removeClass('current');

                $(this).addClass('current');
                $("#"+tab_id).addClass('current');
            });

            $('body #block-latestfromblogblock ul.tabs li').click(function(){
                var tab_id = $(this).attr('data-tab');

                $('body #block-latestfromblogblock ul.tabs li').removeClass('current');
                $('body #block-latestfromblogblock .tab-content').removeClass('current');

                $(this).addClass('current');
                $("#"+tab_id).addClass('current');
            });

        }
    };

    // Function for adding new class on li>ul if there is another ul inside

    Drupal.behaviors.newClassForMenu = {
        attach:function(context) {
            $('#block-furniture-theme-main-menu li.nav-list-1', context).has('ul').parent('ul').addClass('has-children');

        }

    };

    Drupal.behaviors.newClassMobMenu = {
        attach:function(context) {
            $('li.menu-item-2', context).has('ul').addClass('got-pseudo');
            $('li.menu-item-1', context).has('ul').addClass('got-pseudo');

        }

    };

    Drupal.behaviors.mobileMenue = {
        attach: function (context) {

            // Function for mobile version for main navigation

            $('.menu--main-navigation-mobile > .menu-toggle', context).on('click', function () {
                // console.log('test');
                $('.menu--main-navigation-mobile').toggleClass('first-lev-show');
                $('.menu-item-1').removeClass('show');
            });

            $('.menu-level-1 > .menu-item-1>a', context).on('click', function (e) {
                if ($(this).siblings('ul').length > 0) {
                    e.preventDefault();
                    $(this).parent().toggleClass('show');
                }

            });

            $('li.menu-item-2>a', context).on('click', function (e) {
                console.log($(this).siblings('ul').length);
                if ($(this).siblings('ul').length > 0) {
                    e.preventDefault();
                    // $(this).addClass('got-pseudo');
                    $(this).parent().toggleClass('show');
                }

            });

        }
    };


    // Function for 'back to top' button

    Drupal.behaviors.backToTop = {
        attach:function(context) {
            var btn = $('#block-backtotop a');

            $(window).scroll(function() {
                if ($(window).scrollTop() > 100) {
                    btn.addClass('show');
                } else {
                    btn.removeClass('show');
                }
            });

            btn.on('click', function(e) {
                e.preventDefault();
                $('html, body').animate({scrollTop:0}, '100');
            });
        }
    };

    Drupal.behaviors.sliderBlog = {
      attach:function() {
              $('.from-the-blog .flexslider').flexslider({
                  animation: "slide",
                  animationLoop: true,
                  itemWidth: 420,
                  itemMargin: 10,
                  minItems: 1,
                  maxItems: 2
              });

          $('.about_us .flexslider').flexslider({
              animation: "slide",
              animationLoop: true,
              itemWidth: 200,
              itemMargin: 10,
              minItems: 1,
              maxItems: 3
          });

      }
    };


})(jQuery);




