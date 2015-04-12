<?php
/**
 * Config-file for navigation bar.
 *
 */
$navbar =  [

    // Use for styling the menu
    'class' => 'navbar',
 
    // Here comes the menu strcture
    'items' => [

        'home'  => [
            'text'  => 'Home',
            'url'   => $this->di->get('url')->create(''),
            'title' => 'Home route of current frontcontroller'
        ],
 
        'questions'  => [
            'text'  => 'Questions',
            'url'   => $this->di->get('url')->create('questions'),
            'title' => 'Questions',
        ],
 
        'tags' => [
            'text'  =>'Tags',
            'url'   => $this->di->get('url')->create('questions/tags'),
            'title' => 'Tags',
            'mark-if-parent-of' => 'tags',
        ],
		
        'members' => [
            'text'  =>'Users',
            'url'   => $this->di->get('url')->create('users'),
            'title' => 'Users',
        ],
		
		// This is a menu item
        'about' => [
            'text'  =>'About',
            'url'   => $this->di->get('url')->create('about'),
            'title' => 'About the website'
        ],
		
		'createquestion' => [
            'text'  =>'Ask Question',
            'url'   => $this->di->get('url')->create('question/create'),
            'title' => 'Ask A Question'
        ],
		
    ],
 


    /**
     * Callback tracing the current selected menu item base on scriptname
     *
     */
    'callback' => function ($url) {
        if ($url == $this->di->get('request')->getCurrentUrl(false)) {
            return true;
        }
    },



    /**
     * Callback to check if current page is a decendant of the menuitem, this check applies for those
     * menuitems that has the setting 'mark-if-parent' set to true.
     *
     */
    'is_parent' => function ($parent) {
        $route = $this->di->get('request')->getRoute();
        return !substr_compare($parent, $route, 0, strlen($parent));
    },



   /**
     * Callback to create the url, if needed, else comment out.
     *
     */
   /*
    'create_url' => function ($url) {
        return $this->di->get('url')->create($url);
    },
    */
];

$items = $this->di->users->userNavbar($this->di->session->get('member_loggedIn'), $this->di->session->get('member_id'));
$navbar['items'] = array_merge($navbar['items'],$items);

return $navbar;