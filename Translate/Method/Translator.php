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

use Eko\GoogleTranslateBundle\Translate\Method;
use Eko\GoogleTranslateBundle\Translate\MethodInterface;
use GuzzleHttp\ClientInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Class Translator.
 *
 * This is the class to translate text from a source language to a target one
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class Translator extends Method implements MethodInterface
{
    /**
     * Economic mode delimiter character.
     */
    const ECONOMIC_DELIMITER = '#';

    /**
     * Google API supports a maximum GET size of 2000 bytes.
     * See https://cloud.google.com/translate/v2/using_rest#detect-WorkingResults
     * If the query is longer than that, we need to split it up
     * We use a lower value to account for other parameters that add up.
     */
    const MAXIMUM_TEXT_SIZE = 1800;

    /**
     * @var Detector Detector method service
     */
    protected $detector;

    /**
     * @var string Google Translate API translate url
     */
    protected $url = 'https://www.googleapis.com/language/translate/v2';

    /**
     * Constructor.
     *
     * @param string          $apiKey    Google Translate API key
     * @param ClientInterface $client
     * @param Detector        $detector  A Detector service
     * @param Stopwatch       $stopwatch Symfony profiler stopwatch service
     */
    public function __construct($apiKey, ClientInterface $client, Detector $detector, Stopwatch $stopwatch = null)
    {
        $this->detector = $detector;

        return parent::__construct($apiKey, $client, $stopwatch);
    }

    /**
     * Returns detector service.
     *
     * @return Detector
     */
    public function getDetector()
    {
        return $this->detector;
    }

    /**
     * Translates given string in given target language from a source language via the Google Translate API.
     * If source language is not defined, it use detector method to detect string language.
     *
     * @param string|array $query     A query string to translate
     * @param string       $target    A target language
     * @param string       $source    A source language
     * @param bool         $economic  Enable the economic mode? (only 1 request)
     * @param bool         $plainText The source (and response) are plain text
     *
     * @return array|string
     */
    public function translate($query, $target, $source = null, $economic = false, $plainText = false)
    {
        if (!is_array($query)) {
            return $this->handle($query, $target, $source, $plainText);
        }

        if ($economic) {
            $results = $this->handle(implode(self::ECONOMIC_DELIMITER, $query), $target, $source, $plainText);
            $results = explode(self::ECONOMIC_DELIMITER, $results);

            return array_map('trim', $results);
        }

        $results = [];

        foreach ($query as $item) {
            $results[] = $this->handle($item, $target, $source, $plainText);
        }

        return $results;
    }

    /**
     * Handles a translation request.
     *
     * @param string $query     A query string to translate
     * @param string $target    A target language
     * @param string $source    A source language
     * @param bool   $plainText The source (and response) are plain text
     *
     * @return string
     */
    protected function handle($query, $target, $source = null, $plainText = false)
    {
        if (null === $source) {
            $source = $this->getDetector()->detect($query);
        }

        // Split up the query if it is too long. See MAXIMUM_TEXT_SIZE description for more info.
        {
            $queryArray = [];
            $remainingQuery = $query;
            while (strlen($remainingQuery) >= self::MAXIMUM_TEXT_SIZE) {
                // Get closest breaking character, but not farther than MAXIMUM_TEXT_SIZE characters away.
                $i = 0;
                $find = ["\n", '.', ' '];

                while (false === ($pos = strrpos($remainingQuery, $find[$i], -(strlen($remainingQuery) - self::MAXIMUM_TEXT_SIZE)))) {
                    $i++;
                    if ($i >= count($find)) {
                        break;
                    }
                }
                if (false === $pos || 0 === $pos) {
                    break;
                }

                // Split.
                $queryArray[] = substr($remainingQuery, 0, $pos);
                $remainingQuery = substr($remainingQuery, $pos);
            }
            $queryArray[] = $remainingQuery;
        }

        // Translate piece by piece.
        $result = '';
        foreach ($queryArray as $subQuery) {
            $options = [
                'key'    => $this->apiKey,
                'q'      => $subQuery,
                'source' => $source,
                'target' => $target,
                'format' => ($plainText ? 'text' : 'html'),
            ];

            $result .= $this->process($options);
        }

        return $result;
    }

    /**
     * Process requests and retrieve JSON result(s).
     *
     * @param array $options
     *
     * @return string|null
     */
    protected function process(array $options)
    {
        $result = null;

        $client = $this->getClient();

        $event = $this->startProfiling(
            $this->getName(),
            $client->getDefaultOption('query'),
            $client->getDefaultOption('source'),
            $client->getDefaultOption('target')
        );

        $response = $client->get($this->url, ['query' => $options]);
        $json = json_decode($response->getBody()->getContents(), true);

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
