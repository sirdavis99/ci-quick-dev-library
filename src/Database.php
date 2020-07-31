<?php
// author : friday davis
// framework : Almighty Database Functions
// short-code : adb
class Database extends CI_Model
{   
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    // searches for row or rows in a database
    public function adb_select($data){
        if(!empty($data) && is_array($data)){
            $extra = isset($data['extra']) ? $data['extra'] : "";
            $sql = "SELECT ".$data['values']." FROM ".$data['table']." WHERE ".$data['arg']." ".$extra."";
            $query = $this->db->query($sql);
            if(!empty($query->result())):
               return $query;
            else:
                return FALSE;
            endif;
            
        }else{
            return FALSE;
        }
    }
    // inserting row or rows into a database
    public function adb_insert($data){
        if(isset($data['batch']) && $data['batch'] == true){
            $query = $this->db->insert_batch($data["table"], $data['data']);
        }else{
            $query = $this->db->insert($data["table"], $data['data']);
        }
        if($query){
            return true;
        }else{
            return false;
        }
    }
    // updating row or rows in a database
    public function adb_update($data){
        if(isset($data['batch']) && $data['batch'] == true){
            $query = $this->db->update_batch($data["table"], $data['data'], $data['arg']);
        }else{
            $query = $this->db->update($data["table"], $data['data'], $data['arg']);
        }
        if($this->db->affected_rows() > 0){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    // deleting row or rows in a database
    public function adb_delete($data){
        $query = $this->db->delete($data["tables"], $data['arg']);
        if($query){
            return true;
        }else{
            return false;
        }
    }
    // deleting row or rows in a database
    public function adb_empty_table($data){
        $query = $this->db->empty_table($data["table"]);
        if($query){
            return true;
        }else{
            return false;
        }
    }
    // searches for row or rows in a database with the get class
    public function adb_get_where($data){
        if(!empty($data) && is_array($data)):
            $data['limit'] = isset($data['limit']) ? $data['limit'] : 1;
            $data['order'] = isset($data['order']) ? $data['order'] : 'id DESC';
            $this->db->order_by($data['order']);
            $query = $this->db->get_where($data['table'], $data['arg'], $data['limit']);
            if(!empty($query->result())):
                return $query;
            else:
                return FALSE;
            endif;
        else:
            return FALSE;
        endif;
    }
}
