<?php

// this allow users to set their current directory
$set_app_path=""; 

session_start();

function get($route, $path_to_include){
   
  if( $_SERVER['REQUEST_METHOD'] == 'GET' ){ route($route, $path_to_include); }  
}
function post($route, $path_to_include){
  if( $_SERVER['REQUEST_METHOD'] == 'POST' ){ route($route, $path_to_include); }    
}
function put($route, $path_to_include){
  if( $_SERVER['REQUEST_METHOD'] == 'PUT' ){ route($route, $path_to_include); }    
}
function patch($route, $path_to_include){
  if( $_SERVER['REQUEST_METHOD'] == 'PATCH' ){ route($route, $path_to_include); }    
}
function delete($route, $path_to_include){
  if( $_SERVER['REQUEST_METHOD'] == 'DELETE' ){ route($route, $path_to_include); }    
}
function any($route, $path_to_include){ route($route, $path_to_include); }
function route($route, $path_to_include){
  // get it global variable
  global $set_app_path;
  // make a new url
  $ROOT = $_SERVER['DOCUMENT_ROOT'].$set_app_path;

 
  if($route == "/404"){
    // check file error
    $path="$ROOT/$path_to_include";
    if(file_exists($path)===false)die("ERROR $path_to_include  <h2>file not found</h2>");
    // end
    include_once("$ROOT/$path_to_include");
    exit();
  }  

  // remove empty string's in the user path urls
  $set_app_path_urls = array_filter(explode('/', $set_app_path));
  // we need to reset php index of the array  $set_app_path_urls  values, so that they start from 0
  $set_app_path_urls=array_values($set_app_path_urls);

  
  $request_url = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
  $request_url = rtrim($request_url, '/');
  $request_url = strtok($request_url, '?');
  $route_parts = explode('/', $route);
  $request_url_parts = explode('/', $request_url);


  array_shift($route_parts);
  array_shift($request_url_parts);
  

  // we need to remove the the first values of the app  in the $request_url_parts
  if($request_url_parts>  $set_app_path_urls){
     foreach ($set_app_path_urls as $key => $value) {
        // we remove the first items
         unset($request_url_parts[$key]);
     }
    //  then we need to reset $request_url_parts , values, so that they start from 0
     $request_url_parts=array_values( $request_url_parts);
  }


  if( $route_parts[0] == '' && count($request_url_parts) == 0 ){
    // check file error
    $path="$ROOT/$path_to_include";
    if(file_exists($path)===false)die("ERROR $path_to_include  <h2>file not found</h2>");
    // end
    include_once("$ROOT/$path_to_include");
    exit();
  }


  if( count($route_parts) != count($request_url_parts) ){ return; }  
  $parameters = [];
  for( $i = 0; $i < count($route_parts); $i++ ){
    $route_part = $route_parts[$i];
    if( preg_match("/^[$]/", $route_part) ){
      $route_part = ltrim($route_part, '$');
      array_push($parameters, $request_url_parts[$i]);
      $$route_part=$request_url_parts[$i];
    }
    else if( $route_parts[$i] != $request_url_parts[$i] ){
      
      return;
    } 
  }
  // check file error
  $path="$ROOT/$path_to_include";
  if(file_exists($path)===false)die("ERROR $path_to_include  <h2>file not found</h2>");
  // end
  include_once("$ROOT/$path_to_include");
  exit();
}
function out($text){echo htmlspecialchars($text);}
function set_csrf(){
  $csrf_token = bin2hex(random_bytes(25));
  $_SESSION['csrf'] = $csrf_token;
  echo '<input type="hidden" name="csrf" value="'.$csrf_token.'">';
}
function is_csrf_valid(){
  if( ! isset($_SESSION['csrf']) || ! isset($_POST['csrf'])){ return false; }
  if( $_SESSION['csrf'] != $_POST['csrf']){ return false; }
  return true;
}
