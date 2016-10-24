'''
dependencies for this project
'''

def populate(d):
    d.tools=[
        'jsl',
    ]
    d.requirements3=[
        'PyGithub',
    ]

def getdeps():
    return [
        __file__, # myself
    ]
