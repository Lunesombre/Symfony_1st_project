<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/articles/{id}', name: 'article_item')]
    public function display(Article $article): Response
    {
        return $this->render(
            'article/item.html.twig',
            [
                'article' => $article,
            ]
        );
    }
}
