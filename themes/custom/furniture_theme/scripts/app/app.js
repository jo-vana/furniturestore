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

        }
    };

    // Function for adding new class on li>ul if there is another ul inside

    Drupal.behaviors.newClassForMenu = {
        attach:function(context) {
            $('#block-furniture-theme-main-menu li.nav-list-1', context).has('ul').parent('ul').addClass('has-children');

        }

    };

    // Function for mobile version for main navigation

    Drupal.behaviors.mobileMenu = {
      attach:function(context) {

                  // If a link has a dropdown, add sub menu toggle.
                  $('#block-mainnavigationmobile ul li a:not(:only-child)').click(function(e) {
                      $(this).siblings('.dropdown-menu').style('display: block');
                      // Close one dropdown when selecting another
                      $('.dropdown-menu').not($(this).siblings()).hide();
                      e.stopPropagation();
                  });

                  //Clicking away from dropdown will remove the dropdown class
                  $('html').not('nav.menu--main-navigation-mobile').click(function() {
                      $('.dropdown-menu').hide();
                  });

                  // Toggle open and close nav styles on click
                  $('#nav-toggle', context).on('click', function() {
                      $('#block-mainnavigationmobile ul').slideToggle();
                  });

          // Toggle open and close nav styles on click
          $('li.expanded.dropdown', context).on('click', function() {
              $('ul.dropdown-menu').slideToggle().show();
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




