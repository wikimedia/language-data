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

use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;

$safeFile = __DIR__ . '/../../vendor/autoload.php';
require_once $safeFile;

$output = new ConsoleOutput();
$logger = new ConsoleLogger( $output );

define( 'DATA_DIRECTORY', __DIR__ . '/../../data' );

$logger->info( "Reading langdb.yaml..." );
$langdbPath = realpath( DATA_DIRECTORY . '/langdb.yaml' );
if ( $langdbPath && is_readable( $langdbPath ) ) {
	$yamlLangdb = file_get_contents( $langdbPath );
} else {
	throw new Exception( "Cannot read langdb.yaml" );
}
$parsedLangdb = spyc_load( $yamlLangdb );

$supplementalDataFilename = 'supplementalData.xml';
$supplementalDataUrl =
	// phpcs:ignore Generic.Files.LineLength
	"https://raw.githubusercontent.com/unicode-org/cldr/master/common/supplemental/supplementalData.xml";

$curl = curl_init( $supplementalDataUrl );
$supplementalDataFile = fopen( $supplementalDataFilename, 'w' );
if ( !$supplementalDataFile ) {
	throw new Exception( "Cannot open file for writing" );
}

curl_setopt( $curl, CURLOPT_FILE, $supplementalDataFile );
curl_setopt( $curl, CURLOPT_HEADER, 0 );

$logger->info( "Trying to download $supplementalDataUrl..." );
$curlSuccess = curl_exec( $curl );
curl_close( $curl );
fclose( $supplementalDataFile );

if ( !$curlSuccess ) {
	$logger->error( "Failed to download CLDR data from $supplementalDataUrl." );
	exit( 1 );
}
$logger->info( "Downloaded $supplementalDataFilename, trying to parse..." );

$supplementalData = simplexml_load_file( $supplementalDataFilename );

if ( !( $supplementalData instanceof SimpleXMLElement ) ) {
	$logger->error( "Attempt to load CLDR data from $supplementalDataFilename failed." );
	exit( 1 );
}

$logger->info( "CLDR supplemental data parsed successfully, reading territories info..." );
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
			$logger->warning( "Unknown language $language for territory $territoryCode" );
			unset( $parsedLangdb['territories'][$territoryCode][$index] );
			continue;
		}

		$data = $parsedLangdb['languages'][$language];
		if ( count( $data ) === 1 ) {
			$logger->info( "Redirect for language $language to {$data[0]} territory $territoryCode" );
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

$logger->info( "Writing JSON langdb..." );
$jsonVerbose = json_encode( $parsedLangdb, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) . "\n";
// For making diff review easier.
$outputFile = DATA_DIRECTORY . '/language-data.json';
$realDataDir = realpath( DATA_DIRECTORY );
if ( $realDataDir && str_starts_with( realpath( dirname( $outputFile ) ), $realDataDir ) ) {
	file_put_contents( $outputFile, $jsonVerbose, LOCK_EX );
} else {
	throw new Exception( "Invalid output path" );
}

$logger->info( "Done." );
