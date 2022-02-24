<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/guzzle-middlewares package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\GuzzleMiddlewares\Formatter;

use Jojo1981\GuzzleMiddlewares\Exception\MissingProcessorException;
use Jojo1981\GuzzleMiddlewares\Formatter\FormatStrategyInterface as FormatStrategy;
use Jojo1981\GuzzleMiddlewares\Formatter\Processor\CodeProcessor;
use Jojo1981\GuzzleMiddlewares\Formatter\Processor\DateProcessor;
use Jojo1981\GuzzleMiddlewares\Formatter\Processor\ErrorProcessor;
use Jojo1981\GuzzleMiddlewares\Formatter\Processor\HostnameProcessor;
use Jojo1981\GuzzleMiddlewares\Formatter\Processor\HostProcessor;
use Jojo1981\GuzzleMiddlewares\Formatter\Processor\MethodProcessor;
use Jojo1981\GuzzleMiddlewares\Formatter\Processor\PhraseProcessor;
use Jojo1981\GuzzleMiddlewares\Formatter\Processor\RequestBodyProcessor;
use Jojo1981\GuzzleMiddlewares\Formatter\Processor\RequestHeadersProcessor;
use Jojo1981\GuzzleMiddlewares\Formatter\Processor\RequestProcessor;
use Jojo1981\GuzzleMiddlewares\Formatter\Processor\RequestVersionProcessor;
use Jojo1981\GuzzleMiddlewares\Formatter\Processor\ResponseBodyProcessor;
use Jojo1981\GuzzleMiddlewares\Formatter\Processor\ResponseHeadersProcessor;
use Jojo1981\GuzzleMiddlewares\Formatter\Processor\ResponseProcessor;
use Jojo1981\GuzzleMiddlewares\Formatter\Processor\ResponseVersionProcessor;
use Jojo1981\GuzzleMiddlewares\Formatter\Processor\TargetProcessor;
use Jojo1981\GuzzleMiddlewares\Formatter\Processor\UriProcessor;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use function sprintf;

/**
 * @package Jojo1981\GuzzleMiddlewares\Formatter
 */
final class ProcessorRegistry implements ProcessorInterface
{
    /** @var ProcessorInterface[] */
    private array $processors;

    /** @var bool */
    private bool $defaultProcessorsAdded = false;

    /**
     * @param ProcessorInterface $processor
     * @return void
     */
    public function registerProcessor(ProcessorInterface $processor): void
    {
        $this->processors[] = $processor;
    }

    /**
     * @param FormatStrategy $formatStrategy
     * @return void
     */
    public function addDefaultProcessors(FormatStrategy $formatStrategy): void
    {
        if (!$this->defaultProcessorsAdded) {
            $this->processors[] = new CodeProcessor();
            $this->processors[] = new DateProcessor();
            $this->processors[] = new ErrorProcessor();
            $this->processors[] = new HostnameProcessor();
            $this->processors[] = new HostProcessor();
            $this->processors[] = new MethodProcessor();
            $this->processors[] = new PhraseProcessor();
            $this->processors[] = new RequestBodyProcessor($formatStrategy);
            $this->processors[] = new RequestHeadersProcessor();
            $this->processors[] = new RequestProcessor();
            $this->processors[] = new RequestVersionProcessor();
            $this->processors[] = new ResponseBodyProcessor($formatStrategy);
            $this->processors[] = new ResponseHeadersProcessor();
            $this->processors[] = new ResponseProcessor();
            $this->processors[] = new ResponseVersionProcessor();
            $this->processors[] = new TargetProcessor();
            $this->processors[] = new UriProcessor();
            $this->defaultProcessorsAdded = true;
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    public function supports(string $key): bool
    {
        foreach ($this->processors as $processor) {
            if ($processor->supports($key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $key
     * @param Request $request
     * @param null|Response $response
     * @param null|string $reason
     * @throws MissingProcessorException
     * @return string
     */
    public function process(string $key, Request $request, ?Response $response = null, ?string $reason = null): string
    {
        foreach ($this->processors as $processor) {
            if ($processor->supports($key)) {
                return $processor->process($key, $request, $response, $reason);
            }
        }

        throw new MissingProcessorException(sprintf(
            'Can not process for key: `%s`, because there is no processor registered which supports that key',
            $key
        ));
    }
}
