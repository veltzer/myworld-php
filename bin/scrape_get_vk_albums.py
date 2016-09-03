#!/usr/bin/python3

'''
This script downloads the public vk albums of a vk public vk user.
You need to supply the user id. This is something you see in the
url when you surfe the users account at vk.com

References:
- http://docs.python-requests.org/en/master
- http://docs.python-guide.org/en/latest/scenarios/scrape
'''

import requests # for post
import lxml.html # for fromstring
import lxml.etree # for tostring
import json # for loads
import shutil # for copyfileobj

def get_real_content(r):
    assert r.status_code==200
    strcontent=r.content.decode()
    strcontent=strcontent[strcontent.find('<input'):]
    c=str.encode('<html><body>')+str.encode(strcontent)+str.encode('</body></html>')
    root=lxml.html.fromstring(c)
    return root

owner=sys.argv[1]
url='https://vk.com/al_photos.php'
data={
    'act': 'show_albums',
    'al': '1',
    'owner': owner,
}
r = requests.post(url, data = data)
root=get_real_content(r)

e_albums = root.xpath('//div[@class="photos_album_title_wrap"]')
albums=dict()
for x in e_albums:
    name_of_album=x.getparent().getparent().getparent().getparent().attrib['id'].split('?')[0]
    e_len=x.xpath('.//div[@class="photos_album_counter fl_r"]/text()')
    albums[name_of_album]=int(e_len[0])
#print(albums)
total_images=0
for v in albums.values():
    total_images+=v
print(total_images)

count=0
urls=set()
got=1
while got:
    got=0
    data={
        'act': 'show_albums',
        'al': '2',
        'owner': owner,
        'offset': count,
    }
    print('doing request', count)
    r = requests.post(url, data = data)
    root=get_real_content(r)
    e_a = root.xpath('//a[@onclick]')
    for x in e_a:
        onclick=x.attrib['onclick']
        if onclick.startswith('return showPhoto'):
            json_str=onclick[onclick.find('{'):onclick.rfind('}')+1]
            # bas string, need fix lots of things...
            json_str=json_str.replace('\'','"')
            json_str=json_str.replace('jumpTo','"jumpTo"')
            json_str=json_str.replace('z:','"z":')
            json_obj=json.loads(json_str)
            base=json_obj['temp']['base']
            if base=='':
                continue
            largest=0
            largest_url=None
            minlen=min(len(v) for k,v in json_obj['temp'].items() if k.endswith('_'))
            if minlen==3:
                for k,v in json_obj['temp'].items():
                    if k!='base':
                        size=v[1]*v[2]
                        if size>largest:
                            largest=size
                            largest_url=v[0]
                full_url=base+largest_url+'.jpg'
                urls.add(full_url)
                got+=1
            if minlen==1:
                for k,v in json_obj['temp'].items():
                    if k!='base':
                            add_url=v[0]
                            full_url=base+add_url+'.jpg'
                            urls.add(full_url)
                got+=1
    count+=got

cnt=0
for url in urls:
    print(url)
    r=requests.get(url, stream=True)
    assert r.status_code==200
    filename='image{0:04}.jpg'.format(cnt)
    with open(filename, 'wb') as f:
        r.raw.decode_content = True
        shutil.copyfileobj(r.raw, f)
    print('written [{0}]...'.format(filename))
    cnt+=1
