<?php

$rpath = dirname(__FILE__) . DIRECTORY_SEPARATOR;
include_once $rpath.'project.php';
include_once $rpath.'notice.php';
include_once $rpath.'../params.php';

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
    
    public function isLogged()
    {
        return $this->isLogged;
    }
    
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
    
    public function getLogin()
    {
        return $this->login;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function clearProjects()
    {
        unset($this->projects);
        $this->projects = array();
    }
    
    public function addProject($pid, $tmp_title)
    {
        $to_insert = new Project($pid, $tmp_title);
        if (!in_array($to_insert, $this->projects, true)) {
            array_push($this->projects, $to_insert);
        }
    }
    
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
    
    public function getProjects()
    {
        return $this->projects;
    }
    
    public function addNotice($title, $content, $type, $perm = false)
    {
        $to_insert = new Notice($title, $content, $type, $perm);
        array_push($this->notices, $to_insert);
    }
    
    public function getNotices()
    {
        return $this->notices;
    }
    
    public function clearNotices()
    {
        unset($this->notices);
        $this->notices = array();
    }
    
    public function getScore()
    {
        return $this->score;
    }
    
    public function getLevel()
    {
        return $this->level;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getCity()
    {
        return $this->city;
    }
    
    public function getPromo()
    {
        return $this->promo;
    }
    
    public function logOut()
    {
        $this->isLogged = false;
        $this->clearNotices();
        $this->clearProjects();
        $this->level = -1;
    }
}
