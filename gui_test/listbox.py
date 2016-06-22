from tkinter import Tk, BOTH, Listbox, StringVar, END, LEFT, BOTTOM
from tkinter.ttk import Frame, Label, Style, Entry

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

		self.columnconfigure(0, pad=3)
		self.columnconfigure(1, pad=3)
		self.rowconfigure(0, pad=3,minsize=50)
		self.rowconfigure(1, pad=3,minsize=50)
		self.rowconfigure(2, pad=3,minsize=50)
		self.rowconfigure(3, pad=3,minsize=50)




		acts = ["shane", "Peter", "Shane", "Peter2", "only", "song", "one", "two", "you", "got", "it"]
		lb = Listbox(self)
		for element in acts:
			lb.insert(END,element)

		lb.bind("<<ListboxSelect>>", self.onSelect)
		lb.grid(row=0, column=0,rowspan=3)
		# lb.geometry("100x400 +50+ 50")

		self.var = StringVar()
		self.label = Label(self, text=0, textvariable=self.var)
		self.label.grid(row=4, column=0)

	def onSelect(self, val):
		sender = val.widget
		idx = sender.curselection()
		value = sender.get(idx)
		self.var.set(value)

def gui():
	root = Tk()
	ex = Example(root)
	root.geometry("350x300+300+300")
	root.mainloop()

if __name__ == '__main__':
	gui()

