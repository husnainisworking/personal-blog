<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('post'));
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
            'tags.*' => 'exists:tags,id'
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [

            'title.required' => 'The  post title is required.',
            'title.max' => 'The post title cannot exceed 255 characters.',
            'content.required' => 'The post content is required.',
            'category_id.exists' => 'The selected category does not exist.',
            'excerpt.max' => 'The excerpt cannot exceed 500 characters.',
            'status.required' => 'The post status is required.',
            'status.in' => 'The post status must be either draft or published.',
            'tags.array' => 'Tags must be provided as an array.',
            'tags.*.exists' => 'One or more selected tags do not exist.'
        ];
    }
}
