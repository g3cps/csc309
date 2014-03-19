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
			$this->load->model('product_model');
			$data['toprint'] = "<p>Here is your cart:</p>";
			$data['products'] = $this->product_model->getAll();
			$this->load->view('template',$data);
		} else { //Not logged in yet
			$data['toprint'] = "<p>Please Login to view cart</p>";
			$this->load->view('template',$data);
		}
	}
	
	function changeqty(){
		if ($this->session->userdata('cart') && sizeof($this->session->userdata('cart')) > 0) {
			$this->load->library('form_validation');
			foreach($this->session->userdata('cart') as $item){
				$this->form_validation->set_rules((String)$item[0],(String)$item[0],'integer|greater_than[-1]');
			}
		} else {
			$this->load->view('template',$data);
		}
		
		if ($this->form_validation->run() == true) {
			//Update quantity
			$toremove = array();
			$cart = $this->session->userdata('cart');
			foreach($this->session->userdata('cart') as $item){
				$qty = $this->input->get_post($item[0]);
				if ($qty >= 0){
					for ($i = 0; $i < sizeof($cart); $i++) {
						if (($qty > 0) && ($cart[$i][0] == $item[0])){
							$cart[$i][1] = $qty;
						} elseif (($qty == 0) && ($cart[$i][0] == $item[0])){
							array_push($toremove, $i); //remember what to remove
						}
					}
				}
			}
			//Remove 0 quantity items
			$offset = 0;
			foreach($toremove as $index){			
				unset($cart[$index + $offset]);
				$cart = array_values($cart);
				$offset--;
			}
			//Set cart to updated cart in session
			$this->session->set_userdata('cart', $cart);
			redirect('checkout/index', 'refresh');
		} else {
			$this->load->model('product_model');
			$data['main']='checkout/cart.php';
			$data['toprint'] = "<p>Here is your cart:</p>";
			$data['products'] = $this->product_model->getAll();
			$data['title']='Checkout Shopping Cart';
			$data['errormsg'] = "<p>Please input number only with no decimal place!</p>";
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

