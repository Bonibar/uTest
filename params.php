<?php

// SETUP
$LOCAL = true;
$DEBUG = true;

// DEBUG MODE (comment to disable)
if ($LOCAL) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Installation
$installFolder =    '//'.$_SERVER["HTTP_HOST"].'/utest/';

// Database
if ($LOCAL) {
$host =     'localhost';
$sqluser =  'root';
$sqlpass =  '';
$bdd =      'utest';
} else {
$host =     '';
$sqluser =  '';
$sqlpass =  '';
$bdd =      '';
}

// Tables
$bddtable['user'] = 'utest_user';
$bddtable['utest'] = 'utest_utest';
$bddtable['votes'] = 'utest_votes';

// Access level
$min_level = 0;
$banlist = [];

$rank = [
    0 => "Etudiant",
    1 => "Etudiant actif",
    2 => "Etudiant à l'honneur",
    7 => "Beta-testeur",
    10 => "Administrateur"
];
?>