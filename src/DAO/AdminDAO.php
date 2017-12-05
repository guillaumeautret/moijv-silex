<?php

namespace DAO;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * Description of UserDAO
 *
 * @author Etudiant
 */
class AdminDAO extends UserDAO
{

   protected $tableName = 'user';
    
    public function loadUserByUsername($username)
    {
        // retourne un utilisateur grace a un username
        // SELECT * FROM user WHERE username = ? LIMIT 1
        // bindValue(1, $username) : le 1 siginifie le premier point d'interrogation trouver
        $user = $this->findOne(array(
            'username = ?' => $username,
            'role LIKE ?' => '%ROLE_ADMIN%'
        ));
        //findOne : trouver un user, s'arrête a la premire entité
        
        if(!$user){
            throw new UsernameNotFoundException("User with username $username does not exist");
            // exception généré par le service de sécurité
            // Si pas d'user on envoi l'exception
        }
        
        return $user;
    }
}
