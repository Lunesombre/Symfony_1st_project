<?php

namespace App\Security\Voter;

use App\Entity\Article;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ArticleVoter extends Voter
{
    public const EDIT = 'Edit article';
    public const VIEW = 'View article';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!$subject instanceof \App\Entity\Article) {
            return false;
        }
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof \App\Entity\Article;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface)
        // NB: pq pas une instance de User -> c'est comme ça depuis Symfony 5.4
        {
            return false;
        }
        
        // l'admin a le droit de modifier et voir les articles quoiqu'il arrive
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $article = $subject;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // logic to determine if the user can EDIT
                return $this->canEdit($article, $user);
                // return true or false
                break;
            case self::VIEW:
                // logic to determine if the user can VIEW
                return $this->canView($article, $user);
                // return true or false
                break;
        }

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
}
