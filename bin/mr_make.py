#!/usr/bin/python3

'''
This script runs 'make' (make(1)) in every project
that is mine.
'''

import os # for chdir, system
import os.path # for expanduser, isdir, isfile, join
import utils.github # for get_nonforked_repos
import subprocess # for check_call
import yaml # for loads
import sys # for exit

home=os.getenv('HOME')

projects=list()
filename=os.path.expanduser('~/.mrconfig')
for line in open(filename):
    line=line.rstrip()
    if line.startswith('['):
        project_root=os.path.join(home, line[1:-1])
        project_name=line[1:-1].split('/')[-1]
        projects.append((project_name, project_root))

project_options_file=os.path.expanduser('~/.mroptions.yaml')
if os.path.isfile(project_options_file):
    with open(project_options_file) as f:
        opts=yaml.load(f)
else:
    opts=dict()
#print(opts)
#sys.exit(1)

def run_check_string(args, string):
    ''' this method runs make and checks that the output does not have lines with warnings in them '''
    p=subprocess.Popen(args, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    res_out, res_err = p.communicate()
    res_out=res_out.decode()
    res_err=res_err.decode()
    error=False
    if p.returncode:
        error=True
    if any(line.find(string)>0 for line in res_err.split()):
        error=True
    if error:
        print(res_out, file=sys.stderr, end='')
        print(res_err, file=sys.stderr, end='')
        sys.exit(p.returncode)

def run_empty_output(args):
    p=subprocess.Popen(args, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    res_out, res_err = p.communicate()
    res_out=res_out.decode()
    res_err=res_err.decode()
    if p.returncode or res_out!='' or res_err!='':
        print(res_out, file=sys.stderr, end='')
        print(res_err, file=sys.stderr, end='')
        sys.exit(p.returncode)

#projects=[(repo.name, os.path.join(home,'git',repo.name)) for repo in utils.github.get_nonforked_repos_list()]

for project_name, project_root in projects:
    if not os.path.isdir(project_root):
        continue
    print('building [{0}] at [{1}]...'.format(project_name, project_root))
    makefile=os.path.join(project_root, 'Makefile')
    bootstrap=os.path.join(project_root, 'bootstrap')
    if os.path.isfile(makefile):
        os.chdir(project_root)
        check_empty_output=True
        if project_name in opts:
            if 'dont_check_empty_output' in opts[project_name]:
                check_empty_output=False
        if check_empty_output:
            run_empty_output(['make'])
        else:
            run_check_string(['make'], string='warning')
        #os.system('make')
    elif os.path.isfile(bootstrap):
        os.chdir(project_root)
        subprocess.check_call(['./bootstrap'])
        #os.system('./bootstrap')
    else:
        #print('dont know how to build [{0}]...'.format(project_name))
        pass
