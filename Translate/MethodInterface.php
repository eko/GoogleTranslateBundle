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

/**
 * Interface MethodInterface
 *
 * @package Eko\GoogleTranslateBundle\Translate
 */
interface MethodInterface
{
    /**
     * Returns method name
     *
     * @return string
     */
    public function getName();
}