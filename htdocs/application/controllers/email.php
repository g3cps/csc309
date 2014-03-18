<?php

class Email extends CI_Controller {
   
     
    function __construct() {
    		parent::__construct();
	}
    
	function index(){
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

