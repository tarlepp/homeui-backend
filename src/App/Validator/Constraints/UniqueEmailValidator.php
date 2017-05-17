<?php
declare(strict_types=1);
/**
 * /src/App/Validator/Constraints/UniqueEmailValidator.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace App\Validator\Constraints;

use App\DTO\Console\Interfaces\User as UserInterface;
use App\Repository\User as UserRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class UniqueEmailValidator
 *
 * @package App\Validator\Constraints
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
class UniqueEmailValidator extends ConstraintValidator
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * UniqueUsernameValidator constructor.
     *
     * @param   EntityRepository    $repository
     */
    public function __construct(EntityRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @throws  NonUniqueResultException
     *
     * @param   UserInterface               $value      The value that should be validated
     * @param   Constraint|UniqueUsername   $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$this->repository->isEmailAvailable($value->getEmail(), $value->getId())) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}
