<?php

namespace Tests\Feature\Components;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CommentFormComponentTest extends TestCase
{
    use RefreshDatabase;

    private Post $post;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->post = Post::factory()->create([

            'user_id' => $user->id,
            'status' => 'published',
            'slug' => 'et-aut-mollitia-eos-harum-sunt-eaque',
        ]);
    }

    #[Test]
    public function it_renders_comment_form_with_all_required_fields()
    {
        $view = $this->blade(
            '<x-comment-form :post="$post" />',
            ['post' => $this->post]
        );

        $view->assertSee('Leave a Comment')
            ->assertSee('Name')
            ->assertSee('Email')
            ->assertSee('Comment')
            ->assertSee('Post Comment')
            ->assertSee('name="name"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="content"', false);

    }

    #[Test]
    public function it_renders_with_custom_title()
    {
        $view = $this->blade(
            '<x-comment-form :post="$post" title="Reply to this post." />',
            ['post' => $this->post]
        );

        $view->assertSee('Reply to this post.')
            ->assertDontSee('Leave a Comment');
    }

    #[Test]
    public function it_renders_with_custom_button_text()
    {
        $view = $this->blade(

            '<x-comment-form :post="$post" buttonText="Submit Reply" />',
            ['post' => $this->post]
        );

        $view->assertSee('Submit Reply')
            ->assertDontSee('Post Comment');
    }

    #[Test]
    public function it_includes_honeypot_field()
    {
        $view = $this->blade(

            '<x-comment-form :post="$post" />',
            ['post' => $this->post]
        );

        $view->assertSee('name="valid_from"', false);
    }

    #[Test]
    public function it_includes_csrf_token()
    {
        $view = $this->blade(

            '<x-comment-form :post="$post" />',
            ['post' => $this->post]
        );

        $view->assertSee('name="_token"', false);
    }

    #[Test]
    public function it_renders_with_parent_id_for_nested_comments()
    {
        $view = $this->blade(

            '<x-comment-form :post="$post" :parentId="5" />',
            ['post' => $this->post]

        );

        $view->assertSee('name="parent_id"', false)
            ->assertSee('value="5"', false);
    }

    #[Test]
    public function it_accepts_custom_css_classes()
    {
        $view = $this->blade(

            '<x-comment-form :post="$post" class="my-custom-class" />',
            ['post' => $this->post]
        );
        $view->assertSee('my-custom-class', false);
    }

    #[Test]
    public function it_displays_validation_errors()
    {
        $view = $this->withViewErrors([
            'name' => 'The name field is required.',
            'email' => 'The email field is required.',
            'content' => 'The content field is required.',
        ])->blade(
            '<x-comment-form :post="$post" />',
            ['post' => $this->post]
        );

        $view->assertSee('The name field is required.')
            ->assertSee('The email field is required.')
            ->assertSee('The content field is required.');
    }

    #[Test]
    public function it_displays_success_message_from_session()
    {
        $view = $this->withSession([
            'success' => 'Comment posted successfully!'])
            ->blade(
                '<x-comment-form :post="$post" />',
                ['post' => $this->post]
            );
        $view->assertSee('Comment posted successfully!');

    }

    #[Test]
    public function it_has_correct_form_action()
    {
        $expectedRoute = route('comments.store', $this->post);

        $view = $this->blade(
            '<x-comment-form :post="$post" />',
            ['post' => $this->post]
        );

        $view->assertSee('action="'.$expectedRoute.'"', false);
    }

    #[Test]
    public function it_applies_errors_styling_to_invalid_fields()
    {
        $view = $this->withViewErrors(['name' => ' Invalid name'])
            ->blade(
                '<x-comment-form :post="$post" />',
                ['post' => $this->post]
            );

        $view->assertSee('border-red-500', false);
    }
}
