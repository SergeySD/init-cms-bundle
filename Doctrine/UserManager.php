<?php
/**
 * This file is part of the init_cms_sandbox package.
 *
 * (c) net working AG <info@networking.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Networking\InitCmsBundle\Doctrine;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Doctrine\UserManager as FOSDoctrineUserManager;

/**
 * @author Yorkie Chadwick <y.chadwick@networking.ch>
 */
class UserManager extends FOSDoctrineUserManager
{



    public function getLatestActivity()
    {
        $tenMinutesAgo = new \DateTime('- 10 minutes');
        $qb = $this->repository->createQueryBuilder('u');
        $qb->where('u.updatedAt >= :datetime')
            ->setParameter(':datetime', $tenMinutesAgo);

        return $qb->getQuery()->getResult();
    }

}
