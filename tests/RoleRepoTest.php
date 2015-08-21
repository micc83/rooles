<?php

use Rooles\Role;
use Rooles\RoleRepo;

/**
 * Class RoleRepoTest
 *
 * Tests the Roles Repository
 */
class RoleRepoTest extends BaseCase
{

    /**
     * @test
     */
    public function it_allows_to_create_and_store_roles ()
    {

        $roleRepo = new RoleRepo();

        $roleRepo->create('sales agent')->grant([
            'users.own.read'
        ]);

        $salesAgent = $roleRepo->get('sales agent');

        $this->assertTrue($salesAgent->can('users.own.read'));
        $this->assertFalse($salesAgent->can('users.read'));

    }

    /**
     * @test
     */
    public function it_allows_to_get_existing_role_or_create_a_new_one ()
    {

        $roleRepo = new RoleRepo();

        $roleRepo->getOrCreate('admin')->grant('*');

        $this->assertTrue($roleRepo->getOrCreate('admin')->can('*'));

    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Role not found
     */
    public function it_throws_exception_if_role_doesnt_exists ()
    {

        (new RoleRepo())->get('detractor');

    }

    /**
     * @test
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage Duplicated role!
     */
    public function it_throws_exception_if_role_with_same_name_already_exists ()
    {

        $roleRepo = new RoleRepo();

        $roleRepo->add((new Role('test'))->grant('*'));
        $roleRepo->add(new Role('test'));

    }

}
