<?php

namespace Skobkin\Bundle\PointToolsBundle\DTO\Api;

use JMS\Serializer\Annotation as JMSS;

/**
 * @JMSS\ExclusionPolicy("none")
 * @JMSS\AccessType("public_method")
 */
class Auth
{
    /**
     * @var string
     *
     * @JMSS\SerializedName("token")
     * @JMSS\Type("string")
     */
    private $token;

    /**
     * @var string
     *
     * @JMSS\SerializedName("csrf_token")
     * @JMSS\Type("string")
     */
    private $csRfToken;

    /**
     * @var string
     *
     * @JMSS\SerializedName("error")
     * @JMSS\Type("string")
     */
    private $error;

    /**
     * @return string|null
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     *
     * @return Auth
     */
    public function setToken(string $token = null): Auth
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCsRfToken()
    {
        return $this->csRfToken;
    }

    /**
     * @param string $csRfToken
     *
     * @return Auth
     */
    public function setCsRfToken(string $csRfToken = null)
    {
        $this->csRfToken = $csRfToken;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string|null $error
     *
     * @return Auth
     */
    public function setError(string $error = null): Auth
    {
        $this->error = $error;

        return $this;
    }
}