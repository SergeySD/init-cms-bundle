<?php
/**
 * This file is part of the Networking package.
 *
 * (c) net working AG <info@networking.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Networking\InitCmsBundle\Document;

use Doctrine\ORM\EntityRepository;

/**
 * HelpTextRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class HelpTextRepository extends EntityRepository
{
    /**
     * @param $translationKey
     * @param $locale
     * @return object
     */
    public function getHelpTextByKeyLocale($translationKey, $locale )
    {
        $helpText = $this->findOneBy(array('translationKey' => $translationKey, 'locale' => $locale));
        if($helpText === NULL)
        {
            $helpText = $this->findOneBy(array('translationKey' => 'not_found', 'locale' => $locale));
        }
        return $helpText;
    }

    /**
     * @param $translationKey
     * @param $locale
     * @return array
     */
    public function searchHelpTextByKeyLocale($translationKey, $locale )
    {
        $qb = $this->createQueryBuilder('h');
        $qb->where('h.locale = :locale')
            ->andWhere('h.translationKey LIKE :translationKey')
            ->orderBy('h.id', 'asc')
            ->setParameter(':locale', $locale)
            ->setParameter(':translationKey', $translationKey."%");

        return $qb->getQuery()->getResult();
    }
}
