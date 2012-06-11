<?php
function create_movies($params) {
#	// running id for the divs
#	static $p_id=1;
#	$code=<<<EOD
#		<div id="movies_$p_id">
#		</div>
#		<script>
#			Ext.onReady(function() {
#				create_movies('movies_$p_id');
#			});
#		</script>
#	$p_id++;
#EOD;
	$code=<<<EOD
		<iframe frameborder="0" src="/public/movies.html" width="100%" height="600px"></iframe>
EOD;
	return $code;
}
?>
