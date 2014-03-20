<?php

class CandyStore extends CI_Controller {
   
     
    function __construct() {
    		// Call the Controller constructor
	    	parent::__construct();
	    	
	    	
	    	$config['upload_path'] = './images/product/';
	    	$config['allowed_types'] = 'gif|jpg|png';
/*	    	$config['max_size'] = '100';
	    	$config['max_width'] = '1024';
	    	$config['max_height'] = '768';
*/
	    		    	
	    	$this->load->library('upload', $config);
	    	
    }
	
	function index() {
		$data['title']='Candystore';
		if ($this->session->userdata('logged_in')){
			redirect('candystore/home', 'refresh');
		} else {
			$data['main']='customer/login.php';
		}
		$this->load->view('template',$data);
	}
	
    function processLogin() {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('login', 'Login', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required|callback_checkLogin');
		
		if($this->form_validation->run() == FALSE) {
			$data['title']='Candystore';
			$data['main']='customer/login.php';
			$data['errormsg'] = validation_errors();
			$this->load->view('template',$data);
		} else {
			redirect('candystore/home', 'refresh');
		}
	}
	
	function checkLogin($password) {
		$this->load->model('customer_model');
		$login = $this->input->post('login');
		$result = $this->customer_model->login($login, $password);
		
		if ($result) {
			$sessionArray = array();
			foreach ($result as $row) {
				$sessionArray = array('id'=>$row->id, 'login'=>$row->login);
				$this->session->set_userdata('logged_in', $sessionArray);
			}
			return true;
		} elseif ($login === 'admin') {
			$sessionArray = array('id'=>-1, 'login'=>'admin');
			$this->session->set_userdata('logged_in', $sessionArray);
			redirect('candystore/administrator');
		} else {
			$this->form_validation->set_message('checkLogin', 'Invalid username or password.');
			return false;
		}
	}
	
	function logout() {
		$this->session->unset_userdata('logged_in');
		$this->session->unset_userdata('cart');
		redirect('candystore/index', 'refresh');
	}
	
	function home() {
		if ($this->session->userdata('logged_in')) {
			$session_data = $this->session->userdata('logged_in');
			$data['login'] = $session_data['login'];
			$data['title'] = 'Home';
			$data['main'] = 'home/home.php';
			$this->load->view('template.php', $data);
		} else {
			redirect('candystore/index', 'refresh');
		}
	}

    function products() {
		
		if ($this->session->userdata('logged_in')) {
			$session_data = $this->session->userdata('logged_in');
			if ($session_data['login'] === 'admin') {
				$this->load->model('product_model');
				$products = $this->product_model->getAll();
				$data['products']=$products;
				$data['main']='product/list.php';
				$data['title']='Products';
				$this->load->view('template',$data);
			} else {
				$this->load->model('product_model');
				$products = $this->product_model->getAll();
				$data['products']=$products;
				$data['main']='home/list.php';
				$data['title'] = 'Products';			
				$this->load->view('template', $data);
			}
		} else {
			redirect('candystore/index', 'refresh');
		}
		


    }

    function newForm() {
			$data['title']='New Product';
			$data['main']='product/newForm.php';
	    	$this->load->view('template.php', $data);
    }
    
	function create() {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name','Name','required|is_unique[product.name]');
		$this->form_validation->set_rules('description','Description','required');
		$this->form_validation->set_rules('price','Price','required|greater_than[0]');
		
		$fileUploadSuccess = $this->upload->do_upload();
		
		if ($this->form_validation->run() == true && $fileUploadSuccess) {
			$this->load->model('product_model');

			$product = new Product();
			$product->name = $this->input->get_post('name');
			$product->description = $this->input->get_post('description');
			$product->price = $this->input->get_post('price');
			
			$data = $this->upload->data();
			$product->photo_url = $data['file_name'];
			
			$this->product_model->insert($product);

			//Then we redirect to the index products again
			redirect('candystore/products', 'refresh');
		}
		else {
			if ( !$fileUploadSuccess) {
				$data['fileerror'] = $this->upload->display_errors();
				$data['title']='New Product';
				$data['main']='product/newForm.php';
				$this->load->view('template.php',$data);
				return;
			}
			$data['title']='New Product';
			$data['main']='product/newForm.php';
			$this->load->view('template.php', $data);
		}	
	}
	
	function read($id) {
		$this->load->model('product_model');
		$product = $this->product_model->get($id);
		$data['product']=$product;
		$data['title']='Read';
		$data['main']='product/read.php';
		$this->load->view('template.php',$data);
	}
	
	function editForm($id) {
		$this->load->model('product_model');
		$product = $this->product_model->get($id);
		$data['product']=$product;
		$data['title']='Edit Product';
		$data['main']='product/editForm.php';
		$this->load->view('template.php',$data);
	}
	
	function update($id) {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name','Name','required');
		$this->form_validation->set_rules('description','Description','required');
		$this->form_validation->set_rules('price','Price','required|greater_than[0]');
		
		if ($this->form_validation->run() == true) {
			$product = new Product();
			$product->id = $id;
			$product->name = $this->input->get_post('name');
			$product->description = $this->input->get_post('description');
			$product->price = $this->input->get_post('price');
			
			$this->load->model('product_model');
			$this->product_model->update($product);
			//Then we redirect to the product page again
			redirect('candystore/products', 'refresh');
		}
		else {
			$product = new Product();
			$product->id = $id;
			$product->name = set_value('name');
			$product->description = set_value('description');
			$product->price = set_value('price');
			$data['product']=$product;
			$data['title']='Edit Product';
			$data['main']='product/editForm.php';
			$this->load->view('template.php',$data);
		}
	}
    	
	function delete($id) {
		$this->load->model('product_model');
		
		if (isset($id)) 
			$this->product_model->delete($id);
		
		//Then we redirect to the products page again
		redirect('candystore/products', 'refresh');
	}
    
    function login() {		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('login','Login','required|callback_username_error');
		$this->form_validation->set_rules('password','Password','password_error|callback_login_error');
		if ($this->form_validation->run() == true) {
        	redirect('candystore/index', 'refresh');
		} else {
			$data['title']='Candystore';
			$data['main']='customer/login.php';
			$data['errormsg']=validation_errors();
			$this->load->view('template',$data);
		}
    }
	
	//Check if login name is in database
	public function username_error($name){
		$this->load->model('customer_model');
		$customers = $this->customer_model->getAll();
		foreach ($customers as $customer) {
			if ($customer->login == $name) {
				return(TRUE);
			}
		}
		return(FALSE);
	}
	
	//Check if password matches the username
	public function login_error(){
		$username = $this->input->post('login');
   		$password = $this->input->post('password');
		$this->load->model('customer_model');
		$customers = $this->customer_model->getAll();
		foreach ($customers as $customer) {
			if ($customer->login == $username && $customer->password == $password){
				return(TRUE);
			}
		}
		return(FALSE);
	}
	
	function registerForm() {
		$data['title']='Register';
		$data['main']='customer/register';
    	$this->load->view('template',$data);
	}
	
	function register() {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('login','Login','required|is_unique[customer.login]');
		$this->form_validation->set_rules('password','Password','required|min_length[6]');
		$this->form_validation->set_rules('passwordconf','Password Confirmation','matches[password]');
		$this->form_validation->set_rules('first','First Name','required');
		$this->form_validation->set_rules('last','Last Name','required');
		$this->form_validation->set_rules('email','Email','required|valid_email|is_unique[customer.email]');
		
		if ($this->form_validation->run() == true) {
			$this->load->model('customer_model');

			$customer = new Customer();
			$customer->login = $this->input->get_post('login');
			$customer->password = $this->input->get_post('password');
			$customer->first = $this->input->get_post('first');
			$customer->last = $this->input->get_post('last');
			$customer->email = $this->input->get_post('email');
			
			$this->customer_model->insert($customer);

			//Then we redirect to the index page again
			redirect('candystore/index', 'refresh');
		}
		else {
			$data['main' ]= 'customer/register';
			$data['errormsg'] = validation_errors();
    		$this->load->view('template',$data);
		}
	}

    function administrator() {
		
		if ($this->session->userdata('logged_in')) {
			$session_data = $this->session->userdata('logged_in');
			if ($session_data['login'] === 'admin') {
				$data['login'] = $session_data['login'];
				$data['title']='Administrator Page';
				$data['main']='administrator/administrator.php';
				$this->load->view('template',$data);
			} else {
				redirect('candystore/home', 'refresh');
			}
		} else {
			redirect('candystore/index', 'refresh');
		}
		
		
		
    }

    function users() {
		
		if ($this->session->userdata('logged_in')) {
			$session_data = $this->session->userdata('logged_in');
			if ($session_data['login'] === 'admin') {
				$this->load->model('customer_model');
				$customers = $this->customer_model->getAll();
				$data['customers']=$customers;
				$data['title']='Users';
				$data['main']='administrator/users.php';
				$this->load->view('template',$data);
			} else {
				redirect('candystore/home', 'refresh');
			}
		} else {
			redirect('candystore/index', 'refresh');
		}
		
		

    }

    function deleteuser($id) {
        $this->load->model('customer_model');
		
		if (isset($id)) 
			$this->customer_model->delete($id);
		
		//Then we redirect to the index page again
		redirect('candystore/users', 'refresh');
    }

    function orders() {
		
		if ($this->session->userdata('logged_in')) {
			$session_data = $this->session->userdata('logged_in');
			if ($session_data['login'] === 'admin') {
				$this->load->model('order_model');
				$orders = $this->order_model->getAll();
				$data['orders']=$orders;
				$data['main']='administrator/orders.php';
				$data['title']='Orders';
				$this->load->view('template',$data);
			} else {
				redirect('candystore/home', 'refresh');
			}
		} else {
			redirect('candystore/index', 'refresh');
		}
		

    }

    function deleteorder($id) {
        $this->load->model('order_model');
		
		if (isset($id)) 
			$this->order_model->delete($id);
		
		//Then we redirect to the index page again
		redirect('candystore/orders', 'refresh');
    }
    
    function addToCart() {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('quantity','Quantity','required|integer|greater_than[0]');
		
		if ($this->form_validation->run() == true) {
			$order_item = Array();
			$order_item[] = intval($this->input->get_post('product_id'));
			$order_item[] = intval($this->input->get_post('quantity'));

			if ($this->session->userdata('cart')) {
				$newcart = $this->session->userdata('cart');
				
				$existed = false;
				for ($i = 0; $i < count($newcart); $i++) {
					if ($newcart[$i][0] == $order_item[0]) {
						$existed = true;
						$newcart[$i][1] = $newcart[$i][1] + $order_item[1];
					}
				}
				
				if ($existed === false) {
					$newcart[] = $order_item;
				}

				$this->session->set_userdata('cart', $newcart);
			} else {
				$newcart = Array();
				$newcart[] = $order_item;
				$this->session->set_userdata('cart', $newcart);
			}

			redirect('candystore/products', 'refresh');
		}
		else {
			$this->load->model('product_model');
			$products = $this->product_model->getAll();
			$data['products']=$products;
			$data['main' ]= 'home/list.php';
			$data['title' ]= 'Products';
			$data['errormsg'] = validation_errors();
    		$this->load->view('template',$data);
		}
	}
    
}

