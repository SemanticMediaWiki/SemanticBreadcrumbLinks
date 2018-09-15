<?php

namespace SBL\Tests;

use SBL\SubpageParentAnnotator;
use SMW\DIWikiPage;
use SMW\DIProperty;
use Title;

/**
 * @covers \SBL\SubpageParentAnnotator
 * @group semantic-breadcrumb-links
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class SubpageParentAnnotatorTest extends \PHPUnit_Framework_TestCase {

	private $parserData;
	private $parserOutput;

	protected function setUp() {

		$title = Title::newFromText( 'Foo/Bar' );

		$this->parserOutput = $this->getMockBuilder( '\ParserOutput' )
			->disableOriginalConstructor()
			->getMock();

		$this->parserData = $this->getMockBuilder( '\SMW\ParserData' )
			->disableOriginalConstructor()
			->getMock();

		$this->parserData->expects( $this->any() )
			->method( 'getTitle' )
			->will( $this->returnValue( $title ) );

		$this->parserData->expects( $this->any() )
			->method( 'getOutput' )
			->will( $this->returnValue( $this->parserOutput ) );
	}

	public function testCanConstruct() {

		$this->assertInstanceOf(
			'\SBL\SubpageParentAnnotator',
			new SubpageParentAnnotator( $this->parserData )
		);
	}

	public function testAddAnnotation() {

		$property = DIProperty::newFromUserLabel( SBL_PROP_PARENTPAGE );
		$subject = DIWikiPage::newFromText( 'Foo' );

		$semanticData = $this->getMockBuilder( '\SMW\SemanticData' )
			->disableOriginalConstructor()
			->getMock();

		$semanticData->expects( $this->once() )
			->method( 'getPropertyValues' )
			->will( $this->returnValue( [] ) );

		$semanticData->expects( $this->once() )
			->method( 'addPropertyObjectValue' )
			->with(
				$this->equalTo( $property ),
				$this->equalTo( $subject ) );

		$this->parserData->expects( $this->atLeastOnce() )
			->method( 'getSemanticData' )
			->will( $this->returnValue( $semanticData ) );

		$instance = new SubpageParentAnnotator(
			$this->parserData
		);

		$instance->addAnnotation();
	}

	public function testDisabledAnnotationFunctionality() {

		$this->parserData->expects( $this->never() )
			->method( 'getSemanticData' );

		$instance = new SubpageParentAnnotator(
			$this->parserData
		);

		$instance->enableSubpageParentAnnotation( false );
		$instance->addAnnotation();
	}

	public function testDisabledAnnotationFunctionalityDueToPreexisingValues() {

		$semanticData = $this->getMockBuilder( '\SMW\SemanticData' )
			->disableOriginalConstructor()
			->getMock();

		$semanticData->expects( $this->once() )
			->method( 'getPropertyValues' )
			->will( $this->returnValue( [ 'Foo' ] ) );

		$semanticData->expects( $this->never() )
			->method( 'addPropertyObjectValue' );

		$this->parserData->expects( $this->atLeastOnce() )
			->method( 'getSemanticData' )
			->will( $this->returnValue( $semanticData ) );

		$instance = new SubpageParentAnnotator(
			$this->parserData
		);

		$instance->enableSubpageParentAnnotation( true );
		$instance->addAnnotation();
	}

	public function testNoAnnotationOnPagesWithSpaces() {

		$title = Title::newFromText( 'Foo / Bar' );

		$parserData = $this->getMockBuilder( '\SMW\ParserData' )
			->disableOriginalConstructor()
			->getMock();

		$parserData->expects( $this->any() )
			->method( 'getTitle' )
			->will( $this->returnValue( $title ) );

		$semanticData = $this->getMockBuilder( '\SMW\SemanticData' )
			->disableOriginalConstructor()
			->getMock();

		$semanticData->expects( $this->once() )
			->method( 'getPropertyValues' )
			->will( $this->returnValue( [] ) );

		$semanticData->expects( $this->never() )
			->method( 'addPropertyObjectValue' );

		$parserData->expects( $this->atLeastOnce() )
			->method( 'getSemanticData' )
			->will( $this->returnValue( $semanticData ) );

		$instance = new SubpageParentAnnotator(
			$parserData
		);

		$instance->enableSubpageParentAnnotation( true );
		$instance->addAnnotation();
	}

	public function testDisableTranslationSubpageAnnotation() {

		$this->parserOutput->expects( $this->once() )
			->method( 'getExtensionData' )
			->with( $this->equalTo( 'translate-translation-page' ) )
			->will( $this->returnValue( true ) );

		$instance = new SubpageParentAnnotator(
			$this->parserData
		);

		$instance->disableTranslationSubpageAnnotation( true );
		$instance->addAnnotation();
	}

}
