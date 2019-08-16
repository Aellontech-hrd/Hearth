require([
	'jquery'
], function(jQuery){
	(function($) {
		$(document).ready(function(){
			if (document.location.pathname.indexOf("/content/brands/") == 0) {
			    $("body").addClass("wp-brands");
			}
			if (document.location.pathname.indexOf("/content/products/") == 0) {
			    $("body").addClass("wp-products");
			}
		});
		
	})(jQuery);
});