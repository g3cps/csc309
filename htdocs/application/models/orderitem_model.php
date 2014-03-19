<?php
class Orderitem_model extends CI_Model {

	function getAll()
	{  
		$query = $this->db->get('order_item');
		return $query->result('OrderItem');
	}  
	
	function get($id)
	{
		$query = $this->db->get_where('order_item',array('id' => $id));
		
		return $query->row(0,'OrderItem');
	}
	
	function delete($id) {
		return $this->db->delete("order_item",array('id' => $id ));
	}
	
	function insert($orderItem) {	
		return $this->db->insert("order_item", array('order_id' => $orderItem->order_id,
				                                  'product_id' => $orderItem->product_id,
												  'quantity' => $orderItem->quantity));
	}
}
?>
