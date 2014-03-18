<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title; ?></title>
<link rel='stylesheet' href="<?= base_url(); ?>css/template.css">
</head>
<body>
	<div id='header'>
    	<?php $this->load->view('header');?>
    </div>
    <div id='nav'>
    	<ul>
    		<li><?php echo "<p>" . anchor('candystore/index','Home') . "</p>";?></li>
            <li><?php echo "<p>" . anchor('candystore/products','Products') . "</p>";?></li>
            <li><?php echo "<p>" . anchor('candystore/administrator','Admin') . "</p>";?></li>
    	</ul>
    </div>
	<div id='main'>
		<?php 
		$this->load->view($main);
		if (isset($errormsg))
			echo "<div id='errormsg'>" . $errormsg . "</div>";
		?>
	</div>
</body>
</html>