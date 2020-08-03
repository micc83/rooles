<?php

use Illuminate\Support\Facades\Route;

/**
 * Class PermsMiddlewareTest
 */
class PermsMiddlewareTest extends BaseCase
{

    /**
     * Setup tests
     */
    public function setUp(): void
    {
        parent::setUp();

        Route::get('restricted', [
            'middleware' => 'perms:users.read|users.write',
            function () {
                return 'Done!';
            }
        ]);

        Route::get('veryRestricted', [
            'middleware' => 'perms:users.read&users.write',
            function () {
                return 'Done!';
            }
        ]);

        $roleRepo = $this->app->make(Rooles\Contracts\RoleRepository::class);

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
        $this->visitAndCatchException('restricted', 'Rooles\ForbiddenHttpException')
             ->assertDontSee('Done!')
             ->assertStatus(403);
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

        $this->visitAndCatchException('veryRestricted', 'Rooles\ForbiddenHttpException')
             ->assertDontSee('Done!')
             ->assertStatus(403);

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

        $this->get('restricted')->assertSee('Done!');

        $this->be(new UserMock([
            'name' => 'Master',
            'role' => 'admin'
        ]));

        $this->get('veryRestricted')->assertSee('Done!');
    }

    /**
     * @test
     */
    public function it_respond_with_json_encoded_unauthorized_error_on_ajax_calls()
    {
        $this->get('restricted', ['X-Requested-With' => 'XMLHttpRequest'])
             ->assertJsonFragment(['message' => 'Forbidden'])
             ->assertStatus(403);
    }

}
