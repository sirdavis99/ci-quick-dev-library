<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Author extends CI_Model{
    
    public function check_logged_in(){
        $details = array(
            "database"=>["table"=>"logins", "col"=>"uid"],
            "code" => "cid"
        );
        $check_valid_device = $this->fnc->check_valid_device();
        $check_valid_sess_user = $this->spec->check_valid_user_session($details);

        if($check_valid_device &&  $check_valid_sess_user): return TRUE;
        else: return FALSE;
        endif;
    }

    public function redirect_valid_session_user($forward=false, $backward=false){
        if($this->check_logged_in()):
            if($forward):
                redirect($forward,'refresh');
            endif;
        else:
            if($backward):
                redirect($backward,'refresh');
            endif;
        endif;
    }

    public function create_user_session($details){
        $data = array(
            "eid"=>$this->fnc->fetch_device(true),
            "cid"=>$details['uid']
        );
        $this->sess->set_userdata($data);
    }

    public function get_settings(){
        // get the settings for your webpage if exists
        if($this->sess->userdata('sitex')):
            $settings = $this->sess->userdata('sitex');
        else:
            $query = array("table"=>"site_details", "arg"=>"id > 0", "limit"=>"50");
            $sql = $this->adb->adb_get_where($query);
            if($sql):
                $settings = [];
                foreach($sql->result() as $sets){
                    // print_r($sets);
                    $data = json_decode($sets->data, TRUE);
                    $settings[$sets->category] = ["data"=>$data, "date"=>$sets->reg_date];
                }
                $this->sess->set_userdata(["sitex"=>$settings]);
            endif;
        endif;
        return isset($settings) ? $settings : FALSE;
    }
    
    public function is_requested(){
        $check_global_get = !empty($_GET) ? $_GET : FALSE;
        if($check_global_get !== FALSE):
            $global_get_keys =  array_keys($check_global_get);
            $encrypted_string = $global_get_keys[0];
            if($encrypted_string > 25):
                return $this->fnc->json_enc($encrypted_string, "decrypt");
            else:
                return FALSE;
            endif;
        else:
            return FALSE;
        endif;
    }

    public function set_seen_return_msg($store_sess, $success) {
        if($store_sess !== FALSE):
//            $this->usr_act->check_user_pages_open('user/prison');
            $this->usr_act->set_user_lastseen("last_seen", "user_id");
            return $this->lang->line($success);
            // returns success message after validating and storing session
        else:
            return $store_sess;
        endif;
    }


    
}

