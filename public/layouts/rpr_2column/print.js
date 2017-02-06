/**
 * Connect the actula print function to the button
 * @param {type} param
 */
jQuery(document).ready(function() {
	jQuery('span.print-link a').click(function() {
			jQuery(print_options.print_area).print({
					globalStyles: false,
					noPrintSelector: print_options.no_print_area,
					stylesheet: print_options.print_css
			});
			return false;
	});
});
