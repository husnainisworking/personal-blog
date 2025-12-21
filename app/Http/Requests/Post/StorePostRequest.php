<?php

namespace App\Http\Requests\Post;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Post::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'nullable|exists:categories,id',
            'excerpt' => 'nullable|max:500',
            'status' => 'required|in:draft,published',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',

            // Image upload validation
            'featured_image' => [

                'nullable',
                'image',
                'mimes:jpeg,jpg,png,gif,webp',
                'max:2048',
                'dimensions:min_width=100,min_height=100,max-width=4000,max_height=4000',
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [

            'title.required' => 'The post title is required.',
            'title.max' => 'The post title cannot exceed 255 characters.',
            'content.required' => 'The post content is required.',
            'category_id.exists' => 'The selected category does not exist.',
            'excerpt.max' => 'The excerpt cannot exceed 500 characters.',
            'status.required' => 'The post status is required.',
            'status.in' => 'The post status must be either draft or published.',
            'tags.array' => 'Tags must be provided as an array.',
            'tags.*.exists' => 'One or more selected tags do not exist.',

            // Image validation messages
            'featured_image.image' => 'The featured image must be an image file.',
            'featured_image.mimes' => 'The featured image must be a file of type: jpeg,jpg, png, gif, or webp.',
            'featured_image.max' => 'The featured image size cannot exceed 2MB.',
            'featured_image.dimensions' => 'The featured image must be between 100x100 and 4000x4000 pixels.',

        ];
    }
}
