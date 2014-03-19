<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<input class="noprint button" type="button" value="Print Receipt" onclick="window.print()">

<div class='printable'>
<?php
	echo $head;
	echo $receipt;
?>
</div>

</body>

<script>
</script>

</html>
