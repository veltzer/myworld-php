'''
Module to help build command line menus and y/n questions.

    Mark Veltzer <mark@veltzer.net>
'''

import os  # for system

# is the back functionality implemented?
backIsImplemented=False

def clear_screen():
    os.system('clear')

class Menu:
    def __init__(self, text):
        self.text=text
        self.items=[]
    def add_option(self, text, returnValue):
        self.items.append((text, returnValue))
    def select(self):
        print(self.text)
        over=False
        while not over:
            if backIsImplemented:
                print('0) Back')
            i=1
            for text, returnValue in self.items:
                print(str(i)+') '+text)
                i+=1
            print('your selection ---> ', end='')
            sel=input()
            try:
                option=int(sel)
            except ValueError:
                print(f'selection [{sel}] is problematic...')
                continue
            if backIsImplemented:
                startCheck=0
            else:
                startCheck=1
            if option>=startCheck and option<=len(self.items):
                over=True
            else:
                print(f'selection [{sel}] is problematic...')
        return self.items[option-1][1]

class YNMenu:
    def __init__(self, text):
        self.text=text
    def select(self):
        over=False
        while not over:
            print(self.text)
            res=input()
            if res.startswith(('y', 'Y')):
                ret=True
                over=True
                continue
            if res.startswith(('n', 'N')):
                ret=False
                over=True
                continue
            print(f'I dont know what you mean by [{res}]\n')
        return ret
