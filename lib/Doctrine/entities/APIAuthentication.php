<?php
/*
 * Copyright (C) 2016 STFC
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use Doctrine\DBAL\Types\BooleanType;

/**
  * The APIAuthenticationEntity defines a credential that can be used to makce
  * changes throught he API for a specific {@see Site}. Each site can have
  * 0-many APIAuthentication entities associated with it. Each entity has an ID,
  * type, identifier (e.g. DN for x509) and parent site.
  *
  * @author George Ryall (github.com/GRyall)
  *
  * @Entity @Table(name="APIAuthenticationEntities",uniqueConstraints={@UniqueConstraint(name="siteIdentifier", columns={"parentSite_id", "type", "identifier"})})
  */
   class APIAuthentication
  {
    /** @Id @Column(type="integer") @GeneratedValue  */
    protected $id;

    /**
     * One site may have zero-to-many APIAuthentication entities
     *
     * @ManyToOne(targetEntity="Site", inversedBy="APIAuthenticationEntities")
     * @JoinColumn(name="parentSite_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parentSite = null;

    /**
    * Defines the type of the authentication entity (e.g 'x509').
    * @Column(type="string", nullable=false) */
    protected $type = null;

    /**
    * The unique identifier for the authentication (e.g. DN for x509)
    * @Column(type="string", nullable=false) */
    protected $identifier = null;

    /**
     * The registered User that added this APIAuthentication entity
     * One user may have zero or more APIAuthentication entities.
     * If renewed, this will be the user that renewed it.
     *
     * @ManyToOne(targetEntity="User", inversedBy="APIAuthenticationEntities")
     * @JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user = null;

     /**
      * When this APIAuthentication entity was first used/registered.
      * If renewed, this will be the time of renewal.
      * @Column(type="datetime", nullable=false)
      */
    protected $createTime = null;

    /**
     * When this APIAuthentication entity was most recently used.
     * @Column(type="datetime", nullable=true)
     */
    protected $lastUsedTime = null;

    /**
     * @Column(type="boolean", nullable=false)
     */
    protected $allowAPIWrite = true;

    /**  */
    public function __construct() {
        //TODO-irn: Verify this is the correct time value to store in the db.
        $this->createTime = new DateTime("now");
    }
    /**
     * Get PK of Authentication entity
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get the authentication enties parent site
     * @return \Site
     */
    public function getParentSite() {
        return $this->parentSite;
    }

    /**
     * Get they autentication type of this entity
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Get the unique identifier for this autnentication entity.
     * @return string
     */
    public function getIdentifier() {
        return $this->identifier;
    }

    /**
     * @return \User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @return \DateTime
     */
    public function getFirstUsedTime () {
        return $this->firstUsedTime;
    }

    /**
     * @return \DateTime
     */
    public function getLastUsedTime () {
        return $this->lastUsedTime;
    }

    /**
     * @return bool $allowAPIWrite
     */
    public function getAllowAPIWrite () {
        return $this->allowAPIWrite;
    }

    /**
     * Set the type of this authentication entity
     * @param string $name
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * Set the unique identifier of this authentication entity.
     * @param string $identifier
     */
    public function setIdentifier($identifier) {
        $this->identifier = $identifier;
    }

    /**
     * Called to update the creation time field when _setUser is invoked.
     * @return \DateTime $time
     */
    protected function getCreateTime(\DateTime $time) {
        return $this->createTime;
    }

    /**
     * @param \DateTime $time
     */
    public function setLastUsedTime (\DateTime $time) {
        $this->lastUsedTime = $time;
    }
    /**
     * @param bool $allowAPIWrite
     */
    public function setAllowAPIWrite ($allowWrite) {
        if (!is_bool($allowWrite)) {
            throw new LogicException("Expected bool, received".gettype($allowWrite));
        }
        $this->allowAPIWrite = $allowWrite;
    }

    /**
     * Do not call in client code, always use the opposite
     * <code>$site->addAuthenticationEntityDoJoin($authenticationEntity)</code>
     * instead which internally calls this method to keep the bidirectional
     * relationship consistent.
     *
     * This is the OWNING side of the ORM relationship so this method WILL
     * establish the relationship in the database.
     *
     * @param \Site $site
     */
    public function _setParentSite(\Site $site){
        $this->parentSite = $site;
    }
    /**
    * @param \User $user
    */
    public function _setUser(\User $user) {
        $this->user = $user;
    }
  }
