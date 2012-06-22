<?php

namespace Liip\MagentoBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class MagentoUserProvider implements UserProviderInterface
{
    protected $class;

    protected $loginType;

    public function __construct($class, $loginType)
    {
        $this->class = $class;
        $this->loginType = $loginType;
    }

    /**
     * @param $id int Magento user ID
     */
    public function loadUserByUsername($id)
    {
        if ('admin' == $this->loginType) {
            $user = \Mage::getModel('admin/user')->load($id);
            $roleId = $user->getRole()? $user->getRole()->getId(): null;

            if ($user->getId()) {
                return new $this->class($user->getId(), $user->getEmail(), $user->getFirstname(), $user->getLastname(), $roleId, $user->getUsername(), true);
            }
        } else {
            $customer = \Mage::getModel('customer/customer')->load($id);

            if ($customer->getId()) {
                return new $this->class($customer->getId(), $customer->getEmail(), $customer->getFirstname(), $customer->getLastname(), $customer->getGroupId());
            }
        }

        throw new UsernameNotFoundException(sprintf('User "%s" not found.', $id));
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof MagentoUserInterface) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getId());
    }

    public function supportsClass($class)
    {
        return $class === $this->class;
    }
}
