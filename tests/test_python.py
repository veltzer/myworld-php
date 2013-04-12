from mod_python import apache

# this is the main entry point
def index(req):
	return "<html><body><h1>Hi from Python CGI</h1></body></html>"
# this one will be accessed if you access the {url/hello}
def hello(req):
	return "we are in hello"
