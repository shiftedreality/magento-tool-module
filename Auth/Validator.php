<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\RemoteManage\Auth;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Validation\ValidationException;

class Validator
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
    public function validate()
    {
        $authPublicKey = $this->request->getParam('public_key');
        $authSign = $this->request->getParam('sign');

        if (!$authPublicKey || !$authSign) {
            throw new ValidationException(__(
                'Missing auth credentials'
            ));
        }

        $envPrivateKey = $this->deploymentConfig->get('remote/private_key');
        $envPublicKey = $this->deploymentConfig->get('remote/public_key');

        if (!$envPrivateKey || !$envPublicKey) {
            throw new ValidationException(__(
                'Env variables are not defined'
            ));
        }

        if ($authPublicKey !== $envPublicKey) {
            throw new ValidationException(__(
                'Keys are not valid'
            ));
        }

        $params = $this->request->getParams();

        unset(
            $params['sign'],
            $params['public_key'],
            $params['type']
        );

        $callback = function ($item) use (&$callback) {
            if (is_array($item)) {
                return array_filter($item, $callback);
            }
            if (!empty($item)) {
                return true;
            }

            return null;
        };

        $params = array_filter($params, $callback);

        ksort($params);

        $envSign = sha1($this->toString($params) . $envPublicKey . $envPrivateKey);

        if ($authSign !== $envSign) {
            throw new ValidationException(__(
                'Auth failed'
            ));
        }
    }

    /**
     * @param array $params
     * @param string $string
     * @return string
     */
    private function toString(array $params, $string = ''): string
    {
        foreach ($params as $key => $value) {
            if (is_array($value) && $value) {
                $string .= $this->toString($value, $string);
            } elseif (is_string($value) && $value) {
                $string .= $key . '=' . $value;
            }
        }

        return $string;
    }
}
