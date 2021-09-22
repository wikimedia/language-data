Adding new languages
=========================

New languages must be added to the ``data/langdb.yaml`` file.

The file format is: `ISO 639 <https://en.wikipedia.org/wiki/ISO_639>`_ code:
``[writing system code, [regions list], autonym]``

The writing system is indicated using `ISO 15924 <https://en.wikipedia.org/wiki/ISO_15924>`_
codes. Make sure that the code appears in the ``scriptgroups`` section towards the end of
the file, and add it, if it doesn't.

The list of region codes appears at the end of ``data/langdb.yaml``.

The autonym is the name of the language in the language itself. In some cases, for example for
extinct languages such as Jewish Babylonian Aramaic (tmr), the name can be something that is
useful for modern users, but in most cases it should be the natural name in the language itself.
Please do your best to verify that it's spelled correctly in reliable sources.

Example: ``myv: [Cyrl, [EU], эрзянь]``

This is the `Erzya language <https://en.wikipedia.org/wiki/Erzya_language>`_. Its writing system
is Cyrillic (ISO 15924: Cyrl). It's spoken in Europe (EU). Its autonym is "эрзянь".

Some languages are listed as redirects. In this case, the only value in the square brackets is
the target language code.

Example: ``fil: [tl]``

This is the Filipino language (fil), which is a redirect to Tagalog (tl).

After adding a language to ``data/langdb.yaml``, run: ``php src/util/ulsdata2json.php`` in the
base directory to generate the ``language-data.json`` file. Don't edit ``language-data.json`` manually.

Before running ``php src/util/ulsdata2json.php`` for the first time on your machine,
you also need to run ``composer install`` in the base directory to install
some dependencies. You also need to install PHP curl support on your machine
to allow downloading the CLDR language data file.
