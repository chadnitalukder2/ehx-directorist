<?php
namespace EhxDirectorist\Http\Requests;

class StoreCategoryRequest extends RequestGuard {
    
    /**
     * Define validation rules
     */
    public function rules() {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'slug' => 'nullable|string',
        ];
    }

    /**
     * Define custom validation messages
     */
    public function messages() {
        return [
            'name.required' => esc_html__('Category name is required.', 'ehx-directorist'),
            'name.string' => esc_html__('Category name must be a string.', 'ehx-directorist'),
            'name.max' => esc_html__('Category name must not exceed 255 characters.', 'ehx-directorist'),
        ];
    }

    /**
     * Define sanitization rules
     */
    public function sanitize() {
        return [
            'name' => 'sanitize_text_field',
            'description' => 'wp_kses_post',
            'slug' => 'sanitize_text_field'
        ];
    }
}
