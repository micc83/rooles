<?php

/**
 * Class RoleMiddlewareTest
 */
class RoleMiddlewareTest extends BaseCase
{

    /**
     * Setup tests
     */
    public function setUp()
    {

        parent::setUp();

        get('restricted', [
            'middleware' => 'role:admin|root',
            function () {
                return 'Hello World';
            }
        ]);

        $roleRepo = $this->app->make('Rooles\RoleRepo');

        $roleRepo->create('admin');
        $roleRepo->create('root');
        $roleRepo->create('operator');

    }

    /**
     * @test
     */
    public function it_throw_exception_if_user_not_logged_in()
    {
        $this->get('restricted')->dontSee('Hello World')->seeStatusCode(401);
    }

    /**
     * @test
     */
    public function it_throw_exception_if_user_hasnt_the_needed_role()
    {

        $this->be(new UserMock([
            'name' => 'Jhonny Mnemonic',
            'role' => 'operator'
        ]));

        $this->get('restricted')->dontSee('Hello World')->seeStatusCode(401);

    }

    /**
     * @test
     */
    public function it_passes_if_user_has_the_needed_role()
    {

        $this->be(new UserMock([
            'name' => 'Jhonny Mnemonic',
            'role' => 'admin'
        ]));

        $this->get('restricted')->see('Hello World');

        $this->be(new UserMock([
            'name' => 'The Pope',
            'role' => 'root'
        ]));

        $this->get('restricted')->see('Hello World');

    }

    /**
     * @test
     */
    public function it_respond_with_unauthorized_to_ajax_calls()
    {
        $this->get('restricted', ['X-Requested-With' => 'XMLHttpRequest'])
             ->see('Unauthorized')
             ->seeStatusCode(401);
    }

}