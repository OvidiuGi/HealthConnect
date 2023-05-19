import '../styles/web.css';
import 'bootstrap';
import '../../node_modules/bootstrap/dist/css/bootstrap.css';
import '../bootstrap';
import $ from 'jquery';
require('bootstrap');

$(document).ready(function () {
    // Add a click event listener to all checkboxes with the class "icheck"
    $('.icheck').click(function () {
        // Get the current checkbox and its ID
        var checkbox = $(this);
        var id = checkbox.attr('id');

        // Uncheck all other checkboxes
        $('.icheck').not(checkbox).prop('checked', false);
    });
});