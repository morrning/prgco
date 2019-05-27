function jsCoreRun()
{
    //add small size to bootstrap controls
    $('.form-control').addClass('form-control-sm');
    $('button').addClass('btn-sm');
    $('a.btn').addClass('btn-sm');
    $('.card-header').addClass('py-2');
}

var SysIsModalOn = false;

$(document).ready(function(){
    $('.JdateInput').mask('0000/00/00');
    $('.MoneyInput').mask('000,000,000,000,000',{reverse: true})
});

function SysChangeValueMoney(value,id)
{
    $('.MoneyInput' + id).val(value.replace(/,/g,''));
}
function SysCheckMax(value,id,maxValue)
{
    if(! $.isNumeric(value))
    {
        alert();
    }
}
function SysLoadMsg(helpID) {
    $.ajax({
        url: Routing.generate('SystemLoadMessage', { 'id': helpID }),
        success: function(data) {
            SysCreateModal(data)
        }
    });
}

function SysCheckDateInput(date,id)
{
    if(date.length != 0)
    {
        $.ajax({
            url: Routing.generate('SystemJdateValidation', { 'jdate': '**' + date + '**' }),
            success: function(data) {
                if(data != 'ok')
                {
                    SysCreateModal(data)
                    $('.jdateInputTextbox' + id).addClass('is-invalid').removeClass('is-valid');
                    $('.jdateInputTextbox' + id).val('');
                    $('.jdateIconValidation' + id).html('close');
                }
                else
                {
                    $('.jdateInputTextbox' + id).addClass('is-valid').removeClass('is-invalid');
                    $('.jdateIconValidation' + id).html('done');
                }

            }
        });
    }

}

