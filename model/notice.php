<?php

/**
 * Contains Notice class
 * @package utest_notice
 */

/**
 * Class for all notifications on website
 * @package utest_notice_class
 * @author Bonibar
 */
class Notice
{
	// Types
	// 0. info
	// 1. success
	// 2. warning
	// 3. danger
    private $title;
    private $content;
    private $type;
    private $permanent;
    

    /**
     * Default constructor of a Notice
     * @param string $title Title of the Notice
     * @param string $content Content to display in the Notice
     * @param string $type Type of the Notice (info, success, warning or danger)
     * @param bool $permanent If true, the Notice will always be displayed (default: false)
     */
    public function __construct($title, $content, $type, $permanent = false)
    {
        $this->title = $title;
        $this->content = $content;
        $this->type = $type;
        $this->permanent = $permanent;
    }
    

    /**
     * Return the title of the Notice
     * @return string Title of the Notice
     */
    public function getTitle()
    {
        return $this->title;
    }
    

    /**
     * Return the content of the Notice
     * @return string Content of the Notice
     */
    public function getContent()
    {
        return $this->content;
    }
    

    /**
     * Return the type of the Notice
     * @return string Type of the Notice (possible values: info, warning, success, danger)
     */
    public function getType()
    {
        return $this->type;
    }
    

    /**
     * Return the display state of the Notice
     * @return bool True if the Notice is always displayed. Otherwise, false.
     */
    public function getPermanent()
    {
        return $this->permanent;
    }
    

    /**
     * Return the HTML code of the Notice
     * @return string HTML code of the Notice
     */
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