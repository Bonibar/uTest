<?php
global $installFoder;
if ($_SESSION['user']->isLogged()) {
    header('location: '.$installFolder.'home');
}
?>

<div class="bs-docs-content">
    <div class="page-header">
        <h1>Connexion</h1>
        <div class="text-warning" id="annonce"></div>
    </div>
    <div id="login_form" class='form'>
        <div class="form-group">
            <label class="control-label" for="login">Login</label>
            <input type="text" class="form-control" id="login" placeholder="Login" autofocus>
        </div>
        <div class="form-group">
            <label class="control-label" for="password">Password</label>
            <input type="password" class="form-control" id="password" placeholder="Password Unix">
        </div>
            <button id="submit" class="btn btn-primary">Connexion</button>
    </div>
</div>

<!-- Scripts -->
<script type="application/javascript" src="js/connect.js"></script>