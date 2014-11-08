<?php

namespace Eko\GoogleTranslateBundle\Tests\Method;

use Eko\GoogleTranslateBundle\Translate\Method\Languages;

/**
 * Languages class test
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class LanguagesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Languages $languages Languages service
     */
    protected $languages;

    /**
     * @var \GuzzleHttp\Message\Response mock
     */
    protected $responseMock;

    /**
     * Set up methods services
     */
    protected function setUp()
    {
        $this->languages = $this->getMock(
            'Eko\GoogleTranslateBundle\Translate\Method\Languages',
            array('getClient'),
            array('fakeapikey')
        );

        $clientMock = $this->getClientMock();

        $this->languages->expects($this->any())->method('getClient')->will($this->returnValue($clientMock));
    }

    /**
     * Test simple get method
     */
    public function testSimpleGet()
    {
        // Given
        $this->responseMock->expects($this->any())->method('json')->will($this->returnValue(
            array('data' => array('languages' => array(array('language' => 'en'), array('language' => 'fr'))))
        ));

        // When
        $values = $this->languages->get();

        // Then
        $this->assertCount(2, $values, 'Should return 2 values');

        foreach ($values as $value) {
            $this->assertArrayHasKey('language', $value, 'Should have an array key "language"');
            $this->assertTrue(in_array($value['language'], array('fr', 'en'), 'Language should be "fr" or "en"'));
        }
    }

    /**
     * Test get method with a target parameter
     */
    public function testGetWithTarget()
    {
        // Given
        $this->responseMock->expects($this->any())->method('json')->will($this->returnValue(
            array('data' => array('languages' => array(
                array('language' => 'en', 'name' => 'Anglais'),
                array('language' => 'fr', 'name' => 'Français')
            )))
        ));

        // When
        $values = $this->languages->get('fr');

        // Then
        $this->assertCount(2, $values, 'Should return 2 values');

        foreach ($values as $value) {
            $this->assertArrayHasKey('language', $value, 'Should have an array key "language"');
            $this->assertArrayHasKey('name', $value, 'Should have an array key "name"');

            $this->assertTrue(in_array($value['language'], array('fr', 'en'), 'Language should be "fr" or "en"'));
            $this->assertTrue(in_array($value['name'], array('Français', 'Anglais'), 'Language should be "Français" or "Anglais"'));
        }
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
