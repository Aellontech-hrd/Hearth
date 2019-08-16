define([
   'jquery',
   'tippedjs'
], function($, Tipped){
   'use strict';

   return function (config, element) {
         $(document).ready(function() {
            $('.mageinn-hint-toggle').each(function () {
               Tipped.create(this, $(this).next('div:hidden'), {showOn: 'click', hideOn: 'click', hideOnClickOutside: true});
            });
         });
   }

});