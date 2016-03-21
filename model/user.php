<?php

/**
 * Contains User class
 * @package utest_user
 */

$rpath = dirname(__FILE__) . DIRECTORY_SEPARATOR;
include_once $rpath.'project.php';
include_once $rpath.'notice.php';
include_once $rpath.'../params.php';


/**
 * Class that represente the User
 * This class is stored in $SESSION variables
 * @package utest_user_class
 * @author Bonibar
 */
class User
{
    private $login;
    private $name;
    private $city;
    private $promo;
    private $projects = array();
    
    private $id;
    private $score;
    private $level;
    
    private $isLogged = false;
    private $notices = array();
    

    /**
     * Update uTest User infos
     * This function requires a database connection.
     * Update infos stored in uTest database
     * @return string Error message or null if all goes well
     */
    public function getInternalUser()
    {
        global $host, $sqluser, $sqlpass, $bdd, $bddtable;
        $sql = new mysqli($host, $sqluser, $sqlpass, $bdd);
        if (($def = mysqli_connect_error())) {
            return 'Impossible de se connecter au serveur de base de données ! '.$def;
        }
        $res = $sql->query("SELECT * FROM ".$bddtable['user']." WHERE login='".$this->login."' AND promo='".$this->promo."';");
        if (!$res) {
            $sql->close();
            return 'Erreur lors de la vérification du compte !';
        }
        if ($res->num_rows > 1) {
            return 'Comptes multiples. Merci de prendre contact avec un administrateur.';
        } elseif ($res->num_rows == 0) {
            $res->free();
            $res = $sql->query("INSERT INTO ".$bddtable['user']." (login, promo, city, score, level) VALUES ('".$this->login."', ".$this->promo.", '".$this->city."', 0, 0);");
            if (!$res) {
                $sql->close();
                return 'Erreur lors de la création du compte !';
            }
            return 'reload';
        }
        $row = $res->fetch_array();
        if (!$row) {
            return 'Erreur lors de la récupération du compte !';
        }
        $this->id = $row['id'];
        $this->score = $row['score'];
        $this->level = $row['level'];
        $res->free();
        $sql->close();
        return null;
    }
    

    /**
     * Return all Users with the same promo ordered by score
     * This function requires a database connection.
     * @return mixed Array of database representation of Users or a string containing the error message
     */
    public function getRankedUsers()
    {
        global $host, $sqluser, $sqlpass, $bdd, $bddtable;
        $sql = new mysqli($host, $sqluser, $sqlpass, $bdd);
        if (($def = mysqli_connect_error())) {
            return 'Impossible de se connecter au serveur de base de données ! '.$def;
        }
        $res = $sql->query("SELECT * FROM ".$bddtable['user']." WHERE promo=".$this->promo." ORDER BY score DESC;");
        if (!$res) {
            $sql->close();
            return 'Erreur lors de la récupération des utilisateurs !';
        }
        if ($res->num_rows == 0) {
            return 'Erreur lors de la récupération des utilisateurs !';
        } else {
            $users = [];
            while (($row = $res->fetch_array())) {
                array_push($users, $row);
            }
        }
        $res->free();
        $sql->close();
        return $users;
    }
    

    /**
     * Return the connection state of the User
     * @return bool True if the User is logged in. Otherwise, false.
     */
    public function isLogged()
    {
        return $this->isLogged;
    }
    

    /**
     * Try to connect the User
     * Check credentials or if the User is not banned
     * You must not trust return value and always check isLogged() function
     * @param string $user Users' Epitech login
     * @param string $name Users' real name
     * @param string $city Users' Epitech city
     * @param string $promo Users' Epitech promotion
     * @return int Error message or null if all goes well
     */
    public function logIn($user, $name, $city, $promo)
    {
        global $min_level, $banlist;
        $this->login = $user;
        $this->name = $name;
        $this->city = $city;
        $this->promo = $promo;
        $error = $this->getInternalUser();
        if ($error) {
            if ($error == 'reload') {
                $error = $this->getInternalUser();
                if ($error) {
                    return $error;
                }
            } else {
                return $error;
            }
        }
        if ($this->level < $min_level || in_array($user, $banlist)) {
            return 'Accès refusé';
        }
        $this->isLogged = true;
        return null;
    }
    

