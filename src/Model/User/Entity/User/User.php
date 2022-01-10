<?php
declare(strict_types=1);
namespace App\Model\User\Entity\User;

use App\Model\User\Entity\Certificate\Certificate;
use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\Notification;
use App\Model\User\Entity\Profile\Profile;
use App\Model\User\Entity\User\Notification\NotificationType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="user_users", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"email"}),
 *     @ORM\UniqueConstraint(columns={"reset_token_token"})
 * })
 */
class User
{
    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="user_user_id")
     */
    private $id;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", name="created_at")
     */
    private $createdAt;

    /**
     * @var Email | null
     * @ORM\Column(type="user_user_email", nullable=true, unique=true)
     */
    private $email;

    /**
     * @var string | null
     * @ORM\Column(type="string", nullable=true, name="password_hash")
     */
    private $passwordHash;

    /**
     * @var ConfirmToken | null
     * @ORM\Embedded(class="ConfirmToken", columnPrefix="confirm_token_")
     */
    private $confirmToken;

    /**
     * @var Status
     * @ORM\Column(type="user_status_type")
     */
    private $status;

    /**
     * @var Profile
     * @ORM\OneToOne(targetEntity="App\Model\User\Entity\Profile\Profile", mappedBy="user", orphanRemoval=true, cascade={"persist"})
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $profile;

    /**
     * @var Role
     * @ORM\Column(type="user_user_role")
     */
    private $role;

    /**
     * @var ResetToken | null
     * @ORM\Embedded(class="ResetToken", columnPrefix="reset_token_")
     */
    private $resetToken;

    /**
     * @var NewEmailResetToken | null
     * @ORM\Embedded(class="NewEmailResetToken", columnPrefix="new_email_reset_token_")
     */
    private $newEmailResetToken;

    /**
     * @var Email | null
     * @ORM\Column(type="user_user_email", name="new_email", nullable=true)
     */
    private $newEmail;

    /**
     * @var ArrayCollection|Certificate[]
     * @ORM\OneToMany(targetEntity="App\Model\User\Entity\Certificate\Certificate", mappedBy="user", orphanRemoval=true, cascade={"all"})
     */
    private $certificates;

    /**
     * @var Notification[]|ArrayCollection
     * @ORM\OneToMany(
     *     targetEntity="App\Model\User\Entity\User\Notification\Notification",
     *     mappedBy="profile",
     *     orphanRemoval=true,
     *     cascade={"persist"},
     *     )
     */
    private $notifications;

    /**
     * @var string
     * @ORM\Column(type="string", name="client_ip")
     */
    private $clientIp;


    private function __construct(Id $id, \DateTimeImmutable $createdAt) {
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->status = Status::new();
        $this->certificates = new ArrayCollection();
        $this->role = Role::user();
        $this->profile = null;
    }

    /**
     * @param Id $id
     * @param \DateTimeImmutable $createdAt
     * @param Email $email
     * @param string $passwordHash
     * @param string $confirmToken
     * @param string $clientIp
     * @return User
     */
    public static function signUpByEmail(Id $id, \DateTimeImmutable $createdAt, Email $email, string $passwordHash, string $confirmToken, string $clientIp): self {
        $user = new self($id, $createdAt);
        if (!$user->status->isNew()){
            throw new \DomainException("User is already signed up.");
        }
        $user->email = $email;
        $user->passwordHash = $passwordHash;
        $user->confirmToken = new ConfirmToken($confirmToken, new \DateTimeImmutable());
        $user->status = Status::wait();
        $user->clientIp = $clientIp;
        return $user;
    }

    public function getId(): Id {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeImmutable {
        return $this->createdAt;
    }

    public function getEmail(): Email {
        return $this->email;
    }

    public function getPasswordHash(): string {
        return $this->passwordHash;
    }

    public function getConfirmToken(): ?ConfirmToken {
        return $this->confirmToken;
    }

    public function getResetToken(): ? ResetToken {
        return $this->resetToken;
    }

    public function getRole(): Role {
        return $this->role;
    }

    public function getProfile(): Profile {
        return $this->profile;
    }

    public function getExistsProfile(): ? Profile {
        return $this->profile;
    }


    /**
     * @return Status
     */
    public function getStatus(): Status {
        return $this->status;
    }

    /**
     * Обновление токена для подтверждения email
     * @param string $token
     * @param \DateTimeImmutable $date
     * @return bool
     */
    public function updateConfirmToken(string $token, \DateTimeImmutable $date): bool {
        if ($this->confirmToken->isExpiredTo($date))
        {
            $this->confirmToken = new ConfirmToken($token, $date->add(new \DateInterval('PT2H')));

            return true;
        }
        else
            return false;
    }

    /**
     * Подтверждение регистрации
     */
    public function confirmSignUp(): void {
        if (!$this->status->isWait()){
            throw new \DomainException("User is already confirmed.");
        }
        $this->status = Status::active();
        $this->confirmToken = null;
    }

    /**
     * Отправка запроса на сброс пароля
     * @param ResetToken $resetToken
     * @param \DateTimeImmutable $date
     */
    public function requestPasswordReset(ResetToken $resetToken, \DateTimeImmutable $date): void {
        if (!$this->status->isActive()){
            throw new \DomainException("User is not active.");
        }

        if (!$this->email){
            throw new \DomainException("Email is not specified.");
        }

        if ($this->resetToken && !$this->resetToken->isExpiredTo($date)){
            throw new \DomainException("Resetting is already requested.");
        }
        $this->resetToken = $resetToken;
    }

    /**
     * Изменение пароля
     * @param \DateTimeImmutable $date
     * @param string $passwordHash
     */
    public function passwordReset(\DateTimeImmutable $date, string $passwordHash): void {
        if (!$this->resetToken) {
            throw new \DomainException('Resetting is not requested.');
        }
        if ($this->resetToken->isExpiredTo($date)) {
            throw new \DomainException('Reset token is expired.');
        }
        $this->passwordHash = $passwordHash;
        $this->resetToken = null;
    }

    /**
     * Изменение роли
     * @param Role $role
     */
    public function changeRole(Role $role): void {
        if ($this->role->isEqual($role)){
            throw new \DomainException("Role is already same.");
        }
        $this->role = $role;
    }

    /**
     * Отправка запроса на изменение email
     * @param Email $newEmail
     * @param NewEmailResetToken $newEmailResetToken
     * @param \DateTimeImmutable $date
     */
    public function requestEmailChanging(Email $newEmail, NewEmailResetToken $newEmailResetToken, \DateTimeImmutable $date): void {
        if (!$this->status->isActive()){
            throw new \DomainException("User is not active.");
        }

        if ($this->email && $this->email->isEqual($newEmail)){
            throw new \DomainException('Email is already same.');
        }

        if ($this->newEmailResetToken && !$this->newEmailResetToken->isExpiredTo($date)){
            throw new \DomainException("Resetting is already requested.");
        }

        $this->newEmailResetToken = $newEmailResetToken;
        $this->newEmail = $newEmail;
    }

    /**
     * Подтверждение смены email
     * @param string $token
     * @param \DateTimeImmutable $date
     */
    public function confirmEmailChanging(string $token, \DateTimeImmutable $date): void {
        if (!$this->newEmailResetToken) {
            throw new \DomainException('Resetting is not requested.');
        }

        if ($this->newEmailResetToken->isExpiredTo($date)) {
            throw new \DomainException('Reset token is expired.');
        }

        if ($this->newEmailResetToken->getToken() !== $token){
            throw new \DomainException('Incorrect changing token.');
        }

        $this->email = $this->newEmail;
        $this->newEmail = null;
        $this->newEmailResetToken = null;
    }

    /**
     * @ORM\PostLoad()
     */
    public function checkEmbeds(): void {
        if ($this->resetToken->isEmpty()){
            $this->resetToken = null;
        }
    }

    /**
     * Добавление профиля
     * @param Profile $profile
     */
    public function addProfile(Profile $profile): void {
        $this->profile = $profile;
    }

    /**
     * @param Certificate $certificate
     */
    public function addCertificate(Certificate $certificate): void {
        foreach ($this->certificates as $existing) {
            if ($existing->isForCertificate($certificate)) {
                throw new \DomainException('Certificate is already attached.');
            }
        }
        $this->certificates->add($certificate);
    }

    /**
     * Блокировка юзера
     */
    public function locked(): void{
        if($this->status->isBlocked()){
            throw new \DomainException('Пользователь уже заблокирован');
        }
        $this->status = Status::blocked();
    }

    /**
     * Разблокировка пользователя
     */
    public function unlocked(): void{
        if($this->status->isActive()){
            throw new \DomainException('Пользователь уже активный');
        }
        $this->status = Status::active();
    }

/*    /**
     * @param NotificationType $notificationType

    public function createNotification(NotificationType $notificationType){
        if($this->role->isAdmin()){
            $category = Category::categoryAdmin();
        }else{
            $category = Category::categoryOne();
        }
    /*    $this->notifications->add(new Notification(
            \App\Model\User\Entity\User\Notification\Id::next(),
            $notificationType,
            $this,
            $category,
            new \DateTimeImmutable()
        ));
    } */
}