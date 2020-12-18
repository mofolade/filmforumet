<?php
require_once(dirname(__FILE__).'/classMySQL.php');

class ACLsettingsClass extends MySQL{

    public function __construct(){
        parent::__construct();
    }
    
    
    public function users($user, $method, $userRoles, $paramId) {

    // Allow everyone to create a user if the userRole is user
    if ($method === 'POST' && strpos($userRoles,'user')) { return true; }
    // Allow admins to create a user with any role...
    if ($method === 'POST' && strpos($userRoles,'admin')) { return true;}
    // Allow admins to change info about a user
    if ($method === 'PUT' && strpos($userRoles,'admin')) { return true; }
    // Allow a user to change info about him/herself
    // (the split pop thing is how we get the id from the url
    // since we do not have req.params available in middleware)
    if ($method === 'PUT' && $paramId === $user['id']) { return true; }
    // Allow admins to delete users
    if ($method === 'DELETE' && strpos($userRoles,'admin')) { return true; }
    return false; // otherwise do not allow the request

  }

  public function userXRole($user, $method, $userRoles, $paramId) {
    // Allow admins to a see a list of user roles
    if ($method === 'GET' && strpos($userRoles,'admin')) { return true; }
    // Allow admins to change info user roles
    if ($method === 'PUT' && strpos($userRoles,'admin')) { return true; }
    // Allow admins to delete role
    if ($method === 'DELETE' && strpos($userRoles,'admin')) { return true; }
    return false; // otherwise do not allow the request
  }

  public function login() { 
    // Everyone should always be allowd to try to login and to logout
    return true;
  }

  public function topics($user, $method, $userRoles, $paramId) {
    // Allow admins to create a topic.
    if ($method === 'POST' && strpos($userRoles,'admin')) { return true;}
    // Allow all to a see a list of topic
    if ($method === 'GET') { return true; }
    // Allow admins to change info about a topic
    if ($method === 'PUT' && strpos($userRoles,'admin')) { return true; }
    // Allow admins to delete topic
    if ($method === 'DELETE' && strpos($userRoles,'admin')) { return true; }
    return false; // otherwise do not allow the request
  }

  public function topicComments($user, $method, $userRoles, $paramId) {
    // Allow user to create a comment.
    if ($method === 'POST' && strpos($userRoles,'user')) { return true;}
    // Allow all to a see a list of comment.
    if ($method === 'GET') { return true; }
    // Allow admins to delete topic
    if ($method === 'DELETE' && (strpos($userRoles,'admin') || strpos($userRoles,'moderator'))) { return true; }
    return false; // otherwise do not allow the request
  }
}

?>