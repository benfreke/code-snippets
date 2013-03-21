/**
 * Assuming you have google analytics set up, this fires an event on form submission
 */
function initEventTracking() {
    // Set up an event when a user enters details in a form
    jQuery('#myform').on('submit.myNamespace', trackSomething);
}

var trackSomething = function(e) {    
    // Prevent the form being submitted just yet
    e.preventDefault();

    // Keep a reference to this dom element for the callback
    var that = jQuery(this);

    // Get the value searched for
    var searchVal = that.find('#myinput').val();

    if (searchVal) {
        // Lets push the event to Google
        _gaq.push(['_trackEvent', 'Category', 'Name of Event', 'Value (searchVal)']);
        _gaq.push(function() {
            // Unbind this function from the this element
            that.off('submit.myNamespace');
            // Submit the parent form with no interruptions this time
            this.submit();
        });           
    } 
};