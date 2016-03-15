<?php

$rpath = dirname(__FILE__).DIRECTORY_SEPARATOR;
include_once $rpath.'../model/user.php';

session_start();

function get_type_code($url, $form)
{
    $chc = curl_init();
    
    curl_setopt($chc, CURLOPT_URL, "https://intra.epitech.eu".$url."?format=json");
    curl_setopt($chc, CURLOPT_POST, 1);
    curl_setopt($chc, CURLOPT_POSTFIELDS, $form);
    curl_setopt($chc, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($chc, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($chc, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($chc, CURLOPT_HEADER, 1);

    if (($code = curl_exec($chc)) == false) {
        return "Curl Error: ".curl_error($chc);
    }
    curl_close($chc);
    
    list($header, $content) = explode("\r\n\r\n", $code, 2);
    $content = str_replace("// Epitech JSON webservice ...\n", "", $content);
    $json = json_decode($content);
    return $json->{"type_code"};
}

if (isset($_POST['login']) && isset($_POST['password'])) {
    $login = $_POST['login'];
    $password = $_POST['password'];
    $form = "login=" . urlencode($login) . "&password=" . urlencode($password). "&remind=true";
    
    $chc = curl_init();
    
    curl_setopt($chc, CURLOPT_URL, "https://intra.epitech.eu?format=json");
    curl_setopt($chc, CURLOPT_POST, 1);
    curl_setopt($chc, CURLOPT_POSTFIELDS, $form);
    curl_setopt($chc, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($chc, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($chc, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($chc, CURLOPT_HEADER, 1);

    if (($code = curl_exec($chc)) == false) {
        echo "<div>Curl Error: ".curl_error($chc)."</div>";
    }
    curl_close($chc);
    
    list($header, $content) = explode("\r\n\r\n", $code, 2);
    $content = str_replace("// Epitech JSON webservice ...\n", "", $content);
    $json = json_decode($content);
    
    date_default_timezone_set('Europe/Paris');
    $date = date('m/d/Y h:i:s a', time());
    $compareDate = DateTime::createFromFormat('j/m/Y, H\hi', date('j/m/Y, H\hi', time()));
    
    if (isset($json->{"message"})) {
        echo "<div>Identifiants incorrects.</div>";
    } else {
        if (($error = $_SESSION['user']->logIn($_POST['login'], $json->{"infos"}->{"title"}, $json->{"infos"}->{"location"}, $json->{"infos"}->{"promo"}))) {
            echo "<div>Une erreur est survenue lors de la connexion : ".$error."</div>";
            return (1);
        }
        
        $proj = $json->{"board"}->{"projets"};
        $_SESSION['user']->clearProjects();

        foreach ($proj as $value) {
            $type_code = get_type_code($value->{"title_link"}, $form);
            if ($type_code == "proj") {
                if ($value->{"timeline_end"} == false) {
                    $_SESSION['user']->addProject($value->{"id_activite"}, $value->{"title"});
                } else {
                    $dateProjectBgn = DateTime::createFromFormat('j/m/Y, H:i', $value->{"timeline_start"});
                    $dateProjectEnd = DateTime::createFromFormat('j/m/Y, H:i', $value->{"timeline_end"});
                    if ($dateProjectBgn <= $compareDate && $dateProjectEnd >= $compareDate) {
                        $_SESSION['user']->addProject($value->{"id_activite"}, $value->{"title"});
                    }
                }
            }
        }
        
        echo "<div class='success'>Bonjour ". $_POST['login'] .", heureux de vous voir sur uTest !<br />Nous allons vous rediriger automatiquement dans 3 secondes.<br /><br /><a href='./home'>Cliquez ici</a> si rien ne se passe.</div>";
    }
} else {
    echo "<div>Erreur</div>";
}
