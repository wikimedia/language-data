<?php
require __DIR__ . '/../../src/LanguageData.php';

use PHPUnit\Framework\TestCase;
use Wikimedia\LanguageData;

/**
 * @coversDefaultClass \Wikimedia\LanguageData
 */
class LanguageDataTest extends TestCase {
	/**
	 * @var LanguageData
	 */
	protected $languageData;

	private const UNKNOWN_LANGUAGE_CODE = 'xyz';

	protected function setUp(): void {
		parent::setUp();
		$this->languageData = LanguageData::get();
	}

	/**
	 * @covers isKnown
	 */
	public function testIsKnown() {
		$this->assertTrue( $this->languageData->isKnown( 'en' ) );
		$this->assertFalse( $this->languageData->isKnown( self::UNKNOWN_LANGUAGE_CODE ) );
	}

	/**
	 * @covers isRedirect
	 */
	public function testIsRedirect() {
		$this->assertFalse( $this->languageData->isRedirect( 'en' ) );
		$this->assertEquals( $this->languageData->isRedirect( 'aeb' ), 'aeb-arab' );
	}

	/**
	 * @covers getScript
	 */
	public function testGetScript() {
		$this->assertEquals( $this->languageData->getScript( 'en' ), 'Latn' );
		$this->assertFalse( $this->languageData->getScript( self::UNKNOWN_LANGUAGE_CODE ) );
	}

	/**
	 * @covers getRegions
	 */
	public function testGetRegions() {
		$this->assertFalse( $this->languageData->getRegions( self::UNKNOWN_LANGUAGE_CODE ) );
		$this->assertEquals( [ 'AF' ], $this->languageData->getRegions( 'aeb' ) );

		$expected = [ 'EU', 'AM', 'AS' ];
		$regions = $this->languageData->getRegions( 'en' );
		foreach ( $expected as $region ) {
			$this->assertContains( $region, $regions );
		}
	}

	/**
	 * @covers getAutonym
	 */
	public function testGetAutonym() {
		$this->assertFalse( $this->languageData->getAutonym( self::UNKNOWN_LANGUAGE_CODE ) );
		$this->assertEquals(
			'تونسي',
			$this->languageData->getAutonym( 'aeb' ),
			'Redirects return proper value in getAutonym.'
		);

		$this->assertEquals( 'English', $this->languageData->getAutonym( 'en' ) );
	}

	/**
	 * @covers getAutonyms
	 */
	public function testGetAutonyms() {
		$autonyms = $this->languageData->getAutonyms();
		$this->assertEquals( 'English', $autonyms['en'] );
		$this->assertFalse(
			isset( $autonyms['aeb'] ),
			'Redirects are not present in getAutonyms.'
		);
	}

	/**
	 * @covers getLanguagesInScripts
	 */
	public function testGetLanguagesInScripts() {
		$this->assertEmpty(
			$this->languageData->getLanguagesInScripts( [ self::UNKNOWN_LANGUAGE_CODE ] )
		);

		$expectedValues = $this->languageData->getLanguagesInScripts( [ 'Latn', 'Grek' ] );

		$this->assertContains( 'zu', $expectedValues );
		$this->assertContains( 'pnt', $expectedValues );
		$this->assertNotContains(
			'sr-el',
			$expectedValues,
			'Redirects are not present when fetching languages in scripts.'
		);
	}

	/**
	 * @covers getGroupOfScript
	 */
	public function testGetGroupOfScript() {
		$this->assertEquals( 'Latin', $this->languageData->getGroupOfScript( 'Latn' ) );
		$this->assertEquals(
			LanguageData::OTHER_SCRIPT_GROUP,
			$this->languageData->getGroupOfScript( self::UNKNOWN_LANGUAGE_CODE )
		);
	}

	/**
	 * @covers getScriptGroupOfLanguage
	 */
	public function testGetScriptGroupOfLanguage() {
		$this->assertEquals(
			LanguageData::OTHER_SCRIPT_GROUP,
			$this->languageData->getScriptGroupOfLanguage( self::UNKNOWN_LANGUAGE_CODE )
		);

		$this->assertEquals(
			'Latin',
			$this->languageData->getScriptGroupOfLanguage( 'en' )
		);
	}

	/**
	 * @covers getLanguagesByScriptGroup
	 */
	public function testGetLanguagesByScriptGroup() {
		$actuals = $this->languageData->getLanguagesByScriptGroup( [ 'en', 'sr-el', 'tt-cyrl' ] );

		$this->assertContains( 'tt-cyrl', $actuals['Cyrillic'] );
		$this->assertContains( 'en', $actuals['Latin'] );
		$this->assertContains( 'sr-el', $actuals['Latin'] );
	}

