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
     * Set up methods services
     */
    protected function setUp()
    {
        $this->translator = $this->getMock(
            '\Eko\GoogleTranslateBundle\Translate\Method\Translator',
            array('process'),
            array('fakeapikey', $this->getMockDetector())
        );
    }

    /**
     * Test simple translate method
     */
    public function testSimpleTranslate()
    {
        $this->translator
            ->expects($this->any())
            ->method('process')
            ->will($this->returnValue('salut'));

        $value = $this->translator->translate('hi', 'fr', 'en');
        $this->assertEquals($value, 'salut', 'Should return "salut"');
    }

    /**
     * Returns mock detector
     *
     * @return \Eko\GoogleTranslateBundle\Translate\Method\Detector
     */
    public function getMockDetector()
    {
        return $this->getMockBuilder('\Eko\GoogleTranslateBundle\Translate\Method\Detector')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
