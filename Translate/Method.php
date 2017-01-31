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

use GuzzleHttp\ClientInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

/**
 * Class Method.
 *
 * This is the main Method class that is instancing Guzzle HTTP client
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class Method
{
    /**
     * @var string Google Translate API key
     */
    protected $apiKey = null;

    /**
     * @var ClientInterface A Guzzle client instance
     */
    protected $client;

    /**
     * @var string API translation url
     */
    protected $url = null;

    /**
     * @var array Symfony profiler profiles data
     */
    protected $profiles = [];

    /**
     * @var Stopwatch Symfony profiler Stopwatch service
     */
    protected $stopwatch;

    /**
     * @var int
     */
    protected $counter = 1;

    /**
     * Method constructor.
     *
     * @param string          $apiKey    API key retrieved from configuration
     * @param ClientInterface $client
     * @param Stopwatch       $stopwatch Symfony profiler Stopwatch service
     */
    public function __construct($apiKey, ClientInterface $client, Stopwatch $stopwatch = null)
    {
        $this->apiKey    = $apiKey;
        $this->client    = $client;
        $this->stopwatch = $stopwatch;
    }

    /**
     * Returns Guzzle HTTP client instance.
     *
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Returns profiled data.
     *
     * @return array
     */
    public function getProfiles()
    {
        return $this->profiles;
    }

    /**
     * Starts profiling.
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
            $this->profiles[$this->counter] = [
                'query'        => urldecode($query),
                'source'       => $source,
                'target'       => $target,
                'duration'     => null,
                'memory_start' => memory_get_usage(true),
                'memory_end'   => null,
                'memory_peak'  => null,
            ];

            return $this->stopwatch->start($name);
        }
    }

    /**
     * Stops the profiling.
     *
     * @param StopwatchEvent $event A stopwatchEvent instance
     */
    protected function stopProfiling(StopwatchEvent $event = null)
    {
        if ($this->stopwatch instanceof Stopwatch) {
            $event->stop();

            $values = [
                'duration'    => $event->getDuration(),
                'memory_end'  => memory_get_usage(true),
                'memory_peak' => memory_get_peak_usage(true),
            ];

            $this->profiles[$this->counter] = array_merge($this->profiles[$this->counter], $values);

            $this->counter++;
        }
    }
}
