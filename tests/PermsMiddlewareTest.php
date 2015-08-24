<?php

/**
 * Class PermsMiddlewareTest
 */
class PermsMiddlewareTest extends BaseCase
{

    /**
     * Setup tests
     */
    public function setUp()
    {

        parent::setUp();

        get('restricted', [
            'middleware' => 'perms:users.read|users.write',
            function () {
                return 'Done!';
            }
        ]);

        get('veryRestricted', [
            'middleware' => 'perms:users.read&users.write',
            function () {
                return 'Done!';
            }
        ]);

        $roleRepo = $this->app->make('Rooles\RoleRepo');

        $roleRepo->create('admin')->grant([
            'users.read',
            'users.write'
        ]);

        $roleRepo->create('operator')->grant('users.read');

        $roleRepo->create('user')->grant('profile.read');

    }

    /**
     * @test
     */
    public function it_throw_exception_if_user_not_logged_in()
    {

        $this->get('restricted')->dontSee('Done!')->seeStatusCode(401);

    }

    /**
     * @test
     */
    public function it_throw_exception_if_user_hasnt_the_needed_role()
    {

        $this->be(new UserMock([
            'name' => 'Jhonny Mnemonic',
            'role' => 'user'
        ]));

        $this->get('restricted')->dontSee('Done!')->seeStatusCode(401);

    }

    /**
     * @test
     */
    public function it_passes_if_user_role_has_the_needed_permissions()
    {

        $this->be(new UserMock([
            'name' => 'Jhonny Mnemonic',
            'role' => 'operator'
        ]));

        $this->get('restricted')->see('Done!');
        $this->get('veryRestricted')->dontSee('Done!')->seeStatusCode(401);

        $this->be(new UserMock([
            'name' => 'Master',
            'role' => 'admin'
        ]));

        $this->get('veryRestricted')->see('Done!');
    }

    /**
     * @test
     */
    public function it_respond_with_json_encoded_unauthorized_error_on_ajax_calls()
    {
        $this->get('restricted', ['X-Requested-With' => 'XMLHttpRequest'])
             ->see('"message":"Unauthorized"')
             ->seeStatusCode(401);
    }

}
