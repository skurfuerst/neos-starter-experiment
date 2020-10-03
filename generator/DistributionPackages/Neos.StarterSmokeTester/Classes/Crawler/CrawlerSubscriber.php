<?php

declare(strict_types=1);
namespace Neos\StarterSmokeTester\Crawler;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LogLevel;
use Symfony\Contracts\HttpClient\ChunkInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Terminal42\Escargot\CrawlUri;
use Terminal42\Escargot\EscargotAwareInterface;
use Terminal42\Escargot\EscargotAwareTrait;
use Terminal42\Escargot\Subscriber\ExceptionSubscriberInterface;
use Terminal42\Escargot\Subscriber\HtmlCrawlerSubscriber;
use Terminal42\Escargot\Subscriber\SubscriberInterface;
use Terminal42\Escargot\Subscriber\Util;
use Terminal42\Escargot\SubscriberLoggerTrait;


class CrawlerSubscriber implements SubscriberInterface, EscargotAwareInterface, ExceptionSubscriberInterface, LoggerAwareInterface
{
    use EscargotAwareTrait;
    use LoggerAwareTrait;
    use SubscriberLoggerTrait;

    public function shouldRequest(CrawlUri $crawlUri): string
    {
        // Skip the links that have the "type" attribute set and it's not text/html
        if ($crawlUri->hasTag(HtmlCrawlerSubscriber::TAG_NO_TEXT_HTML_TYPE)) {
            return SubscriberInterface::DECISION_NEGATIVE;
        }

        // Skip links that do not belong to our BaseUriCollection
        if ($this->escargot->getBaseUris()->containsHost($crawlUri->getUri()->getHost())) {
            return SubscriberInterface::DECISION_POSITIVE;
        }

        return SubscriberInterface::DECISION_ABSTAIN;
    }

    public function needsContent(CrawlUri $crawlUri, ResponseInterface $response, ChunkInterface $chunk): string
    {
        return 200 === $response->getStatusCode() && Util::isOfContentType($response, 'text/html') ? SubscriberInterface::DECISION_POSITIVE : SubscriberInterface::DECISION_NEGATIVE;
    }

    public function onLastChunk(CrawlUri $crawlUri, ResponseInterface $response, ChunkInterface $chunk): void
    {
        // Do something with the data
    }

    public function onTransportException(CrawlUri $crawlUri, TransportExceptionInterface $exception, ResponseInterface $response): void
    {
        if (null !== $this->logger) {
            $this->logWithCrawlUri($crawlUri, LogLevel::ERROR, 'TRANSPORT Exception ' . $exception->getMessage());
        }
    }

    public function onHttpException(CrawlUri $crawlUri, HttpExceptionInterface $exception, ResponseInterface $response, ChunkInterface $chunk): void
    {
        if (null !== $this->logger) {
            $this->logWithCrawlUri($crawlUri, LogLevel::ERROR, 'HTTP Exception ' . $exception->getMessage());
        }
    }
}
