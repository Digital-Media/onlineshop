$(document).ready(function () {
    $("#dbdemoform").submit(function (event) {
        $.post("dbajaxdemo.php", $( "#dbdemoform" ).serialize(), function (data) {
            $("#messages").empty();
            if (data.errorMessages){
                let errMsg  = '<div class="Error">' +
                    '    <ul class="Error-list">';
                Object.keys(data).forEach(function(key) {
                    if (key !== "errorMessages") {
                        errMsg  += '        <li class="Error-listItem">' + data[key] + '</li>'
                    }
                });
                errMsg  +=     '    </ul>' +
                    '</div>';
                $("#messages").append(errMsg);
            }
            if(data.statusMessage) {
                let stgMsg  = '<div class="Status">' +
                    '    <p class="Status-message"><i class="fas fa-check"></i>' + data.statusMessage + '</p>' +
                    '</div>';
                $("#messages").append(stgMsg);
                if (data.aid) {
                    let newEntry =
                        '<tr>\n' +
                        '      <td> ' + data.aid + ' </td>\n' +
                        '      <td> ' + $( "input[name=ptype]" ).val() + ' </td>\n' +
                        '</tr>\n';
                    $(newEntry).appendTo("#table");
                }
                $('#dbdemoform')[0].reset();
            }
        }, "json").fail(function () {
            console.log("Es gab ein Problem mit der Anfrage.");
        });
        event.preventDefault();
    });
});