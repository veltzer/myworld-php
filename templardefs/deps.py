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
    d.packs=[
        # for wmctrl(1)
        'wmctrl',
        # for compressing javascript
        'yui-compressor',
        # for inserting blobs into the database
        'python-pythonmagick',
        # for convert(1) for image manipulation
        'imagemagick',
        # so that I could install python packages via pip
        'python-pip',
        # so that I could install python packages via pip
        'python3-pip',
        # mp3 tagging library
        'python-id3',
        # mp3 tagging library
        'python-eyed3',
        # mp3 tagging library
        'python-mutagen',
        # for youtube-dl
        'youtube-dl',
        # for imdb python module
        'python-imdbpy',
        # for video meta data
        'python-enzyme',
        # for video meta data
        'python3-enzyme',
        # for video meta data
        'python-mediainfodll',
        # for video meta data
        'python3-mediainfodll',
        # for video meta data
        'python-kaa-metadata',
        # for html parsing
        'python-bs4',
        # for html parsing
        'python3-bs4',
        # for imdb API
        'python-mysqldb',
        # for github
        'python-github',
        # for github
        'python3-github',
        # for jsonpickle
        'python3-jsonpickle',
        # for perl access to imdb
        'libimdb-film-perl',
        'libxml-simple-perl',
        'libyaml-perl',
        'libdate-manip-perl',
        'libmp3-info-perl',
        'libvideo-info-perl',
        'libnet-github-perl',
        'libfile-homedir-perl',
        'libconfig-tiny-perl',
        'libconfig-inifiles-perl',
        # for synching files
        'unison',
    ]

def get_deps():
    return [
        __file__, # myself
    ]
