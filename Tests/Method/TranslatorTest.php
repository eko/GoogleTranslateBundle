<?php

namespace Eko\GoogleTranslateBundle\Tests\Method;

use Eko\GoogleTranslateBundle\Translate\Method\Translator;

/**
 * Translator class test
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class TranslatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Translator $translator Translator service
     */
    protected $translator;

    /**
     * @var \GuzzleHttp\Message\Response mock
     */
    protected $responseMock;

    /**
     * Set up methods services
     */
    protected function setUp()
    {
        $this->translator = $this->getMock(
            'Eko\GoogleTranslateBundle\Translate\Method\Translator',
            array('getClient'),
            array('fakeapikey', $this->getDetectorMock())
        );

        $clientMock = $this->getClientMock();

        $this->translator->expects($this->any())->method('getClient')->will($this->returnValue($clientMock));
    }

    /**
     * Test simple translate method
     */
    public function testSimpleTranslate()
    {
        // Given
        $this->responseMock->expects($this->any())->method('json')->will($this->returnValue(
            array('data' => array('translations' => array(array('translatedText' => 'salut'))))
        ));

        // When
        $value = $this->translator->translate('hi', 'fr', 'en');

        // Then
        $this->assertEquals($value, 'salut', 'Should return "salut"');
    }

    /**
     * Test multiple translate method using an array
     */
    public function testMultipleTranslate()
    {
        // Given
        $this->responseMock->expects($this->any())->method('json')->will($this->returnValue(
            array('data' => array('translations' => array(array('translatedText' => 'salut'))))
        ));

        // When
        $values = $this->translator->translate(array('hi', 'hi'), 'fr', 'en');

        // Then
        $this->assertCount(2, $values, 'Should return an array with 2 elements');

        foreach ($values as $value) {
            $this->assertEquals($value, 'salut', 'Should return "salut"');
        }
    }

    /**
     * Test multiple translate method using an array and the economic mode
     */
    public function testMultipleEconomicTranslate()
    {
        // Given
        $this->responseMock->expects($this->once())->method('json')->will($this->returnValue(
            array('data' => array('translations' => array(array('translatedText' => 'salut # salut'))))
        ));

        // When
        $values = $this->translator->translate(array('hi', 'hi'), 'fr', 'en', true);

        // Then
        $this->assertCount(2, $values, 'Should return an array with 2 elements');

        foreach ($values as $value) {
            $this->assertEquals($value, 'salut', 'Should return "salut"');
        }
    }

    /**
     * Test translate using detector method
     */
    public function testTranslateUsingDetector()
    {
        // Given
        $this->responseMock->expects($this->any())->method('json')->will($this->returnValue(
            array('data' => array('translations' => array(array('translatedText' => 'comment allez-vous ?'))))
        ));

        // When
        $value = $this->translator->translate('how are you?', 'fr');

        // Then
        $this->assertEquals($value, 'comment allez-vous ?', 'Should return "comment allez-vous ?"');
    }

    /**
     * Returns detector service mock
     *
     * @return \Eko\GoogleTranslateBundle\Translate\Method\Detector
     */
    public function getDetectorMock()
    {
        return $this->getMockBuilder('Eko\GoogleTranslateBundle\Translate\Method\Detector')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns Guzzle HTTP client mock and sets response mock property
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getClientMock()
    {
        $clientMock = $this->getMockBuilder('GuzzleHttp\ClientInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->responseMock = $this->getMockBuilder('GuzzleHttp\Message\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $clientMock->expects($this->any())->method('get')->will($this->returnValue($this->responseMock));

        return $clientMock;
    }
}
