<?php

// Types
// 0. info
// 1. success
// 2. warning
// 3. danger

class Notice
{
    private $title;
    private $content;
    private $type;
    private $permanent;
    
    public function __construct($title, $content, $type, $permanent = false)
    {
        $this->title = $title;
        $this->content = $content;
        $this->type = $type;
        $this->permanent = $permanent;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function getContent()
    {
        return $this->content;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function getPermanent()
    {
        return $this->permanent;
    }
    
    public function toString()
    {
        $class = "";
        if (!$this->getPermanent())
            $class = "alert-dismissible ";
        //return '<div class="'.$class.'">'.$this->getTitle().': '.$this->getContent().'</div>';
        $alert = '<div class="alert '.$class.' alert-'.$this->getType().'">';
        if (!$this->getPermanent())
            $alert .= '<button type="button" class="close" aria-label="Close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>';
        $alert .= '<h4>'.$this->getTitle().'</h4>';
        $alert .= '<p>'.$this->getContent().'</p>';
        $alert .= '</div>';
        return $alert;
    }
}