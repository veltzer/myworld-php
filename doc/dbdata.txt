some database conventions:
all tables have "id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY"
all tables are TbCamelCase with words which are legal.
fileds are FdCamelCase with words which are legal. 
every project has tables starting with it's name
the name of the project does NOT have to be a legal name
if a field refers to a table Foo it should have the name FdPurposeFooId

The audio book project
main concepts: author, title, hearing, review.
author is one person (name, family name etc...).
many authors per title.
title has publisher,isbn and whatever.
many reviews per hearer.
many hearings per hearer.

projects:
db - database schema definition in a database.
id - identity data (contant and person information).
lc - location data.
rs - resources: tags, images, texts, colors.
tv - title/view information.
wg - word groups.
wk - works.
