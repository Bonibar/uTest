<?php

global $installFolder;

$_SESSION['user']->addNotice('Chemin non trouvé', 'La page demandé n\'existe pas !', 'danger');
header('location: '.$installFolder.'home');

echo '404 not found';
