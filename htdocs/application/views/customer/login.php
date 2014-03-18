<h2>Login</h2>

<style>
	input { display: block;}
	
</style>

<?php 
	echo "<p>" . anchor('candystore/registerForm','New User') . "</p>";
	
	echo form_open_multipart('candystore/processLogin');
		
	echo form_label('Login'); 
	//echo form_error('login');
	echo form_input('login',set_value('login'),"required");

	echo form_label('Password');
	//echo form_error('password');
	echo form_password('password',set_value(''),"required"); 	
	
	echo form_submit('submit', 'Log In');
	echo form_close();
?>	

