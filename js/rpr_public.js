/* Unobtrusive print link
 * Found here: http://trevordavis.net/blog/unobtrusive-javascript-print-link-with-jquery
 */
jQuery(document).ready(function() {
	 jQuery('span.print-link').prepend('<a class="fa fa-print" href="#print"></a>');
	 jQuery('span.print-link a').click(function() {
		 jQuery(rpr_printarea).print();
	  return false;
	 });
	});  