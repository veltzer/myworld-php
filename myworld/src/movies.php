<?php
function create_movies($params) {
$code=<<<EOD
	<script>
		var loc=get_my_location()
		Ext.onReady(function() {
			create_movies(create_div_at_location(loc))
		});
	</script>
EOD;
	return $code;
}

function create_movies_iframe($params) {
	$code=<<<EOD
		<iframe id="myframe" frameborder="0" src="/public/movies.html" width="100%"></iframe>
		<script type="text/javascript" language="javascript"> 
			// This function is here to receive an event from the iframe about the size
			// that it needs so that it could resize the iframe as needed and we would
			// not need to hardcode a size for the iframe in the above 'iframe'
			// declaration...
			function alertsize(pixels){
				// this small padding is to avoid browsers creating scroll bars around
				// this iframe.
				pixels+=32
				document.getElementById('myframe').style.height=pixels+"px"
			}
		</script>
EOD;
	return $code;
}
?>
