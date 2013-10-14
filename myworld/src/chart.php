<?php
function create_chart($params) {
	if(!array_key_exists('type',$params)) {
		return '<strong>Error, must pass type</strong>';
	}
	$p_type=$params['type'];
	// the next string is in double quotes to allow for variable interpolation
	return "<script>create_chart_here(\"$p_type\")</script>";
}

function create_chart_dojo($params) {
	if(!array_key_exists('type',$params)) {
		return '<strong>Error, must pass type</strong>';
	}
	if(!array_key_exists('max',$params)) {
		return '<strong>Error, must pass max</strong>';
	}
	if(!array_key_exists('doWidth',$params)) {
		return '<strong>Error, must pass doWidth</strong>';
	}
	$p_doWidth=$params['doWidth'];
	$p_type=$params['type'];
	$p_max=$params['max'];
	$p_ticks=$p_max/10;
	if($p_doWidth) {
		if(!array_key_exists('width',$params)) {
			return '<strong>Error, must pass width</strong>';
		}
		$p_width=$params['width'];
		$style='style="width: '.$p_width.'"';
	} else {
		$style='';
	}
	return "<script>create_chart_dojo_here($p_max, $p_ticks, \"$p_type\")</script>";
}
?>
