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
      attach:function(context, settings) {

                  // If a link has a dropdown, add sub menu toggle.
                  $('#block-mainnavigationmobile ul li a:not(:only-child)').click(function(e) {
                      $(this).siblings('.dropdown-menu').toggle();
                      // Close one dropdown when selecting another
                      $('.dropdown-menu').not($(this).siblings()).hide();
                      e.stopPropagation();
                  });
                  // Clicking away from dropdown will remove the dropdown class
                  $('html').click(function() {
                      $('.dropdown-menu').hide();
                  });
                  // Toggle open and close nav styles on click
                  $('#nav-toggle').click(function() {
                      $('#block-mainnavigationmobile ul').slideToggle();
                  });

      }

    };

})(jQuery);





//# sourceMappingURL=../scripts/application.js.map
