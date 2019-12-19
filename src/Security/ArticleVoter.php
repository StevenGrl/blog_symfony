<?php

namespace App\Security;

use App\Entity\Article;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ArticleVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof Article && in_array($attribute, ['view', 'edit', 'like']);
    }

    protected function voteOnAttribute($attribute, $article, TokenInterface $token)
    {
        // Tout le monde peut voir un article publié
        if ($article->getPublished() && 'view' === $attribute) {
            return true;
        }

        $userId = $token->getUser()->getId();

        $owner = $article->getUser();

        if ($owner instanceof User) {
            // Le propriétaire d'un article peut voir ses articles
            if ('view' === $attribute && $userId === $owner->getId()) {
                return true;
            }

            //Seul un utilisateur non propriétaire de l'article peut liker l'article
            if ('like' === $attribute && $userId != $owner->getId()) {
                return true;
            }

            // Seul le propriétaire de l'article peut le modifier ou le supprimer
            if ('edit' === $attribute && $userId === $owner->getId()) {
                return true;
            }
        }

        return false;
    }

}