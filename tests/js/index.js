var languageData = require( __dirname + '/../../src/index' ),
	assert = require( 'assert' );

describe( 'languagedata', function () {
	var orphanScripts, badRedirects, invalidCodes,
		doubleRedirects, doubleAutonyms, languagesWithoutAutonym;
	/*
	 * Runs over all script codes mentioned in langdb and checks whether
	 * they belong to the 'Other' group.
	 */
	orphanScripts = function () {
		var language, script,
			result = [];
		for ( language in languageData.getLanguages() ) {
			script = languageData.getScript( language );
			if ( languageData.getGroupOfScript( script ) === 'Other' ) {
				result.push( script );
			}
		}
		return result;
	};
	/*
	 * Runs over all languages and checks that all redirects have a valid target.
	 */
	badRedirects = function () {
		var language, target,
			result = [];
		for ( language in languageData.getLanguages() ) {
			target = languageData.isRedirect( language );
			if ( target && !languageData.getLanguages()[ target ] ) {
				result.push( language );
			}
		}
		return result;
	};
	/*
	 * Runs over all languages and checks that all redirects have a valid target.
	 */
	invalidCodes = function () {
		var languageCode,
			invalidCharsRe = /[^0-9a-z-]/,
			result = [];

		for ( languageCode in languageData.getLanguages() ) {
			if ( languageCode.match( invalidCharsRe ) ) {
				result.push( languageCode );
			}
		}

		return result;
	};
	/*
	 * Runs over all languages and checks that all redirects point to a language.
	 * There's no reason to have double redirects.
	 */
	doubleRedirects = function () {
		var language, target,
			result = [];
		for ( language in languageData.getLanguages() ) {
			target = languageData.isRedirect( language );
			if ( target && languageData.isRedirect( target ) ) {
				result.push( language );
			}
		}
		return result;
	};
	/*
	 * Runs over all languages and checks that all autonyms are unique.
	 */
	doubleAutonyms = function () {
		var language, autonym,
			autonyms = [],
			duplicateAutonyms = [];

		for ( language in languageData.getLanguages() ) {
			if ( languageData.isRedirect( language ) ) {
				continue;
			}

			autonym = languageData.getAutonym( language );

			if ( autonyms.indexOf( autonym ) > -1 ) {
				duplicateAutonyms.push( language );
			}

			autonyms.push( autonym );
		}

		return duplicateAutonyms;
	};
	/*
	 * Runs over all script codes mentioned in langdb and checks whether
	 * they have something that looks like an autonym.
	 */
	languagesWithoutAutonym = function () {
		var language,
			result = [];
		for ( language in languageData.getLanguages() ) {
			if ( typeof languageData.getAutonym( language ) !== 'string' ) {
				result.push( language );
			}
		}
		return result;
	};

	it( 'language tags', function () {
		assert.ok( languageData.isKnown( 'ar' ), 'Language is unknown' );
		assert.ok( !languageData.isKnown( 'unknownLanguageCode!' ), 'Language is known' );
		assert.deepEqual( invalidCodes(), [], 'All language codes have no invalid characters.' );
	} );

	it( 'autonyms', function () {
		var autonyms, chineseScriptLanguages, i,
			languagesWithParentheses = [];
		// Add a language in run time.
		// This is done early to make sure that it doesn't break other functions.
		languageData.addLanguage( 'qqq', {
			script: 'Latn',
			regions: [ 'SP' ],
			autonym: 'Language documentation'
		} );
		assert.ok( languageData.getAutonym( 'qqq' ), 'Language documentation', 'Language qqq was added with the correct autonym' );
		autonyms = languageData.getAutonyms();
		assert.strictEqual( autonyms[ 'zu' ], 'isiZulu', 'Correct autonym is returned for Zulu using getAutonyms().' );
		assert.strictEqual( autonyms[ 'pa' ], undefined, 'Language "pa" is not listed in autonyms, because it is a redirect' );
		assert.strictEqual( autonyms[ 'pa-guru' ], 'ਪੰਜਾਬੀ', 'Language "pa-guru" has the correct autonym' );
		assert.deepEqual( languagesWithoutAutonym(), [], 'All languages have autonyms.' );
		assert.strictEqual( languageData.getAutonym( 'pa' ), 'ਪੰਜਾਬੀ', 'Correct autonym of the Punjabi language was selected using code pa.' );
		assert.strictEqual( languageData.getAutonym( 'pa-guru' ), 'ਪੰਜਾਬੀ', 'Correct autonym of the Punjabi language was selected using code pa-guru.' );
		// autonyms: gn: avañe'ẽ, de: deutsch, hu: magyar, fi: suomi
		assert.deepEqual( [ 'de', 'fi', 'gn', 'hu' ].sort( languageData.sortByAutonym ), [
			'gn', 'de', 'hu', 'fi'
		], 'Languages are correctly sorted by autonym' );

		chineseScriptLanguages = languageData.getLanguagesInScripts( [ 'Hans', 'Hant', 'Hani' ] );
		for ( i = 0; i < chineseScriptLanguages.length; ++i ) {
			if ( languageData.getAutonym( chineseScriptLanguages[i] ).match( /[()]/ ) ) {
				languagesWithParentheses.push( chineseScriptLanguages[i] );
			}
		}
		assert.deepEqual( languagesWithParentheses, [], 'Chinese script languages\' autonyms don\'t have Western parentheses' );
	} );
	it( 'regions and groups', function () {
		var languagesAM;
		// This test assumes that we don't want any scripts to be in the 'Other'
		// group. Actually, this may become wrong some day.
		assert.deepEqual( orphanScripts(), [], 'All scripts belong to script groups.' );

		assert.deepEqual( languageData.getRegions( 'lzz' ), [
			'EU', 'ME'
		], 'Correct regions of the Laz language were selected' );
		assert.strictEqual( languageData.getRegions( 'no-such-language' ), 'UNKNOWN', 'The region of an invalid language is "UNKNOWN"' );
		assert.ok( languageData.getLanguagesInTerritory( 'RU' ).includes( 'sah' ), 'Sakha language is spoken in Russia' );
		assert.deepEqual(
			languageData.getLanguagesInTerritory( 'no-such-country' ),
			[],
			'An invalid country has no languages and returns an empty array'
		);

		languagesAM = [ 'atj', 'chr', 'chy', 'cr', 'en', 'es', 'fr', 'gn', 'haw', 'ike-cans', 'ik', 'kl', 'nl', 'pt', 'qu', 'srn', 'yi' ];
		assert.deepEqual(
			languageData.sortByScriptGroup( languagesAM.sort( languageData.sortByAutonym ) ),
			[ 'atj', 'gn', 'en', 'es', 'fr', 'haw', 'ik', 'kl', 'nl', 'pt', 'qu', 'srn', 'chy', 'yi', 'ike-cans', 'cr', 'chr' ],
			'languages in region AM are ordered correctly by script group'
		);
	} );
	it( 'scripts', function () {
		// This test assumes that we don't want any scripts to be in the 'Other'
		// group. Actually, this may become wrong some day.
		assert.deepEqual( orphanScripts(), [], 'All scripts belong to script groups.' );
		assert.deepEqual( languageData.getLanguagesInScript( 'Guru' ), [ 'pa-guru' ], '"pa-guru" is written in script Guru, and "pa" is skipped as a redirect' );
		assert.deepEqual( languageData.getLanguagesInScripts( [ 'Geor', 'Armn' ] ), [ 'hy', 'hyw', 'ka', 'xmf' ], 'languages in scripts Geor and Armn are selected correctly' );
		assert.deepEqual( languageData.getLanguagesInScript( 'Knda' ), [
			'kn', 'tcy'
		], 'languages in script Knda are selected correctly' );
		assert.strictEqual( languageData.getGroupOfScript( 'Beng' ), 'SouthAsian', 'Bengali script belongs to the SouthAsian group.' );
		assert.strictEqual( languageData.getScriptGroupOfLanguage( 'iu' ), 'NativeAmerican', 'The script of the Inupiaq language belongs to the NativeAmerican group.' );
	} );
	it( 'redirects', function () {
		assert.strictEqual( languageData.isRedirect( 'sr-ec' ), 'sr-cyrl', '"sr-ec" is a redirect to "sr-cyrl"' );
		assert.deepEqual( badRedirects(), [], 'All redirects have valid targets.' );
		assert.deepEqual( doubleRedirects(), [], 'There are no double redirects.' );
		assert.deepEqual( doubleAutonyms(), [], 'All languages have distinct autonyms.' );
		assert.strictEqual( languageData.getScript( 'no-such-language' ), 'Zyyy', 'A script for an unknown language is Zyyy - undetermined' );
		assert.strictEqual( languageData.getScript( 'ii' ), 'Yiii', 'Correct script of the Yi language was selected' );
	} );
	it( 'directionality', function () {
		assert.strictEqual( languageData.isRtl( 'te' ), false, 'Telugu language is not RTL' );
		assert.strictEqual( languageData.isRtl( 'dv' ), true, 'Divehi language is RTL' );
		assert.strictEqual( languageData.getDir( 'mzn' ), 'rtl', 'Mazandarani language is RTL' );
		assert.strictEqual( languageData.getDir( 'uk' ), 'ltr', 'Ukrainian language is LTR' );
	} );
} );
