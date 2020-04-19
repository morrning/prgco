//writed by babak alizadeh for rahkarbase system
function showModal(onclick,type='info',title=null,body=null){
    if(title == null)
        title = 'پیام';
    if(body == null)
        body = 'آیا برای انجام این عملیات مطمئن هستید؟';

    var modalTemplate ='<div class="modal fade" id="SystemModalCenter" tabindex="-1" role="dialog" aria-labelledby="SystemModalCenter" aria-hidden="true">\n' +
        '    <div class="modal-dialog" role="document">\n' +
        '        <div class="modal-content">\n' +
        '            <div class="modal-header bg-success text-light">\n' +
        '                <h5 class="modal-title" id="exampleModalLabel">' + title +'</h5>\n' +
        '                <button type="button" class="close" data-dismiss="modal" aria-label="Close">\n' +
        '                    <span aria-hidden="true">&times;</span>\n' +
        '                </button>\n' +
        '            </div>\n' +
        '            <div class="modal-body">\n' +
        '                <p>' +
        body +
        '</p>\n' +
        '            </div>\n' +
        '      <div class="modal-footer">\n' +
        '        <button type="button" class="btn btn-primary" onclick="$(' + "'.modal').modal(" + "'toggle');"  +
        onclick +
        '">قبول</button>\n' +
        '        <button type="button" class="btn btn-secondary" data-dismiss="modal">بازگشت</button>\n' +
        '      </div>\n' +
        '        </div>\n' +
        '    </div>\n' +
        '</div>'

    if(SysIsModalOn == false)
    {
        $('.SystemModal').empty();
        $('.SystemModal').append(modalTemplate);
        $('#SystemModalCenter').on('show.bs.modal', function (e) {
            SysIsModalOn = true;
        })

        $('#SystemModalCenter').on('hidden.bs.modal', function (e) {
            SysIsModalOn = false;
        })
        $('#SystemModalCenter').modal();
    }
}

function showModalNotification(title,body,type, reload=false){
    var modalTemplate ='<div class="modal fade" id="SystemModalCenter" tabindex="-1" role="dialog" aria-labelledby="SystemModalCenter" aria-hidden="true">\n' +
        '    <div class="modal-dialog" role="document">\n' +
        '        <div class="modal-content">\n' +
        '            <div class="modal-header bg-'+
        type +
        ' text-light">\n' +
        '                <h5 class="modal-title" id="exampleModalLabel">' + title +'</h5>\n' +
        '                <button type="button" class="close" data-dismiss="modal" aria-label="Close">\n' +
        '                    <span aria-hidden="true">&times;</span>\n' +
        '                </button>\n' +
        '            </div>\n' +
        '            <div class="modal-body">\n' +
        '                <p>' +
        body +
        '</p>\n' +
        '            </div>\n' +
        '      <div class="modal-footer">\n' +
        '        <button type="button" class="btn btn-primary" onclick="$(' + "'.modal').modal(" + "'toggle');"  +
        '">قبول</button>\n' +
        '      </div>\n' +
        '        </div>\n' +
        '    </div>\n' +
        '</div>'

    if(SysIsModalOn == false)
    {
        $('.SystemModal').empty();
        $('.SystemModal').append(modalTemplate);
        $('#SystemModalCenter').on('show.bs.modal', function (e) {
            SysIsModalOn = true;
        })

        $('#SystemModalCenter').on('hidden.bs.modal', function (e) {
            SysIsModalOn = false;
        })
        $('#SystemModalCenter').modal();
    }

}

function SysCreateModal(data)
{
    if(SysIsModalOn == false)
    {
        $('.SystemModal').empty();
        $('.SystemModal').append(data);
        $('#SystemModalCenter').on('show.bs.modal', function (e) {
            SysIsModalOn = true;
        })

        $('#SystemModalCenter').on('hidden.bs.modal', function (e) {
            SysIsModalOn = false;
        })
        $('#SystemModalCenter').modal();
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
