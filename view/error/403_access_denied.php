<?php

global $installFolder;

$_SESSION['user']->addNotice('Accès refusé', 'Vous ne disposez pas des droits suffisants !', 'danger');
header('location: '.$installFolder.'home');

echo '403 access denied';
