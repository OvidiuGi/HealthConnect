<?php

namespace App\Entity;

use App\Dto\UserDto;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as MyAssert;

#[Entity(repositoryClass: UserRepository::class)]
#[Table(name: '`user`')]
//Mai joaca te cu asta. Ca mai trebuie adaugate si CNP si alte chestii
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    public const ROLE_USER = 'ROLE_USER';

    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public const ROLE_MEDIC = 'ROLE_MEDIC';

    public const ROLES = ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_MEDIC'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 256, unique: false)]
    #[Assert\NotBlank(message: 'Please enter your first name', groups: ['create-user', 'edit-user'])]
    #[Assert\Regex("/^[A-Z][a-z]+$/", groups: ['create-user', 'edit-user'])]
    public string $firstName = '';

    #[ORM\Column(type: 'string', length: 256, unique: false)]
    #[Assert\NotBlank(message: 'Please enter your last name', groups: ['create-user', 'edit-user'])]
    #[Assert\Regex("/^[A-Z][a-z]+$/", groups: ['create-user', 'edit-user'])]
    public string $lastName = '';

    #[ORM\Column(type: 'string', length: 256, unique: true)]
    #[Assert\Email(message:'Please enter a valid email', groups: ['create-user', 'edit-user', 'forgot-password'])]
    #[Assert\NotBlank(message: 'Please enter an email', groups: ['create-user', 'forgot-password'])]
    public string $email = '';

    #[ORM\Column(type: 'string', length: 256, unique: true)]
    public string $password = '';

    #[Assert\NotBlank(message: 'Please enter a password', groups: ['create-user', 'reset-password'])]
    #[MyAssert\Password(groups: ['create-user', 'reset-password'])]
    public string $plainPassword = '';

    #[ORM\Column(type: 'string', length: 256, unique: true)]
    #[Assert\NotBlank(message: 'Please enter your telephone number', groups: ['create-user', 'edit-user'])]
    #[MyAssert\TelephoneNumber(groups: ['create-user', 'edit-user'])]
    public string $telephoneNr = '';

    #[ORM\Column(type: 'string', length: 256, unique: true)]
    #[Assert\NotBlank(message: 'Please enter your CNP', groups: ['create-user', 'edit-user'])]
    #[MyAssert\Cnp(groups: ['create-user', 'edit-user'])]
    public string $cnp = '';

    // One User has Many Appointments.
    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Appointment::class)]
    private Collection $appointments;

    #[ORM\Column(type: 'string', length: 256, unique: false)]
    #[Assert\Choice(choices: User::ROLES, multiple: false)]
    public string $role = '';

    // Many Doctors work at One Building.
    #[ORM\ManyToOne(targetEntity: Building::class, inversedBy: 'doctors')]
    #[ORM\JoinColumn(name: 'building_id', nullable: true, onDelete: 'CASCADE')]
    private ?Building $office;

    // Many Doctors have Many Services.
    #[ORM\ManyToMany(targetEntity: Service::class, mappedBy: 'doctors')]
    private Collection $services;

    #[ORM\Column(type: 'string', length: 256, unique: false)]
    public ?string $specialization = '';

    #[ORM\Column(type: 'integer', length: 256, unique: false)]
    public ?int $experience = 0;

    // One Doctor has Many Schedules.
    #[ORM\OneToMany(mappedBy: 'doctor', targetEntity: Schedule::class)]
    private ?Collection $schedules;

    #[ORM\Column(type: 'string', length: 256, unique: false, nullable: true)]
    public ?string $forgotPasswordToken = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $forgotPasswordTokenExpiresAt = null;

    #[ORM\Column(type: 'boolean')]
    public bool $isSubscribed = false;

    public function __construct()
    {
        $this->appointments = new ArrayCollection();
        $this->services = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setAppointments(Collection $appointments): self
    {
        $this->appointments = $appointments;

        return $this;
    }

    public function getAppointments(): Collection
    {
        return $this->appointments;
    }

    public function addAppointment(Appointment $appointment): self
    {
        if (!$this->appointments->contains($appointment)) {
            $this->appointments[] = $appointment;
            $appointment->setCustomer($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): self
    {
        if ($this->appointments->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getCustomer() === $this) {
                $appointment->setCustomer(null);
            }
        }

        return $this;
    }

    public function setServices(Collection $services): self
    {
        $this->services = $services;

        return $this;
    }

    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
            $service->addDoctor($this);
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        if ($this->services->removeElement($service)) {
            $service->removeDoctor($this);
        }

        return $this;
    }

    public function setOffice(?Building $office): self
    {
        $this->office = $office;

        return $this;
    }

    public function getOffice(): ?Building
    {
        return $this->office;
    }

    public static function createFromUserDto(UserDto $dto): self
    {
        $user = new self();
        $user->firstName = $dto->firstName;
        $user->lastName = $dto->lastName;
        $user->email = $dto->email;
        $user->plainPassword = $dto->password;
        $user->telephoneNr = $dto->telephoneNr;
        $user->cnp = $dto->cnp;
        $user->role = $dto->role;

        return $user;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return [$this->role];
    }

    public function eraseCredentials()
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getSchedule(): Collection
    {
        return $this->schedules;
    }

    public function setSchedule(Collection $schedules): self
    {
        $this->schedules = $schedules;

        return $this;
    }

    public function addSchedule(Schedule $schedule): self
    {
        if (!$this->schedules->contains($schedule)) {
            $this->schedules->add($schedule);
            $schedule->setDoctor($this);
        }

        return $this;
    }

    public function removeSchedule(Schedule $schedule): self
    {
        if (!$this->schedules->contains($schedule)) {
            return $this;
        }

        $this->schedules->removeElement($schedule);
        $schedule->setDoctor(null);

        return $this;
    }

    public function getForgotPasswordTokenExpiresAt(): ?\DateTimeImmutable
    {
        return $this->forgotPasswordTokenExpiresAt;
    }

    public function setForgotPasswordTokenExpiresAt(?\DateTimeImmutable $forgotPasswordTokenExpiresAt): self
    {
        $this->forgotPasswordTokenExpiresAt = $forgotPasswordTokenExpiresAt;

        return $this;
    }
}
