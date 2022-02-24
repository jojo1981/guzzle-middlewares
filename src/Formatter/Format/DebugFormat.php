<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/guzzle-middlewares package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\GuzzleMiddlewares\Formatter\Format;

use Jojo1981\GuzzleMiddlewares\Formatter\FormatInterface;

/**
 * @package Jojo1981\GuzzleMiddlewares\Formatter\Format
 */
final class DebugFormat implements FormatInterface
{
    /**
     * @return string
     */
    public function getPatternString(): string
    {
        return ">>>>>>>>>>>\n{req_headers}\n{req_body}\n\n{res_headers}\n{res_body}{error}\n<<<<<<<<<<<";
    }
}
