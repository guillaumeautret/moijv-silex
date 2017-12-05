<?php

namespace Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Constraint\UniqueEntity;
use Symfony\Component\Validator\Constraint;
/**
 * Description of UniqueEntityValidator
 *
 * @author Etudiant
 */
class UniqueEntityValidator extends ConstraintValidator
{   
    public function validate($value, Constraint $constraint)
    {
        $field = $constraint->getField();
        $dao = $constraint->getDao();
        
        $entity = $dao->findOne(["$field = ?" => $value]);
        
        if($entity){
            $this->content->buildViolation($constraint->message)
                    ->setParameter('{{column}}', $field)
                    ->addViolation();
        }
    }
}
