<?php

namespace Beelab\UserBundle\Tests\Event;

use Beelab\UserBundle\Event\FormEvent;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class FormEventTest extends TestCase
{
    public function testGetForm(): void
    {
        $form = $this->getMockBuilder('Symfony\Component\Form\FormInterface')->getMock();
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();
        $formEvent = new FormEvent($form, $request);
        $this->assertEquals($form, $formEvent->getForm());
    }

    public function testGetRequest(): void
    {
        $form = $this->getMockBuilder('Symfony\Component\Form\FormInterface')->getMock();
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();
        $formEvent = new FormEvent($form, $request);
        $this->assertEquals($request, $formEvent->getRequest());
    }

    public function testSetResponse(): void
    {
        $form = $this->getMockBuilder('Symfony\Component\Form\FormInterface')->getMock();
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();
        $response = $this->getMockBuilder('Symfony\Component\HttpFoundation\Response')->getMock();
        $formEvent = new FormEvent($form, $request);
        $formEvent->setResponse($response);
        $this->assertEquals($response, $formEvent->getResponse());
    }
}
