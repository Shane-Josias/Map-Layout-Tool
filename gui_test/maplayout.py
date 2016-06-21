import matplotlib.pyplot as plt 
import numpy as np
from tkinter import Tk, Frame, BOTH

class Example(Frame):
	def __init__(self, parent):
		Frame.__init__(self, parent, background="white")
		self.parent = parent

		# self.initUI()

		self.parent.title("Centered Window")
		self.pack(fill=BOTH, expand=1)
		self.centerWindow()


	def centerWindow(self):
		w = 400
		h = 300

		sw = self.parent.winfo_screenwidth()
		sh = self.parent.winfo_screenheight()

		x = (sw - w) / 2
		y = (sh - h) / 2

		self.parent.geometry("%dx%d+%d+%d" % (w,h,x,y))

	# def initUI(self):
	# 	self.parent.title("Centered")
	# 	self.pack(fill=BOTH, expand=1)




def gui():
	root = Tk()
	# root.geometry("250x150+300+300")
	app = Example(root)
	root.mainloop()

if __name__ == '__main__':
	gui()