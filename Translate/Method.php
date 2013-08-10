<?php
/*
 * This file is part of the Eko\GoogleTranslateBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\GoogleTranslateBundle\Translate;

use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

use Guzzle\Http\Client as GuzzleClient;

/**
 * Class Method
 *
 * This is the main Method class that is instancing Guzzle HTTP client
 *
 * @package Eko\GoogleTranslateBundle\Translate
 */
class Method {
    /**
     * @var string $apiKey Google Translate API key
     */
    protected $apiKey = null;

    /**
     * @var Client $client A Guzzle client instance
     */
    protected $client;

    /**
     * @var string $url API translation url
     */
    protected $url = null;

    /**
     * @var array $profiles Symfony profiler profiles data
     */
    protected $profiles = array();

    /**
     * @var Stopwatch $stopwatch Symfony profiler Stopwatch service
     */
    protected $stopwatch;

    /**
     * @var integer
     */
    protected $counter = 1;

    /**
     * Constructor
     *
     * @param string    $apiKey    API key retrieved from configuration
     * @param Stopwatch $stopwatch Symfony profiler Stopwatch service
     */
    public function __construct($apiKey, Stopwatch $stopwatch = null)
    {
        $this->apiKey = $apiKey;
        $this->client = new GuzzleClient($this->url);

        $this->stopwatch = $stopwatch;
    }

    /**
     * Returns Guzzle HTTP client instance
     *
     * @return GuzzleClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Returns profiled data
     *
     * @return array
     */
    public function getProfiles()
    {
        return $this->profiles;
    }

    /**
     * Starts profiling
     *
     * @param string $name   Method name
     * @param string $query  Query text
     * @param string $source Source language
     * @param string $target Target language
     *
     * @return StopwatchEvent
     */
    protected function startProfiling($name, $query, $source = null, $target = null)
    {
        if ($this->stopwatch instanceof Stopwatch) {
            $this->profiles[$this->counter] = array(
                'query'        => urldecode($query),
                'source'       => $source,
                'target'       => $target,
                'duration'     => null,
                'memory_start' => memory_get_usage(true),
                'memory_end'   => null,
                'memory_peak'  => null,
            );

            $name = sprintf('%s (method: %s)', $query, $name);

            return $this->stopwatch->start($name);
        }
    }

    /**
     * Stops the profiling
     *
     * @param StopwatchEvent $event A stopwatchEvent instance
     * @param string         $name  Method name
     */
    protected function stopProfiling(StopwatchEvent $event = null, $name)
    {
        if ($this->stopwatch instanceof Stopwatch) {
            $event->stop();

            $values = array(
                'duration'    => $event->getDuration(),
                'memory_end'  => memory_get_usage(true),
                'memory_peak' => memory_get_peak_usage(true),
            );

            $this->profiles[$this->counter] = array_merge($this->profiles[$this->counter], $values);

            $this->counter++;
        }
    }
}
