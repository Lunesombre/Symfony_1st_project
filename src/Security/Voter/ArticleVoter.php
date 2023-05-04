<?php

namespace App\Security\Voter;

use App\Entity\Article;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;


class ArticleVoter extends Voter
{
    public const EDIT = 'Edit article';
    public const VIEW = 'View article';
    public const DELETE = 'Delete article';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!$subject instanceof Article) {
            return false;
        }
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Article;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        if (!$subject instanceof Article) {
            throw new LogicException('Wrong subject type provided');
        }

        // l'admin a le droit de modifier et voir les articles quoiqu'il arrive
        if ($this->security->isGranted('ROLE_ADMIN') || $subject->getAuthor() === $user) {
            return true;
        }

        // ancienne version, pas nécessaire vu qu'on vérifie la même chose pour les trois méthodes, à savoir si l'utilisateur connecté est l'auteur
        // $article = $subject;
        // // ... (check conditions and return true to grant permission) ...
        // switch ($attribute) {
        //     case self::EDIT:
        //         // logic to determine if the user can EDIT
        //         return $this->canEdit($article, $user);
        //         // return true or false
        //         break;
        //     case self::VIEW:
        //         // logic to determine if the user can VIEW
        //         return $this->canView($article, $user);
        //         // return true or false
        //         break;
        //     case self::DELETE:
        //         return $this->canDelete($article, $user);
        //         break;
        // }

        return false;
    }

    private function canEdit(Article $article, User $user): bool
    {
        return $user === $article->getAuthor();
    }

    private function canView(Article $article, User $user): bool
    {
        if ($this->canEdit($article, $user)) {
            return true;
        }
        // dans la doc:  return !$article->isPrivate(); mais j'ai pas trop compris
        return false;
    }

    private function canDelete(Article $article, User $user): bool
    {
        return $user === $article->getAuthor();
    }
}
