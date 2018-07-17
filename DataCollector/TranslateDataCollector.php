<?php
/*
 * This file is part of the Eko\GoogleTranslateBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\GoogleTranslateBundle\DataCollector;

use Eko\GoogleTranslateBundle\Translate\MethodManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

/**
 * Class TranslateDataCollector.
 *
 * This collects all methods calls that are done in your application
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class TranslateDataCollector extends DataCollector implements DataCollectorInterface
{
    /**
     * @var MethodManager
     */
    protected $manager;

    /**
     * Constructor.
     *
     * @param MethodManager $manager
     */
    public function __construct(MethodManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        foreach ($this->manager->all() as $name => $method) {
            $this->data[$name] = $method->getProfiles();
        }
    }

    /**
     * Returns profiles data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'eko.google_translate.data_collector.translate';
    }

    public function reset()
    {
        return true;
    }
}
