function upvote(sender, pid, utid) {
    "use strict";
    $.ajax({
        url: "script/upvote.php",
        method: "POST",
        dataType: "html",
        data: {
            project_id: pid,
            utest_id: utid
        },
        timeout: 100000
    }).done(function (msg) {
        if (msg === "SUCCESS") {
            sender.innerHTML = (parseInt(sender.innerHTML) + 1).toString();
            sender.className = "badge pull-right label-success";
            
        } else {
            console.error(msg);
        }
    });
}