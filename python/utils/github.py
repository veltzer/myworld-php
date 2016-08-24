import github # for Github
import configparser # for ConfigParser
import os.path # for expanduser

inifile=os.path.expanduser('~/.details.ini')
config=configparser.ConfigParser()
config.read(inifile)
opt_username=config.get('github','username')

def get_nonforked_repos():
    g=github.Github()
    for repo in g.get_user(opt_username).get_repos():
        if not repo.fork:
            yield repo
