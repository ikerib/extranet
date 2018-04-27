
window.$ = require('jquery');
require("popper.js");
require('bootstrap');
import fontawesome from '../../node_modules/@fortawesome/fontawesome'
import faUser from '../../node_modules/@fortawesome/fontawesome-free-solid/faUser'

fontawesome.library.add(faUser);

require('./../css/app.scss');

const swal = require("sweetalert2");

'use strict';
$(() => {
    console.log("kaixoooooooooo");
});