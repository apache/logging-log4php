============================
Apache log4php documentation
============================

This directory contains the documentation for Apache log4php.

The documentation is in reStructuredText_ format and can be viewed using any
text editor. Conversion to other formats, such as html, epub and pdf is achieved
by using the Sphinx_ documentation system, and requires Python_.

.. _reStructuredText: http://docutils.sourceforge.net/rst.html
.. _Sphinx: http://sphinx-doc.org/
.. _Python: http://www.python.org/

Setting up
----------
It's recommended to have the latest version of Python 2.7.x, along with pip and
virtualenv. If you're not sure how to set this up, there's a good guide for all
platforms `here <https://python-guide.readthedocs.org/en/latest/>`_.

First, create a virutal environment in _env directory and activate it.

Linux:

.. code-block:: bash

    virtualenv _env
    source _env/bin/activate

Windows:

.. code-block:: bat

    virtualenv _env
    _env\Scripts\activate.bat

The requirements are listed in `requirements.txt` so it's easy to install them:

.. code-block:: bash

    pip install -r requirements.txt

You're now set up for generating docs.

Generating
----------
Running `make help` displays all possible targets:

.. code-block:: none

    Please use `make <target>` where <target> is one of
      html       to make standalone HTML files
      dirhtml    to make HTML files named index.html in directories
      singlehtml to make a single large HTML file
      pickle     to make pickle files
      json       to make JSON files
      htmlhelp   to make HTML files and a HTML help project
      qthelp     to make HTML files and a qthelp project
      devhelp    to make HTML files and a Devhelp project
      epub       to make an epub
      latex      to make LaTeX files, you can set PAPER=a4 or PAPER=letter
      latexpdf   to make LaTeX files and run them through pdflatex
      text       to make text files
      man        to make manual pages
      texinfo    to make Texinfo files
      info       to make Texinfo files and run them through makeinfo
      gettext    to make PO message catalogs
      changes    to make an overview of all changed/added/deprecated items
      linkcheck  to check all external links for integrity
      doctest    to run all doctests embedded in the documentation (if enabled)

Most common target is ``make html`` which generates HTML documentation into
``_build/html`` directory.

Generating PDF (`latexpdf` target) is done by generating latex and converting it
to PDF. This step required LaTeX. On debian based systems, it can be installed
by running:

.. code-block:: bash

    apt-get install texlive-full

Theme notes
-----------
Apache log4php uses a custom Sphinx theme called (very imaginatively) `log4php`. The theme is
located under ``_theme/log4php``.

One specific hack which this theme provides is making html tabs which are most frequently used
for displaying XML and PHP variants of the same configuration code.

To generate tabs, do the following:

.. code-block:: rst

    .. container:: tabs

        .. rubric:: XML format
        .. code-block:: xml

            <div id="xml_code">...</div>

        .. rubric:: PHP format
        .. code-block:: php

            array("php_code" => array(...))
