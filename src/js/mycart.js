$(document).ready(function (){
    $('.input_qty').on('change keyup', function() {
        var quantity = $(this).val();
        var pid = $(this).attr("id");
        $.post("mycart.php", {quantity: quantity, pid: pid }, function(result){
            var myObj = JSON.parse(result);
            var messages = document.getElementById("messages");
            messages.innerHTML="";
            if ("errorMessages" in myObj ) {
                var errors = document.createElement("div");
                var ulist = document.createElement("ul");
                var listitem = document.createElement("li");
                errors.setAttribute("class", "Error");
                ulist.setAttribute("class", "Error-list");
                listitem.setAttribute("class", "Error-listItem");
                messages.appendChild(errors);
                errors.appendChild(ulist);

                myObj.errorMessages.forEach(function(error) {
                    ulist.appendChild(listitem);
                    if ("pid" in error) {
                        var txt = document.createTextNode(error.pid);
                    }
                    if ("quantity" in error) {
                        var txt = document.createTextNode(error.quantity);
                    }
                    listitem.appendChild(txt);
                })
            }
            if ("statusMessage" in myObj) {
                var statusmsg = document.createElement("div");
                var p = document.createElement("p");
                var txt = document.createTextNode(myObj.statusMessage);
                statusmsg.setAttribute("class", "Status");
                p.setAttribute("class", "Status-message");
                messages.appendChild(statusmsg);
                statusmsg.appendChild(p);
                p.appendChild(txt);
            }
            if ("deleteMessage" in myObj) {
                var td = document.getElementById(pid).parentElement;
                var tr = td.parentElement;
                var table = tr.parentElement;
                table.removeChild(tr);
                if (table.children.length == 1) {
                    var p = document.createElement("p");
                    var txt = document.createTextNode("No Products in Card");
                    p.appendChild(txt);
                    var a = document.getElementById("checkout").parentNode;
                    a.replaceChild(p, document.getElementById("checkout"));
                }
                var deletemsg = document.createElement("div");
                var p = document.createElement("p");
                var txt = document.createTextNode(myObj.deleteMessage);
                deletemsg.setAttribute("class", "Status");
                p.setAttribute("class", "Status-message");
                messages.appendChild(deletemsg);
                deletemsg.appendChild(p);
                p.appendChild(txt);
            }
        });
    });
});