<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Validator {

  public $CI; #using codeigniters global variable

  function __construct(){
    // get the super global CI within QDL
      $this->CI = &get_instance();
      $this->CI->load->library([
        "form_validation"
      ]);
  }

  public function run_input_validation($config, $file_config=FALSE){
      $this->CI->form_validation->set_rules($config);

      if($this->CI->form_validation->run() !== FALSE):
          $result =  $this->CI->input->post();
          // check if files too are to send
          if(is_array($file_config)):
              for($i=0;$i<count($file_config);$i++){
                  $upload_files = $this->upload_file($file_config[$i]);
                  if(!isset($upload_files["error_msgs"])):
                      $result[$file_config[$i]['files']] = $upload_files;
                  else:
                      $result = $upload_files;
                  endif;
              }
          endif;

      else:
          $result = ["error_msgs"=>$this->CI->form_validation->error_array()];
      endif;
//        $this->CI->form_validation->reset_validation();
      return $result;
  }

  public function add_category_validate($check, $category_rule) {
      $posts = $this->CI->input->post();
      $row_config = [];
      $post_keys = array_keys($posts);
      $last_key = array_pop($post_keys);
      $explode_last_key = explode('-', $last_key);
      $fetch_index = array_pop($explode_last_key);
      $index = intval($fetch_index);
      if(is_int($index)):
          for($i=1;$i<=$index;$i++){
              $str_i = strval($i);
              if(isset($posts[$check.$str_i])):
                  $new_config = [];
                  for($cat=0;$cat<count($category_rule);$cat++) {
                      $temp_array = [
                        'field' => $category_rule[$cat]['field'].$str_i,
                        'label' => $category_rule[$cat]['label'].' ('.$str_i.')',
                        'rules' => $category_rule[$cat]['rules']
                      ];
                      array_push($new_config, $temp_array);
                  }
                  $row_config = array_merge($row_config, $new_config);
              endif;
          }
          return $row_config;
      endif;
  }

  public function upload_file($econfig=[]){
      $config = array(
          'upload_path' => './uploads/',
          'allowed_types' => isset($econfig['allowed']) ? $econfig['allowed'] : 'jpg|png',
          'max_size'     => isset($econfig['max_size']) ? $econfig['max_size'] : '12000',
          'max_width' => isset($econfig['max_width']) ? $econfig['max_width'] : '1024',
          'max_height' => isset($econfig['max_height']) ? $econfig['max_height'] :'768',
          'encrypt_name' => TRUE
      );
      $config['upload_path'] .= isset($econfig['folder']) ? $econfig['folder'] : 'flyers/';
      $files_input =  $econfig['files'];

      $this->CI->load->library('upload', $config);
      if(!$this->CI->upload->do_upload($files_input)):
          return array("error_msgs" => [$files_input => $this->CI->upload->display_errors()]);
      else:
          return $this->CI->upload->data('file_name');
      endif;
  }

  public function fetch_validator_error($error){
      $vali_keys = array_keys($error['error_msgs']);
      return ["msg"=>$error['error_msgs'][$vali_keys[0]], "cat"=>"warning", "field"=>$vali_keys];
  }


}
