# CLDR based language data and utilities

[![npm][npm]][npm-url]

The language data with following details are populated from the current version of [CLDR supplemental data](http://unicode.org/repos/cldr/trunk/common/supplemental/supplementalData.xml)

1. The script in which a language is written.
2. The script code
3. The language code
4. The regions in which the language is spoken
5. The autonym - language name written in its own script
6. The directionality of the text

## Adding languages

New languages must be added to the data/langdb.yaml file.

The file format is:

ISO 639 code: [writing system code, [regions list], autonym]

The writing system is indicated using ISO 15924 codes. Make sure that the code appears in the scriptgroups section towards the end of the file, and add it if it doesn't.

The list of region codes appears at the end of data/langdb.yaml.

The autonym is the name of the language in the language itself. In some cases, for example for extinct languages such as Jewish Babylonian Aramaic (tmr), the name can be something that is useful for modern users, but in most cases it should be the natural name in the language itself. Please do your best to verify that it's spelled correctly in reliable sources.

After adding a language to data/langdb.yaml, run `php ulsdata2json.php` in the data/ directory to generate the language-data.json file. Don't edit language-data.json manually.

Example:
`myv: [Cyrl, [EU], эрзянь]`

This is the [Erzya language](https://en.wikipedia.org/wiki/Erzya_language). Its writing system is Cyrillic (ISO 15924: Cyrl). It's spoken in Europe (EU). Its autonym is "эрзянь".

Some languages are listed as redirects. In this case, the only value in the square brackets is the target language code. For example:
`fil: [tl]`

This is the Filipino language, which is a redirect to Tagalog (tl).

[npm]: https://img.shields.io/npm/v/@wikimedia/language-data.svg
[npm-url]: https://npmjs.com/package/@wikimedia/language-data