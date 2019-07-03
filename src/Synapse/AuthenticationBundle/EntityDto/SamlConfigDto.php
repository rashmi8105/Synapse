<?php
namespace Synapse\AuthenticationBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for SAML authentication settings
 * *
 *
 * @package Synapse\AuthenticationBundle\EntityDto
 *
 */
class SamlConfigDto
{

    /**
     * entity_id
     *
     * @var string @JMS\Type("string")
     *
     * @Assert\Length(max = 255,
     *      maxMessage = "entity ID attribute cannot be longer than {{ limit }} characters long"
     * )
     *
     */
    private $entityId;

    /**
     * federation_metadata
     *
     * @var string @JMS\Type("string")
     *
     * @Assert\Length(max = 255,
     *      maxMessage = "federation metadata attribute cannot be longer than {{ limit }} characters long"
     * )
     *
     */
    private $federation_metadata;

    /**
     * sso_url
     *
     * @var string @JMS\Type("string")
     *
     * @Assert\Length(max = 255,
     *      maxMessage = "sso url attribute cannot be longer than {{ limit }} characters long"
     * )
     *
     */
    private $ssoUrl;

    /**
     * public_key_file
     *
     * @var string @JMS\Type("string")
     *
     * @Assert\Length(max = 255,
     *      maxMessage = "public key file attribute cannot be longer than {{ limit }} characters long"
     * )
     *
     */
    private $publicKeyFile;

    /**
     * settings_override
     *
     * @var string @JMS\Type("string")
     *
     * @Assert\Length(max = 255,
     *      maxMessage = "settings override attribute cannot be longer than {{ limit }} characters long"
     * )
     *
     */
     private $settingsOverride;
     



    /**
     * Sets the entity_id
     *
     * @param string @JMS\Type("string") $entityId the entity id
     *
     * @return self
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * Gets the entity_id
     *
     * @return string @JMS\Type("string")
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Sets the federation_metadata
     *
     * @param string @JMS\Type("string") $federation_metadata the federation metadata
     *
     * @return self
     */
    public function setFederationMetadata($federation_metadata)
    {
        $this->federation_metadata = $federation_metadata;

        return $this;
    }

    /**
     * Gets the federation_metadata
     *
     * @return string @JMS\Type("string")
     */
    public function getFederationMetadata()
    {
        return $this->federation_metadata;
    }

    /**
     * Sets the sso_url
     *
     * @param string @JMS\Type("string") $ssoUrl the sso url
     *
     * @return self
     */
    public function setSsoUrl($ssoUrl)
    {
        $this->ssoUrl = $ssoUrl;

        return $this;
    }

    /**
     * Gets the sso_url
     *
     * @return string @JMS\Type("string")
     */
    public function getSsoUrl()
    {
        return $this->ssoUrl;
    }

    /**
     * Sets the public_key_file
     *
     * @param string @JMS\Type("string") $publicKeyFile the public key file
     *
     * @return self
     */
    public function setPublicKeyFile($publicKeyFile)
    {
        $this->publicKeyFile = $publicKeyFile;

        return $this;
    }

    /**
     * Gets the public_key_file
     *
     * @return string @JMS\Type("string")
     */
    public function getPublicKeyFile()
    {
        return $this->publicKeyFile;
    }

    /**
    * Sets the settings_override
    *
    * @param string @JMS\Type("string") $settingsOverride setting json array
    *
    * @return self
    */
    public function setSettingsOverride($settingsOverride)
    {
        $this->settingsOverride = $settingsOverride;

        return $this;
    }
 
    /**
    * Gets the settings_override
    *
    * @return string @JMS\Type("string")
    */
    public function getSettingsOverride()
    {
        return $this->settingsOverride;
    }
}
