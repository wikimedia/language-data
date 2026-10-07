import re
import pytest
import languagedata

UNKNOWN = 'xyz'


# ---------------------------------------------------------------------------
# Helpers (mirrors JS orphanScripts / badRedirects / doubleRedirects helpers)
# ---------------------------------------------------------------------------

def orphan_scripts():
    return [
        languagedata.getScript(lang)
        for lang in languagedata.getLanguages()
        if languagedata.getGroupOfScript(languagedata.getScript(lang)) == 'Other'
    ]


def bad_redirects():
    return [
        lang
        for lang in languagedata.getLanguages()
        if languagedata.isRedirect(lang) and languagedata.isRedirect(lang) not in languagedata.getLanguages()
    ]


def double_redirects():
    return [
        lang
        for lang in languagedata.getLanguages()
        if languagedata.isRedirect(lang) and languagedata.isRedirect(languagedata.isRedirect(lang))
    ]


def invalid_codes():
    invalid = re.compile(r'[^0-9a-z-]')
    return [code for code in languagedata.getLanguages() if invalid.search(code)]


def double_autonyms():
    seen = []
    duplicates = []
    for lang in languagedata.getLanguages():
        if languagedata.isRedirect(lang):
            continue
        autonym = languagedata.getAutonym(lang)
        if autonym.lower() in seen:
            duplicates.append(lang)
        seen.append(autonym.lower())
    return duplicates


def languages_without_autonym():
    return [
        lang
        for lang in languagedata.getLanguages()
        if not isinstance(languagedata.getAutonym(lang), str)
    ]


# ---------------------------------------------------------------------------
# Tests
# ---------------------------------------------------------------------------

class TestLanguageTags:
    def test_known_language(self):
        assert languagedata.isKnown('ar')

    def test_unknown_language(self):
        assert not languagedata.isKnown('unknownLanguageCode!')

    def test_invalid_codes(self):
        assert invalid_codes() == []


class TestAutonyms:
    def test_add_language(self):
        languagedata.addLanguage('qqq', {
            'script': 'Latn',
            'regions': ['SP'],
            'autonym': 'Language documentation'
        })
        assert languagedata.getAutonym('qqq') == 'Language documentation'

    def test_get_autonyms_zulu(self):
        assert languagedata.getAutonyms()['zu'] == 'isiZulu'

    def test_redirect_not_in_autonyms(self):
        assert 'pa-guru' not in languagedata.getAutonyms()

    def test_punjabi_autonym_via_getAutonyms(self):
        assert languagedata.getAutonyms()['pa'] == 'ਪੰਜਾਬੀ'

    def test_no_double_autonyms(self):
        assert double_autonyms() == []

    def test_all_languages_have_autonym(self):
        assert languages_without_autonym() == []

    def test_autonym_direct(self):
        assert languagedata.getAutonym('pa') == 'ਪੰਜਾਬੀ'

    def test_autonym_via_redirect(self):
        assert languagedata.getAutonym('pa-guru') == 'ਪੰਜਾਬੀ'

    def test_sort_by_autonym(self):
        # autonyms: gn: avañe'ẽ, de: Deutsch, hu: magyar, fi: suomi
        assert languagedata.sortByAutonym(['de', 'fi', 'gn', 'hu']) == ['gn', 'de', 'hu', 'fi']

    def test_chinese_autonyms_no_western_parentheses(self):
        langs = languagedata.getLanguagesInScripts(['Hans', 'Hant', 'Hani'])
        with_parens = [l for l in langs if re.search(r'[()]', languagedata.getAutonym(l))]
        assert with_parens == []


class TestRegionsAndGroups:
    def test_no_orphan_scripts(self):
        assert orphan_scripts() == []

    def test_regions_laz(self):
        assert languagedata.getRegions('lzz') == ['EU', 'ME']

    def test_regions_unknown(self):
        assert languagedata.getRegions('no-such-language') == 'UNKNOWN'

    def test_territory_russia_sakha(self):
        assert 'sah' in languagedata.getLanguagesInTerritory('RU')

    def test_territory_invalid(self):
        assert languagedata.getLanguagesInTerritory('no-such-country') == []

    def test_sort_by_script_group(self):
        languages_am = ['atj', 'chr', 'chy', 'cr', 'en', 'es', 'fr', 'gn', 'haw',
                        'ike-cans', 'ik', 'kl', 'nl', 'pt', 'qu', 'srn', 'yi']
        result = languagedata.sortByScriptGroup(languagedata.sortByAutonym(languages_am))
        assert result == ['atj', 'gn', 'en', 'es', 'fr', 'haw', 'ik', 'kl', 'nl',
                          'pt', 'qu', 'srn', 'chy', 'yi', 'ike-cans', 'cr', 'chr']

    def test_languages_by_script_group(self):
        result = languagedata.getLanguagesByScriptGroup(['en', 'sr-el', 'tt-cyrl'])
        assert 'tt-cyrl' in result['Cyrillic']
        assert 'en' in result['Latin']
        assert 'sr-el' in result['Latin']

    def test_languages_by_script_group_in_regions(self):
        result = languagedata.getLanguagesByScriptGroupInRegions(['AS', 'PA'])
        assert 'tpi' in result['Latin']
        assert 'ug-arab' in result['Arabic']
        assert 'zh-sg' in result['CJK']
        assert 'az-arab' not in result.get('Arabic', [])


class TestScripts:
    def test_no_orphan_scripts(self):
        assert orphan_scripts() == []

    def test_languages_in_script_guru(self):
        assert languagedata.getLanguagesInScript('Guru') == ['pa']

    def test_languages_in_scripts_geor_armn(self):
        assert languagedata.getLanguagesInScripts(['Geor', 'Armn']) == ['hy', 'hyw', 'ka', 'xmf']

    def test_languages_in_script_knda(self):
        assert languagedata.getLanguagesInScript('Knda') == ['kn', 'tcy']

    def test_group_of_script_beng(self):
        assert languagedata.getGroupOfScript('Beng') == 'SouthAsian'

    def test_script_group_of_language_iu(self):
        assert languagedata.getScriptGroupOfLanguage('iu') == 'NativeAmerican'


class TestRedirects:
    def test_redirect_sr_ec(self):
        assert languagedata.isRedirect('sr-ec') == 'sr-cyrl'

    def test_no_bad_redirects(self):
        assert bad_redirects() == []

    def test_no_double_redirects(self):
        assert double_redirects() == []

    def test_unknown_language_script(self):
        assert languagedata.getScript('no-such-language') == 'Zyyy'

    def test_yi_script(self):
        assert languagedata.getScript('ii') == 'Yiii'


class TestDirectionality:
    def test_telugu_not_rtl(self):
        assert languagedata.isRtl('te') is False

    def test_divehi_rtl(self):
        assert languagedata.isRtl('dv') is True

    def test_mazandarani_dir(self):
        assert languagedata.getDir('mzn') == 'rtl'

    def test_ukrainian_dir(self):
        assert languagedata.getDir('uk') == 'ltr'
