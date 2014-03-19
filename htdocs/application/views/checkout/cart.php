<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>

<?php
	if(isset($toprint))
		echo $toprint;
	echo form_open_multipart('checkout/changeqty');
	if ($this->session->userdata('logged_in') && $this->session->userdata('cart') && sizeof($this->session->userdata('cart')) > 0){
		echo "<p>You can change quantity for each item here.</p>";
		echo "<p>Set the quantity to 0 to remove item from cart</p>";
		$total = 0;
		foreach ($products as $product){
			foreach ($this->session->userdata('cart') as $added){
				if ($product->id == $added[0]){
					echo "<img id='incart' src='" . base_url() . 
						"images/product/" . $product->photo_url . 
						"'></img>";
					echo form_label($product->name . " - $" . $product->price . " each");
					echo form_input($added[0],$added[1]);
					$total += $added[1] * $product->price;
					break;
				}
			}
		}
		echo form_submit('submit', 'Update');
		echo form_close();
		echo "<p>Total: $$total </p>";
		echo "<p>" . anchor('checkout/finalize','Finalize Order') . "</p>";
	} else { //Nothing is in the cart
		echo "<p>Nothing in the cart :(</p>";
	}
?>

</body>
</html>
