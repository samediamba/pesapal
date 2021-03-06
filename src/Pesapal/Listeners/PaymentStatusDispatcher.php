<?php
/**
 * Created by PhpStorm.
 * User: jacob
 * Date: 12/11/14
 * Time: 11:49
 */

namespace Pesapal\Listeners;

use BigName\EventDispatcher\Event;
use BigName\EventDispatcher\Listener;
use Pesapal\Events\IsAPaymentEvent;
use Pesapal\Events\PaymentEvent;
use Pesapal\Contracts\PaymentListener;

class PaymentStatusDispatcher implements Listener
{
    protected $payment;
    protected $promise;

    function __construct(PaymentListener $promise)
    {
        $this->setPromise($promise);
    }

    /**
     * @param PaymentListener $promise
     */
    public function setPromise(PaymentListener $promise)
    {
        $this->promise = $promise;
    }


    /**
     * @param IsAPaymentEvent $event
     */
    public function handle(Event $event)
    {
        $this->whenSuccessfulPayment($event);
        $this->whenPaymentFailed($event);
        $this->whenProgressUpdate($event);
        $this->whenInvalidPayment($event);

    }


    function whenSuccessfulPayment(IsAPaymentEvent $event)
    {
        if ($this->isEventStatus($event, "COMPLETED")) {
            $this->promise->paid($event->getPayment());
        }
    }

    function whenPaymentFailed(IsAPaymentEvent $event)
    {
        if ($this->isEventStatus($event, "FAILED")) {
            $this->promise->failed($event->getPayment());
        }
    }

    function whenInvalidPayment(IsAPaymentEvent $event)
    {
        if ($this->isEventStatus($event, "INVALID")) {
            $this->promise->invalid($event->getPayment());
        }
    }

    function whenProgressUpdate(IsAPaymentEvent $event)
    {
        if ($this->isEventStatus($event, "PROGRESS")) {
            $this->promise->inProgress($event->getPayment());
        }
    }

    function isEventStatus(IsAPaymentEvent $event, $status)
    {
        return $event->getStatus() == $status;
    }

    /**
     * @return PaymentListener
     */
    public function getPromise()
    {
        return $this->promise;
    }


}