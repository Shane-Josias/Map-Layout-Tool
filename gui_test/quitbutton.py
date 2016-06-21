from tkinter import Tk, BOTH, RIGHT, RAISED, LEFT
from tkinter.ttk import Frame, Button, Style


class Example(Frame):
	def __init__(self, parent):
		Frame.__init__(self,parent)
		self.parent = parent
		self.initGUI()

	def initGUI(self):
		self.parent.title("quit button")
		self.style = Style()
		self.style.theme_use("clam")

		frame = Frame(self, relief=RAISED, borderwidth=1)
		frame.pack(fill=BOTH, expand=1)

		self.pack(fill=BOTH, expand=1)
		quitButton = Button(self, text="Quit", command=self.quit)

		quitButton.pack(side=RIGHT, padx=5,pady=5)

		okButton = Button(self, text="OK")
		okButton.pack(side=LEFT, padx = 5)



def gui():
	root = Tk()
	root.geometry("300x200+300+300")
	app = Example(root)
	root.mainloop()

if __name__ == '__main__':
	gui()