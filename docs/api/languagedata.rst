LanguageData
============

A singleton utility class to query the language data.

:Qualified name: ``Wikimedia\LanguageData``

.. php:class:: LanguageData

  .. php:method:: addLanguage (string $languageCode, array $options)

    Adds a language in run time and sets its options as provided. If the target option is provided, the language is defined as a redirect. Other possible options are script (string), regions (array) and autonym (string).

    :param string $languageCode:
      New language code.
    :param array $options:
      Language properties.

  .. php:method:: getAutonym (string $languageCode)

    Returns the autonym of the language

    :param string $languageCode:
      Language code
    :returns: string|bool Autonym of the language or false if the language is unknown

  .. php:method:: getAutonyms () -> array

    Returns all language codes and corresponding autonyms

    :returns: array -- The key is the language code, and the values are corresponding autonym

  .. php:method:: getDir (string $languageCode)

    Return the direction of the language

    :param string $languageCode:
      Language code
    :returns: string|bool Returns 'rtl' or 'ltr'. If the language code is unknown, returns false.

  .. php:method:: getGroupOfScript (string $script) -> string

    Returns the script group of a script or "Other" if it doesn't belong to any group

    :param string $script:
      Name of the script
    :returns: string -- Script group name or "Other" if the script doesn't belong to any group

  .. php:method:: getLanguages ()

    Get all the languages. The properties in the returned object are ISO 639 language codes The value of each property is an array that has, [writing system code, [regions list], autonym]

    :returns: object

  .. php:method:: getLanguagesByScriptGroup (array $languageCodes) -> array

    Return the list of languages passed, grouped by their script group

    :param array $languageCodes:
      List of language codes to group
    :returns: array -- List of language codes grouped by script group

  .. php:method:: getLanguagesByScriptGroupInRegion (string $region) -> LanguageData::getLanguagesByScriptGroupInRegions

    Returns an associative array of languages in a region, grouped by their script

    :param string $region:
      Region code
    :returns: :class:`LanguageData::getLanguagesByScriptGroupInRegions` -- 

  .. php:method:: getLanguagesByScriptGroupInRegions (array $regions) -> array

    Returns an associative array of languages in several regions, grouped by script group

    :param array $regions:
      List of strings representing region codes
    :returns: array -- Returns an associative array. They key is the script group name, and the value is a list of language codes in that region.

  .. php:method:: getLanguagesInScript (string $script) -> array

    Returns all languages written in the given script

    :param string $script:
      Name of the script
    :returns: array -- 

  .. php:method:: getLanguagesInScripts (array $scripts) -> array

    Returns all languages written in the given scripts

    :param array $scripts:
      List of strings, each being the name of a script
    :returns: array -- 

  .. php:method:: getLanguagesInTerritory (string $territory)

    Returns the languages spoken in a territory

    :param string $territory:
      Territory code
    :returns: array|bool List of language codes in the territory, or else false if invalid territory is passed

  .. php:method:: getRegions (string $languageCode)

    Returns the regions in which a language is spoken

    :param string $languageCode:
      Language code
    :returns: array|bool List of regions or false if language is unknown

  .. php:method:: getScript (string $languageCode)

    Returns the script of the language

    :param string $languageCode:
      Language code
    :returns: string|bool Language script or false if the language is unknown

  .. php:method:: getScriptGroupOfLanguage (string $languageCode) -> string

    Returns the script group of a language. Language belongs to a script, and the script belongs to a script group

    :param string $languageCode:
      Language code
    :returns: string -- script group name

  .. php:method:: isKnown (string $languageCode) -> bool

    Checks if a language code is valid

    :param string $languageCode:
      Language code
    :returns: bool -- 

  .. php:method:: isRedirect (string $languageCode)

    Checks if the language is a redirect and returns the target language code

    :param string $languageCode:
      Language code
    :returns: string|bool Target language code if it's a redirect or false if it's not

  .. php:method:: isRtl (string $languageCode) -> bool

    Check if a language is right-to-left

    :param string $languageCode:
      Language code
    :returns: bool -- true if it is an RTL language, else false. Returns false if an unknown language code is passed.

  .. php:method:: sortByAutonym (array $languageCodes) -> array

    Sort languages by their autonym

    :param array $languageCodes:
      List of language codes to sort
    :returns: array -- List of sorted language codes returned by their autonym

  .. php:method:: sortByScriptGroup (array $languageCodes) -> array

    Return the list of languages sorted by their script groups

    :param array $languageCodes:
      List of language codes to sort
    :returns: array -- Sorted list of strings containing language codes

  .. php:staticmethod:: get () -> LanguageData

    Returns an instance of the class that can be used to then call the other methods in the class.

    :returns: :class:`LanguageData` -- 

