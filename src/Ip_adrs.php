<?php defined('BASEPATH') OR exit('No direct script access allowed');
/*
  Fetchs and Authenticate ip address details
*/
class Ip_adrs extends CI_Model{
  
  public function validate_ip($ip) {
      if (filter_var($ip, FILTER_VALIDATE_IP, 
                           FILTER_FLAG_IPV4 | 
                           FILTER_FLAG_IPV6 |
                           FILTER_FLAG_NO_PRIV_RANGE | 
                           FILTER_FLAG_NO_RES_RANGE) === false)
          return false;
    //   self::$ip = $ip;
      return true;
  }
    
  public function get_ip_address() {
    // Check for shared internet/ISP IP
    if (!empty($_SERVER['HTTP_CLIENT_IP']) && $this->validate_ip($_SERVER['HTTP_CLIENT_IP']))
     return $_SERVER['HTTP_CLIENT_IP'];
  
    // Check for IPs passing through proxies
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
     // Check if multiple IP addresses exist in var
      $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
      foreach ($iplist as $ip) {
       if ($this->validate_ip($ip))
        return $ip;
      }
    }
    
    if (!empty($_SERVER['HTTP_X_FORWARDED']) && $this->validate_ip($_SERVER['HTTP_X_FORWARDED']))
     return $_SERVER['HTTP_X_FORWARDED'];

    if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && $this->validate_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
     return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
     
    if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && $this->validate_ip($_SERVER['HTTP_FORWARDED_FOR']))
     return $_SERVER['HTTP_FORWARDED_FOR'];
     
    if (!empty($_SERVER['HTTP_FORWARDED']) && $this->validate_ip($_SERVER['HTTP_FORWARDED']))
     return $_SERVER['HTTP_FORWARDED'];
  
    // Return unreliable IP address since all else failed
    return $_SERVER['REMOTE_ADDR'];
  }
    
  public function get_client_ip() {
      $ipaddress = '';
      if (getenv('HTTP_CLIENT_IP'))
          $ipaddress = getenv('HTTP_CLIENT_IP');
      else if(getenv('HTTP_X_FORWARDED_FOR'))
          $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
      else if(getenv('HTTP_X_FORWARDED'))
          $ipaddress = getenv('HTTP_X_FORWARDED');
      else if(getenv('HTTP_FORWARDED_FOR'))
          $ipaddress = getenv('HTTP_FORWARDED_FOR');
      else if(getenv('HTTP_FORWARDED'))
         $ipaddress = getenv('HTTP_FORWARDED');
      else if(getenv('REMOTE_ADDR'))
          $ipaddress = getenv('REMOTE_ADDR');
      else
          $ipaddress = 'UNKNOWN';
      return $ipaddress;
  }

}