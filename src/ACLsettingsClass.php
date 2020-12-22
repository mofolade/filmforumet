<?php

class ACLSettingsClass{


  public function users($method) {
    // Allow everyone to create a user
    if ($method === 'POST') { return true; }
  }

  public function login() { 
    // Everyone should always be allowd to try to login and to logout
    return true;
  }

  public function admin($method, $userRoleId) {
    // Allow admins to edit roles.
    if ($method === 'POST' && $userRoleId==1) { return true;}
    return false; // otherwise do not allow the request
  }

  public function moderator($method, $userRoleId) {
    // Allow admins and moderator to a see a list of topics and rights
    if ($method === 'GET' && ($userRoleId==1 || $userRoleId==2)) { return true; }
    // Allow admins to edit roles.
    if ($method === 'POST' && $userRoleId==1) { return true;}
    return false; // otherwise do not allow the request
  }

  public function topics($method, $userRoleId, $userId) {
    // Allow all to create a topic.
    if ($method === 'POST' && isset($userId)) { return true;}
    // Allow all to a see a list of topic
    if ($method === 'GET') { return true; }
    // Allow admins to delete topic
    if ($method === 'DELETE' && ($userRoleId==1 || $userRoleId==2)) { return true; }
    return false; // otherwise do not allow the request
  }

  public function categories($method, $userRoleId) {
    // Allow admins to create a topic.
    if ($method === 'POST' && ($userRoleId==1)) { return true;}
    // Allow all to a see a list of topic
    if ($method === 'GET') { return true; }
    // Allow admins to delete topic
    if ($method === 'DELETE' && ($userRoleId==1 || $userRoleId==2)) { return true; }
    return false; // otherwise do not allow the request
  }

  public function comments($method) {
    // Allow admins to create a topic.
    if ($method === 'POST') { return true;}
    // Allow all to a see a list of topic
    if ($method === 'GET') { return true; }
    return false; // otherwise do not allow the request
  }

}

?>