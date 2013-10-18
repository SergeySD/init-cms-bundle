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

use Networking\InitCmsBundle\Model\LayoutBlock as ModelLayoutBlock;

/**
 * Networking\InitCmsBundle\Entity\LayoutBlock
 * @author net working AG <info@networking.ch>
 */
class LayoutBlock extends ModelLayoutBlock
{

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = $this->updatedAt = new \DateTime("now");
    }

    /**
     * Hook on pre-update operations
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime('now');
    }
}
