<?php
class Customer_model extends CI_Model {

	function getAll()
	{  
		$query = $this->db->get('customer');
		return $query->result('Customer');
	}  
	
	function get($id)
	{
		$query = $this->db->get_where('customer',array('id' => $id));
		
		return $query->row(0,'Customer');
	}
	
	function delete($id) {
		return $this->db->delete("customer",array('id' => $id ));
	}
	
	function insert($customer) {
		return $this->db->insert("customer", array('first' => $customer->first,
				                                  'last' => $customer->last,
											      'login' => $customer->login,
												  'password' => $customer->password,
												  'email' => $customer->email));
	}
	
	function login($login, $password) {
		$this->db->select('id, login, password');
		$this->db->from('customer');
		$this->db->where('login', $login);
		$this->db->where('password', $password);
		$this->db->limit(1);
		
		$query = $this->db->get();
		if ($query->num_rows() == 1) {
			return $query->result();
		} else {
			return false;
		}
	}
}
?>
