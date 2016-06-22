from kartograph import Kartograph

cfg = {
   "layers": [{
       "src": "../map_files/Camp_Blocks_2016.shp"
   }]
}

K = Kartograph()
K.generate(cfg, outfile='mymap.svg')

