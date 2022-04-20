<?php

namespace App\DTO\Player;

use Symfony\Component\Validator\Constraints as Assert;

class PlayerDTOCreate
{
    const GENDER = ['m', 'f'];

    #[Assert\Email]
    #[Assert\Length(max: 180)]
    #[Assert\NotBlank]
    private string $email;

    #[Assert\Length(max: 100)]
    #[Assert\NotBlank]
    private string $firstname;

    #[Assert\Length(max: 100)]
    #[Assert\NotBlank]
    private string $lastname;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: self::GENDER, message: "Choose a valid gender")]
    private string $gender;

    #[Assert\NotBlank]
    private \DateTimeInterface $birthday;

    #[Assert\Regex(pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#$@!%&*?])[A-Za-z\d#$@!%&*?]{8,}$/',
        message: "Must contain min 8 char, min one digit, min one lowercase, min one uppercase and one special char" )]
    #[Assert\NotBlank]
    private string $plainPassword;

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string
     */
    public function getGender(): string
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender(string $gender): void
    {
        $this->gender = $gender;
    }


    /**
     * @return \DateTimeInterface
     */
    public function getBirthday(): \DateTimeInterface
    {
        return $this->birthday;
    }

    /**
     * @param \DateTimeInterface $birthday
     */
    public function setBirthday(\DateTimeInterface $birthday): void
    {
        $this->birthday = $birthday;
    }


    /**
     * @return string
     */
    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }



}