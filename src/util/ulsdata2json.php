<?php
/**
 * Script to create the language data in JSON format for ULS.
 *
 * Copyright (C) 2020 Alolita Sharma, Amir Aharoni, Arun Ganesh, Brandon Harris,
 * Niklas Laxström, Pau Giner, Santhosh Thottingal, Siebrand Mazeland and other
 * contributors. See https://github.com/wikimedia/language-data/graphs/contributors
 * for a list.
 *
 * @file
 * @ingroup Extensions
 * @license GPL-2.0-or-later
 */

require_once __DIR__ . '/../../vendor/autoload.php';

define( 'DATA_DIRECTORY', __DIR__ . '/../../data' );

print "Reading langdb.yaml...\n";
$yamlLangdb = file_get_contents( DATA_DIRECTORY . '/langdb.yaml' );
$parsedLangdb = spyc_load( $yamlLangdb );

$supplementalDataFilename = 'supplementalData.xml';
$supplementalDataUrl =
	// phpcs:ignore Generic.Files.LineLength
	"https://raw.githubusercontent.com/unicode-org/cldr/master/common/supplemental/supplementalData.xml";

$curl = curl_init( $supplementalDataUrl );
$supplementalDataFile = fopen( $supplementalDataFilename, 'w' );

curl_setopt( $curl, CURLOPT_FILE, $supplementalDataFile );
curl_setopt( $curl, CURLOPT_HEADER, 0 );

print "Trying to download $supplementalDataUrl...\n";
$curlSuccess = curl_exec( $curl );
curl_close( $curl );
fclose( $supplementalDataFile );

if ( !$curlSuccess ) {
	die( "Failed to download CLDR data from $supplementalDataUrl.\n" );
}
print "Downloaded $supplementalDataFilename, trying to parse...\n";

$supplementalData = simplexml_load_file( $supplementalDataFilename );

if ( !( $supplementalData instanceof SimpleXMLElement ) ) {
	die( "Attempt to load CLDR data from $supplementalDataFilename failed.\n" );
}

print "CLDR supplemental data parsed successfully, reading territories info...\n";
$parsedLangdb['territories'] = [];

foreach ( $supplementalData->territoryInfo->territory as $territoryRecord ) {
	$territoryAtributes = $territoryRecord->attributes();
	$territoryCodeAttr = $territoryAtributes['type'];
	$territoryCode = (string)$territoryCodeAttr[0];
	$parsedLangdb['territories'][$territoryCode] = [];

	foreach ( $territoryRecord->languagePopulation as $languageRecord ) {
		$languageAttributes = $languageRecord->attributes();
		$languageCodeAttr = $languageAttributes['type'];
		// Lower case is a convention for language codes in ULS.
		// '_' is used in CLDR for compound codes and it's replaced with '-' here.

		$normalisedCode = strtr( strtolower( (string)$languageCodeAttr[0] ), '_', '-' );

		$parsedLangdb['territories'][$territoryCode][] = $normalisedCode;

		// In case of codes with variants, also add the base because ULS might consider
		// them as separate languages, e.g. zh, zh-hant and zh-hans.
		if ( strpos( $normalisedCode, '-' ) !== false ) {
			$parts = explode( '-', $normalisedCode );
			$parsedLangdb['territories'][$territoryCode][] = $parts[0];
		}
	}
}

foreach ( $parsedLangdb['territories'] as $territoryCode => $languages ) {
	foreach ( $languages as $index => $language ) {
		if ( !isset( $parsedLangdb['languages'][$language] ) ) {
			echo "Unknown language $language for territory $territoryCode\n";
			unset( $parsedLangdb['territories'][$territoryCode][$index] );
			continue;
		}

		$data = $parsedLangdb['languages'][$language];
		if ( count( $data ) === 1 ) {
			echo "Redirect for language $language to {$data[0]} territory $territoryCode\n";
			$parsedLangdb['territories'][$territoryCode][$index] = $data[0];
			continue;
		}
	}

	// Clean-up to save space
	if ( count( $parsedLangdb['territories'][$territoryCode] ) === 0 ) {
		unset( $parsedLangdb['territories'][$territoryCode] );
		continue;
	}

	// Remove duplicates we might have created
	$parsedLangdb['territories'][$territoryCode] =
		array_unique( $parsedLangdb['territories'][$territoryCode] );

	// We need to renumber or json conversion thinks these are objects
	$parsedLangdb['territories'][$territoryCode] =
		array_values( $parsedLangdb['territories'][$territoryCode] );
}

print "Writing JSON langdb...\n";
$jsonVerbose = json_encode( $parsedLangdb, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) . "\n";
// For making diff review easier.
file_put_contents( DATA_DIRECTORY . '/language-data.json', $jsonVerbose );

print "Done.\n";