	/**
	 * @covers getLanguagesByScriptGroupInRegions
	 */
	public function testGetLanguagesByScriptGroupInRegions() {
		$actuals = $this->languageData->getLanguagesByScriptGroupInRegions( [ 'AS', 'PA' ] );

		$this->assertContains( 'tpi', $actuals['Latin'] );
		$this->assertContains( 'ug-arab', $actuals['Arabic'] );
		$this->assertContains( 'zh-sg', $actuals['CJK'] );
		$this->assertNotContains(
			'azb',
			$actuals['Arabic'],
			'Redirects are not present when languages grouped by script in a region.'
		);
	}

	/**
	 * @covers sortByAutonym
	 */
	public function testSortByAutonym() {
		$sorted = $this->languageData->sortByAutonym(
			[
				'atj', 'chr', 'chy',
				'cr', 'en', 'es',
				'fr', 'gn', 'haw',
				'ike-cans', 'ik', 'kl',
				'nl', 'pt', 'qu',
				'srn', 'yi', self::UNKNOWN_LANGUAGE_CODE
			]
		);

		$this->assertEquals(
			[
				'atj', 'gn',  'en',
				'es',  'fr',  'haw',
				'ik',  'kl',  'nl',
				'pt',  'qu',  'srn',
				'chy', 'yi',  'chr',
				'ike-cans',  'cr'
			],
			$sorted
		);
	}

	/**
	 * @covers sortByScriptGroup
	 */
	public function testSortByScriptGroup() {
		$sorted = $this->languageData->sortByScriptGroup(
			$this->languageData->sortByAutonym(
				[
					'atj', 'chr', 'chy',
					'cr', 'en', 'es',
					'fr', 'gn', 'haw',
					'ike-cans', 'ik', 'kl',
					'nl', 'pt', 'qu',
					'srn', 'yi', self::UNKNOWN_LANGUAGE_CODE
				]
			)
		);

		$this->assertEquals(
			[
				'atj', 'gn', 'en',
				'es', 'fr', 'haw',
				'ik', 'kl', 'nl',
				'pt', 'qu', 'srn',
				'chy', 'yi', 'chr',
				'ike-cans', 'cr'
			],
			$sorted
		);
	}

	/**
	 * @covers isRtl
	 */
	public function testIsRtl() {
		$this->assertFalse( $this->languageData->isRtl( 'en' ) );
		$this->assertFalse( $this->languageData->isRtl( self::UNKNOWN_LANGUAGE_CODE ) );
		$this->assertTrue( $this->languageData->isRtl( 'he' ) );
	}

	/**
	 * @covers getDir
	 */
	public function testGetDir() {
		$this->assertEquals( 'ltr', $this->languageData->getDir( 'en' ) );
		$this->assertEquals( 'rtl', $this->languageData->getDir( 'he' ) );
		$this->assertFalse( $this->languageData->getDir( self::UNKNOWN_LANGUAGE_CODE ) );
	}

	/**
	 * @covers getLanguagesInTerritory
	 */
	public function testGetLanguagesInTerritory() {
		$actualsAFG = $this->languageData->getLanguagesInTerritory( 'AF' );
		$actualsAT = $this->languageData->getLanguagesInTerritory( 'AT' );

		$this->assertContains( 'de', $actualsAT );
		$this->assertContains( 'bar', $actualsAT );
		$this->assertNotContains( 'he', $actualsAT );

		$this->assertContains( 'ug-arab', $actualsAFG );
		$this->assertContains( 'tk', $actualsAFG );
		$this->assertNotContains( 'de', $actualsAFG );
	}

	/**
	 * @covers addLanguage
	 */
	public function testAddLanguage() {
		$this->assertFalse( $this->languageData->isKnown( 'xyz' ) );
		$this->assertNotContains(
			'xyz',
			$this->languageData->getLanguagesByScriptGroupInRegion( 'AF' )['Latin']
		);

		$this->languageData->addLanguage( self::UNKNOWN_LANGUAGE_CODE, [
			'script' => "Latn",
			'regions' => [
				"AF"
			],
			'autonym' => "Test Language"
		] );

		$this->assertTrue( $this->languageData->isKnown( self::UNKNOWN_LANGUAGE_CODE ) );
		$this->assertContains(
			self::UNKNOWN_LANGUAGE_CODE,
			$this->languageData->getLanguagesByScriptGroupInRegion( 'AF' )['Latin']
		);
	}
}
