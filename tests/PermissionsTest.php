<?php

use Rooles\Permissions;

/**
 * Class PermissionsTest
 *
 * Tests the Permissions Object
 */
class PermissionsTest extends BaseCase
{

    /**
     * @test
     */
    public function it_allows_to_grant_role_permissions()
    {

        $permissions = new Permissions;

        $permissions->set([
            'users.own.read',
            'profile',
        ], '*');

        $this->assertTrue($permissions->check('users.own.read'));
        $this->assertTrue($permissions->check(['users.own.read', 'profile.edit']));

        $this->assertTrue($permissions->check('profile.read'));
        $this->assertTrue($permissions->check('profile.delete'));

        $this->assertFalse($permissions->check('users.read'));
        $this->assertFalse($permissions->check(['users.own.read', 'users.read']));

    }

    /**
     * @test
     */
    public function it_allows_to_deny_role_permissions()
    {

        $permissions = new Permissions;

        $permissions->set([
            'users',
        ], '*');

        $permissions->set([
            'users.*.write',
            'users.write'
        ], '!');

        $this->assertTrue($permissions->check('users.read'));
        $this->assertTrue($permissions->check('users.customers.all.write.poems'));

        $this->assertFalse($permissions->check('users.write'));
        $this->assertFalse($permissions->check('users.admin.write'));

    }

    /**
     * @test
     */
    public function it_allows_for_full_wildcard_permissions()
    {

        $permissions = new Permissions;

        $permissions->set('*', '*');

        $this->assertTrue($permissions->check('users.read'));
        $this->assertTrue($permissions->check('*'));

    }

    /**
     * @test
     */
    public function it_allows_for_partials_wildcard_permissions()
    {

        $permissions = new Permissions;

        $permissions->set('users.*', '*'); // Same as users

        $this->assertTrue($permissions->check('users'));
        $this->assertTrue($permissions->check('users.*'));

        $permissions = new Permissions;

        $permissions->set('*.read', '*');

        $this->assertTrue($permissions->check('users.read'));
        $this->assertTrue($permissions->check('users.read'));
        $this->assertTrue($permissions->check('news.read'));

        $this->assertFalse($permissions->check('users.write'));
        $this->assertFalse($permissions->check('news.write'));

        $this->assertFalse($permissions->check('*'));

    }

    /**
     * @test
     */
    public function it_allows_for_middle_wildcard_permissions()
    {

        $permissions = new Permissions;

        $permissions->set('users.*.read', '*');

        $this->assertTrue($permissions->check('users.admin.read'));
        $this->assertTrue($permissions->check('users.admin.read.*'));
        $this->assertTrue($permissions->check('users.customer.read.*'));
        $this->assertTrue($permissions->check('users.*.read.*'));

        $this->assertFalse($permissions->check('users.customers.delete'));
        $this->assertFalse($permissions->check('users.*.delete'));
        $this->assertFalse($permissions->check('users.*'));
        $this->assertFalse($permissions->check('users.read'));

    }

    /**
     * @test
     */
    public function it_provide_operators_to_check_permissions()
    {

        $permissions = new Permissions();

        $permissions->set([
            'users.read',
            'users.delete',
            'customers.read'
        ], '*');

        // OR
        $this->assertTrue($permissions->check('users.delete|users.remove'));
        $this->assertFalse($permissions->check('users.remove|users.create'));

        // OR + AND
        $this->assertTrue($permissions->check(['users.delete|users.remove', 'users.remove|customers.read']));

        // AND
        $this->assertTrue($permissions->check('users.read&users.delete'));
        $this->assertFalse($permissions->check('users.delete&users.create'));

        // OR + AND
        $this->assertTrue($permissions->check('users.delete|users.remove&users.remove|customers.read'));
        $this->assertFalse($permissions->check('users.delete|users.remove&users.remove|customers.create'));

    }

}
