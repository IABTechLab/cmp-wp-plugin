<?php
if (!empty($_POST['digitrust']['json'])) {
	$GLOBALS['digitrust_json'] = $_POST['digitrust']['json'];
}
?>
<script type="text/javascript"> 
	var digitrustConfig = <?php echo ''.$GLOBALS['digitrust_json']; ?>;
	console.log(digitrustConfig);
</script>