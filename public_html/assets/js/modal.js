//writed by babak alizadeh for rahkarbase system
function showModal(onclick,type='info',title=null,body=null){
    if(title == null)
        title = 'پیام';
    if(body == null)
        body = 'آیا برای انجام این عملیات مطمئن هستید؟';
    var modalString =
        '<div class="modal" tabindex="-1" role="dialog">\n' +
        '  <div class="modal-dialog modal-dialog-centered" role="document">\n' +
        '    <div class="modal-content">\n' +
        '      <div class="modal-header bg-' + type + ' text-light">\n' +
        '        <h5 class="modal-title">' +
        title +
        '</h5>\n' +
        '        <button type="button" class="close" data-dismiss="modal" aria-label="Close">\n' +
        '          <span aria-hidden="true">&times;</span>\n' +
        '        </button>\n' +
        '      </div>\n' +
        '      <div class="modal-body">\n' +
        '        <p>' +
        body +
        '</p>\n' +
        '      </div>\n' +
        '      <div class="modal-footer">\n' +
        '        <button type="button" class="btn btn-primary" onclick="$(' + "'.modal').modal(" + "'toggle');"  +
            onclick +
        '">قبول</button>\n' +
        '        <button type="button" class="btn btn-secondary" data-dismiss="modal">بازگشت</button>\n' +
        '      </div>\n' +
        '    </div>\n' +
        '  </div>\n' +
        '</div>';
    $('#modalPart').html(modalString);
    $('.modal').modal('show');
}

function showModalNotification(title,body,type, reload=false){
    var modalString =
        '<div class="modal" tabindex="-1" role="dialog">\n' +
        '  <div class="modal-dialog modal-dialog-centered" role="document">\n' +
        '    <div class="modal-content">\n' +
        '      <div class="modal-header bg-' + type + ' text-light">\n' +
        '        <h5 class="modal-title">' +
        title +
        '</h5>\n' +
        '        <button type="button" class="close" data-dismiss="modal" aria-label="Close">\n' +
        '          <span aria-hidden="true">&times;</span>\n' +
        '        </button>\n' +
        '      </div>\n' +
        '      <div class="modal-body">\n' +
        '        <p>' +
        body +
        '</p>\n' +
        '      </div>\n' +
        '      <div class="modal-footer">\n' +
        '        <button type="button" class="btn btn-secondary" data-dismiss="modal"' ;
        if(reload == true){
            modalString +=
                ' onclick="' + 'location.reload(true);' + '" '
        }
        modalString +=
        '>بازگشت</button>\n' +
        '      </div>\n' +
        '    </div>\n' +
        '  </div>\n' +
        '</div>';
    $('#modalPart').html(modalString);
    $('.modal').modal('show');

}
