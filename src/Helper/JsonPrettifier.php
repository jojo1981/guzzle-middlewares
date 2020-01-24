<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/guzzle-middlewares package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\GuzzleMiddlewares\Helper;

/**
 * @package Jojo1981\GuzzleMiddlewares\Helper
 */
final class JsonPrettifier
{
    /**
     * Private constructor, prevent getting an instance of this class
     */
    private function __construct()
    {
        // Nothing to do here
    }

    /**
     * @param string $jsonString
     * @return string
     */
    public static function prettyPrint(string $jsonString): string
    {
        if (empty($jsonString)) {
            return $jsonString;
        }

        $data = \json_decode($jsonString, false);

        return null !== $data ? \json_encode($data, JSON_PRETTY_PRINT) : $jsonString;
    }
}