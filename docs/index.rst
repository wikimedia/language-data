.. LanguageData documentation master file, created by
   sphinx-quickstart on Thu Jan 30 13:47:31 2020.
   You can adapt this file completely to your liking, but it should at least
   contain the root `toctree` directive.

CLDR based language data and utilities
========================================

This library contains language related data, and utility libraries written in PHP and Node.js to
interact with that data.

The language related data comprises of the following,

1. The script in which a language is written
2. The script code
3. The language code
4. The regions in which the language is spoken
5. The autonym - language name written in its own script
6. The directionality of the text

This data is populated from the current version of
`CLDR supplemental data <http://unicode.org/repos/cldr/trunk/common/supplemental/supplementalData.xml>`_.

Using the PHP library
----------------------------
|php-build|

.. |php-build| image:: https://github.com/Abijeet/language-data/workflows/PHP%20build/badge.svg
		 :target: https://github.com/Abijeet/language-data/actions?query=workflow%3A%22PHP+build%22

Installation
^^^^^^^^^^^^^
You can add this library to your project by running:

.. code-block:: bash

		composer install wikimedia/language-data

Basic usage
^^^^^^^^^^^^^
The basic usage is like this:

.. code-block:: php

		<?php
		use Wikimedia\LanguageData;

		$languageData = LanguageData::get();
		// Returns English
		$languageData->getAutonym( 'en' );

For a full list of methods see the documentation for the `LanguageData <api/languagedata.html>`_ class.

Using the Node.js library
------------------------------

|npm| |npm-build|

.. |npm| image:: https://img.shields.io/npm/v/@wikimedia/language-data.svg
      :target: https://npmjs.com/package/@wikimedia/language-data


.. |npm-build| image:: https://github.com/Abijeet/language-data/workflows/Node.js%20build/badge.svg
      :target: https://github.com/Abijeet/language-data/actions?query=workflow%3A%22Node.js+build%22

Installation
^^^^^^^^^^^^^
You can add this library to your project by running,

.. code-block:: bash

		npm i @wikimedia/language-data

Basic usage
^^^^^^^^^^^^^
The basic usage is like this:

.. code-block:: js

		const languageData = require('@wikimedia/language-data');

		// Returns English
		languageData.getAutonym( 'en');

The exposed methods are similar to the methods present in the PHP `LanguageData <api/languagedata.html>`_ class.

Contribute
----------

- Issue Tracker: https://github.com/wikimedia/language-data/issues
- Source Code: https://github.com/wikimedia/language-data

Navigation
==========

.. toctree::
    :maxdepth: 1
    :caption: User Documentation

		Adding new languages <user/adding_new_language.rst>

.. toctree::
    :maxdepth: 2
    :caption: PHP API Documentation

  	LanguageData class <api/languagedata.rst>


Indices and tables
==================

* :ref:`genindex`
* :ref:`search`
