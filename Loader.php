<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Loader {

  public $CI; #using codeigniters global variable


  public function __construct() {
    // get the super global CI within QDL
    $this->CI = &get_instance();

    $libraries = array(
      "quick_ci_dev/Functions"  => "qcdl_fnc",
      "quick_ci_dev/Validator" => "qcdl_val",
      "quick_ci_dev/Database"  => "qcdl_db",
    );

    $this->CI->load->library($libraries);
  }

  public function load_actions($actions){
    if(is_array($actions) || is_string($actions)):
      // if string convert to array
      if(is_string($actions)):
        $actions = array($actions => $actions);
      endif;
      // loop through all requested actions
      foreach ($actions as $action => $addr) {
        if(file_exists(APPPATH."models/actions/".$action."_actions.php")):
          $this->CI->load->model("actions/".$action."_actions", $addr);
        endif;
      }

    endif;
  }

  public function load_validation($validations){
    if(is_array($validations) || is_string($validations)):
      // if string convert to array
      if(is_string($validations)):
        $validations = array($validations => $validations);
      endif;
      // loop through all requested actions
      foreach ($validations as $validation => $addr) {
        if(file_exists(APPPATH."models/validations/".$validation."_validations.php")):
          $this->CI->load->model("validations/".$validation."_validations", $addr);
        endif;
      }

    endif;
  }
}
