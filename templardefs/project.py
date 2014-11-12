'''
project definitions for templar
'''

def populate(d):
	d.project_name='myworld'
	d.project_long_description='life feed project'
	d.project_year_started='2011'
	d.project_description='This is a collection of scripts and wordpress plugins that I use for my own databases.'

def getdeps():
	return [
		__file__, # myself
	]
