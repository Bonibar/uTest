<?php

/**
 * Contais Project class
 * @package utest_project
 */

$rpath = dirname(__FILE__) . DIRECTORY_SEPARATOR;
include_once $rpath.'utest.php';


/**
 * Class for every project on the website
 * @author Bonibar
 */
class Project
{
    private $pid;
    private $title;
    

    /**
     * Default constructor of a Project
     * All projects are generated based on the Epitech intranet (https://intra.epitech.eu/)
     * @param int $pid Id of the project
     * @param string $title Title of the project
     */
    public function __construct($pid, $title)
    {
        $this->pid = $pid;
        $this->title = $title;
    }
    

    /**
     * Return the title of the Project
     * @return string Title of the Project
     */
    public function getTitle()
    {
        return str_replace('[PROJET] ', '', $this->title);
    }
    

    /**
     * Return the id of the Project
     * @return int Id of the Project
     */
    public function getId()
    {
        return $this->pid;
    }
    

    /**
     * Return a specific associated uTest
     * This function requires a database connection.
     * It returns an instance of the Utest class (see model/utest.php) of the specific utest asked in parameters (or null if not found)
     * @see model/utest.php
     * @param int $utid The id of the uTest to find in the Project Database
     * @return utest Return an instance of the Utest class or null if the test was not found
     */
    public function getUTest($utid)
    {
        global $host, $sqluser, $sqlpass, $bdd, $bddtable;
        $sql = new mysqli($host, $sqluser, $sqlpass, $bdd);
        $result = null;
        if (mysqli_connect_error()) {
            return 'Impossible de se connecter au serveur de base de données !';
        }
        $res = $sql->query("SELECT ".$bddtable['utest'].".*, ".$bddtable['user'].".login FROM ".$bddtable['utest'].", ".$bddtable['user']." WHERE ".$bddtable['utest'].".fk_user=".$bddtable['user'].".id AND ".$bddtable['utest'].".project_id='".$this->pid."' AND ".$bddtable['utest'].".id = '".$utid."' ORDER BY ".$bddtable['utest'].".score ASC, ".$bddtable['utest'].".date DESC;");
        if (!$res) {
            $sql->close();
            return 'Erreur lors de la récupération des tests';
        }
        if ($res->num_rows > 0) {
            if (!$result) {
                $result = array();
            }
            while (($tmp = $res->fetch_array())) {
                $to_insert = new utest($tmp['id'], $tmp['score'], $tmp['command'], $tmp['stdin'], $tmp['stdout'], $tmp['return_value'], date_create_from_format("Y-m-d H:i:s", $tmp['date']), $tmp['fk_user'], $tmp['login'], $tmp['opt_file']);
                array_push($result, $to_insert);
            }
        }
        $res->free();
        $sql->close();
        return $result;
    }
    

    /**
     * Return all associated uTests
     * This function requires a database connection.
     * It returns an array of instances of the Utest class (see model/utest.php)
     * @see model/utest.php
     * @return utest[] Return all uTests associated with the Project or null if there is no utest
     */
    public function getUTests()
    {
        global $host, $sqluser, $sqlpass, $bdd, $bddtable;
        $sql = new mysqli($host, $sqluser, $sqlpass, $bdd);
        $result = null;
        if (mysqli_connect_error()) {
            return 'Impossible de se connecter au serveur de base de données !';
        }
        $res = $sql->query("SELECT ".$bddtable['utest'].".*, ".$bddtable['user'].".login FROM ".$bddtable['utest'].", ".$bddtable['user']." WHERE ".$bddtable['utest'].".fk_user=".$bddtable['user'].".id AND ".$bddtable['utest'].".project_id='".$this->pid."' ORDER BY ".$bddtable['utest'].".score DESC, ".$bddtable['utest'].".date DESC;");
        if (!$res) {
            $sql->close();
            return 'Erreur lors de la récupération des tests';
        }
        if ($res->num_rows > 0) {
            if (!$result) {
                $result = array();
            }
            while (($tmp = $res->fetch_array())) {
                $to_insert = new utest($tmp['id'], $tmp['score'], $tmp['command'], $tmp['stdin'], $tmp['stdout'], $tmp['return_value'], date_create_from_format("Y-m-d H:i:s", $tmp['date']), $tmp['fk_user'], $tmp['login'], $tmp['opt_file']);
                array_push($result, $to_insert);
            }
        }
        $res->free();
        $sql->close();
        return $result;
    }
    

    /**
     * Add a uTest to the Project
     * This function requires a database connection.
     * This function associate a utest (see model/utest.php) to the Project
     * @see model/utest.php
     * @param utest $utest Instance of the Utest class to add to the project
     * @param int $uid Id of the user
     * @return string If the test was added, returns null. Otherwise, return the error message
     */
    public function addUTest($utest, $uid)
    {
        global $host, $sqluser, $sqlpass, $bdd, $bddtable;
        $sql = new mysqli($host, $sqluser, $sqlpass, $bdd);
        if (mysqli_connect_error()) {
            return 'Impossible de se connecter au serveur de base de données !';
        }
        $stdin = 'NULL';
        if ($utest->getStdin() != null) {
            $stdin = "'".$utest->getStdin()."'";
        }
        $stdout = 'NULL';
        if ($utest->getStdout() != null) {
            $stdout = "'".$utest->getStdout()."'";
        }
        $ret = 'NULL';
        if ($utest->getReturnValue() != null) {
            $ret = $utest->getReturnValue();
        }
        $opt_file = 'NULL';
        if ($utest->getOptFile() != null) {
            $opt_file = "'".$utest->getOptFile()."'";
        }
        $res = $sql->query("INSERT INTO ".$bddtable['utest']." (project_id, fk_user, score, command, stdin, stdout, return_value, opt_file, date) VALUES (".$this->getId().", ".$uid.", 0, '".$utest->getCommand()."', ".$stdin.", ".$stdout.", ".$ret.", ".$opt_file.", '".date("Y-m-d H:i:s")."');");
        if (!$res) {
            $sql->close();
            return 'Erreur lors de la création du test unitaire !';
        }
        $sql->close();
        return null;
    }
}
