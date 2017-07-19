<?php

declare(strict_types=1);

/*
 * This file is part of CSBill project.
 *
 * (c) 2013-2017 Pierre du Plessis <info@customscripts.co.za>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace CSBill\SettingsBundle\Twig\Extension;

use CSBill\ClientBundle\Entity\Address;
use CSBill\SettingsBundle\Exception\InvalidSettingException;
use CSBill\SettingsBundle\SystemConfig;
use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\Exception\TableNotFoundException;

class SettingsExtension extends \Twig_Extension
{
    /**
     * @var SystemConfig
     */
    private $config;

    public function __construct(SystemConfig $config)
    {
        $this->config = $config;
    }

    public function getFunctions()
    {
        return [
            new \Twig_Function('setting', [$this, 'getSetting']),
            new \Twig_Function('address', [$this, 'renderAddress']),
        ];
    }

    public function renderAddress(array $address)
    {
        return (string) Address::fromArray($address);
    }

    public function getSetting(string $setting, $default = null, $decode = false)
    {
        try {
            $setting = $this->config->get($setting);

            if ($decode) {
                return json_decode($setting, true);
            }

            return $setting;
        } catch (InvalidSettingException | TableNotFoundException | ConnectionException $e) {
            return $default;
        }
    }
}