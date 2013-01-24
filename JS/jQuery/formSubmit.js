function initEventTracking() {
    // Set up an event when a user enters details in a form
    jQuery('#pcodeform').on('submit.myNamespace', trackSomething);
}

var trackSomething = function(e) {    
    // Prevent the form being submitted just yet
    e.preventDefault();

    // Keep a reference to this dom element for the callback
    var _this = jQuery(this);

    // Get the value searched for
    var searchVal = _this.find('#lb01').val();

    if (searchVal) {
        // Lets push the event to Google
        _gaq.push(['_trackEvent', 'Dealer Locator', 'Search', searchVal]);
        _gaq.push(function() {
            // Unbind this function from the this element
            _this.off('submit.myNamespace');
            // Submit the parent form with no interruptions this time
            this.submit();
        });           
    } 
};