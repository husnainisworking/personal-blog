<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Comments are public, so anyone can submit
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [

                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[\pL\s\'\-\.]+$/u', // Only letters, spaces, apostrophes, hyphens.
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
            ],
            'content' => [
                'required',
                'string',
                'min:10',
                'max:1000',
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [

            'name.required' => 'Your name is required.',
            'name.regex' => 'Name can only contain letters, spaces, apostrophes, and hyphens.',
            'name.min' => 'Name must be at least 2 characters.',
            'name.max' => 'Name cannot exceed 255 characters.',
            'email.required' => 'Your email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'Email cannot exceed 255 characters.',
            'content.required' => 'Comment content is required.',
            'content.min' => 'Comment must be at least 10 characters.',
            'content.max' => 'Comment cannot exceed 1000 characters.',
        ];
    }
}
