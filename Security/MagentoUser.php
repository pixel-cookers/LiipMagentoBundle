<?php

namespace Liip\MagentoBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class MagentoUser implements MagentoUserInterface
{
    protected $id;
    protected $username;
    protected $email;
    protected $firstname;
    protected $lastname;
    protected $groupId;
    protected $isAdmin;

    public function __construct($id, $email, $firstname, $lastname, $groupId, $username = null, $isAdmin = false)
    {
        $this->id = $id;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->groupId = $groupId;
        $this->username = $username;
        $this->isAdmin = $isAdmin;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
    }

    public function getId() {
        return $this->id;
    }

    public function getGroupId() {
        return $this->groupId;
    }

    public function equals(UserInterface $user)
    {
        if ($user instanceof MagentoUser) {
            return $user->getId() === $user->id;
        }
        return $user->getUsername() === $this->email;
    }

    public function __toString()
    {
        return $this->getEmail();
    }

    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Password is not stored.
     *
     * @return null
     */
    public function getPassword()
    {
        return null;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getRoles()
    {
        if ($this->isAdmin) {
            return array('ROLE_MAGENTO_ADMIN_' . $this->groupId);
        }
        return array('ROLE_MAGENTO_' . $this->groupId);
    }

    public function hasRole($role)
    {
        return in_array((string) $role, $this->getRoles());
    }

    public function __sleep()
    {
        return array('id', 'email', 'firstname', 'lastname', 'groupId');
    }
}
