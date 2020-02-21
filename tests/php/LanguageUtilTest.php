<?php
use PHPUnit\Framework\TestCase;
use Wikimedia\LanguageData\LanguageUtil;

/**
 * @coversDefaultClass \Wikimedia\LanguageData
 */
class LanguageUtilTest extends TestCase {
	/**
	 * @var LanguageUtil
	 */
	protected $languageUtil;

	private const UNKNOWN_LANGUAGE_CODE = 'xyz';

	protected function setUp(): void {
		parent::setUp();
		$this->languageUtil = LanguageUtil::get();
	}

	/**
	 * @covers isKnown
	 */
	public function testIsKnown() {
		$this->assertTrue( $this->languageUtil->isKnown( 'en' ) );
		$this->assertFalse( $this->languageUtil->isKnown( self::UNKNOWN_LANGUAGE_CODE ) );
	}

	/**
	 * @covers isRedirect
	 */
	public function testIsRedirect() {
		$this->assertFalse( $this->languageUtil->isRedirect( 'en' ) );
		$this->assertEquals( $this->languageUtil->isRedirect( 'aeb' ), 'aeb-arab' );
	}

	/**
	 * @covers getScript
	 */
	public function testGetScript() {
		$this->assertEquals( $this->languageUtil->getScript( 'en' ), 'Latn' );
		$this->assertFalse( $this->languageUtil->getScript( self::UNKNOWN_LANGUAGE_CODE ) );
	}

	/**
	 * @covers getRegions
	 */
	public function testGetRegions() {
		$this->assertFalse( $this->languageUtil->getRegions( self::UNKNOWN_LANGUAGE_CODE ) );
		$this->assertEquals( [ 'AF' ], $this->languageUtil->getRegions( 'aeb' ) );

		$expected = [ 'EU', 'AM', 'AS' ];
		$regions = $this->languageUtil->getRegions( 'en' );
		foreach ( $expected as $region ) {
			$this->assertContains( $region, $regions );
		}
	}

	/**
	 * @covers getAutonym
	 */
	public function testGetAutonym() {
		$this->assertFalse( $this->languageUtil->getAutonym( self::UNKNOWN_LANGUAGE_CODE ) );
		$this->assertEquals(
			'تونسي',
			$this->languageUtil->getAutonym( 'aeb' ),
			'Redirects return proper value in getAutonym.'
		);

		$this->assertEquals( 'English', $this->languageUtil->getAutonym( 'en' ) );
	}

	/**
	 * @covers getAutonyms
	 */
	public function testGetAutonyms() {
		$autonyms = $this->languageUtil->getAutonyms();
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
			$this->languageUtil->getLanguagesInScripts( [ self::UNKNOWN_LANGUAGE_CODE ] )
		);

		$expectedValues = $this->languageUtil->getLanguagesInScripts( [ 'Latn', 'Grek' ] );

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
		$this->assertEquals( 'Latin', $this->languageUtil->getGroupOfScript( 'Latn' ) );
		$this->assertEquals(
			LanguageUtil::OTHER_SCRIPT_GROUP,
			$this->languageUtil->getGroupOfScript( self::UNKNOWN_LANGUAGE_CODE )
		);
	}

	/**
	 * @covers getScriptGroupOfLanguage
	 */
	public function testGetScriptGroupOfLanguage() {
		$this->assertEquals(
			LanguageUtil::OTHER_SCRIPT_GROUP,
			$this->languageUtil->getScriptGroupOfLanguage( self::UNKNOWN_LANGUAGE_CODE )
		);

		$this->assertEquals(
			'Latin',
			$this->languageUtil->getScriptGroupOfLanguage( 'en' )
		);
	}

	/**
	 * @covers getLanguagesByScriptGroup
	 */
	public function testGetLanguagesByScriptGroup() {
		$actuals = $this->languageUtil->getLanguagesByScriptGroup( [ 'en', 'sr-el', 'tt-cyrl' ] );

		$this->assertContains( 'tt-cyrl', $actuals['Cyrillic'] );
		$this->assertContains( 'en', $actuals['Latin'] );
		$this->assertContains( 'sr-el', $actuals['Latin'] );
	}

	/**
	 * @covers getLanguagesByScriptGroupInRegions
	 */
	public function testGetLanguagesByScriptGroupInRegions() {
		$actuals = $this->languageUtil->getLanguagesByScriptGroupInRegions( [ 'AS', 'PA' ] );

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
		$sorted = $this->languageUtil->sortByAutonym(
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
		$sorted = $this->languageUtil->sortByScriptGroup(
			$this->languageUtil->sortByAutonym(
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
		$this->assertFalse( $this->languageUtil->isRtl( 'en' ) );
		$this->assertFalse( $this->languageUtil->isRtl( self::UNKNOWN_LANGUAGE_CODE ) );
		$this->assertTrue( $this->languageUtil->isRtl( 'he' ) );
	}

	/**
	 * @covers getDir
	 */
	public function testGetDir() {
		$this->assertEquals( 'ltr', $this->languageUtil->getDir( 'en' ) );
		$this->assertEquals( 'rtl', $this->languageUtil->getDir( 'he' ) );
		$this->assertFalse( $this->languageUtil->getDir( self::UNKNOWN_LANGUAGE_CODE ) );
	}

	/**
	 * @covers getLanguagesInTerritory
	 */
	public function testGetLanguagesInTerritory() {
		$actualsAFG = $this->languageUtil->getLanguagesInTerritory( 'AF' );
		$actualsAT = $this->languageUtil->getLanguagesInTerritory( 'AT' );

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
		$this->assertFalse( $this->languageUtil->isKnown( 'xyz' ) );
		$this->assertNotContains(
			'xyz',
			$this->languageUtil->getLanguagesByScriptGroupInRegion( 'AF' )['Latin']
		);

		$this->languageUtil->addLanguage( self::UNKNOWN_LANGUAGE_CODE, [
			'script' => "Latn",
			'regions' => [
				"AF"
			],
			'autonym' => "Test Language"
		] );

		$this->assertTrue( $this->languageUtil->isKnown( self::UNKNOWN_LANGUAGE_CODE ) );
		$this->assertContains(
			self::UNKNOWN_LANGUAGE_CODE,
			$this->languageUtil->getLanguagesByScriptGroupInRegion( 'AF' )['Latin']
		);
	}
}
