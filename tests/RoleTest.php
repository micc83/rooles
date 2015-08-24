<?php

use Rooles\Role;

/**
 * Class RoleTest
 *
 * Tests the Role Object
 */
class RoleTest extends BaseCase
{

    /**
     * @test
     */
    public function it_allows_to_check_role_name()
    {

        $customer = new Role('customer');

        $this->assertTrue($customer->is('customer'));
        $this->assertFalse($customer->is('admin'));

    }

    /**
     * @test
     */
    public function it_allows_to_grant_role_permissions()
    {

        $areaManager = new Role('area manager');

        $areaManager->grant([
            'users.own.read',
            'profile',
        ]);

        $this->assertTrue($areaManager->can('users.own.read'));
        $this->assertTrue($areaManager->can(['users.own.read', 'profile.edit']));

        $this->assertTrue($areaManager->can('profile.read'));
        $this->assertTrue($areaManager->can('profile.delete'));

        $this->assertFalse($areaManager->can('users.read'));
        $this->assertFalse($areaManager->can(['users.own.read', 'users.read']));

    }

    /**
     * @test
     */
    public function it_allows_to_deny_role_permissions()
    {

        $areaManager = new Role('area manager');

        $areaManager->grant([
            'users',
        ]);

        $areaManager->deny([
            'users.*.write',
            'users.write'
        ]);

        $this->assertTrue($areaManager->can('users.read'));
        $this->assertTrue($areaManager->can('users.customers.all.write.poems'));

        $this->assertFalse($areaManager->can('users.write'));
        $this->assertFalse($areaManager->can('users.admin.write'));

    }

    /**
     * @test
     */
    public function provide_a_cannot_method_which_inverts_the_result_of_can()
    {

        $role = new Role('role');

        $role->grant('users.write');

        $this->assertTrue($role->can('users.write'));
        $this->assertFalse($role->cannot('users.write'));

    }

    /**
     * @test
     */
    public function it_allows_for_full_wildcard_permissions()
    {

        $admin = new Role('admin');

        $admin->grant('*');

        $this->assertTrue($admin->can('users.read'));
        $this->assertFalse($admin->cannot('*'));

    }

    /**
     * @test
     */
    public function it_allows_for_partials_wildcard_permissions()
    {

        $testRole = new Role('test');

        $testRole->grant('*.read');

        $this->assertTrue($testRole->can('users.read'));
        $this->assertTrue($testRole->can('users.read'));
        $this->assertTrue($testRole->can('news.read'));

        $this->assertTrue($testRole->cannot('users.write'));
        $this->assertTrue($testRole->cannot('news.write'));

        $this->assertTrue($testRole->cannot('*'));

    }

    /**
     * @test
     */
    public function it_allows_for_middle_wildcard_permissions()
    {

        $testRole = new Role('test');

        $testRole->grant('users.*.read');

        $this->assertTrue($testRole->can('users.admin.read'));
        $this->assertTrue($testRole->can('users.admin.read.*'));
        $this->assertTrue($testRole->can('users.customer.read.*'));
        $this->assertTrue($testRole->can('users.*.read.*'));

        $this->assertTrue($testRole->cannot('users.customers.delete'));
        $this->assertTrue($testRole->cannot('users.*.delete'));
        $this->assertTrue($testRole->cannot('users.*'));
        $this->assertTrue($testRole->cannot('users.read'));

    }

    /**
     * @test
     */
    public function it_provide_operators_to_check_permissions()
    {

        $testRole = new Role('test');

        $testRole->grant([
            'users.read',
            'users.delete',
            'customers.read'
        ]);

        // OR
        $this->assertTrue($testRole->can('users.delete|users.remove'));
        $this->assertFalse($testRole->can('users.remove|users.create'));

        // OR + AND
        $this->assertTrue($testRole->can(['users.delete|users.remove', 'users.remove|customers.read']));

        // AND
        $this->assertTrue($testRole->can('users.read&users.delete'));
        $this->assertFalse($testRole->can('users.delete&users.create'));

        // OR + AND
        $this->assertTrue($testRole->can('users.delete|users.remove&users.remove|customers.read'));
        $this->assertFalse($testRole->can('users.delete|users.remove&users.remove|customers.create'));

    }

}
