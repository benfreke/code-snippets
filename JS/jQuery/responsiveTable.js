/**
 * Makes tables responsive. This does rely on jQuery.
 * Based completely on this: http://css-tricks.com/responsive-data-tables/
 */

/**
 * This is my base CSS
 * This is wrapped in a media query inside the main css
 */

/** Mobile Tables
.mobileTable,
.mobileTable tbody,
.mobileTable th,
.mobileTable td,
.mobileTable tr {
	display: block;
}

.mobileTable thead {
	display: none;
}

.mobileTable tr {
	border: 1px solid #ccc;
}

.mobileTable td {
	border: none;
	border-bottom: 1px solid #eee;
	position: relative;
	padding-left: 50%;
}

.mobileTable td:before {
	position: absolute;
	top: 6px;
	left: 6px;
	width: 45%;
	padding-right: 10px;
	white-space: nowrap;
}
**/


// Add css to make tables display on mobiles
// Find all the tables
var allTables = jQuery('table');
// if we have some, start up doing stuff
if (allTables.length) {
	// Create an element we can append our styles to
	var tablecss = document.createElement('style');
	var $tablecss = jQuery(tablecss);

	// Target mobile viewers only. Change the hardcoded value as and when needed
	var outputCss = '@media only screen and (max-width: 500px) {';

	//
	allTables.each(function(tableIndex) {
		var thisTable = jQuery(this);

		// Only do this if we have a header row
		var headerRow = thisTable.find('thead th');
		if (headerRow.length) {

			// Make sure we can something to target
			var tableClass = 'table' + tableIndex;
			thisTable.addClass('mobileTable');
			thisTable.addClass(tableClass);

			// All the basic styling is done in the base css. We need to do the fancy before stuff now
			headerRow.each(function(rowIndex) {
				var headerText = jQuery(this).html();
				console.log(headerText);
				// Strip tags
				headerText = headerText.replace(/(<([^>]+)>)/ig,"");
				console.log(headerText);
				// Replace control chars with spaces
				headerText = headerText.replace(/(\r\n|\n|\r)/gm," ");
				console.log(headerText);
				// Replace double spaces with single spaces
				headerText = headerText.replace(/(\s+)/gm," ");
				console.log(headerText);

				outputCss += "\n\t." + tableClass + ' td:nth-of-type(' + (parseInt(rowIndex) + 1) +'):before { ';
				outputCss += 'content: "' + headerText +'"';
				outputCss += '}';
			});
		}
	});
	outputCss += '}';
	$tablecss.text(outputCss);
	jQuery('body').append($tablecss);
}