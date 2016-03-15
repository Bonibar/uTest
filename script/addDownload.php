<?php

$rpath = dirname(__FILE__) . DIRECTORY_SEPARATOR;
include_once $rpath.'../model/user.php';

session_start();

if (isset($_POST['id'])) {
    if (isset($_SESSION['download_ids']) && $_SESSION['download_ids'] != '') {
        $ids = explode(";", $_SESSION['download_ids']);
        if ($ids && in_array($_POST['id'], $ids)) {
            $key = array_search($_POST['id'], $ids);
            unset($ids[$key]);
            $_SESSION['download_ids'] = implode(";", $ids);
            echo "DELETED";
        } else {
            $_SESSION['download_ids'] .= ";".$_POST['id'];
            echo "ADDED";
        }
    } else {
        $_SESSION['download_ids'] = $_POST['id'];
        echo "ADDED";
    }
} else {
    echo "ERROR";
}
