#!/usr/bin/perl

# Notes:
# - make sure that this file is executable and is accessible to the apache web server
 
# lets print some headers...
print "Content-type: text/html\n";
# this next line is critical since it separates HTTP headers
# from HTTP content
print "\n";
print <<HTML;
<html>
	<head>
		<title>A Simple Perl CGI</title>
	</head>
	<body>
		<h1>A Simple Perl CGI</h1>
		<p>Hello World</p>
	</body>
</html>
HTML
