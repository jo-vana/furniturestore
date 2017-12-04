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

})(jQuery);




