<?php defined('BASEPATH') OR exit('No direct script access allowed');
// author : friday davis
// framework : Almighty Session Functions
// short-code : adb
class Functions extends CI_Model
{	
    public function __construct(){
        parent::__construct();
        $this->load->library('user_agent');
        
    }
    public function redirector($page, $sleep=1){
        sleep($sleep);
        redirect($page);
    }

    public function goto_reload($data){
        if(isset($data->reload) && $data->reload !== "null"):
            header("Refresh:0");
        elseif(isset($data->goto) && $data->goto !== "null"):
            header("Refresh:2; url=".$data->goto);;
        endif;
    }

    public function fetch_device($encrypt=FALSE){
        if($encrypt === TRUE):
            $device = $this->agent->agent_string()."|".$_SERVER['REMOTE_ADDR'];
            $this_device = $this->enc($device);
        else:
            $this_device = $this->agent->agent_string()."|".$_SERVER['REMOTE_ADDR'];
        endif;
        return $this_device;
    }
    
    public function set_current_page_sesion($page){
        $split = explode('/', $page);
        if($split[0] !== 'preview'):
            $this->sess->set_tempdata('last_page', $page, 9000);
        endif;
    }
    
    public function enc($msg, $ch="encrypt", $hb="bin2hex"){
        if($ch === "encrypt"):
            if($hb === "bin2hex"):
                $result  = bin2hex($this->encrypt->encrypt($msg));
            else:
                $result  = $this->encrypt->encrypt($msg);
            endif;
        elseif($ch === "decrypt"):
            if($hb === "hex2bin"):
                $result  = $this->encrypt->decrypt(hex2bin($msg));
            else:
                return FALSE;
            endif;
        endif;
        return $result;
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
    
    public function check_valid_device() {
        $auth_device_loc = $this->fetch_device();
        if($this->sess->userdata('eid')):
            $current_sess = $this->sess->userdata();
            if($this->enc($current_sess['eid'], 'decrypt', 'hex2bin') !== $auth_device_loc):
                # destroy session if user is not owner of session 
                return FALSE;
            else:
                return TRUE;
            endif;
        else:
            return FALSE;
        endif;
        
    }
        
    public function check_last_page(){
        if($this->sess->tempdata('last_page')):
            return $this->sess->tempdata('last_page');
        else:
            return FALSE;
        endif;
    }
        
    public function json_enc($array, $fnc=true){
        if(is_array($array) && $fnc):
            $convert_array_2_json = json_encode($array);
            return $this->enc($convert_array_2_json);
//            returns encrypted string of the json data
        elseif(is_string($array) && $fnc === false):
            $decrypted = $this->enc($array, "decrypt", "hex2bin");
            return json_decode($decrypted, TRUE);
        else:
            return FALSE;
        endif;
    }
    
    public function check_exist($data){
        $result = $this->adb->adb_get_where($data);
        if(is_array($result)):
            if(is_array($result->result()) && !empty($result->result())):
                return TRUE;
            else:
                return FALSE;
            endif;
        else:
            return FALSE;
        endif;
    }
    
    public function to_array($item){
        $encode_item = json_encode($item);
        $item_array_decoded = json_decode($encode_item, TRUE);
        return $item_array_decoded;
    }

    public function get_page_name(){
        $page = $this->check_last_page();
        if($page !== FALSE):
            $ex = explode('/', $page);
            return array_pop($ex);
        else:
            return $this->auth->this_site;
        endif;
    }

    public function check_matched_item_combination($query, $check){
        $check_data = array(
            "table"=>$query['table'],
            "arg"=>$query['arg'],
            "order"=>"id DESC"
        );
        $try = $this->adb->adb_get_where($check_data);
        if(is_object($try)):
            $checked = $try->result_array();
            if(is_array($checked) && !empty($checked)):
                if(password_verify($check, $checked[0][$query['check']])):
                    return TRUE;
                else:
                    return FALSE;
                endif;
            else: return null;
            endif;
        else: return null;
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
        
        $this->load->library('upload', $config);
        if(!$this->upload->do_upload($files_input)):
            return array("error_msgs" => [$files_input => $this->upload->display_errors()]);
        else:
            return $this->upload->data('file_name');
        endif;
    }
    
    public function categorize_into_json($check, $posts, $values, $offset=FALSE) {
        $rows = [];
        $post_keys = array_keys($posts);
        if($offset !== FALSE):
            $set_offset = count($post_keys) - $offset;
            $last_key = $post_keys[$set_offset];
        else:
            $last_key = array_pop($post_keys);
        endif;
        $explode_last_key = explode('-', $last_key);
        $fetch_index = array_pop($explode_last_key);
        $index = intval($fetch_index);
        if(is_int($index)):
            for($i=1;$i<=$index;$i++){
                $str_i = strval($i);
                if(isset($posts[$check.$str_i])):
                    $new_row = [];
                    for($cat=0;$cat<count($values);$cat++){
                        $new_row[$values[$cat]] = $posts[$values[$cat].'-'.$str_i];
                    }
                else:
                    return FALSE;
                endif;
                array_push($rows, $new_row);
            }
            return json_encode($rows);
        else:
            return FALSE;
        endif;
    }

    public function getmirrored($value){
        $convert_val_2_array = str_split($value);
        $num_array = [0,1,2,3,4,5,6,7,8,9];
        $mirror_array = [2,4,6,8,0,1,3,5,9];
        $new_val = [];
        foreach ($convert_val_2_array as $val){
            $intval = intval($val);
            if(is_int($intval)):
                for($i=0;$i<count($num_array);$i++){
                    if($intval === $num_array[$i]):
                        $mirror_val = $mirror_array[$i];
                        array_push($new_val, $mirror_val);
                    endif;
                }
            endif;
        }
        $mirrored_val = implode('', $new_val);
        return $mirrored_val;

    }

    public function fetch_sql_data($query){
        if(isset($query['table'])):
            $data = array(
                "table" => $query['table'],
                "limit"  => isset($query['limit']) ? $query['limit'] : 10,
                "arg" => isset($query['arg']) ? $query['arg'] : 'id > 0',
                "order" => isset($query['order']) ? $query['order'] : "id DESC"
            );
            $fetch = $this->adb->adb_get_where($data);
            if($fetch !== FALSE): return $fetch;
            else: return FALSE;
            endif;
        else:
            return FALSE;
        endif;
    }
}