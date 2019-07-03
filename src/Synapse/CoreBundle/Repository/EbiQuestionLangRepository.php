<?php
namespace Synapse\CoreBundle\Repository;

use Doctrine\ORM\Query\Expr\Expr;
use Doctrine\ORM\Query\Expr\Join;
use JMS\Serializer\Tests\Fixtures\Publisher;
use Synapse\CoreBundle\Entity\EbiQuestionLang;

class EbiQuestionLangRepository extends SynapseRepository
{
    const REPOSITORY_KEY = 'SynapseCoreBundle:EbiQuestionsLang';
}