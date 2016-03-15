<?php

$rpath = dirname(__FILE__) . DIRECTORY_SEPARATOR;
include_once $rpath.'../model/user.php';

session_start();

$project = $_SESSION['user']->getProject($_POST['project_id']);

if (!$project) {
    echo "Couldn't find project";
    return (1);
}

$cmd = $_POST['cmd'];
if (!$cmd || $cmd == "") {
    echo "Command cannot be empty";
    return (1);
}

$stdin = ($_POST['stdin'] != "" ? $_POST['stdin'] : null);
$stdout = ($_POST['stdout'] != "" ? $_POST['stdout'] : null);
$retval = ($_POST['retval'] != "" ? $_POST['retval'] : null);
$opt_file = ($_POST['opt_file'] != "" ? $_POST['opt_file'] : null);

if ($stdin == null && $stdout == null && $retval == null) {
    echo "Please enter at least an action to perform";
    return (1);
}

date_default_timezone_set("Europe/Paris");
$date = date("Y-m-d H:i:s");

$error = $project->addUtest(new utest(1, $_SESSION['user']->getScore(), $cmd, $stdin, $stdout, $retval, $date, $_SESSION['user']->getId(), $_SESSION['user']->getLogin(), $opt_file), $_SESSION['user']->getId());

if ($error) {
    echo $error;
    return (1);
}

$_SESSION['user']->addNotice('Nouveau uTest', 'Merci de votre contribution !', 'success');

echo "SUCCESS";
return (0);
