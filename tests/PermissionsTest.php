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

        $this->assertTrue($permissions->evaluate('users.own.read'));
        $this->assertTrue($permissions->evaluate(['users.own.read', 'profile.edit']));

        $this->assertTrue($permissions->evaluate('profile.read'));
        $this->assertTrue($permissions->evaluate('profile.delete'));

        $this->assertFalse($permissions->evaluate('users.read'));
        $this->assertFalse($permissions->evaluate(['users.own.read', 'users.read']));

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
            'users.*.write'
        ], '!');

        $this->assertTrue($permissions->evaluate('users.customers.read'));
        $this->assertTrue($permissions->evaluate('users.customers.all.write.poems'));

        $this->assertFalse($permissions->evaluate('users.customers.write'));
        $this->assertFalse($permissions->evaluate('users.admins.write'));

    }

    /**
     * @test
     */
    public function it_allows_for_full_wildcard_permissions()
    {

        $permissions = new Permissions;

        $permissions->set('*', '*');

        $this->assertTrue($permissions->evaluate('users.read'));
        $this->assertTrue($permissions->evaluate('*'));

    }

    /**
     * @test
     */
    public function it_allows_for_partials_wildcard_permissions()
    {

        $permissions = new Permissions;

        $permissions->set('users.*', '*'); // Same as users

        $this->assertTrue($permissions->evaluate('users'));
        $this->assertTrue($permissions->evaluate('users.*'));

        $permissions = new Permissions;

        $permissions->set('*.read', '*');

        $this->assertTrue($permissions->evaluate('users.read'));
        $this->assertTrue($permissions->evaluate('users.read'));
        $this->assertTrue($permissions->evaluate('news.read'));

        $this->assertFalse($permissions->evaluate('users.write'));
        $this->assertFalse($permissions->evaluate('news.write'));

        $this->assertFalse($permissions->evaluate('*'));

    }

    /**
     * @test
     */
    public function it_allows_for_middle_wildcard_permissions()
    {

        $permissions = new Permissions;

        $permissions->set('users.*.read', '*');

        $this->assertTrue($permissions->evaluate('users.admin.read'));
        $this->assertTrue($permissions->evaluate('users.admin.read.*'));
        $this->assertTrue($permissions->evaluate('users.customer.read.*'));
        $this->assertTrue($permissions->evaluate('users.*.read.*'));

        $this->assertFalse($permissions->evaluate('users.customers.delete'));
        $this->assertFalse($permissions->evaluate('users.*.delete'));
        $this->assertFalse($permissions->evaluate('users.*'));
        $this->assertFalse($permissions->evaluate('users.read'));

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
            'customers.read',
        ], '*');

        // OR
        $this->assertTrue($permissions->evaluate('users.delete|users.remove'));
        $this->assertTrue($permissions->evaluate('users.remove|users.delete'));
        $this->assertFalse($permissions->evaluate('users.remove|users.create'));

        // OR + AND
        $this->assertTrue($permissions->evaluate([
            'users.delete|users.remove',
            'users.remove|customers.read'
        ]));

        // AND
        $this->assertTrue($permissions->evaluate('users.read&users.delete'));
        $this->assertFalse($permissions->evaluate('users.delete&users.create'));

        // OR + AND
        $this->assertTrue($permissions->evaluate('users.delete|users.remove&users.remove|customers.read'));
        $this->assertFalse($permissions->evaluate('users.delete|users.remove&users.remove|customers.create'));

        // Complex query
        $this->assertTrue($permissions->evaluate([
            'users.delete|users.remove&users.remove|customers.read&users.read|users.delete|customers.read',
            'customers.read&users.delete'
        ]));

    }


    /**
     * @test
     */
    public function it_should_pass_complex_queries () {

        $permissions = new Permissions();

        $permissions->set([
            'users',
            'customers.*.read',
            '*.drafts',
        ], '*')->set([
            'users.delete',
            'customers.*.write',
            '*.drafts.delete',
            '*.drafts.read'
        ],'!');

        // To fix this I should think about checking on different paths
        // or think about a specification such as:
        // Una regola precisa passa sopra ad una dichiarata con wildcard...
        $this->assertFalse($permissions->evaluate('customers.drafts.read'));

        $this->assertFalse($permissions->evaluate('*'));

        $this->assertTrue($permissions->evaluate('users.read'));
        $this->assertTrue($permissions->evaluate('users.read.lot'));
        $this->assertTrue($permissions->evaluate('users.read.*'));
        $this->assertTrue($permissions->evaluate('users.read.*.*'));
        $this->assertTrue($permissions->evaluate('users.read.*.*.*'));
        $this->assertTrue($permissions->evaluate('users.read.*.*.*.read'));

        $this->assertFalse($permissions->evaluate('users.delete'));
        $this->assertFalse($permissions->evaluate('users.*'));

        $this->assertTrue($permissions->evaluate('customers.books.read'));
        $this->assertTrue($permissions->evaluate('customers.books.read.*.*'));
        $this->assertTrue($permissions->evaluate('customers.books.read.*.*.all'));
        $this->assertTrue($permissions->evaluate('customers.magazines.read'));
        $this->assertTrue($permissions->evaluate('customers.magazines.old.read'));

        $this->assertFalse($permissions->evaluate('customers.*'));
        $this->assertFalse($permissions->evaluate('customers.*.write'));
        $this->assertFalse($permissions->evaluate('customers.*.*'));
        $this->assertFalse($permissions->evaluate('customers.*.*.read'));
        $this->assertFalse($permissions->evaluate('customers.*.*.*.read'));

        $this->assertTrue($permissions->evaluate('comments.drafts.show'));
        $this->assertTrue($permissions->evaluate('comments.drafts.show.fully'));
        $this->assertTrue($permissions->evaluate('*.drafts.show'));
        $this->assertTrue($permissions->evaluate('*.drafts.show.*'));

        $this->assertFalse($permissions->evaluate('comments.drafts'));
        $this->assertFalse($permissions->evaluate('comments.drafts.delete'));
        $this->assertFalse($permissions->evaluate('comments.drafts.delete.fully'));
        $this->assertFalse($permissions->evaluate('comments.drafts.*.fully'));
        $this->assertFalse($permissions->evaluate('comments.drafts.*.fully.*'));

        $this->assertTrue($permissions->evaluate('users.drafts.delete'));

        $this->assertFalse($permissions->evaluate('customers.drafts.delete'));
        $this->assertFalse($permissions->evaluate('customers.drafts.write'));
        $this->assertFalse($permissions->evaluate('customers.drafts.play'));

    }

}
