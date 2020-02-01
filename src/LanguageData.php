<?php
/**
 * Contains a utility class to query the language data.
 *
 * @file
 * @license GPL-2.0-or-later
 */

namespace Wikimedia;

/**
 * Utility class to query the language data.
 */
class LanguageData {
	private static $instance;

	public const OTHER_SCRIPT_GROUP = 'Other';

	private const LANGUAGE_DATA_PATH = '../data/language-data.json';

	private $data;

	/**
	 * Returns an instance of the class
	 * @return LanguageData
	 */
	public static function get(): LanguageData {
		if ( self::$instance === null ) {
			self::$instance = new LanguageData();
			self::$instance->loadData();
		}

		return self::$instance;
	}

	private function loadData() {
		$this->data = json_decode( file_get_contents( __DIR__ . '/' . self::LANGUAGE_DATA_PATH ) );
	}

	/**
	 * Checks if a language code is valid
	 * @param string $languageCode
	 * @return bool
	 */
	public function isKnown( string $languageCode ): bool {
		return isset( $this->data->languages->$languageCode );
	}

	/**
	 * Is this language a redirect to another language?
	 * @param string $languageCode Language code
	 * @return string|bool Target language code if it's a redirect or false if it's not
	 */
	public function isRedirect( string $languageCode ) {
		if (
			$this->isKnown( $languageCode ) &&
			count( $this->getLanguage( $languageCode ) ) === 1
		) {
			return $this->getLanguage( $languageCode )[0];
		}

		return false;
	}

	/**
	 * Get all the languages
	 * @return object
	 */
	public function getLanguages() {
		return $this->data->languages;
	}

	/**
	 * Returns the script of the language or false
	 * @param string $languageCode
	 * @return string|bool Language script if its a known language, else false.
	 */
	public function getScript( string $languageCode ) {
		if ( !$this->isKnown( $languageCode ) ) {
			return false;
		}

		$targetCode = $this->isRedirect( $languageCode );
		if ( $targetCode ) {
			return $this->getScript( $targetCode );
		}

		return $this->getLanguage( $languageCode )[0];
	}

	/**
	 * Returns the regions in which a language is spoken.
	 * @param string $languageCode
	 * @return string[]|bool Array of regions or false if language is unknown.
	 */
	public function getRegions( string $languageCode ) {
		if ( !$this->isKnown( $languageCode ) ) {
			return false;
		}

		$targetCode = $this->isRedirect( $languageCode );
		if ( $targetCode ) {
			return $this->getRegions( $targetCode );
		}

		return $this->getLanguage( $languageCode )[1];
	}

	/**
	 * Returns the autonym of the language.
	 * @param string $languageCode Language code
	 * @return string|bool
	 */
	public function getAutonym( $languageCode ) {
		if ( !$this->isKnown( $languageCode ) ) {
			return false;
		}

		$targetCode = $this->isRedirect( $languageCode );
		if ( $targetCode ) {
			return $this->getAutonym( $targetCode );
		}

		$language = $this->getLanguage( $languageCode );
		return count( $language ) >= 2 ? $language[2] : $languageCode;
	}

	/**
	 * Returns all language codes and corresponding autonyms
	 * @return array
	 */
	public function getAutonyms(): array {
		$languages = $this->getLanguages();
		$languageAutonyms = [];
		foreach ( $languages as $languageCode => $languageData ) {
			if ( $this->isRedirect( $languageCode ) ) {
				continue;
			}
			$languageAutonyms[$languageCode] = $this->getAutonym( $languageCode );
		}

		return $languageAutonyms;
	}

	/**
	 * Returns all languages written in the given scripts.
	 * @param string[] $scripts
	 * @return string[]
	 */
	public function getLanguagesInScripts( array $scripts ): array {
		$languages = $this->getLanguages();
		$languagesInScripts = [];
		foreach ( $languages as $languageCode => $languageData ) {
			if ( $this->isRedirect( $languageCode ) ) {
				continue;
			}

			$script = $this->getScript( $languageCode );
			if ( in_array( $script, $scripts ) ) {
				$languagesInScripts[] = $languageCode;
			}
		}

		return $languagesInScripts;
	}

	/**
	 * Returns all languages written in the given script.
	 * @param string $script
	 * @return string[]
	 */
	public function getLanguagesInScript( string $script ): array {
		return $this->getLanguagesInScripts( [ $script ] );
	}

	/**
	 * Returns the script group of a script or 'Other' if it doesn't
	 * belong to any group.
	 * @param string $script Script code
	 * @return string script group name
	 */
	public function getGroupOfScript( string $script ): string {
		$scriptGroups = $this->data->scriptgroups;
		foreach ( $scriptGroups as $scriptGroup => $scriptGroupData ) {
			if ( in_array( $script, $scriptGroupData ) ) {
				return $scriptGroup;
			}
		}

		return self::OTHER_SCRIPT_GROUP;
	}

	/**
	 * Returns the script group of a language.
	 * @param string $languageCode Language code
	 * @return string script group name
	 */
	public function getScriptGroupOfLanguage( string $languageCode ): string {
		return $this->getGroupOfScript( $this->getScript( $languageCode ) );
	}

