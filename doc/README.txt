This is the dbdata project
==========================
- this project imports pieces of data from files and into the database.
- the idea is that some data is better left alone as files but we still need
	it in the database.
- a prime example is a collection of blobs: images, mp3's, pdfs, icons and more.
- moreover - some of these files are not to be imported before they undergo
	some kind of processing: digital signature, scaling, tagging and more
	are examples of this.
- The idea is to have a build system that processes and imports ONLY new files
	while files that are detected to already have been imported are left alone.
