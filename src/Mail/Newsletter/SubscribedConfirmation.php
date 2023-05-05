<?php

namespace App\Mail\Newsletter;

use App\Entity\Newsletter;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SubscribedConfirmation
{
    public function __construct(
        private MailerInterface $mailer,
        private UrlGeneratorInterface $urlGenerator
        )
    {
        
    }

    public function send(Newsletter $newsletter)
    {
        $email = (new Email())
        ->from('admin@1st_project.com')
        ->to($newsletter->getEmail())
        ->subject('Inscription à la newsletter')
        ->text('Bonjour '.$newsletter->getEmail().'! Veuillez suivre ce lien pour finaliser votre inscription : '.$this->urlGenerator->generate(
            'newsletter_confirm',
            ['token' => $newsletter->getToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        ));


    $this->mailer->send($email);
    }
}