<?php

namespace Synapse\CoreBundle\Entity;


interface BaseEntityInterface {

    /**
     * Returns the entity identifier
     * @return int identifier
     */
    public function getId();

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return BaseEntity
     */
    public function setCreatedAt($createdAt);

    /**
     * Get createdAt
     *
     * @return int
     */
    public function getCreatedAt();

    /**
     * Set createdBy
     *
     * @param int $createdBy
     * @return BaseEntity
     */
    public function setCreatedBy($createdBy);

    /**
     * Get createdBy
     *
     * @return int
     */
    public function getCreatedBy();

    /**
     * Set modifiedAt
     *
     * @param \DateTime $modifiedAt
     * @return BaseEntity
     */
    public function setModifiedAt($modifiedAt);

    /**
     * Get modifiedAt
     *
     * @return int
     */
    public function getModifiedAt();

    /**
     * Set modifiedBy
     *
     * @param int $modifiedBy
     * @return BaseEntity
     */
    public function setModifiedBy($modifiedBy);

    /**
     * Get modifiedBy
     *
     * @return int
     */
    public function getModifiedBy();

    /**
     * Set deletedBy
     *
     * @param int $deletedBy
     * @return BaseEntity
     */
    public function setDeletedBy($deletedBy);

    /**
     * Get deletedBy
     *
     * @return int
     */
    public function getDeletedBy();

    /**
     * Set deletedAt
     *
     * @param \DateTime $modifiedAt
     * @return BaseEntity
     */
    public function setDeletedAt($deletedAt);

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt();

}