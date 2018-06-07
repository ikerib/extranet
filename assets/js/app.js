window.$ = require("jquery");
require("popper.js");
require("../../node_modules/bootstrap/dist/js/bootstrap.bundle.min");

require("./../css/app.scss");

const swal = require("sweetalert2");

"use strict";
$(() => {

    $("[data-toggle=\"popover\"]").popover();

    $("#btnDelete").on("click", function (e) {
        e.preventDefault();

        if ($(this).hasClass('disabled')) {
            return;
        }

        let array_files_to_delete = [];

        let sel = $("input[name='seleccion']:checked");
        $.each(sel, function(index,value){
            array_files_to_delete.push($(value).data("oldfilename"));
        });

        $('#form_filefolders').val(JSON.stringify(array_files_to_delete));

        swal({
            title: 'Ziur zaude?',
            text: "Ezingo duzu atzera egin!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Bai, ezabatu!'
        }).then((result) => {
            if (result.value) {
                $("#filesFoldersDeleteForm").submit();
            }
        })

    });

});