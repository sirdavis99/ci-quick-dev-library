<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Database {
  /** Quick Codeigniter Library (Database Management)
   **
   ** Data Details and use
   ** $data["table"]  = database table to be worked on
   ** $data["data"]   = data to be used in database query
   ** $data["arg"]    = argument in place for query
   ** $data["batch"]  = batch data or not to be used in database
   **
  **/

  public $CI; #using codeigniters global variable

  public function __construct(){
    // get the super global CI within QDL
      $this->CI = &get_instance();
      $this->CI->load->database();
  }

  # inserting row or rows into a database
  public function insert($data){
      if(isset($data['batch']) && $data['batch'] == true):
        // Inserts batch data into table in database if $data["batch"] set to true
          $query = $this->CI->db->insert_batch(
              $data["table"], $data['data']
          );
      else:
        // Inserts single data into database
          $query = $this->CI->db->insert(
            $data["table"], $data['data']
          );
      endif;
      // check query completion and return boolen result			$this->load->database();

      if($query): return true;
      else :  return false;
      endif;
  }

  // updating row or rows in a database
  public function update($data){
      if(isset($data['batch']) && $data['batch'] == true):
        // Updates batch data into table in database if $data["batch"] set to true
          $query = $this->CI->db->update_batch(
            $data["table"], $data['data'], $data['arg']
          );
      else:
        // Updates single data into database
          $query = $this->CI->db->update(
            $data["table"], $data['data'], $data['arg']
          );
      endif;
      // check query completion
      if($this->CI->db->affected_rows() > 0):  return true;
      else: return false;
      endif;
  }

    // deleting row or rows in a table in database
    public function delete($data){
        $query = $this->CI->db->delete(
            $data["tables"], $data['arg']
        );
        // check query completion and return boolen result
        if($query): return true;
        else: return false;
        endif;
    }

    public function empty_table($data){
        $query = $this->CI->db->empty_table($data["table"]);
        // check query completion and return boolen result
        if($query): return true;
        else: return false;
        endif;
        // deletes all rows and columns in a table in the database
        // check query completion and return boolen result
    }

    // searches for row or rows in a database with the get class
    public function get_where($data){
        if(!empty($data) && is_array($data)):
            $data['limit'] = isset($data['limit']) ? $data['limit'] : 1;
            $data['order'] = isset($data['order']) ? $data['order'] : 'id DESC';
            $this->CI->db->order_by($data['order']);
            $query = $this->CI->db->get_where(
                $data['table'], $data['arg'], $data['limit']
            );
            if(!empty($query->result())): return $query;
            else: return FALSE;
            endif;
        else: 
          return FALSE;
        endif;
    }

    public function multi_get_where(array $query){
        // fetches data from relational tables with join
        if(!empty($query)):
            /* fields: 
                    [example] : 'u.*, c.company, r.description'
                    [default_value] : '*''
            */
            $this->CI->db->select(isset($query['fields']) ? $query['fields'] : '*');
            $this->CI->db->from($query['tables']);/*tables: eg - 'users u, company c, roles r'*/
            $this->CI->db->where($query['args']);/*args: eg - 'c.id = u.id_company'*/
            // run query
            $execution = $this->CI->db->get();
            // check if results exists
            if(!empty($execution->result())): return $execution;
            else: return false;
            endif;
        else: 
            return false;
        endif;
    }


    public function check_exist($data){
        $result = $this->get_where($data);
        if($result):
          return true;
        else:
          return false;
        endif;
    }

}
