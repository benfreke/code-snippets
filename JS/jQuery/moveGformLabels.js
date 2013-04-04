/**
 * This will move gravity form labels above their input fields, in places where they are automatically prepended (like email confirm)
 * To use, simply add the class "movelabel" in the form administration
 */
jQuery(document).ready(function() {
	// Move labels around and delete the old one
	var wrongLabelHolders = jQuery('.movelabel');
	// Because we may have more than one label that needs moving
	wrongLabelHolders.each(function() {
		var myLabels = jQuery(this).find('div span label');
		if (myLabels.length == 2) {
			myLabels.eq(0).replaceWith(myLabels.eq(1));
		}
	});
});