<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Functions {

  public $CI; #using codeigniters global variable

  function __construct(){
    // get the super global CI within QDL
      $this->CI = &get_instance();
      // $this->CI->load->library([
      //   "form_validation"
      // ]);
  }

  public function check_device($encrypted_string=false, $encrypt=false){
      // using the codigniter "user_agent" library
      $this->CI->load->library('user_agent');
      // fetch device full agent list
      $device = $this->CI->agent->agent_string()."|".$_SERVER['REMOTE_ADDR'];

      if($encrypted_string && password_verify($device, $encrypted_string)):
          // 
          return $device;

      elseif(!$encrypted_string && $encrypt):
          // 
          return password_hash($device, PASSWORD_DEFAULT);
          // 
      elseif (!$encrypted_string && !$encrypt) :
          // 
          return $device;
      else:
          // 
          return false;

      endif;

  }

  public function return_val($data){
      $values = array(
          "msg"=> isset($data['msg']) ? $data['msg'] : "An <b>Error</b> occured ... please try again",
          "cat"=> isset($data['cat']) ? $data['cat'] : "info",
          "icon"=> isset($data['icon']) ? $data['icon'] : "travel_info",
          "goto"=> isset($data['goto']) ? $data['goto'] : "null",
          "field"=> isset($data['field']) ? $data['field'] : "null",
          "reload"=> isset($data['reload']) ? $data['reload'] : "null"
      );
      $msg = json_encode($values);
      return $msg;
  }

  public function encrypt($msg, $ch="encrypt", $hb="bin2hex"){
      $this->CI->load->library("encryption");
      if($ch === "encrypt"):
          if($hb === "bin2hex"):
              $result  = bin2hex($this->CI->encryption->encrypt($msg));
          else:
              $result  = $this->CI->encryption->encrypt($msg);
          endif;
      elseif($ch === "decrypt"):
          if($hb === "hex2bin"):
              $result  = $this->CI->encryption->decrypt(hex2bin($msg));
          else:
              return FALSE;
          endif;
      endif;
      return $result;
  }

    public function json_encrypt($array, $encrypt=true){
        if(is_array($array) && $encrypt):
            $convert_array_2_json = json_encode($array);
            return $this->encrypt($convert_array_2_json);
//            returns encrypted string of the json data
        elseif(is_string($array) && $encrypt === false):
            $decrypted = $this->encrypt($array, "decrypt", "hex2bin");
            return json_decode($decrypted, TRUE);
        else:
            return FALSE;
        endif;
    }

    public function set_form_defaults(array $form_defaults){

        $default = array();
        foreach ($form_defaults as $field => $defval) {
            if(!empty(set_value($field))){
                $default[$field] = set_value($field);
            }else{
                $default[$field] = $defval;
            }
        }

        return $default;
    }
}

?>
