'''
Various python utils until they get sorted
'''

import os.path # for join
import urllib.parse # for urlparse

def filename_switch(p_folder, f_tname, f_externalCode):
    if f_tname=='youtube_video_id':
        return os.path.join(p_folder, f_tname, f_externalCode)
    if f_tname=='ted_video_id':
        return os.path.join(p_folder, f_tname, f_externalCode)
    if f_tname=='download_url':
        o=urllib.parse.urlparse(f_externalCode)
        return os.path.join(p_folder, f_tname, o.path[1:])
