<h2>Administrator</h2>
<?php 
	echo "<p>" . anchor('candystore/products','Manage Products') . "</p>";
    echo "<p>" . anchor('candystore/orders', 'Manage Orders') . "</p>";
    echo "<p>" . anchor('candystore/users', 'Manage Users') . "</p>";
    echo "<p>" . anchor('candystore/logout', 'Log Out') . "</p>";
?>
