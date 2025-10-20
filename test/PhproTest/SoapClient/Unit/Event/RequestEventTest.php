<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Unit;

use Phpro\SoapClient\Event\RequestEvent;
use Phpro\SoapClient\Type\RequestInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class RequestEventTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var RequestInterface & ObjectProphecy
     */
    private RequestInterface $request;

    private RequestEvent $event;

    protected function setUp(): void
    {
        $this->request = $this->prophesize(RequestInterface::class)->reveal();
        $this->event = new RequestEvent('method', $this->request);
    }

    #[Test]
    public function it_contains_a_request(): void
    {
        self::assertSame($this->request, $this->event->getRequest());
    }

    #[Test]
    public function it_contains_a_method(): void
    {
        self::assertSame('method', $this->event->getMethod());
    }

    #[Test]
    public function it_can_overwrite_request(): void
    {
        $new = $this->prophesize(RequestInterface::class)->reveal();
        $this->event->registerRequest($new);

        self::assertSame($new, $this->event->getRequest());
        self::assertNotSame($this->request, $this->event->getRequest());
    }
}
