<?php

namespace Eko\GoogleTranslateBundle\Tests\Method;

use Eko\GoogleTranslateBundle\Translate\Method\Detector;

/**
 * Detector class test
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class DetectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Detector $detector Detector service
     */
    protected $detector;

    /**
     * Set up methods services
     */
    protected function setUp()
    {
        $this->detector = $this->getMock(
            '\Eko\GoogleTranslateBundle\Translate\Method\Detector',
            array('process'),
            array('fakeapikey')
        );
    }

    /**
     * Test simple detect method
     */
    public function testSimpleDetect()
    {
        $this->detector
            ->expects($this->any())
            ->method('process')
            ->will($this->returnValue('en'));

        $language = $this->detector->detect('hi');
        $this->assertEquals($language, 'en', 'Should return language "en"');
    }
}