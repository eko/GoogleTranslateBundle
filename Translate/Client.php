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

use Guzzle\Http\Client as GuzzleClient;

/**
 * Class Client
 *
 * This is the main client class to instancing Guzzle HTTP client
 *
 * @package Eko\GoogleTranslateBundle\Translate
 */
class Client {
    /**
     * @var string $apiKey Google Translate API key
     */
    protected $apiKey = null;

    /**
     * @var Client $client A Guzzle client instance
     */
    protected $client;

    /**
     * Constructor
     *
     * @string $apiKey API key retrieved from configuration
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
        $this->client = new GuzzleClient($this->url);
    }
}