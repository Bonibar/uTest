<?php

/**
 * Contais router class
 * @package utest_router
 */


/**
 * Class that manage rooting system
 * This class will include views and generate keys based on current URL
 * @package utest_router_class
 * @author Bonibar
 */
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
    

    /**
     * Execute rooting
     * Include views based on URL and rooting table (see $route)
     */
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
    

    /**
     * Return the subtitle of the current route
     * @return string Subtitle of the route
     */
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
    

    /**
     * Return keys of the current route
     * Keys are based on the current URL. They are after the route name
	 * They are generated during route() function
     * @return string[] Keys of the route
     */
    public function getKeys()
    {
        return $this->keys;
    }
}
