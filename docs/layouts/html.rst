================
LoggerLayoutHTML
================

LoggerLayoutHTML formats the log as an HTML document.

Parameters
----------

+----------------+---------+----------+----------+-------------------------------------------------+
| Parameter      | Type    | Required | Default  | Description                                     |
+================+=========+==========+==========+=================================================+
| locationInfo   | boolean | No       | true     | If set to true, adds the file name and line     |
|                |         |          |          | number at which the log statement originated.   |
+----------------+---------+----------+----------+-------------------------------------------------+
| title          | string  | No       | Log3php  | Sets the title of the generated HTML document.  |
|                |         |          | Log      |                                                 |
|                |         |          | Messages |                                                 |
+----------------+---------+----------+----------+-------------------------------------------------+

Examples
--------

.. container:: tabs

    .. rubric:: XML format
    .. code-block:: xml

        <configuration xmlns="http://logging.apache.org/log4php/">
            <appender name="default" class="LoggerAppenderEcho">
                <layout class="LoggerLayoutHtml">
                    <param name="locationInfo" value="true" />
                </layout>
            </appender>
            <root>
                <appender_ref ref="default" />
            </root>
        </configuration>

    .. rubric:: PHP format
    .. code-block:: php

        array(
            'appenders' => array(
                'default' => array(
                    'class' => 'LoggerAppenderEcho',
                    'layout' => array(
                        'class' => 'LoggerLayoutHtml',
                    )
                )
            ),
            'rootLogger' => array(
                'appenders' => array('default')
            ),
        )

Running the following code:

.. code-block:: php

    Logger::configure("layout_xml.xml");
    $log = Logger::getRootLogger();
    $log->debug("Hello World!");
    $log->info("Hello World!");

Produces the output as a HTML table:

+------+--------+-------+----------+--------------------------------+--------------+
| Time | Thread | Level | Category | File\:Line                     | Message      |
+======+========+=======+==========+================================+==============+
| 0    | 5868   | DEBUG | root     | /tmp/log4php/layout_html.php:3 | Hello world! |
+------+--------+-------+----------+--------------------------------+--------------+
| 2    | 5868   | INFO  | root     | /tmp/log4php/layout_html.php:4 | Hello world! |
+------+--------+-------+----------+--------------------------------+--------------+

Source of the output:

.. code-block:: html

    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
    <html>
    <head>
        <title>Log4php Log Messages</title>
        <style type="text/css">
        <!--
        body, table {font-family: arial,sans-serif; font-size: x-small;}
        th {background: #336699; color: #FFFFFF; text-align: left;}
        -->
        </style>
    </head>
    <body bgcolor="#FFFFFF" topmargin="6" leftmargin="6">
    <hr size="1" noshade>
    Log session start time 09/22/11 13:19:23<br>
    <br>
    <table cellspacing="0" cellpadding="4" border="1" bordercolor="#224466" width="100%">
        <tr>
            <th>Time</th>
            <th>Thread</th>
            <th>Level</th>
            <th>Category</th>
            <th>File:Line</th>
            <th>Message</th>
        </tr>
        <tr>
            <td>0</td>
            <td title="5868 thread">5868</td>
            <td title="Level"><font color="#339933">DEBUG</font></td>
            <td title="root category">root</td>
            <td>D:\Projects\apache\log4php-config-adapters\src\examples\php\layout_html.php:23</td>
            <td title="Message">Hello World!</td>
        </tr>
        <tr>
            <td>2</td>
            <td title="5868 thread">5868</td>
            <td title="Level">INFO</td>
            <td title="root category">root</td>
            <td>D:\Projects\apache\log4php-config-adapters\src\examples\php\layout_html.php:24</td>
            <td title="Message">Hello World!</td>
        </tr>
    </table>
    <br>
    </body>
    </html>
