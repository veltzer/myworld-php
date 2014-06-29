<?php
function create_chart($params) {
	if(!array_key_exists('type',$params)) {
		return '<strong>Error, must pass type</strong>';
	}
	if(!array_key_exists('model_x_label',$params)) {
		return '<strong>Error, must pass model_x_label</strong>';
	}
	if(!array_key_exists('model_y_label',$params)) {
		return '<strong>Error, must pass model_y_label</strong>';
	}
	if(!array_key_exists('box_title',$params)) {
		return '<strong>Error, must pass box_title</strong>';
	}
	if(!array_key_exists('count_title',$params)) {
		return '<strong>Error, must pass count_title</strong>';
	}
	if(!array_key_exists('field_title',$params)) {
		return '<strong>Error, must pass field_title</strong>';
	}
	$p_type=$params['type'];
	$p_model_x_label=$params['model_x_label'];
	$p_model_y_label=$params['model_y_label'];
	$p_box_title=$params['box_title'];
	$p_count_title=$params['count_title'];
	$p_field_title=$params['field_title'];
	// the next string is in double quotes to allow for variable interpolation
	return "<script>create_chart_here(\"$p_type\", \"$p_model_x_label\", \"$p_model_y_label\", \"$p_box_title\", \"$p_count_title\", \"$p_field_title\")</script>";
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
