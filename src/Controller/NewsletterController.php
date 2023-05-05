<?php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Event\NewsletterSubscribedEvent;
use App\Form\NewsletterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class NewsletterController extends AbstractController
{
    #[Route('/newsletter/subscribe', name: 'newsletter_subscribe')]
    public function subscribe(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
    ): Response {
        $newsletter = new Newsletter();
        $form = $this->createForm(NewsletterType::class, $newsletter);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // persister la nouvelle adresse email
            $em->persist($newsletter);
            $em->flush();

            $event = new NewsletterSubscribedEvent($newsletter);
            $dispatcher->dispatch($event, NewsletterSubscribedEvent::NAME);



            $this->addFlash('success', 'Votre inscription a été prise en compte, un email de confirmation vous a été envoyé.');

            return $this->redirectToRoute('app_index');
        }

        // renderForm est dépréciée à partir de Symfony 6.2 et sera supprimé en 7.0, qui gérera automatiquement les form avec render() de base.
        return $this->renderForm('newsletter/subscribe.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/newsletter/confirm/{token}', name: 'newsletter_confirm')]
    public function confirm(Newsletter $newsletter, EntityManagerInterface $em): Response
    // on type-hint Newsletter parce qu'on va lui demander d'aller récupérer le {token} dans Newsletter
    {
        $newsletter
            ->setActive(true)
            ->setToken(null);

        $em->flush();
        $this->addFlash('success', 'Mail vérifié, inscription confirmée. Bisous. lol.');
        return $this->redirectToRoute('app_index');
    }
}
