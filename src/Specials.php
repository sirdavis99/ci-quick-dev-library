<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Specials extends CI_Model{	

    public function __construct(){
        parent::__construct();
    }

    public function check_valid_user_session($details){
        if($this->sess->has_userdata($details["code"])):

            // fetch user encrypted session string is set
            $user = $this->sess->userdata($details["code"]);

            // checks if it exists in db
            $details["database"]['arg'] = [$details["database"]["col"] => $user];
            $db_user_check = $this->adb->adb_get_where($details["database"]);

            if($db_user_check): 
                return TRUE;
            else: 
                return FALSE;
            endif;

        else: 
            return FALSE;
        endif;
    }


}

