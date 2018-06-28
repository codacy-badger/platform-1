<?php declare(strict_types=1);

namespace Shopware\Core\System\User;

use Shopware\Core\Framework\ORM\Entity;
use Shopware\Core\Framework\ORM\Search\EntitySearchResult;
use Shopware\Core\System\Locale\LocaleStruct;

class UserStruct extends Entity
{
    /**
     * @var string
     */
    protected $localeId;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var \DateTime|null
     */
    protected $lastLogin;

    /**
     * @var bool
     */
    protected $active;

    /**
     * @var int
     */
    protected $failedLogins;

    /**
     * @var \DateTime|null
     */
    protected $lockedUntil;

    /**
     * @var \DateTime|null
     */
    protected $createdAt;

    /**
     * @var \DateTime|null
     */
    protected $updatedAt;

    /**
     * @var LocaleStruct|null
     */
    protected $locale;

    /**
     * @var EntitySearchResult|null
     */
    protected $media;

    /**
     * @var EntitySearchResult|null
     */
    protected $accessKeys;

    public function getLocaleId(): string
    {
        return $this->localeId;
    }

    public function setLocaleId(string $localeId): void
    {
        $this->localeId = $localeId;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getLastLogin(): ?\DateTime
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTime $lastLogin): void
    {
        $this->lastLogin = $lastLogin;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getFailedLogins(): int
    {
        return $this->failedLogins;
    }

    public function setFailedLogins(int $failedLogins): void
    {
        $this->failedLogins = $failedLogins;
    }

    public function getLockedUntil(): ?\DateTime
    {
        return $this->lockedUntil;
    }

    public function setLockedUntil(?\DateTime $lockedUntil): void
    {
        $this->lockedUntil = $lockedUntil;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getLocale(): ?LocaleStruct
    {
        return $this->locale;
    }

    public function setLocale(LocaleStruct $locale): void
    {
        $this->locale = $locale;
    }

    public function getMedia(): ?EntitySearchResult
    {
        return $this->media;
    }

    public function setMedia(EntitySearchResult $media): void
    {
        $this->media = $media;
    }

    public function getAccessKeys(): ?EntitySearchResult
    {
        return $this->accessKeys;
    }

    public function setAccessKeys(EntitySearchResult $accessKeys): void
    {
        $this->accessKeys = $accessKeys;
    }
}