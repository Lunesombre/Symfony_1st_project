<?php

namespace App\EventSubscriber;

use App\Mail\Newsletter\SubscribedConfirmation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\String\ByteString;

class NewsletterCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $em, private SubscribedConfirmation $confirmationEmail)
    {
    }

    public function onNewsletterSubscribed($event): void
    {
        $newsletter = $event->getNewsletter();

        $newsletter->setToken(
            ByteString::fromRandom(32)->toString()
        );

        $this->em->flush();
        $this->confirmationEmail->send($newsletter);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'newsletter.subscribed' => 'onNewsletterSubscribed',
        ];
    }
}
