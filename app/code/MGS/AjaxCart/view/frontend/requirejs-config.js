var config = {
    map: {
        '*': {
			"mgsajaxcart/owlcarousel": "MGS_AjaxCart/js/owl.carousel.min",
            catalogAddToCart:       'MGS_AjaxCart/js/action/catalog-add-to-cart',
            widgetAddToCart:        'MGS_AjaxCart/js/action/widget-add-to-cart',
        }
    },
    shim: {
		"mgsajaxcart/owlcarousel": "MGS_AjaxCart/js/owl.carousel.min",
        'magnificPopup':           ['jquery']
    },
    paths: {
		"MGS_AjaxCart/js/owl.carousel.min": ["jquery"],
        'magnificPopup':           'MGS_AjaxCart/js/lib/magnific-popup'
    }
};
