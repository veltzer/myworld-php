'''
Module to help build command line menus and y/n questions.

    Mark Veltzer <mark@veltzer.org>
'''

import os  # for system

# is the back functionality implemented?
back_is_implemented=False

def clear_screen():
    os.system('clear')

class Menu:
    def __init__(self, text):
        self.text=text
        self.items=[]
    def add_option(self, text, return_value):
        self.items.append((text, return_value))
    def select(self):
        print(self.text)
        while True:
            if back_is_implemented:
                print('0) Back')
            for i, (text, _) in enumerate(self.items, 1):
                print(str(i)+') '+text)
            print('your selection ---> ', end='')
            sel=input()
            try:
                option=int(sel)
            except ValueError:
                print(f'selection [{sel}] is problematic...')
                continue
            start_check = 0 if back_is_implemented else 1
            if start_check<=option<=len(self.items):
                return self.items[option-1][1]
            print(f'selection [{sel}] is problematic...')

class YNMenu:
    def __init__(self, text):
        self.text=text
    def select(self):
        while True:
            print(self.text)
            res=input()
            if res.startswith(('y', 'Y')):
                return True
            if res.startswith(('n', 'N')):
                return False
            print(f'I dont know what you mean by [{res}]\n')
