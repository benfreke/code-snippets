function initEventTracking() {
    // Set up an event when a user enters details in a form
    jQuery('#pcodeform').bind('submit', trackDealerSearch); 
}

var trackDealerSearch = function(e) {    
    // Prevent the form being submitted just yet
    e.preventDefault();

    // Keep a reference to this dom element for the callback
    var _this = jQuery(this);

    // Get the value searched for
    var searchVal = _this.find('#lb01').val();

    if (searchVal) {                    
        _gaq.push(['_trackEvent', 'Dealer Locator', 'Search', searchVal]);
        _gaq.push(function() {             
            // Submit the parent form
            _this.unbind('submit', trackDealerSearch);
            this.submit();
        });           
    } 
};