jQuery.fn.extend({
    disable: function(state) {
        return this.each(function() {
            this.disabled = state;
        });
    }
});

function addUtest(project_id) {
    var cmd = $("#nut_cmd").val().replace(/'/g, '"');
    
    $("#nut_cmd").parent().parent().removeClass("has-error");
    $("#nut_stdin").parent().parent().removeClass("has-error");
    $("#nut_stdout").parent().parent().removeClass("has-error");
    $("#nut_retval").parent().parent().removeClass("has-error");
    
    if (cmd == "" || (cmd.match(new RegExp('"', "g")) || []).length % 2 == 1) {
        $("#nut_cmd").parent().parent().addClass("has-error");
        return 1;
    }
    var stdin = $("#nut_stdin").val();
    var stdout = $("#nut_stdout").val();
    var retval = $("#nut_retval").val();
    var opt_file = $("#nut_opt_file").val();
    
    if (stdin == "" && stdout == "" && retval == "") {
        $("#nut_stdin").parent().parent().addClass("has-error");
        $("#nut_stdout").parent().parent().addClass("has-error");
        $("#nut_retval").parent().parent().addClass("has-error");
        return 1;
    }
    
    $("#nut_reset").disable(true);
    $("#nut_submit").disable(true);
    
    $.ajax({
        url: "script/addUtest.php",
        method: "POST",
        dataType: "html",
        data: {
            project_id: project_id,
            cmd: cmd,
            stdin: stdin,
            stdout: stdout,
            retval: retval,
            opt_file: opt_file
        },
        timeout: 100000
    }).done(function(msg) {
        if (msg != "SUCCESS") {
            console.error(msg);
            $("#nut_reset").disable(false);
            $("#nut_submit").disable(false);
        } else {
            document.location.href = './project/'+project_id;
        }
    });
    return 0;
}
