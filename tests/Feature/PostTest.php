<?php

namespace Tests\Feature;

// Feature tests test full HTTP requests (like a real user would do)
// Different from Unit tests which test small units of code in isolation

use App\Models\Category;  // need to create users
use App\Models\Post; // the thing we are testing
use App\Models\Tag; // posts need categories
use App\Models\User; // posts can have tags
use Illuminate\Foundation\Testing\RefreshDatabase; // Trait that reset database after each test
use Illuminate\Foundation\Testing\WithFaker; // Trait to generate fake data (names, emails, etc)
use Spatie\Permission\Models\Role; // Base test class with Laravel testing helpers
use Tests\TestCase; // Import spatie role model (for admin/user roles)

class PostTest extends TestCase

    /**  extends TestCase inherits laravel's testing capabilities
     *    laravel looks for methods starting with test_ and runs them
     */
{
    use RefreshDatabase,
        // Create Tables -> Runs Test -> Drops tables -> Repeat
        WithFaker;
    // Gives access to $this->faker->name() etc

    protected User $admin;

    /** protected = Can be accessed by this class and subclasses
     * User $admin = Will store an instance of User model (Created in setUp method)
     */
    protected User $regularUser;

    // will store a regular user instance
    protected Category $category;
    // will store a test category (needed for posts)

    protected function setUp(): void
    /** special method that runs before each test
     * void means it does not return anything
     * userd to prepare test environment (create )
     */
    {
        parent::setUp();
        // Call Laravel's setUp method to initialize testing environment, setup DB and boots apps etc.

        // Create roles
        Role::create(['name' => 'admin']);
        // Role::create creates a new role in the roles table, spatie permission need these roles to exist.
        Role::create(['name' => 'user']);

        // Create Permissions
        $permissions = [

            'view posts',
            'create posts',
            'edit posts',
            'delete posts',
            'publish posts',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::create(['name' => $permission]);
        }

        // Create an admin user
        $this->admin = User::factory()->create();
        // User::factory()->create() creates and saves a user in the test database
        $this->admin->assignRole('admin');
        // assignRole is a spatie permission method to assign a role to a user

        // Give admin role all permissions
        $adminRole = Role::findByName('admin');
        $adminRole->givePermissionTo($permissions);

        // Create a regular user
        $this->regularUser = User::factory()->create();
        $this->regularUser->assignRole('user');

        // Create a test category
        $this->category = Category::create(
            [
                'name' => 'Test Category',
                'slug' => 'test-category',
                'description' => 'Test description',
            ]);
        // Posts need a category, so we create one here
    }

    #[Test]
    public function test_admin_can_view_posts_index()
    // What this tests: Admin user can access the posts index page (/admin/posts)
    {
        $this->actingAs($this->admin);
        // simulate being logged in as admin user

        $response = $this->get(route('posts.index'));
        /**  $this-> get() - Makes a GET request (like clicking a link)
         *   route('posts.index') - generates URL for posts index page
         *   $response - stores the HTTP response (status code, view, redirects, etc.)
         */
        $response->assertStatus(200);
        // assertStatus(200) checks that the response status code is 200 (OK), otherwise the test fails

        $response->assertViewIs('posts.index');
        // assertViewIs('posts.index') checks that the returned view is posts.index, otherwise the test fails

        $response->assertViewHas('posts');
        // assertViewHas('posts') checks that the view has a variable named 'posts'.

    }

    #[Test]
    public function test_guest_cannot_view_posts_index()
    // What this tests: A guest (not logged in user) cannot access the posts index page (/admin/posts)
    {
        $response = $this->get(route('posts.index'));
        // No actingAs() means we are not logged in (guest)

        $response->assertRedirect(route('login'));
        // assertRedirect(route('login')) checks that the response is a redirect to the login page, this confirms auth middleware is working
    }

    #[Test]
    public function test_admin_can_view_create_post_form()
    {
        $this->actingAs($this->admin);
        // simulate being logged in as admin user

        $response = $this->get(route('posts.create'));
        /**  $this-> get() - Makes a GET request (like clicking a link)
         *  route('posts.create') - generates URL for create post form
         * $response - stores the HTTP response (status code, view, redirects, etc.)
         */
        $response->assertStatus(200);
        // assertStatus(200) checks that the response status code is 200 (OK),
        $response->assertViewIs('posts.create');
        // assertViewIs('posts.create') checks that the returned view is posts.create
        $response->assertViewHas(['categories', 'tags']);
    }

    #[Test]
    public function test_admin_can_create_draft_post()
    {
        $this->actingAs($this->admin);
        // simulate being logged in as admin user

        $postData = [

            'title' => 'Test Blog Post',
            'content' => 'This is the test content for the blog post.',
            'excerpt' => 'This is a test excerpt.',
            'category_id' => $this->category->id,
            'status' => 'draft',
        ];
        // This creates an array with post data to be used in creating a post
        // $this->category->id gets the id of the test category created in setUp method

        // Send POST request to create a new post
        $response = $this->post(route('posts.store'), $postData);

        /**
         * $this->post() - Makes a POST request to the given URL with the provided data
         * route('posts.store') - generates URL for storing a new post
         * $postData - the data to be sent in the POST request body ( like $_POST in plain PHP)
         */
        $response->assertRedirect(route('posts.index'));
        // assertRedirect(route('posts.index')) checks that the response is a redirect to the posts index page after creating the post

        $response->assertSessionHas('success');
        // assertSessionHas('success') checks that the session has a 'success' message, indicating the post was created successfully

        $this->assertDatabaseHas('posts', [

            'title' => 'Test Blog Post',
            'slug' => 'test-blog-post',
            'status' => 'draft',
            'user_id' => $this->admin->id,
        ]);

    }

    #[Test]
    public function test_admin_can_create_published_post()
    {

        $this->actingAs($this->admin);
        // simulate being logged in as admin user
        $postData = [

            'title' => 'Published Post',
            'content' => 'This is published content.',
            'category_id' => $this->category->id,
            'status' => 'published',
        ];
        $response = $this->post(route('posts.store'), $postData);

        $response->assertRedirect(route('posts.index'));

        $this->assertDatabaseHas('posts', [

            'title' => 'Published Post',
            'status' => 'published',
        ]);

        $post = Post::where('title', 'Published Post')->first();
        $this->assertNotNull($post->published_at);
    }

    #[Test]
    public function test_post_slug_is_automatically_generated()
    {
        $this->actingAs($this->admin);

        $postData = [

            'title' => 'Test Slug Generation',
            'content' => 'Content here',
            'status' => 'draft',
        ];

        $this->post(route('posts.store'), $postData);

        $this->assertDatabaseHas('posts', [

            'title' => 'Test Slug Generation',
            'slug' => 'test-slug-generation',
        ]);

    }

    #[Test]
    public function test_duplicate_slug_is_handled()
    {
        $this->actingAs($this->admin);

        // Create first post
        Post::create([

            'user_id' => $this->admin->id,
            'title' => 'Unique Title',
            'slug' => 'unique-title',
            'content' => 'Content',
            'category_id' => $this->category->id,
            'status' => 'draft',
        ]);

        // Create second post with same title to test slug uniqueness
        $postData = [

            'title' => 'Unique Title',
            'content' => 'Different content',
            'category_id' => $this->category->id,
            'status' => 'draft',
        ];

        /**  Send POST request to create a new post with duplicate title, testing slug uniqueness, $postData contains the data for the new post ,
         * route('posts.store') generates the URL for storing a new post
         * $this->post() makes the POST request with the provided data
         */
        $this->post(route('posts.store'), $postData);

        $this->assertDatabaseHas('posts', [
            'slug' => 'unique-title-1',
        ]);

    }

    #[Test]
    public function test_admin_can_attach_tags_to_post()
    {

        $this->actingAs($this->admin);

        $tag1 = Tag::create(['name' => 'PHP', 'slug' => 'php']);
        $tag2 = Tag::create(['name' => 'Laravel', 'slug' => 'laravel']);

        $postData = [

            'title' => 'Tagged Post',
            'content' => 'Content with tags',
            'status' => 'draft',
            'tags' => [$tag1->id, $tag2->id],
        ];

        $this->post(route('posts.store'), $postData);

        $post = Post::where('title', 'Tagged Post')->first();
        $this->assertCount(2, $post->tags);
        /**
         * $this->assertCount(2, $post->tags) checks that the post has exactly 2 tags associated with it, confirming that the tags were correctly attached to the post upon creation.
         */
        $this->assertTrue($post->tags->contains($tag1));
        $this->assertTrue($post->tags->contains($tag2));

        /**
         * $this->assertTrue($post->tags->contains($tag1)) checks that the post's tags collection contains tag1, confirming that tag1 was successfully attached to the post.
         * $this->assertTrue($post->tags->contains($tag2)) checks that the post's tags collection contains tag2, confirming that tag2 was successfully attached to the post.
         *
         * Sends tag IDs in post data
         * Verifies many-to-many relationship between posts and tags is functioning correctly, pivoting table is populated as expected.
         */
    }

    #[Test]
    public function test_admin_can_view_edit_form()
    {
        $this->actingAs($this->admin);

        $post = Post::factory()->create([

            'user_id' => $this->admin->id,
        ]);

        $response = $this->get(route('posts.edit', $post));

        $response->assertStatus(200);
        $response->assertViewIs('posts.edit');
        $response->assertViewHas('post', $post);
    }

    #[Test]
    public function test_admin_can_update_post()
    {
        $this->actingAs($this->admin);

        $post = Post::factory()->create([
            'user_id' => $this->admin->id,
            'title' => 'Original Title',
        ]);

        $updateData = [

            'title' => 'Updated Title',
            'content' => 'Updated content',
            'status' => 'published',
        ];

        $response = $this->put(route('posts.update', $post), $updateData);
        /**
         * $this->put() - Makes a PUT request to update the post with the provided data,
         * route('posts.update', $post) - generates the URL for updating the specific post,
         * $updateData - the data to be sent in the PUT request body,
         * $response - stores the HTTP response.
         */
        $response->assertRedirect(route('posts.index'));

        $this->assertDatabaseHas('posts', [

            'id' => $post->id,
            'title' => 'Updated Title',
            'status' => 'published',
        ]);

    }

    #[Test]
    public function test_admin_can_delete_post()
    {
        $this->actingAs($this->admin);

        $post = Post::factory()->create([

            'user_id' => $this->admin->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->delete(route('posts.destroy', $post));

        $response->assertRedirect(route('posts.index'));

        $this->assertSoftDeleted('posts', [

            'id' => $post->id,
        ]);

    }

    #[Test]
    public function test_guest_can_view_published_post()
    {
        $post = Post::factory()->create([

            'user_id' => $this->admin->id,
            /**
             * 'user_id' => $this->admin->id assigns the post to the admin user created in setUp method,
             * ensuring the post has a valid author.
             */
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get(route('posts.public.show', $post->slug));
        /**
         * $this->get() - Makes a GET request to view the published post,
         * route('posts.public.show', $post->slug) - generates the URL for viewing
         * the specific published post using its slug,
         */
        $response->assertStatus(200);
        $response->assertViewIs('posts.show');
        // assertViewIs('posts.show') checks that the returned view is posts.show
        $response->assertSee($post->title);
        // assertSee($post->title) checks that the response contains the post's title
    }

    #[Test]
    public function test_post_requires_title()
    {
        $this->actingAs($this->admin);

        $postData = [

            'content' => 'Content without title',
            'status' => 'draft',
            // 'title' is intentionally missing to test validation
        ];

        $response = $this->post(route('posts.store'), $postData);
        /**
         * Send POST request with missing title to test validation,
         * $postData contains the incomplete data, route('posts.store')
         * generates the URL for storing a new post, $response stores the HTTP response.
         */
        $response->assertSessionHasErrors('title');
        /**
         * $response->assertSessionHasErrors('title') checks that the session has validation errors for the 'title' field,
         * confirming that the validation rules are working correctly when required fields are missing.
         */
    }

    #[Test]
    public function test_post_requires_content()
    {
        $this->actingAs($this->admin);
        // simulate being logged in as admin user

        $postData = [

            'title' => 'Title without content',
            'status' => 'draft',
            // 'content' is intentionally missing to test validation
        ];

        $response = $this->post(route('posts.store'), $postData);

        $response->assertSessionHasErrors('content');
        // $response->assertSessionHasErrors('content') checks that the session has validation errors for the 'content' field,
        // confirming that the validation rules are working correctly when required fields are missing.
    }

    #[Test]
    public function test_post_status_must_be_valid()
    {
        $this->actingAs($this->admin);
        // simulate being logged in as admin user

        $postData = [

            'title' => 'Test Post',
            'content' => 'Test content',
            'status' => 'invalid-status',
            // 'status' has an invalid value to test validation
        ];

        $response = $this->post(route('posts.store'), $postData);

        $response->assertSessionHasErrors('status');
    }
}
