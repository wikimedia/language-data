from typing import Union, List
from importlib.resources import files
import functools
import json

with files('languagedata').joinpath('language-data.json').open('r') as f:
    languageData = json.load(f)


def isKnown(languageCode):
    return languageCode in languageData['languages']


def isRedirect(language):
    if isKnown(language) and len(languageData['languages'][language]) == 1:
        return languageData['languages'][language][0]
    else:
        return False


def getLanguages():
    return languageData['languages']


def getScript(language):
    target = isRedirect(language)
    if target:
        return getScript(target)
    elif not isKnown(language):
        return 'Zyyy'
    else:
        return languageData['languages'][language][0]


def getRegions(language: str) -> Union[str, List[str]]:
    target = isRedirect(language)
    if target:
        return getRegions(target)
    elif isKnown(language) and languageData['languages'][language][1]:
        return languageData['languages'][language][1]
    else:
        return 'UNKNOWN'


def getAutonym(language: str) -> str:
    target = isRedirect(language)
    if target:
        return getAutonym(target)
    return languageData['languages'][language][2] if (isKnown(language) and len(languageData['languages'][language]) > 2) else language


def getAutonyms():
    autonymsByCode = {}
    for language in languageData['languages']:
        if isRedirect(language):
            continue
        autonymsByCode[language] = getAutonym(language)
    return autonymsByCode


def getLanguagesInScripts(scripts):
    languagesInScripts = []
    for language in languageData['languages']:
        if isRedirect(language):
            continue
        for script in scripts:
            if script == getScript(language):
                languagesInScripts.append(language)
                break
    return languagesInScripts


def getLanguagesInScript(script):
    return getLanguagesInScripts([script])


def getGroupOfScript(script):
    for scriptGroup in languageData['scriptgroups']:
        if script in languageData['scriptgroups'][scriptGroup]:
            return scriptGroup
    return 'Other'


def getScriptGroupOfLanguage(language):
    return getGroupOfScript(getScript(language))


def getLanguagesByScriptGroup(languages):
    languagesByScriptGroup = {}
    for language in languages:
        resolvedRedirect = isRedirect(language) or language
        langScriptGroup = getScriptGroupOfLanguage(resolvedRedirect)
        if langScriptGroup not in languagesByScriptGroup:
            languagesByScriptGroup[langScriptGroup] = []
        languagesByScriptGroup[langScriptGroup].append(language)
    return languagesByScriptGroup


def getLanguagesByScriptGroupInRegions(regions):
    languagesByScriptGroupInRegions = {}
    for language in languageData['languages']:
        if isRedirect(language):
            continue
        for region in regions:
            if region in getRegions(language):
                scriptGroup = getScriptGroupOfLanguage(language)
                if scriptGroup not in languagesByScriptGroupInRegions:
                    languagesByScriptGroupInRegions[scriptGroup] = []
                languagesByScriptGroupInRegions[scriptGroup].append(language)
                break
    return languagesByScriptGroupInRegions


def getLanguagesByScriptGroupInRegion(region):
    return getLanguagesByScriptGroupInRegions([region])


def sortByScriptGroup(languages):
    groupedLanguages = getLanguagesByScriptGroup(languages)
    scriptGroups = sorted(groupedLanguages.keys())
    allLanguages = []
    for scriptGroup in scriptGroups:
        allLanguages.extend(groupedLanguages[scriptGroup])
    return allLanguages


def _cmpByAutonym(a, b):
    autonymA = getAutonym(a) or a
    autonymB = getAutonym(b) or b
    return -1 if autonymA.lower() < autonymB.lower() else 1


def sortByAutonym(languages):
    return sorted(languages, key=functools.cmp_to_key(_cmpByAutonym))


def isRtl(language):
    return getScript(language) in languageData['rtlscripts']


def getDir(language):
    return 'rtl' if isRtl(language) else 'ltr'


def getLanguagesInTerritory(territory):
    return languageData.get('territories', {}).get(territory, [])


def addLanguage(code, options):
    if options.get('target'):
        languageData['languages'][code] = [options['target']]
    else:
        languageData['languages'][code] = [options.get('script'), options.get('regions'), options.get('autonym')]
