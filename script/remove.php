<?php

$rpath = dirname(__FILE__) . DIRECTORY_SEPARATOR;
include_once $rpath.'../model/user.php';
include_once $rpath.'../model/utest.php';

session_start();

$project = $_SESSION['user']->getProject($_POST['project_id']);

if (!$project) {
    echo "Couldn't find project";
    return (1);
}

$utestid = $_POST['utest_id'];
if (!$utestid) {
    echo "Utest id error";
    return (1);
}

$utest = $project->getUtest($utestid);
if (!$utest || count($utest) != 1) {
    echo "Error getting utest";
    return (1);
}

echo $utest[0]->remove_test($_SESSION['user']->getId());
return (0);
