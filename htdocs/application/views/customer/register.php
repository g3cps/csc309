<h2>New User</h2>

<style>
	input { display: block;}
	
</style>

<?php 
	echo "<p>" . anchor('candystore/index','Back') . "</p>";
	
	echo form_open_multipart('candystore/register');
		
	echo form_label('Login'); 
	//echo form_error('login');
	echo form_input('login',set_value('login'),"required");

	echo form_label('Password');
	//echo form_error('password');
	echo form_password('password',set_value(''),"required");
	
	echo form_label('Password Confirmation');
	//echo form_error('passwordconf');
	echo form_password('passwordconf',set_value(''),"required");
	
	echo form_label('First Name');
	//echo form_error('first');
	echo form_input('first',set_value('first'),"required");
	
	echo form_label('Last Name');
	//echo form_error('last');
	echo form_input('last',set_value('last'),"required");
	
	echo form_label('Email');
	//echo form_error('email');
	echo form_input('email',set_value('email'),"required");
	
	echo form_submit('submit', 'Register');
	echo form_close();
?>	

