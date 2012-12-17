=======
Layouts
=======

Layouts are components responsible for transforming a logging event into a 
string. 

More often than not, users wish to customize not only the output destination but 
also the output format. This is accomplished by associating a layout with an 
appender. All messages logged by that appender will use the given layout. 


Layout reference
================

.. toctree::
   :maxdepth: 1

   html
   pattern
   serialized
   simple
   ttcc
   xml
