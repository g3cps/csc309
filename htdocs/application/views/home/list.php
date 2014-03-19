<h2>Product Table</h2>
<?php  	  
		echo "<table>";
		echo "<tr><th>Name</th><th>Description</th><th>Price</th><th/><th>Quantity</th></tr>";
		
		foreach ($products as $product) {
			echo "<tr>";
			echo "<td>" . $product->name . "</td>";
			echo "<td>" . $product->description . "</td>";
			echo "<td>" . $product->price . "</td>";
			echo "<td><img src='" . base_url() . "images/product/" . $product->photo_url . "' width='100px' /></td>";
				
			echo "<td>";
			echo form_input('quantity',set_value('quantity'),"required");
			echo "</td>";
			echo "<td>";
			echo form_submit('submit', 'Add To Cart');
			echo "</td>";
			echo "</tr>";
		}
		echo "<table>";
?>	

