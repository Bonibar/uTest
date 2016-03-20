function remove_test(sender, pid, utid) {
    "use strict";
    $.ajax({
        url: "script/remove.php",
        method: "POST",
        dataType: "html",
        data: {
            project_id: pid,
            utest_id: utid
        },
        timeout: 100000
    }).done(function (msg) {
        if (msg === "SUCCESS") {
            $(sender).parents(".panel").fadeOut(400, function() { $(sender).parents(".panel").remove(); });
			//$(sender).parents(".panel").remove();
        } else {
            console.error(msg);
        }
    });
}
