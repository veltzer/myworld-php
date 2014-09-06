This folder contains the code needed to create self signed certificates
to be used by the apache web server to provide ssl services.

The output of this folder is the 'veltzer.pem' and the 'veltzer.key' which
should be placed in the /etc/ssl/certs and /etc/ssl/private folders.

These are referenced by lines in an apache virtual host defintion like this:

<VirtualHost ...>

	...
	...

	SSLCertificateFile    /etc/ssl/certs/veltzer.pem
	SSLCertificateKeyFile /etc/ssl/private/veltzer.key

</VirtualHost>
