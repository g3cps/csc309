<?php

class Checkout extends CI_Controller {
   
     
    function __construct() {
    		parent::__construct();
	}
    
	function index(){
		$data['main']='checkout/cart.php';
		$data['title']='Checkout Shopping Cart';
		$data['toprint'] = '';
		
		if ($this->session->userdata('logged_in')) {
			//There is something in the cart
			if ($this->session->userdata('cart')){
				$this->load->model('product_model');
				$products = $this->product_model->getAll();
				foreach ($products as $product){
					foreach ($this->session->userdata('cart') as $added){
						if ($product->id == $added[0]){
							$data['toprint'] = $data['toprint'] . 
							"<img id='incart' src='" . base_url() . 
							"images/product/" . $product->photo_url . 
							"'></img><p>Qty: " . $added[1] . "</p>";
						}
					}
				}
			} else { //Nothing is in the cart
				$data['toprint'] = "<p>Nothing in the cart :(</p>";
			}
			$this->load->view('template',$data);
		} else { //Not logged in yet
			$data['toprint'] = "<p>Please Login to view cart</p>";
			$this->load->view('template',$data);
		}
	}
	
	function send(){
		$config = Array(
			'protocol' => 'smtp',
			'smtp_host' => 'ssl://smtp.googlemail.com',
			'smtp_port' => 465,
			'smtp_user'=> 'worldbestcandy@gmail.com ',
			'smtp_pass' => 'csc309candy',
			'mailtype'  => 'html'
		);
		
		$this->load->library('email', $config);
		$this->email->set_newline('\r\n');
		
		$this->email->from('worldbestcandy@gmail.com ', "Candy Shop");
		$this->email->to('worldbestcandy@gmail.com ');
		$this->email->subject("Testing Email");
		$this->email->message("IT IS WORKING!");
	
		if ($this->email->send()){
			echo "<p> IT IS SENT! </p>";
		} else {
			show_error($this->email->print_debugger());
		}
	}
}

