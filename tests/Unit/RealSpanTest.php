<?php

namespace ZipkinTests\Unit;

use PHPUnit_Framework_TestCase;
use Zipkin\Endpoint;
use Zipkin\Propagation\DefaultSamplingFlags;
use Zipkin\RealSpan;
use Zipkin\Recorder;
use Zipkin\Reporter;
use Zipkin\Timestamp;
use Zipkin\Annotations;
use Zipkin\TraceContext;

class RealSpanTest extends PHPUnit_Framework_TestCase
{
    const TEST_NAME = 'test_span';
    const TEST_KIND = 'ab';
    const TEST_START_TIMESTAMP = 1472470996199000;

    public function testCreateRealSpanSuccess()
    {
        $context = TraceContext::createAsRoot();
        $recorder = $this->prophesize(Recorder::class);
        $span = RealSpan::create($context, $recorder->reveal());
        $this->assertEquals($context, $span->getContext());
    }

    public function testSetNameSuccess()
    {
        $context = TraceContext::createAsRoot();
        $recorder = $this->prophesize(Recorder::class);
        $recorder->setName($context, self::TEST_NAME)->shouldBeCalled();
        $span = RealSpan::create($context, $recorder->reveal());
        $span->setName(self::TEST_NAME);
    }

    public function testSetKindSuccess()
    {
        $context = TraceContext::createAsRoot();
        $recorder = $this->prophesize(Recorder::class);
        $recorder->setKind($context, self::TEST_KIND)->shouldBeCalled();
        $span = RealSpan::create($context, $recorder->reveal());
        $span->setKind(self::TEST_KIND);
    }

    public function testSetRemoteEndpointSuccess()
    {
        $context = TraceContext::createAsRoot();
        $remoteEndpoint = Endpoint::createAsEmpty();
        $recorder = $this->prophesize(Recorder::class);
        $recorder->setRemoteEndpoint($context, $remoteEndpoint)->shouldBeCalled();
        $span = RealSpan::create($context, $recorder->reveal());
        $span->setRemoteEndpoint($remoteEndpoint);
    }

    public function testAnnotateSuccess()
    {
        $timestamp = Timestamp\now();
        $value = Annotations\WIRE_SEND;
        $context = TraceContext::createAsRoot();
        $recorder = $this->prophesize(Recorder::class);
        $recorder->annotate($context, $timestamp, $value)->shouldBeCalled();
        $span = RealSpan::create($context, $recorder->reveal());
        $span->annotate($value, $timestamp);
    }

    public function testAnnotateFailsDueToInvalidValue()
    {
        $timestamp = Timestamp\now();
        $value = new \stdClass;
        $context = TraceContext::createAsRoot();
        $recorder = $this->prophesize(Recorder::class);
        $recorder->annotate($context, $timestamp, $value)->shouldNotBeCalled();
        $span = RealSpan::create($context, $recorder->reveal());
        $this->expectException(\InvalidArgumentException::class);
        $span->annotate($value, $timestamp);
    }
}
