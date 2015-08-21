<?php

/**
 * Class UserMock
 *
 * Mocks the App\User class using the UserRole Trait
 */
class UserMock extends App\User
{

    use Roole\Traits\UserRole;

    /**
     * Mass assignment
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'role'
    ];

}