window.$ = require("jquery");
require("popper.js");
require("../../node_modules/bootstrap/dist/js/bootstrap.bundle.min");

require("./../css/app.scss");

const swal = require("sweetalert2");

"use strict";
$(() => {

    $("[data-toggle=\"popover\"]").popover();

});