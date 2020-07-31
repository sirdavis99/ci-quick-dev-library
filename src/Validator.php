<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Validator extends CI_Model{
    
    public function run_input_validation($config, $file_config=FALSE){
        $this->form_validator->set_rules($config);
        
        if($this->form_validator->run() !== FALSE):
            $result =  $this->input->post();
            // check if files too are to send
            if(is_array($file_config)):
                for($i=0;$i<count($file_config);$i++){
                    $upload_files = $this->fnc->upload_file($file_config[$i]);
                    if(!isset($upload_files["error_msgs"])):
                        $result[$file_config[$i]['files']] = $upload_files;
                    else:
                        $result = $upload_files;
                    endif;
                }
            endif;

        else:
            $result = ["error_msgs"=>$this->form_validator->error_array()];
        endif;
//        $this->form_validator->reset_validation();
        return $result;
    }
    
    public function add_category_validate($check, $category_rule) {
        $posts = $this->input->post();
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
    
    public function fetch_validator_error($error){
        $vali_keys = array_keys($error['error_msgs']);
        return ["msg"=>$error['error_msgs'][$vali_keys[0]], "cat"=>"warning", "field"=>$vali_keys];
    }


}
