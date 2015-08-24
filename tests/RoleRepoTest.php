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
     * @var RoleRepo
     */
    var $roleRepo;

    /**
     * Setup
     */
    public function setUp()
    {
        $this->roleRepo = new RoleRepo();
    }

    /**
     * @test
     */
    public function it_allows_to_create_and_store_roles()
    {

        $this->roleRepo->create('sales agent')->grant([
            'users.own.read'
        ]);

        $salesAgent = $this->roleRepo->get('sales agent');

        $this->assertTrue($salesAgent->can('users.own.read'));
        $this->assertFalse($salesAgent->can('users.read'));

    }

    /**
     * @test
     */
    public function it_allows_to_get_existing_role_or_create_a_new_one()
    {

        $this->roleRepo->getOrCreate('admin')->grant('*');

        $this->assertTrue($this->roleRepo->getOrCreate('admin')->can('*'));

    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Role not found
     */
    public function it_throws_exception_if_role_doesnt_exists()
    {
        $this->roleRepo->get('detractor');
    }

    /**
     * @test
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage Duplicated role!
     */
    public function it_throws_exception_if_role_with_same_name_already_exists()
    {
        $this->roleRepo->add((new Role('test'))->grant('*'));
        $this->roleRepo->add(new Role('test'));
    }

    /**
     * @test
     */
    public function if_empty_rolename_is_requested_return_a_default_empty_role()
    {
        $this->assertEquals('default', $this->roleRepo->get('')->name());
    }

}
