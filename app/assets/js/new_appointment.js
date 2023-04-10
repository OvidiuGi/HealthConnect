import $ from 'jquery';

var $add_appointment_date = $('#add_appointment_date')
var $add_appointment_service = $('#add_appointment_service')

$('#add_appointment_date, #add_appointment_service').change(function ()
{
    var $form = $(this).closest('form')
    var data = {}
    data[$add_appointment_date.attr('name')] = $add_appointment_date.val()
    data[$add_appointment_service.attr('name')] = $add_appointment_service.val()
    data[$add_appointment_service.attr('duration')] = $add_appointment_service.val()
    $.post($form.attr('action'), data).then(function (response)
    {
        $("#add_appointment_startTime").replaceWith(
            $(response).find("#add_appointment_startTime")
        )
    })
})