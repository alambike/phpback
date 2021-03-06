<?php
/*********************************************************************
PHPBack
Ivan Diaz <ivan@phpback.org>
Copyright (c) 2014 PHPBack
http://www.phpback.org
Released under the GNU General Public License WITHOUT ANY WARRANTY.
See LICENSE.TXT for details.
**********************************************************************/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Get extends CI_Model
{
	public function __construct(){
		parent::__construct();
		$this->load->database();
	}

	public function get_categories(){
    	$sql = $this->db->get("categories");
        $result = $sql->result();
        $a = array();
        foreach ($result as $cat) {
            $a[$cat->id] = $cat;
        }
    	return $a;
    }

    
    public function get_idea_by_id($idea_id){
    	$idea_id = (int) $idea_id;
        return $this->get_row_by_id('ideas', $idea_id);
    }


    public function get_comments_by_id($idea_id){
    	$idea_id = (int) $idea_id;
    	$query = "SELECT * FROM comments WHERE ideaid='$idea_id'";
    	$sql = $this->db->query($query);
    	return $sql->result();
    }

    
    public function get_ideas_aprroved($categoryid){
        $categoryid = (int) $categoryid;
        $query = $this->db->query("SELECT * FROM ideas WHERE categoryid='$categoryid' AND status !='new'");
        return $query->num_rows();
    }

    public function get_ideas_custom($orderby, $isdesc, $from, $limit, $status = array(), $categories = array()){
        $query = "SELECT * FROM ideas ";
        if(count($categories)){
            $query .= "WHERE ( ";
            foreach ($categories as $catid) {
                $query .= "categoryid='$catid' OR ";
            }
            $query = substr($query, 0, -3);
            $query .= ") ";
        }
        if(count($status)){
            if(count($categories)) $query .= "AND (";
            else $query .= "WHERE ( ";
            foreach ($status as $s) {
                $query .= "status='$s' OR ";
            }
            $query = substr($query, 0, -3);
            $query .= ") ";
        }
        $query .= "ORDER BY $orderby ";

        if($isdesc) $query .= "DESC";
        else $query .= "ASC";

        $query .= " LIMIT $from, $limit";
        $sql = $this->db->query($query);
        return $sql->result();
    }

    public function category_exists($id){
        $id = (int) $id;
        $result = $this->db->query("SELECT id FROM categories WHERE id='$id'");
        if($result->num_rows() == 0) return false;
        return true;
    }
    
    public function get_ideas_by_category($category, $order, $type, $page){
        $page = (int) $page;
    	$category = (int) $category;
        $max = $this->get_setting('max_results');
        $from = ($page - 1) * $max;
    	$query = "SELECT * FROM ideas WHERE categoryid='$category' AND status !='new' ORDER BY ";
        switch ($order) {
            case 'id':
                $query .= "id ";
                break;
            case 'title':
                $query .= 'title ';
                break;
            default:
                $query .= "votes ";
                break;
        }
        if($type == "desc") $query .= "DESC";
        else $query .= "ASC";

        if($page != 0){
            $query .= " LIMIT $from, $max";
        }

    	$sql = $this->db->query($query);
        return $sql->result();
    }

    
    public function search_ideas($query){
        $keywords = explode(" ", $query);
        $temp = array_shift($keywords);
        $query = "SELECT * FROM ideas WHERE ( title LIKE '%$temp%'";

        foreach($keywords as $key){
            $query .= " OR title LIKE '%$key%'";
        }
        $query .= ") ORDER BY CASE ";
        $query .= " WHEN title LIKE '$temp%' THEN 0 ";
        $query .= " WHEN title LIKE '%$temp%' THEN 2 ";
        foreach($keywords as $id => $key){
            $query .= " WHEN title LIKE '$key%' THEN ". ($id+1) ." ";
            $query .= " WHEN title LIKE '%$key%' THEN ". ($id + 3) . " ";
        }
        $query .= "END";

        $sql = $this->db->query($query);
        return $sql->result();
        //Search by query, returns an array with list of ideas.
    }

    
    public function get_user_info($user_id){
        $user_id = (int) $user_id;
        return $this->get_row_by_id('users', $user_id);
    }

    public function get_user_ideas($user_id){
        $user_id = (int) $user_id;
        $sql = $this->db->query("SELECT * FROM ideas WHERE authorid='$user_id'");
        return $sql->result();
    }

    public function login($email, $password){
        $sql = $this->db->query("SELECT * FROM users WHERE email=" . $this->db->escape($email));
        if($sql->num_rows() != 0){
            $user = $sql->row();
            if ($this->hashing->matches($password, $user->pass)) return $user->id;
            else return 0;
        }
        else return 0;
    }

    public function get_setting($name){
        $sql = $this->db->query("SELECT * FROM settings WHERE name=" . $this->db->escape($name));
        $data = $sql->row();
        if(@isset($data->value)) return $data->value;
        else return false;
    }

    public function get_all_settings(){
        $sql = $this->db->query("SELECT * from settings ORDER BY id");
        return $sql->result();
    }

    public function get_row_by_id($table, $id){
        $id = (int) $id;
        $sql = $this->db->query("SELECT * FROM $table WHERE id='$id'");
        if($sql->num_rows() == 0) return false;
        return $sql->row();
    }

    public function verify_token($token){
        $token = explode('-', $token);
        $token[0] = (int) $token[0];
        $token[1] = (int) $token[1];
        $sql = $this->db->query("SELECT * FROM _sessions WHERE id='$token[0]' AND userid='$token[1]'");
        if(!$sql->num_rows()) return 0;
        $s = $sql->row();
        if ($this->hashing->matches($token[2], $s->token)){
            $sql = $this->db->query("DELETE FROM _sessions WHERE id='$token[0]'");
            return $token[1];
        }
        else return 0;
    }

    public function new_token($userid){
        $userid = (int) $userid;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $token = '';
        for ($i = 0; $i < 32; $i++) {
            $token .= $characters[rand(0, strlen($characters) - 1)];
        }
        $data = array(
        	'id' => '0',
        	'userid' => $userid,
        	'token' => $this->hashing->hash($token)
        	);
        $this->db->insert('_sessions', $data);
        $sql = $this->db->query("SELECT * FROM _sessions WHERE userid='$userid' ORDER BY id DESC LIMIT 1");
        $t = $sql->row();
        $token = $t->id . '-' . $userid . '-' . $token;

        return $token;
    }

    public function isbanned($id){
        $id = (int) $id;
        $user = $this->get_user_info($id);
        return $user->banned;
    }
    public function unban($id){
        $id = (int) $id;
        $sql = $this->db->query("UPDATE users SET banned='0' WHERE id='$id'");
    }

    public function get_user_comments($id, $limit){
        $id = (int) $id;
        $limit = (int) $limit;
        $sql = $this->db->query("SELECT * FROM comments WHERE userid='$id' ORDER BY id DESC LIMIT $limit");
        $ar = $sql->result();
        $r = array();
        foreach ($ar as $a) {
            $k = $this->get_idea_by_id($a->ideaid);
            $r[] = array('idea' => $k->title, 'ideaid' => $a->ideaid, 'id' => $a->id, 'date' => $a->date);
        }
        return $r;
    }

    public function get_new_ideas($limit){
        $limit = (int) $limit;
        $sql = $this->db->query("SELECT * FROM ideas WHERE status='new' ORDER BY id DESC LIMIT $limit");
        return $sql->result();
    }

    public function get_new_ideas_num(){
        $sql = $this->db->query("SELECT * FROM ideas WHERE status='new'");
        return $sql->num_rows();
    }

    public function get_comment($id){
        $id = (int) $id;
        return $this->get_row_by_id('comments', $id);
    }

    public function get_flags(){
        $sql = $this->db->query("SELECT * FROM flags ORDER BY toflagid DESC");
        $list = $sql->result();
        $end = array();
        $t = 0;
        foreach ($list as $flags) {
            if(!$t){
                $com = $this->get_comment($flags->toflagid);
                $end[] = array('id' => $flags->toflagid, 'content' => $com->content, 'userid' => $com->userid, 'ideaid' => $com->ideaid , 'votes' => 1);
                $t++;
            }
            else{
                if($flags->toflagid == $end[$t-1]['id']){
                    $end[$t-1]['votes']++;
                }
                else{
                $com = $this->get_comment($flags->toflagid);
                $end[] = array('id' => $flags->toflagid, 'content' => $com->content, 'userid' => $com->userid, 'ideaid' => $com->ideaid , 'votes' => 1);
                    $t++;
                }
            }
        }
        return $end;
    }

    public function get_logs($to, $toid, $limit = 0){   
        $toid = (int) $toid;
        $limit = (int) $limit;     
        if($limit != 0) $sql = $this->db->query("SELECT * FROM logs WHERE type=". $this->db->escape($to) ." AND toid='$toid' ORDER BY id DESC LIMIT $limit");
        else $sql = $this->db->query("SELECT * FROM logs WHERE type=". $this->db->escape($to) ." AND toid='$toid' ORDER BY id DESC");
        return $sql->result();
    }

    public function get_last_logs($limit = 30){
        $limit = (int) $limit;
        $sql = $this->db->query("SELECT content,date FROM logs ORDER BY id DESC LIMIT $limit");
        return $sql->result();
    }

    public function get_users($order = "id", $limit = 30){
        $limit = (int) $limit;
        if($order == "banned"){
            $sql = $this->db->query("SELECT * FROM users WHERE banned <> 0 ORDER BY id DESC LIMIT $limit");
        }
        else{
            if($order == "votes");
            else $order="id";
            $sql = $this->db->query("SELECT * FROM users WHERE banned=0 ORDER BY $order DESC LIMIT $limit");
        }
        return $sql->result();
    }

    public function get_user_votes($userid){
        $userid = (int) $userid;
        $sql = $this->db->query("SELECT * FROM votes WHERE userid='$userid'");
        $res = $sql->result();
        $list = array();
        foreach($res as $vote){
            $t = array();
            $idea = $this->get_idea_by_id($vote->ideaid);
            $t['idea'] = $idea->title;
            $t['ideaid'] = $idea->id;
            $t['number'] = $vote->number;
            $t['id'] = $vote->id;
            $list[] = $t;
        }
        return $list;
    }

    public function get_admin_users(){
        $sql = $this->db->query("SELECT * FROM users WHERE isadmin <> 0 ORDER BY id");
        return $sql->result();
    }

    public function category_id($name){
        $sql = $this->db->query("SELECT id FROM categories where name='$name'");
        if($sql->num_rows() == 0) return 0;
        else{
            $cat = $sql->row();
            return $cat->id;
        }
    }

    public function email_config(){
            $config['protocol']    = 'smtp';
            $config['smtp_host']    = $this->get_setting('smtp-host');
            $config['smtp_port']    = $this->get_setting('smtp-port');
            $config['smtp_timeout'] = '7';
            $config['smtp_user']    = $this->get_setting('smtp-user');
            $config['smtp_pass']    = $this->get_setting('smtp-pass');
            $config['charset']    = 'utf-8';
            $config['newline']    = "\r\n";
            $config['mailtype'] = 'text'; // or html
            $config['validation'] = FALSE;  
            return $config;
    }
}
?>