	/**
	 * Return the list of languages passed, grouped by script.
	 * @param string[] $languageCodes Array of language codes to group
	 * @return array Array of language codes grouped by script
	 */
	public function getLanguagesByScriptGroup( array $languageCodes ): array {
		$languagesByScriptGroup = [];

		foreach ( $languageCodes as $languageCode ) {
			if ( !$this->isKnown( $languageCode ) ) {
				continue;
			}

			$targetLanguageCode = $this->isRedirect( $languageCode );
			if ( $targetLanguageCode === false ) {
				$targetLanguageCode = $languageCode;
			}
			$langScriptGroup = $this->getScriptGroupOfLanguage( $targetLanguageCode );

			if ( !isset( $languagesByScriptGroup[$langScriptGroup] ) ) {
				$languagesByScriptGroup[$langScriptGroup] = [];
			}

			$languagesByScriptGroup[$langScriptGroup][] = $languageCode;
		}

		return $languagesByScriptGroup;
	}

	/**
	 * Returns an associative array of languages in several regions,
	 * grouped by script group.
	 * @param string[] $regions array of region codes
	 * @return array
	 */
	public function getLanguagesByScriptGroupInRegions( array $regions ): array {
		$languagesByScriptGroupInRegions = [];
		$languages = $this->getLanguages();

		foreach ( $languages as $languageCode => $languageData ) {
			if ( $this->isRedirect( $languageCode ) ) {
				continue;
			}

			$languageRegions = $this->getRegions( $languageCode );
			foreach ( $regions as $region ) {
				if ( !in_array( $region, $languageRegions ) ) {
					continue;
				}

				$langScriptGroup = $this->getScriptGroupOfLanguage( $languageCode );
				if ( !isset( $languagesByScriptGroupInRegions[$langScriptGroup] ) ) {
					$languagesByScriptGroupInRegions[$langScriptGroup] = [];
				}

				$languagesByScriptGroupInRegions[$langScriptGroup][] = $languageCode;
			}
		}

		return $languagesByScriptGroupInRegions;
	}

	/**
	 * Returns an associative array of languages in a region, grouped by their script.
	 * @param string $region Region code
	 * @return array
	 */
	public function  getLanguagesByScriptGroupInRegion( $region ): array {
		return $this->getLanguagesByScriptGroupInRegions( [ $region ] );
	}

	/**
	 * Return the list of languages sorted by script groups.
	 * @param string[] $languageCodes Array of language codes to sort
	 * @return string[] Array of language codes
	 */
	public function sortByScriptGroup( array $languageCodes ) {
		$groupedLanguageData = $this->getLanguagesByScriptGroup( $languageCodes );
		ksort( $groupedLanguageData, SORT_STRING | SORT_FLAG_CASE );

		$sortedLanguageData = [];
		foreach ( $groupedLanguageData as $languageData ) {
			$sortedLanguageData = array_merge( $sortedLanguageData, $languageData );
		}

		return $sortedLanguageData;
	}

	/**
	 * Sort languages by their autonym.
	 * @param string[] $languageCodes
	 * @return string[]
	 */
	public function sortByAutonym( array $languageCodes ): array {
		$sortedLanguages = [];
		foreach ( $languageCodes as $languageCode ) {
			$autonym = $this->getAutonym( $languageCode );
			if ( $autonym !== false ) {
				$sortedLanguages[$languageCode] = $autonym;
			}
		}

		asort( $sortedLanguages, SORT_STRING | SORT_FLAG_CASE );

		return array_keys( $sortedLanguages );
	}

	/**
	 * Check if a language is right-to-left.
	 * @param string $languageCode Language code
	 * @return bool
	 */
	public function isRtl( string $languageCode ): bool {
		$script = $this->getScript( $languageCode );
		return in_array( $script, $this->data->rtlscripts );
	}

	/**
	 * Return the direction of the language. Returns false if the direction is unknown.
	 * @param string $languageCode Language code
	 * @return string|bool
	 */
	public function getDir( string $languageCode ) {
		if ( $this->isKnown( $languageCode ) ) {
			return $this->isRtl( $languageCode ) ? 'rtl' : 'ltr';
		}

		return false;
	}

	/**
	 * Returns the languages spoken in a territory.
	 * @param string $territory Territory code
	 * @return string[]|bool list of language codes
	 */
	public function getLanguagesInTerritory( string $territory ) {
		if ( isset( $this->data->territories->$territory ) ) {
			return $this->data->territories->$territory;
		}

		return false;
	}

	/**
	 * Adds a language in run time and sets its options as provided.
	 * If the target option is provided, the language is defined as a redirect.
	 * Other possible options are script, regions and autonym.
	 * @param string $languageCode New language code.
	 * @param array $options Language properties.
	 */
	public function addLanguage( string $languageCode, array $options ): void {
		$languages = $this->getLanguages();
		if ( isset( $options['target'] ) ) {
			$languages->$languageCode = [ $options['target'] ];
		} else {
			$languages->$languageCode =
				[ $options['script'], $options['regions'], $options['autonym'] ];
		}
	}

	/**
	 * Return the language data based on language code. Performs no check, meant for
	 * internal use only.
	 * @param string $languageCode
	 * @return array
	 */
	private function getLanguage( string $languageCode ): array {
		return $this->data->languages->$languageCode;
	}
}
