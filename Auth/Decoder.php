<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\RemoteManage\Auth;

use Firebase\JWT\JWT;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Validation\ValidationException;

/**
 * Validator for requests
 */
class Decoder
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var DeploymentConfig
     */
    private $deploymentConfig;

    /**
     * @param RequestInterface $request
     * @param DeploymentConfig $deploymentConfig
     */
    public function __construct(RequestInterface $request, DeploymentConfig $deploymentConfig)
    {
        $this->request = $request;
        $this->deploymentConfig = $deploymentConfig;
    }

    /**
     * Validate request params.
     *
     * @throws ValidationException
     */
    public function decode()
    {
        $key = $this->deploymentConfig->get('remote/key');
        $payload = $this->request->getParam('token');

        if (!$payload) {
            throw new ValidationException(__('Payload not provided'));
        }

        return JWT::decode($payload, $key, ['HS256']);
    }
}
