from tkinter import Tk, BOTH, Listbox, StringVar, END, LEFT, BOTTOM
from tkinter.ttk import Frame, Label, Style

class Example(Frame):
	def __init__(self, parent):
		Frame.__init__(self, parent)
		self.parent = parent

		self.initUI()

	def initUI(self):
		self.parent.title("Listbox and Canvas")
		self.pack(fill=BOTH, expand=1)
		self.style = Style()
		self.style.theme_use("clam")
		acts = ["shane", "Peter", "Shane", "Peter2", "only", "song", "one", "two", "you", "got", "it"]
		lb = Listbox(self)
		for element in acts:
			lb.insert(END,element)

		lb.bind("<<ListboxSelect>>", self.onSelect)
		lb.place(x=50, y=50, height=400, width=100)
		# lb.geometry("100x400 +50+ 50")

		self.var = StringVar()
		self.label = Label(self, text=0, textvariable=self.var)
		self.label.pack(side=LEFT)

	def onSelect(self, val):
		sender = val.widget
		idx = sender.curselection()
		value = sender.get(idx)
		self.var.set(value)

def gui():
	root = Tk()
	ex = Example(root)
	root.geometry("800x600+200+200")
	root.mainloop()

if __name__ == '__main__':
	gui()

