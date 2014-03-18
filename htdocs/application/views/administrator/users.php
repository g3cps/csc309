<h2>Users</h2>
<?php 
		echo "<table>";
		echo "<tr><th>Login</th><th>Password</th><th>First Name</th><th>Last Name</th><th>Email</th></tr>";
		
		foreach ($customers as $customer) {
			echo "<tr>";
			echo "<td>" . $customer->login . "</td>";
			echo "<td>" . $customer->password . "</td>";
			echo "<td>" . $customer->first . "</td>";
            echo "<td>" . $customer->last . "</td>";
            echo "<td>" . $customer->email . "</td>";
			echo "<td>" . anchor("candystore/deleteuser/$customer->id",'Delete',"onClick='return confirm(\"Do you really want to delete this user?\");'") . "</td>";

				
			echo "</tr>";
		}
		echo "<table>";
?>
