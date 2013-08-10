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

/**
 * Class Languages
 *
 * This is the class to list all language availables
 *
 * @package Eko\GoogleTranslateBundle\Translate\Method
 */
class Languages extends Method implements MethodInterface {
    /**
     * @var string $url Google Translate API languages base url
     */
    protected $url = 'https://www.googleapis.com/language/translate/v2/languages?key={key}';

    /**
     * Retrieves all languages availables with Google Translate API
     * If a target language is specified, returns languages name translated in target language
     *
     * @param string $target A target language to translate languages names
     *
     * @return string
     */
    public function get($target = null)
    {
        $parameters = array('key' => $this->apiKey,);

        if (null !== $target) {
            $this->getClient()->setBaseUrl(
                sprintf('%s&target={target}', $this->url)
            );

            $parameters['target'] = $target;
        }

        $this->getClient()->setConfig($parameters);

        return $this->process();
    }

    /**
     * Process requests and retrieve JSON result
     *
     * @return array
     */
    protected function process()
    {
        $event = $this->startProfiling($this->getName(), 'get');

        $json = $this->getClient()->get()->send()->json();

        $result = isset($json['data']['languages']) ? $json['data']['languages'] : array();

        $this->stopProfiling($event, $this->getName(), $result);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Languages';
    }
}