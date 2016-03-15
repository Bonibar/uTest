<?php

if (!$_SESSION['user']->isLogged()) {
    header('location: ./home');
}
$_SESSION['user']->logOut();
?>

<div class='login_form'>
    <div class='container'>
        <h1>Déconnexion</h1>
        <div id="annonce">
            Déconnecté avec succès !<br />
            Nous allons automatiquement vous rediriger dans 3 secondes...<br />
            <br />
            <a href='./home'>Cliquez ici</a> si rien ne se passe.
        </div>
    </div>
</div>

<script type="application/javascript">setTimeout(function() { document.location.href = './home'; }, 3000);</script>
