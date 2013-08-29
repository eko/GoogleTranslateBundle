<?php
/*
 * This file is part of the Eko\GoogleTranslateBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\GoogleTranslateBundle\Translate\Method;

use Symfony\Component\Stopwatch\Stopwatch;

use Eko\GoogleTranslateBundle\Translate\Method;
use Eko\GoogleTranslateBundle\Translate\Method\Detector;
use Eko\GoogleTranslateBundle\Translate\MethodInterface;

/**
 * Class Translator
 *
 * This is the class to translate text from a source language to a target one
 *
 * @package Eko\GoogleTranslateBundle\Translate\Method
 */
class Translator extends Method implements MethodInterface {
    /**
     * @var Detector $detector Detector method service
     */
    protected $detector;

    /**
     * @var string $url Google Translate API translate url
     */
    protected $url = 'https://www.googleapis.com/language/translate/v2?key={key}&q={query}&source={source}&target={target}';

    /**
     * Constructor
     *
     * @param string    $apiKey    Google Translate API key
     * @param Detector  $detector  A Detector service
     * @param Stopwatch $stopwatch Symfony profiler stopwatch service
     */
    public function __construct($apiKey, Detector $detector, Stopwatch $stopwatch = null)
    {
        $this->detector = $detector;

        return parent::__construct($apiKey, $stopwatch);
    }

    /**
     * Returns detector service
     *
     * @return Detector
     */
    public function getDetector()
    {
        return $this->detector;
    }

    /**
     * Translates given string in given target language from a source language via the Google Translate API.
     * If source language is not defined, it use detector method to detect string language
     *
     * @param string $query  A query string to translate
     * @param string $target A target language
     * @param string $source A source language
     *
     * @return string
     */
    public function translate($query, $target, $source = null)
    {
        if (null === $source) {
            $source = $this->getDetector()->detect($query);
        }

        $this->getClient()->setConfig(array(
            'key'    => $this->apiKey,
            'query'  => $query,
            'source' => $source,
            'target' => $target
        ));

        return $this->process();
    }

    /**
     * Process requests and retrieve JSON result(s)
     *
     * @return string|null
     */
    protected function process()
    {
        $result = null;

        $client = $this->getClient();

        $event = $this->startProfiling(
            $this->getName(),
            $client->getConfig('query'),
            $client->getConfig('source'),
            $client->getConfig('target')
        );

        $json = $client->get()->send()->json();

        if (isset($json['data']['translations'])) {
            $current = current($json['data']['translations']);

            $result = $current['translatedText'];
        }

        $this->stopProfiling($event, $this->getName(), $result);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Translator';
    }
}
