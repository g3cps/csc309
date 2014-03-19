<h2>Finalize Order</h2>

<style>
	input { display: block;}
	
</style>

<?php 
	echo form_open_multipart('checkout/finalizeOrder');
		
	echo form_label('Credit Card Number'); 
	echo form_input('creditcard_number',set_value('creditcard_number'),"required");

	echo "<br>";

	echo form_label('Expiry Month (1-12)');
	echo form_input('creditcard_month',set_value('creditcard_month'),"required");
	
	echo "<br>";
	
	echo form_label('Expiry Year');
	echo form_input('creditcard_year',set_value('creditcard_year'),"required");
	
	echo "<br>";
	
	echo form_submit('submit', 'Finalize');
	echo form_close();
?>	

