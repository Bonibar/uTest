function logmeIn() {
    var login = $("#login");
    var pass = $("#password");
    
    $("#login_form").hide();
    $("#annonce").children().remove();
    $("#annonce").append("<img src=\"image/loading.gif\" />").show();
    
    $.ajax({
        url: "script/connect.php",
        method: "POST",
        dataType: "html",
        data: "login=" + login.val() + "&password=" + encodeURIComponent(pass.val()),
        timeout: 100000
    }).done(function(data) {
        $("#annonce").children().remove();
        $("#annonce").append(data);
        if ($(".success").length <= 0) {
            $("#login_form").fadeIn('fast');
            $("#password").val("");
        } else {
            $("#annonce").removeClass("text-warning");
            setTimeout(function() { document.location.href = './home'; }, 3000);
        }
    }).fail(function(data) {
        $("#annonce").children().remove();
        $("#annonce").append("<div>" + data.status + " : " + data.statusText + "</div>");
        if ($(".success").length <= 0) {
            $("#login_form").fadeIn('fast');
            $("#password").val("");
        }
    });
}

$(document).ready(function() {
    $("#annonce").hide();
    
    $(document).keypress(function(key) {
        if (key.which == 13) {
            if ($("#login").val() != "" && $("#password").val() != "")
                logmeIn();
        }
    });
    
    $("#submit").click(logmeIn);
});