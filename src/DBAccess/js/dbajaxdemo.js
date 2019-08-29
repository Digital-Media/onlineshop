'use strict';

let xhr = new XMLHttpRequest();
let form;
let formData;

function sendAJAXRequest(event)
{
    formData = new FormData(form);

    xhr.open("POST", "dbajaxdemo.php", true);
    xhr.responseType = "json";
    xhr.setRequestHeader("Accept", "application/json");
    xhr.addEventListener("load", handleResponse);
    xhr.send(formData);
    event.preventDefault();
}

function handleResponse()
{
    if (xhr.status === 200) {
        let messages;
        messages = document.getElementById("messages");
        messages.innerHTML="";
        let myObj;
        // If parsing as JSON worked, we can use the response directly
        if (xhr.responseType === "json") {
            myObj = xhr.response;
        } // otherwise we have to parse it ourselves
        else {
            myObj = JSON.parse(xhr.responseText);
        }

        if ("errorMessages" in myObj ) {
            // Add errorMessages below h2 tag
            let errors = document.createElement("div");
            let ulist = document.createElement("ul");
            errors.setAttribute("class", "Error");
            ulist.setAttribute("class", "Error-list");
            messages.appendChild(errors);
            errors.appendChild(ulist);
            if ("ptype" in myObj) {
                let listitem = document.createElement("li");
                listitem.setAttribute("class", "Error-listItem");
                ulist.appendChild(listitem);
                let txt = document.createTextNode(myObj.ptype);
                listitem.appendChild(txt);
            }
        }

        if ("statusMessage" in myObj) {
            // add statusMessage below h2 tag
            let statusmsg = document.createElement("div");
            let p = document.createElement("p");
            let txt = document.createTextNode(myObj.statusMessage);
            statusmsg.setAttribute("class", "Status");
            p.setAttribute("class", "Status-message");
            messages.appendChild(statusmsg);
            statusmsg.appendChild(p);
            p.appendChild(txt);
            // Add the new entry to the pageArray listing and empty the input field
            let table = document.getElementById("table");
            let tbody = table.childNodes;
            let newtr = document.createElement("tr");
            let newtd = document.createElement("td");
            let newtd2 = document.createElement("td");
            // get the AutoIncrement value from the returned json.
            let newtxt = document.createTextNode(myObj.aid);
            let newtxt2;
            for (let pair of formData.entries()) {
                newtxt2 = document.createTextNode(pair[1]);
            }
            tbody[1].append(newtr);
            newtr.append(newtd);
            newtd.appendChild(newtxt);
            newtr.append(newtd2);
            newtd2.appendChild(newtxt2);
            // empty the input fields of the form
            form.reset();
        }
    }

}

document.addEventListener(
    "DOMContentLoaded",
    function () {
        form = document.getElementById("dbajaxdemoform");
        form.addEventListener("submit", sendAJAXRequest);
    }
);


