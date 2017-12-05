<?php

namespace DAO;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Entity\User;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * Description of UserDAO
 *
 * @author Etudiant
 */
class UserDAO extends \SimpleDAO\DAO implements UserProviderInterface
{

    // userDAO utilise userproviderinterface de tout ce namespace (\Symfony\component...)  
    public function loadUserByUsername($username)
    {
        // retourne un utilisateur grace a un username
        // SELECT * FROM user WHERE username = ? LIMIT 1
        // bindValue(1, $username) : le 1 siginifie le premier point d'interrogation trouver
        $user = $this->findOne(array('username = ?' => $username));
        //findOne : trouver un user, s'arrête a la premire entité
        
        if(!$user){
            throw new UsernameNotFoundException("User with username $username does not exist");
            // exception généré par le service de sécurité
            // Si pas d'user on envoi l'exception
        }
        
        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        // refreshUser = rafraichit l'information d'un utilisateur

        if (!$user instanceof User) {
            // User = Entity\User
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        // supports class = renvoie un booléen si mon nom de class est bien un user
        return $class === '\Entity\User';
    }


}
