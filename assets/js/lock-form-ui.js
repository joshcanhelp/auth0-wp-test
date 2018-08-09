/* global jQuery, console */
document.addEventListener("DOMContentLoaded", function() {
    'use strict';

    var $ = jQuery;

    $('.auth0-lock-submit').ready(function () {
        $(this).hide();
        // TODO: Remove debugging
        console.log( 'wat' );
    });
    // TODO: Remove debugging
    $('.auth0-lock-input').css({border: "red 1px solid"});

});