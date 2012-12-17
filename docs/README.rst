============================
Apache log4php documentation
============================

This directory contains the documentation for Apache log4php.

The documentation is in `reStructuredText <http://docutils.sourceforge.net/rst.html>`_ format and 
can be viewed using any text editor. Conversion to other formats, such as html, epub and pdf is 
achieved by using the `Sphinx documentation system <http://sphinx-doc.org/>`_, and requires 
`Python <http://python.org/>`_.

To render the documentation to HTML:

* install Sphinx (using ``sudo pip install Sphinx`` or some other method)
* using a virtual environment is recommended
* while in the ``docs`` directory, run ``make html``

The documentation will be rendered to ``_build/html/``.

Theme notes
===========

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

