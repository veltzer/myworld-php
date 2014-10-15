'''
Module to help build command line menus and y/n questions.

	Mark Veltzer <mark@veltzer.net>
'''

from __future__ import print_function
import os # for system

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
			sel=raw_input()
			try:
				option=int(sel)
			except Exception, e:
				print('selection [%s] is problematic...' % (sel))
				continue
			if backIsImplemented:
				startCheck=0
			else:
				startCheck=1
			if option>=startCheck and option<=len(self.items):
				over=True
			else:
				print('selection [%s] is problematic...' % (sel))
		return self.items[option-1][1]

class YNMenu:
	def __init__(self, text):
		self.text=text
	def select(self):
		over=False
		while not over:
			print(self.text)
			res=raw_input()
			if res.startswith('y') or res.startswith('Y'):
				ret=True
				over=True
				continue
			if res.startswith('n') or res.startswith('N'):
				ret=False
				over=True
				continue
			print('I dont know what you mean by [%s]\n' % (res))
		return ret
