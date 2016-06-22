from bokeh.embed import file_html 
from bokeh.plotting import * 
from bokeh.resources import INLINE 
import numpy as np 
import wx 
import wx.html2 

class PlotWidget(wx.Dialog): 
  def __init__(self, *args, **kwds): 
    wx.Dialog.__init__(self, *args, **kwds) 
    sizer = wx.BoxSizer(wx.VERTICAL) 
    self.browser = wx.html2.WebView.New(self) 
    sizer.Add(self.browser, 1, wx.EXPAND, 10) 
    self.SetSizer(sizer) 
    self.SetSize((700, 700)) 

app = wx.App() 
w = PlotWidget(None, -1) 

N = 1000 
x = np.random.random(size=N) * 100 
y = np.random.random(size=N) * 100 
radii = np.random.random(size=N) * 4.5 
colors = ["#%02x%02x%02x" % (r, g, 150) for r, g in zip(np.floor(50+2*x), np.floor(30+2*y))] 
scatter( 
  x,y, radius=radii, fill_color=colors, 
  fill_alpha=0.6, line_color=None, 
) 
html = file_html(curdoc(), INLINE, "WX Embed Example") 

w.browser.SetPage(html, ".") 

w.Show() 

app.MainLoop() 