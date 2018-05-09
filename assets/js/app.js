
window.$ = require('jquery');
require("popper.js");
require('bootstrap');
import fontawesome from '../../node_modules/@fortawesome/fontawesome'

import faUser from '@fortawesome/fontawesome-free-solid/faUser'
import faEye from "@fortawesome/fontawesome-free-solid/faEye"


fontawesome.library.add(faUser);
fontawesome.library.add(faEye);

require('./../css/app.scss');

const swal = require("sweetalert2");

'use strict';
$(() => {
    console.log("kaixoooooooooo");
});