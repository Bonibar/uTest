<?php

class Router
{
    private $path = 'p';
    
    private $route = [
        'default'       =>      ['view/home.php', 'Accueil'],
        'home'          =>      ['view/home.php', 'Accueil'],
        'connect'       =>      ['view/connection.php', 'Connexion'],
        'disconnect'    =>      ['view/disconnection.php', 'Déconnexion'],
        'project'       =>      ['view/project.php', 'Vos projets'],
        'rank'          =>      ['view/rank.php', 'Classement'],

        '403'           =>      ['view/error/403_access_denied.php', 'Permission denied'],
        '404'           =>      ['view/error/404_not_found.php', 'File not found'],
        'wrong_route'   =>      ['view/error/wrong_route.php', 'Route error'],
    ];
    
    private $keys = array();
    
    public function route()
    {
        if (!isset($_GET[$this->path])) {
            $key = 'default';
        } else {
            $key = $_GET[$this->path];
        }
        
        $keys = explode('/', $key);
        $path = $keys[0];
        unset($keys[0]);
        unset($this->keys);
        $this->keys = $keys;
        
        if (array_key_exists($path, $this->route)) {
            $dest = $this->route[$path][0];
        } else {
            $dest = $this->route['404'][0];
        }
        
        if (file_exists($dest)) {
            include $dest;
        } else {
            if (isset($this->route['wrong_route']) && file_exists($this->route['wrong_route'][0])) {
                include $this->route['wrong_route'][0];
            } else {
                echo 'error : couldn\'t find wrong_route.php';
            }
        }
    }
    
    public function getSubtitle()
    {
        if (!isset($_GET[$this->path])) {
            $key = 'default';
        } else {
            $key = $_GET[$this->path];
        }
        
        $keys = explode('/', $key);
        $path = $keys[0];
        unset($keys[0]);
        unset($this->keys);
        $this->keys = $keys;
        
        if (array_key_exists($path, $this->route)) {
            $dest = $this->route[$path][0];
            $subtitle = $this->route[$path][1];
        } else {
            $dest = $this->route['404'][0];
            $subtitle = $this->route['404'][1];
        }
        
        if (!file_exists($dest)) {
            if (isset($this->route['wrong_route']) && file_exists($this->route['wrong_route'][0])) {
                $subtitle = $this->route['wrong_route'][1];
            } else {
                $subtitle = 'error';
            }
        }
        
        return $subtitle;
    }
    
    public function getKeys()
    {
        return $this->keys;
    }
}
