<?php
namespace App\Security;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;


class PostVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if(!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof Post) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if(!$user instanceof User) {
            // the user must logged in. If not, deny access
            return false;
        }

        // Thanks to the previous method 'supports', we know that $subject is a Post object
        /** @var Post $post */
        $post = $subject;

        switch($attribute) {
            case self::VIEW:
                return $this->canView($post, $user);
            case self::EDIT:
                return $this->canEdit($post, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Post $post, User $user) 
    {
        // if they can edit, they can view
        if ($this->canEdit($post, $user)) {
            return true;
        }

        // the Post object could have, for example, a method isPrivate()
        // that checks a boolean $private property
        return !$post->isPrivate();
    }

    private function canEdit(Post $post, User $user)
    {
        // this assumes that the data object has a getUser() method
        // to get the entity of the user who owns this data object
        return $user === $post->getUser();
    }
}
