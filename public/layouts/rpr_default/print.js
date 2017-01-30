/**
 * Connect the actula print function to the button
 * @param {type} param
 */
jQuery(document).ready(function() {
	jQuery('span.print-link').prepend('<a class="fa fa-print" href="#print"> Print</a>');
	jQuery('span.print-link a').click(function() {
		jQuery(rpr_printarea).print();
	return false;
	});
});