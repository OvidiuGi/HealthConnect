// import '../../node_modules/jquery/dist/jquery.js';
import $ from 'jquery';

// import 'jquery';

var $add_appointment_date = $('#add_appointment_date')
var $add_appointment_service = $('#add_appointment_service')
// var $token = $('#add_appointment__token')
// if ($add_appointment_date.change() && $add_appointment_service.change())
// {
//     var $form = $add_appointment_date.closest('form')
//
//     var data = {}
//
//     // data[$token.attr('name')] = $token.val()
//     data[$add_appointment_date.attr('name')] = $add_appointment_date.val()
//     data[$add_appointment_service.attr('name')] = $add_appointment_service.val()
//
//     $.post($form.attr('action'), data).then(function (response)
//     {
//         $("#add_appointment_timeInterval").replaceWith(
//             $(response).find("#add_appointment_timeInterval")
//         )
//     })
// }


$('#add_appointment_date, #add_appointment_service').change(function ()
{
    var $form = $(this).closest('form')

    var data = {}

    // data[$token.attr('name')] = $token.val()
    data[$add_appointment_date.attr('name')] = $add_appointment_date.val()
    data[$add_appointment_service.attr('name')] = $add_appointment_service.val()

    $.post($form.attr('action'), data).then(function (response)
    {
        $("#add_appointment_timeInterval").replaceWith(
            $(response).find("#add_appointment_timeInterval")
        )
    })
})