    /**
     * Return the login of the User
     * @return string Login of the User
     */
    public function getLogin()
    {
        return $this->login;
    }
    

    /**
     * Return the full name of the User
     * @return string Full name of the User
     */
    public function getName()
    {
        return $this->name;
    }
    

    /**
     * Clear saved projects from memory
     * @see model/project.php
     */
    public function clearProjects()
    {
        unset($this->projects);
        $this->projects = array();
    }
    

    /**
     * Add a project in User's memory
     * This function creates its own instance of the Project class
     * @see model/project.php
     * @param int $pid Project Epitech id
     * @param string $tmp_title Project title
     */
    public function addProject($pid, $tmp_title)
    {
        $to_insert = new Project($pid, $tmp_title);
        if (!in_array($to_insert, $this->projects, true)) {
            array_push($this->projects, $to_insert);
        }
    }
    

    /**
     * Return an instance of a Project based on it's id
     * Check if the User has a project with the asked id. If so, it returns it (see model/project.php)
     * Be carefull ! If running in DEBUG mode (see param.php), it will always return an instance of a project withotu checking it's id
     * @see model/project.php
     * @return project Return the asked project or null if it doesn't exist for the current User
     */
    public function getProject($projectId)
    {
        global $DEBUG;
        
        foreach ($this->projects as $project) {
            if ($project->getId() == $projectId) {
                return $project;
            }
        }
        if ($DEBUG && $this->level >= 10) {
            return new Project($projectId, "Be carefull, Running in debug mode");
        }
        return null;
    }
    

    /**
     * Return all Users' projects
     * @return project[] Array containing all Users' projects (can't be null)
     */
    public function getProjects()
    {
        return $this->projects;
    }
    

    /**
     * Store a new Notice in User's memory
     * This function will create its own instance of the Notice class (see model/notice.php)
     * @see model/notice.php
     * @param string $title Title of the Notice
     * @param string $content Content of the Notice
     * @param string $type Type of the Notice (see model/notice.php for possible values)
     * @param bool $perm If true, the Notice will always be displayed (default: false)
     */
    public function addNotice($title, $content, $type, $perm = false)
    {
        $to_insert = new Notice($title, $content, $type, $perm);
        array_push($this->notices, $to_insert);
    }
    

    /**
     * Return all User's Notices
     * @return Notice[] Array containing all User's Notices (can't be null)
     */
    public function getNotices()
    {
        return $this->notices;
    }
    

    /**
     * Clear all Notices in User's memory
     * @see model/notice.php
     */
    public function clearNotices()
    {
        unset($this->notices);
        $this->notices = array();
    }
    

    /**
     * Return the current score of the User
     * The score is based on Upvotes from other Users
     * @return int Score of the User
     */
    public function getScore()
    {
        return $this->score;
    }
    

    /**
     * Return the Level of the User
     * @return int Level of the User
     */
    public function getLevel()
    {
        return $this->level;
    }
    

    /**
     * Return the id of the User
     * @return int Id of the User
     */
    public function getId()
    {
        return $this->id;
    }
    

    /**
     * Return the city of the User
     * The city Respect the Epitech format
     * @return string City of the User
     */
    public function getCity()
    {
        return $this->city;
    }
    

    /**
     * Return the promo of the User
     * @return int Epitech promotion of the User
     */
    public function getPromo()
    {
        return $this->promo;
    }
    

    /**
     * Disconnect the User
     */
    public function logOut()
    {
        $this->isLogged = false;
        $this->clearNotices();
        $this->clearProjects();
        $this->level = -1;
    }
}
