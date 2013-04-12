def application(environ, start_response):
	start_response('200 OK', [('Content-Type', 'text/plain')])
	return "Hello WSGI World from python application"
