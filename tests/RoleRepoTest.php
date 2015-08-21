<?php

use Roole\RoleRepo;

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
    public function it_allows_to_create_and_store_roles()
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
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Role not found
     */
    public function it_throws_exception_if_role_doesnt_exists () {

        (new RoleRepo())->get('detractor');

    }

}
