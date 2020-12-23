var languageData = require( '../data/language-data.json' );

/**
 * Utility functions for querying language data.
 */

/**
 * Check whether the languageCode is known to the language database.
 * For practical purposes it may be same as checking if given language code is valid,
 * but not guaranteed that all valid language codes are in our database.
 *
 * @param {string} languageCode language code
 * @return {boolean}
 */
function isKnown( languageCode ) {
	return !!languageData.languages[ languageCode ];
}

/**
 * Is this language a redirect to another language?
 *
 * @param {string} language  Language code
 * @return {string} Target language code if it's a redirect or false if it's not
 */
function isRedirect( language ) {
	return ( isKnown( language ) && languageData.languages[ language ].length === 1 ) ?
		languageData.languages[ language ][ 0 ] : false;
}

/**
 * Get all the languages
 *
 * @return {Object}
 */
function getLanguages() {
	return languageData.languages;
}

/**
 * Returns the script of the language.
 *
 * @param {string} language Language code
 * @return {string}
 */
function getScript( language ) {
	var target = isRedirect( language );
	if ( target ) {
		return getScript( target );
	}
	if ( !isKnown( language ) ) {
		// Undetermined
		return 'Zyyy';
	}
	return languageData.languages[ language ][ 0 ];
}

/**
 * Returns the regions in which a language is spoken.
 *
 * @param {string} language Language code
 * @return {string[]} 'UNKNOWN'
 */
function getRegions( language ) {
	var target = isRedirect( language );
	if ( target ) {
		return getRegions( target );
	}
	return ( isKnown( language ) && languageData.languages[ language ][ 1 ] ) || 'UNKNOWN';
}

/**
 * Returns the autonym of the language.
 *
 * @param {string} language Language code
 * @return {string}
 */
function getAutonym( language ) {
	var target = isRedirect( language );
	if ( target ) {
		return getAutonym( target );
	}
	return ( isKnown( language ) && languageData.languages[ language ][ 2 ] ) || language;
}

/**
 * Returns all language codes and corresponding autonyms
 *
 * @return {Array}
 */
function getAutonyms() {
	var language,
		autonymsByCode = {};
	for ( language in languageData.languages ) {
		if ( isRedirect( language ) ) {
			continue;
		}
		autonymsByCode[ language ] = getAutonym( language );
	}
	return autonymsByCode;
}

/**
 * Returns all languages written in the given scripts.
 *
 * @param {string[]} scripts
 * @return {string[]} languages codes
 */
function getLanguagesInScripts( scripts ) {
	var language, i,
		languagesInScripts = [];
	for ( language in languageData.languages ) {
		if ( isRedirect( language ) ) {
			continue;
		}
		for ( i = 0; i < scripts.length; i++ ) {
			if ( scripts[ i ] === getScript( language ) ) {
				languagesInScripts.push( language );
				break;
			}
		}
	}
	return languagesInScripts;
}

/**
 * Returns all languages written in script.
 *
 * @param {string} script
 * @return {string[]} array of strings (languages codes)
 */
function getLanguagesInScript( script ) {
	return getLanguagesInScripts( [ script ] );
}

/**
 * Returns the script group of a script or 'Other' if it doesn't
 * belong to any group.
 *
 * @param {string} script Script code
 * @return {string} script group name
 */
function getGroupOfScript( script ) {
	var scriptGroup;
	for ( scriptGroup in languageData.scriptgroups ) {
		if ( languageData.scriptgroups[ scriptGroup ].includes( script ) ) {
			return scriptGroup;
		}
	}
	return 'Other';
}

/**
 * Returns the script group of a language.
 *
 * @param {string} language Language code
 * @return {string} script group name
 */
function getScriptGroupOfLanguage( language ) {
	return getGroupOfScript( getScript( language ) );
}

/**
 * Get the given list of languages grouped by script.
 *
 * @param {string[]} languages Array of language codes to group
 * @return {string[]} Array of language codes
 */
