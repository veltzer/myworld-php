<?php
function create_movies($params) {
	// running id for the divs
	static $p_id=1;
	$code=<<<EOD
		<div id="movies_$p_id">
			<div id="movies_$p_id"></div>
		</div>
		<script>
			Ext.onReady(function() {
				create_movies('movies_$p_id');
			});
		</script>
EOD;
	$p_id++;
	return $code;
}
?>
