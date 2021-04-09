# Changelog

Documentation can be found [here](https://language-data.readthedocs.io/en/latest/). Changelog is divided into the following sections,

- [Language updates](#language-updates)
- [PHP library updates](#php)
- [Node.js library updates](#nodejs)

## Language updates
### 2021-04-01
Language data related changes,
1. [Add Carpathian Romani](https://github.com/wikimedia/language-data/pull/140)
2. [Cleanup the data for the Talysh language](https://github.com/wikimedia/language-data/pull/142)
3. [Add Aruba Papiamento](https://github.com/wikimedia/language-data/pull/143)
4. [Add Rohg to rtlscripts](https://github.com/wikimedia/language-data/pull/144)
5. [Add Nuer language (nus)](https://github.com/wikimedia/language-data/pull/145)
6. [Update the autonym for guc](https://github.com/wikimedia/language-data/pull/147)
7. [Consistently use parentheses in Chinese-script autonyms](https://github.com/wikimedia/language-data/pull/148)
8. [Add nan-hani](https://github.com/wikimedia/language-data/pull/149)
9. [Add Belizean Creole (bzj)](https://github.com/wikimedia/language-data/pull/150)
10. [Add the Basaa language (bas)](https://github.com/wikimedia/language-data/pull/151)
11. [Add the Kom language (bkm)](https://github.com/wikimedia/language-data/pull/152)

### 2021-01-27
Language data related changes,
1. [Update Min Dong Chinese languages](https://github.com/wikimedia/language-data/commit/190423dd29d16fcb44645313b864d794f6a4df36)
2. [Add the Tyap language (kcg)](https://github.com/wikimedia/language-data/commit/69074e24757a59ad9a20be4a28ddbe4285ae06a6)
3. [Change capitalization for koi, olo, and vro ](https://github.com/wikimedia/language-data/commit/17280471ccf691b4bd60970bfdada4d5a035d220)
4. [Add the Nias (nia) language](https://github.com/wikimedia/language-data/commit/762b7c5e0c93e1e7a61f16c6fbeef83d9139c7fb)
5. [Update the autonym of language shi](https://github.com/wikimedia/language-data/commit/4b7cee6c3d4c5ae67f1e4b80f9594fafc92cf8d1)
6. [Add Nanai and Gungbe languages](https://github.com/wikimedia/language-data/commit/c0f628fb15d9910982829904c17597583828cd9c)
7. [Add Rohingya language (rhg)](https://github.com/wikimedia/language-data/commit/f8bc4b8cd49a4964e8a5161a81c730037c8c30ed)

### 2020-10-27
Language data related changes,
1. [Add a redirect from cbk-zam to cbk](https://github.com/wikimedia/language-data/pull/119)

### 2020-07-23
Language data related changes,
1. [Change Madurese autonym to capital](https://github.com/wikimedia/language-data/pull/106)
2. [Add Nuxalk language](https://github.com/wikimedia/language-data/pull/108)
3. [Add Altay languages](https://github.com/wikimedia/language-data/pull/107)
4. [Add Obolo (ann) and Mara (mrh)](https://github.com/wikimedia/language-data/pull/109)
5. [Add Baoule (bci)](https://github.com/wikimedia/language-data/pull/110)

### 2020-05-26
Language data related changes,
1. [Add shy-latn and szy](https://github.com/wikimedia/language-data/pull/76)
2. [Add Chukchi (ckt)](https://github.com/wikimedia/language-data/pull/78)
3. [Fix autonym for Kildin Sami (sjd)](https://github.com/wikimedia/language-data/pull/90)
4. [Change spelling of Innu-aimun autonym](https://github.com/wikimedia/language-data/pull/93)
5. [Add Sylheti ](https://github.com/wikimedia/language-data/pull/94)
6. [Split ary to ary-latn and ary-arab](https://github.com/wikimedia/language-data/pull/92)
7. [Add Middle East and Africa to relevant Arabic varieties](https://github.com/wikimedia/language-data/pull/92)
8. [Add Mongolian in vertical script ](https://github.com/wikimedia/language-data/pull/95)
9. [Add AM as a region for Venetian](https://github.com/wikimedia/language-data/pull/96)
10. [Add Madurese (mad)](https://github.com/wikimedia/language-data/pull/#104)

## PHP
### PHP 1.0.5 (2021-04-01)
- See [language updates 2021-04-01](#2021-04-01).

### PHP 1.0.4 (2021-01-27)
- See [language updates 2021-01-27](#2021-01-27).

### PHP 1.0.3 (2020-10-27)
- See [language updates 2020-10-27](#2020-10-27).
- Update mediawiki-codesniffer to 31.0.0

### PHP 1.0.1 (2020-07-23)
- See [language updates 2020-07-23](#2020-07-23).
- No other changes

### PHP 1.0.0 (2020-05-26)
- See [language updates 2020-05-20](#2020-05-20).
- Add PHP support. [Documentation](https://language-data.readthedocs.io/en/latest/api/languagedata/languageutil.html). [Packagist](https://packagist.org/packages/wikimedia/language-data).

## Node.js

### Node.js 1.0.2 (2021-04-01)
- See [language updates 2021-04-01](#2021-04-01).
- [Fix y18n (required by mocha) related security warning](https://github.com/wikimedia/language-data/pull/153)

### Node.js 1.0.1 (2021-01-27)
- See [language updates 2021-01-27](#2021-01-27).
- [Return an empty array if there are no languages in territory](https://github.com/wikimedia/language-data/commit/4a6136095000a4ea9e9171dad36739a68861b24a)

### Node.js 1.0.0 (2020-10-27)
- See [language updates 2020-10-27](#2020-10-27).
- Bump eslint-config-wikimedia to 0.17.0
- Bump minimum Node.js version to 10.x

### Node.js 0.2.2 (2020-07-28)
- [Fix entrypoint in package.json](https://github.com/wikimedia/language-data/pull/116)

### Node.js 0.2.1 (2020-07-23)
- See [language updates 2020-07-23](#2020-07-23).
- Fix `npm audit` warnings.

### Node.js 0.2.0 (2020-05-26)
- See [language updates 2020-05-20](#2020-05-20).
- Fix `npm audit` warnings.
- Update minimum supported Node.js version to 8.0.0.
