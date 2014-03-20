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
			$data['errormsg'] = "<p>Please input number at least 0 with no decimal place!</p>";
			$this->load->view('template',$data);
		}
	}
	
	function finalize() {
		$data['main']='checkout/finalize.php';
		$data['title'] = 'Finalize Order';
		$this->load->view('template', $data);
	}
	
	function finalizeOrder() {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('creditcard_number', 'Credit Card Number', 'required|integer|exact_length[16]');
		$this->form_validation->set_rules('creditcard_month', 'Credit Card Expiry Month', 'required|integer|greater_than[0]|less_than[13]');
		$this->form_validation->set_rules('creditcard_year', 'Credit Card Expiry Year', 'required|integer|greater_than[2013]');

		if($this->form_validation->run() == FALSE) {
			$data['title']='Finalize Order';
			$data['main']='checkout/finalize.php';
			$data['errormsg'] = validation_errors();
			$this->load->view('template',$data);
		} else {
			if ($this->input->post('creditcard_year') == date("Y") &&
			    $this->input->post('creditcard_month') <= date("m")) {
					$data['title']='Finalize Order';
					$data['main']='checkout/finalize.php';
					$data['errormsg'] = 'You must use a non-expired credit card.';
					$this->load->view('template',$data);
			} else {
				
				$session_data = $this->session->userdata('logged_in');
				

				$this->load->model('product_model');
				$products = $this->product_model->getAll();
				$total = 0;
				foreach ($products as $product){
					foreach ($this->session->userdata('cart') as $added){
						if ($product->id == $added[0]) {
							$total = $total + $added[1] * $product->price;
						}
					}
				}

				$this->load->model('order_model');
				$order = new Order();
				$order->customer_id = $session_data['id'];
				$order->order_date = date('Y-m-d H:i:s');
				$order->order_time = date('H:i:s', strtotime('now'));
				$order->total = $total;
				$order->creditcard_number = strval($this->input->post('creditcard_number'));
				$order->creditcard_month = $this->input->post('creditcard_month');
				$order->creditcard_year = $this->input->post('creditcard_year');
				$orderid = $this->order_model->insert($order);
				
				
				$this->load->model('product_model');
				$products = $this->product_model->getAll();
				
				foreach ($products as $product){
					foreach ($this->session->userdata('cart') as $added){
						if ($product->id == $added[0]) {
							
							$this->load->model('orderitem_model');
							$order_item = new OrderItem();
							$order_item->order_id = $orderid;
							$order_item->product_id = $product->id;
							$order_item->quantity = $added[1];
							$this->orderitem_model->insert($order_item);	
						}
					}
				}
				
				redirect('checkout/send', 'refresh');
			}
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
		
		$message = '';
		$total = 0;
		
		if ($this->session->userdata('logged_in') && 
			$this->session->userdata('cart') && 
			sizeof($this->session->userdata('cart')) > 0){
			
			$this->load->model('product_model');
			$products = $this->product_model->getAll();
			foreach ($products as $product){
				foreach ($this->session->userdata('cart') as $added){
					if ($product->id == $added[0]){
						$message = $message . "<p>" . $product->name . 
							" - $" .  $product->price . " each x" . 
							$added[1] . "</p>";
						$total += $added[1] * $product->price;
						break;
					}
				}
			}
			
			$email = '';
			$name = '';
			
			//Get customer's information
			$this->load->model('customer_model');
			$customers = $this->customer_model->getAll();
			$user = $this->session->userdata('logged_in');
			foreach ($customers as $customer){
				if ($user['id'] == $customer->id){
					$email = $customer->email;
					$name = $customer->first . " " . $customer->last;
					break;
				}
			}
			
			$message = "<p>To: " . $name . "</p>" . 
						$message . "<p>Total: $" . $total . "</p>" .
						"<p>Thank you for your business,<br>World's Best Candy</p>";
			
			$this->load->library('email', $config);
			$this->email->set_newline('\r\n');
			$this->email->from('worldbestcandy@gmail.com ', "Candy Shop");
			$this->email->to($email);
			$this->email->set_mailtype("html");
			$this->email->subject("CandyShop - Thank You for purchasing!");
			$this->email->message($message);
		
			if ($this->email->send()){
				$data['main']='checkout/receipt.php';
				$data['receipt'] = $message;
				$data['head']= "<h1>Receipt:</h1><br><h2>Purchase is complete!<br>A copy of the receipt is sent to: $email </h2>";
				$data['title'] = "Receipt";
				//Remove items in cart since purchase has finalized
				$this->session->unset_userdata('cart');
				$this->load->view('template',$data);
			} else {
				$data['main']='checkout/receipt.php';
				$data['receipt'] = '';
				$data['head']= "<h1>Receipt:</h1>";
				$data['title'] = "Receipt";
				$data['errormsg'] = "There was an error sending the receipt";
				//Remove items in cart since purchase has finalized
				$this->session->unset_userdata('cart');
				$this->load->view('template',$data);
			}
		} else { //Illegal access to send page
			redirect('candystore/index', 'refresh');
		}
	}
}

