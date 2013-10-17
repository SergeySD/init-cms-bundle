<?php

/**
 * This file is part of the Networking package.
 *
 * (c) net working AG <info@networking.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Networking\InitCmsBundle\Model;


/**
 * Networking\InitCmsBundle\Model\TextInterface
 *
 *
 * @author net working AG <info@networking.ch>
 */
interface TextInterface
{


    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set content
     *
     * @param  string $text
     * @return $this
     */
    public function setText($text);

    /**
     * Get content
     *
     * @return string
     */
    public function getText();

    /**
     * Set createdAt
     *
     * @return $this
     */
    public function setCreatedAt();

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Set updatedAt
     *
     * @param  \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @param array $params
     * @return array
     */
    public function getTemplateOptions($params = array());

    /**
     * @return string
     */
    public function getSearchableContent();

    /**
     * @return array
     */
    public function getAdminContent();

    /**
     * @return string
     */
    public function getContentTypeName();
}
