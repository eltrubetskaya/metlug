(function($) {

    function init() {
    
        // Kill links to self
        $('a').each(function(i, item) {
            if (item.href == ('' + window.location).replace(/#.*$/, '').replace(/\?.*$/, '')) {
                $(item).addClass('current');
            }
            else {
                $(item).removeClass('current');
            }
        });

        if (!Modernizr.input.placeholder){
            $('input[placeholder]').placeholder();
        }
        
    };
    
    $(init);

})(jQuery);