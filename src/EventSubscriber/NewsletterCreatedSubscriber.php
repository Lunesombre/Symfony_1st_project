<?php

namespace App\EventSubscriber;

use App\Event\NewsletterSubscribedEvent;
use App\Mail\Newsletter\SubscribedConfirmation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Notifier\Bridge\Discord\DiscordOptions;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordEmbed;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordFieldEmbedObject;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordFooterEmbedObject;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordMediaEmbedObject;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\String\ByteString;

class NewsletterCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private SubscribedConfirmation $confirmationEmail,
        private ChatterInterface $chatter
    ) {
    }

    public function sendConfirmationEmail(NewsletterSubscribedEvent $event): void
    {
        $newsletter = $event->getNewsletter();

        $newsletter->setToken(
            ByteString::fromRandom(32)->toString()
        );

        $this->em->flush();
        $this->confirmationEmail->send($newsletter);
    }

    public function sendDiscordNotification(NewsletterSubscribedEvent $event): void
    {
        $newsletter = $event->getNewsletter();
        $chatMessage = new ChatMessage('');
        // Create Discord Embed
        $discordOptions = (new DiscordOptions())
            ->username('Human Botster')
            ->addEmbed((new DiscordEmbed())
                    ->color(2021216)
                    ->title('Nouvel email dans la Newsletter !')
                    ->thumbnail((new DiscordMediaEmbedObject())
                        ->url('https://store-images.s-microsoft.com/image/apps.46116.70628353720390187.c5ec2284-1a6e-4ed0-a094-b54b14b8d466.f01d3b8d-41e1-42bc-b322-443ee5b1f390'))
                    // ->addField((new DiscordFieldEmbedObject())
                    //     ->name('Track')
                    //     ->value('[Common Ground](https://open.spotify.com/track/36TYfGWUhIRlVjM8TxGUK6)')
                    //     ->inline(true)
                    // )
                    ->addField((new DiscordFieldEmbedObject())
                            ->name('Email')
                            ->value($newsletter->getEmail())
                            ->inline(true)
                    )
                    // ->addField((new DiscordFieldEmbedObject())
                    //     ->name('Album')
                    //     ->value('Dawn Dance')
                    //     ->inline(true)
                    // )
                    ->footer((new DiscordFooterEmbedObject())
                            ->text('HB Newsletter')
                            ->iconUrl('https://cdn-icons-png.flaticon.com/512/1042/1042744.png')
                    )
            );

        // Add the custom options to the chat message and send the message
        $chatMessage->options($discordOptions);

        $this->chatter->send($chatMessage);
    }
    public static function getSubscribedEvents(): array
    {
        return [
            NewsletterSubscribedEvent::NAME
            => [
                ['sendConfirmationEmail', 10],
                ['sendDiscordNotification', 5]
            ]
        ];
    }
}