function getLanguagesByScriptGroup( languages ) {
	var languagesByScriptGroup = {},
		language, languageIndex, resolvedRedirect, langScriptGroup;

	for ( languageIndex = 0; languageIndex < languages.length; languageIndex++ ) {
		language = languages[ languageIndex ];
		resolvedRedirect = isRedirect( language ) || language;
		langScriptGroup = getScriptGroupOfLanguage( resolvedRedirect );
		if ( !languagesByScriptGroup[ langScriptGroup ] ) {
			languagesByScriptGroup[ langScriptGroup ] = [];
		}
		languagesByScriptGroup[ langScriptGroup ].push( language );
	}
	return languagesByScriptGroup;
}

/**
 * Returns an associative array of languages in several regions,
 * grouped by script group.
 *
 * @param {string[]} regions array of region codes
 * @return {Object}
 */
function getLanguagesByScriptGroupInRegions( regions ) {
	var language, i, scriptGroup,
		languagesByScriptGroupInRegions = {};
	for ( language in languageData.languages ) {
		if ( isRedirect( language ) ) {
			continue;
		}
		for ( i = 0; i < regions.length; i++ ) {
			if ( getRegions( language ).includes( regions[ i ] ) ) {
				scriptGroup = getScriptGroupOfLanguage( language );
				if ( languagesByScriptGroupInRegions[ scriptGroup ] === undefined ) {
					languagesByScriptGroupInRegions[ scriptGroup ] = [];
				}
				languagesByScriptGroupInRegions[ scriptGroup ].push( language );
				break;
			}
		}
	}
	return languagesByScriptGroupInRegions;
}

/**
 * Returns an associative array of languages in a region,
 * grouped by script group.
 *
 * @param {string} region Region code
 * @return {Object}
 */
function getLanguagesByScriptGroupInRegion( region ) {
	return getLanguagesByScriptGroupInRegions( [ region ] );
}

/**
 * Return the list of languages sorted by script groups.
 *
 * @param {string[]} languages Array of language codes to sort
 * @return {string[]} Array of language codes
 */
function sortByScriptGroup( languages ) {
	var groupedLanguages, scriptGroups, i,
		allLanguages = [];

	groupedLanguages = getLanguagesByScriptGroup( languages );
	scriptGroups = Object.keys( groupedLanguages ).sort();

	for ( i = 0; i < scriptGroups.length; i++ ) {
		allLanguages = allLanguages.concat( groupedLanguages[ scriptGroups[ i ] ] );
	}

	return allLanguages;
}

/**
 * A callback for sorting languages by autonym.
 * Can be used as an argument to a sort function.
 *
 * @param {string} a Language code
 * @param {string} b Language code
 * @return {number}
 */
function sortByAutonym( a, b ) {
	var autonymA = getAutonym( a ) || a,
		autonymB = getAutonym( b ) || b;
	return ( autonymA.toLowerCase() < autonymB.toLowerCase() ) ? -1 : 1;
}

/**
 * Check if a language is right-to-left.
 *
 * @param {string} language Language code
 * @return {boolean}
 */
function isRtl( language ) {
	return languageData.rtlscripts.includes( getScript( language ) );
}

/**
 * Return the direction of the language
 *
 * @param {string} language Language code
 * @return {string}
 */
function getDir( language ) {
	return isRtl( language ) ? 'rtl' : 'ltr';
}

/**
 * Returns the languages spoken in a territory.
 *
 * @param {string} territory Territory code
 * @return {string[]} list of language codes
 */
function getLanguagesInTerritory( territory ) {
	return languageData.territories[ territory ] || [];
}

/**
 * Adds a language in run time and sets its options as provided.
 * If the target option is provided, the language is defined as a redirect.
 * Other possible options are script, regions and autonym.
 *
 * @param {string} code New language code.
 * @param {Object} options Language properties.
 */
function addLanguage( code, options ) {
	if ( options.target ) {
		languageData.languages[ code ] = [ options.target ];
	} else {
		languageData.languages[ code ] = [ options.script, options.regions, options.autonym ];
	}
}

module.exports = {
	addLanguage,
	getAutonym,
	getAutonyms,
	getDir,
	getGroupOfScript,
	getLanguages,
	getLanguagesByScriptGroup,
	getLanguagesByScriptGroupInRegion,
	getLanguagesByScriptGroupInRegions,
	getLanguagesInScript,
	getLanguagesInScripts,
	getLanguagesInTerritory,
	getRegions,
	getScript,
	getScriptGroupOfLanguage,
	isKnown,
	isRedirect,
	isRtl,
	sortByScriptGroup,
	sortByAutonym
};
