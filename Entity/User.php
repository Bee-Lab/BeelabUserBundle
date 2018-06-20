<?php

namespace Beelab\UserBundle\Entity;

use Beelab\UserBundle\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User.
 *
 * @ORM\MappedSuperclass
 * @UniqueEntity(fields={"email"}, groups={"create", "update"})
 */
abstract class User implements UserInterface, EquatableInterface, \Serializable
{
    /**
     * @var array
     */
    protected static $roleLabels = [
        'ROLE_ADMIN' => 'admin',
        'ROLE_USER' => 'user',
    ];

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(unique=true)
     * @Assert\NotBlank(groups={"create", "update"})
     * @Assert\Email(groups={"create", "update"})
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(length=32, nullable=true)
     */
    protected $salt;

    /**
     * @var string
     *
     * @ORM\Column(length=255)
     */
    protected $password;

    /**
     * Plain password.
     *
     * @var string
     *
     * @Assert\NotBlank(groups={"create"})
     * @Assert\Length(min=6, max=100, groups={"create", "update", "password"})
     */
    protected $plainPassword;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $roles = [];

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean", options={"default"=1})
     * @Assert\Type(type="bool", groups={"create", "update"})
     */
    protected $active = true;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     * @Assert\DateTime(groups={"create", "update"})
     */
    protected $lastLogin = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->salt = md5(uniqid(null, true));
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param array $roles
     *
     * @return User
     */
    public function setRoles(array $roles)
    {
        $this->roles = [];
        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // we need to make sure to have at least one role
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return $roles;
    }

    /**
     * @param string $role
     *
     * @return User
     */
    public function addRole($role)
    {
        $role = strtoupper($role);
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * Never use this to check if this user has access to anything!
     * Use the AuthorizationChecker, or an implementation of AccessDecisionManager
     * instead, e.g. $securityContext->isGranted('ROLE_USER');.
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * @param string $role
     *
     * @return User
     */
    public function removeRole(string $role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * @see \Serializable::serialize()
     *
     * @return string
     */
    public function serialize(): string
    {
        return serialize([$this->id, $this->email]);
    }

    /**
     * @see \Serializable::unserialize()
     *
     * @param string
     */
    public function unserialize($serialized)
    {
        list($this->id, $this->email) = unserialize($serialized);
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set salt.
     *
     * @param string $salt
     *
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set active.
     *
     * @param bool $active
     *
     * @return User
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * IsActive.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param \DateTime $time
     *
     * @return User
     */
    public function setLastLogin(\DateTime $time = null)
    {
        $this->lastLogin = $time;

        return $this;
    }

    /**
     * Gets the last login time.
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * @param string
     *
     * @return User
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired()
    {
        return true;    // not implemented
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {
        return true;    // not implemented
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired()
    {
        return true;    // not implemented
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->active;
    }

    /**
     * {@inheritdoc}
     */
    public function isEqualTo(SymfonyUserInterface $user): bool
    {
        if ($this->email !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    /**
     * Get role labels.
     *
     * @return array
     */
    public static function getRoleLabels(): array
    {
        return static::$roleLabels;
    }

    /**
     * Get role label.
     *
     * @param string $role
     *
     * @return string
     */
    public function getRoleLabel($role): string
    {
        return $role == 'ROLE_SUPER_ADMIN' ? 'super admin' : static::$roleLabels[$role];
    }

    /**
     * Get roles with labels.
     *
     * @param string $glue
     *
     * @return string
     */
    public function getRolesWithLabel($glue = ', '): string
    {
        $labels = [];
        $roles = $this->getRoles();
        foreach ($roles as $role) {
            $labels[] = $this->getRoleLabel($role);
        }

        return implode($glue, $labels);
    }
}
