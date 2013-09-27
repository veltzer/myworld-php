<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title>Movies component development</title>
		<!--link rel="stylesheet" type="text/css" href="http://cdn.sencha.io/ext-4.0.7-gpl/resources/css/ext-all.css" /-->
		<link rel="stylesheet" type="text/css" href="http://cdn.sencha.io/ext-4.1.1-gpl/resources/css/ext-all.css" />
		<!--link rel="stylesheet" type="text/css" href="http://cdn.sencha.io/ext-4.2.0-gpl/resources/css/ext-all.css" /-->
		<!--script src="http://cdn.sencha.io/ext-4.0.7-gpl/ext-all.js"></script-->
		<script src="http://cdn.sencha.com/ext-4.1.1-gpl/ext-all.js"></script>
		<!--script src="http://cdn.sencha.com/ext-4.2.0-gpl/ext-all.js"></script-->
		<script src="../public/movies.js"></script>
	</head>
	<body>
		<div id="movie-grid"></div>
		<script>
			Ext.onReady(function() {
				create_movies('movie-grid');
			});
		</script>
	</body>
</html>
