function addDownload(sender, id) {
    $.ajax({
        url: "script/addDownload.php",
        method: "POST",
        dataType: "html",
        data: {
            id: id
        },
        timeout: 100000
    }).done(function(msg) {
        if (msg == "ADDED") {
            sender.className = "panel panel-success";
            sender.setAttribute("data-original-title", "Déselectionner ce test");
        } else if (msg == "DELETED") {
            var ptye = sender.getAttribute("data-paneltype");
            sender.className = "panel panel-" + ptye;
            sender.setAttribute("data-original-title", "Sélectionner ce test");
        }
    });
}
