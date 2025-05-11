<?php
namespace EhxDirectorist\Http\Requests;

class StoreListingRequest extends RequestGuard {
    
    /**
     * Define validation rules
     */
    public function rules() {
      
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'website_url' => 'required|url|max:255',
            'address' => 'required|string|max:255',
            'category_id' => 'nullable|array',
            'tag_id' => 'nullable|array',
            'logo' => 'required',
            'image' => 'nullable',
            'short_description' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'postal_code' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'meta' => 'nullable|array',
        ];
     
    }

    /**
     * Define custom validation messages
     */
    public function messages() {
        return [
            'name.required' => esc_html__('Listing name is required.', 'ehx-directorist'),
            'email.required' => esc_html__('Email is required.', 'ehx-directorist'),
            'email.email' => esc_html__('Email must be valid.', 'ehx-directorist'),
            'phone.required' => esc_html__('Phone is required.', 'ehx-directorist'),
            'website_url.required' => esc_html__('Website is required.', 'ehx-directorist'),
            'website_url.url' => esc_html__('Website must be a valid URL.', 'ehx-directorist'),
            'address.required' => esc_html__('Address is required.', 'ehx-directorist'),
            'short_description.required' => esc_html__('Short description is required.', 'ehx-directorist'),
            'logo.required' => esc_html__('Logo is required.', 'ehx-directorist'),
        ];
    }

    /**
     * Define sanitization rules
     */
    public function sanitize() {
        return [
            'name' => 'sanitize_text_field',
            'email' => 'sanitize_email',
            'phone' => 'sanitize_text_field',
            'website_url' => 'esc_url_raw',
            'address' => 'sanitize_text_field',
            'short_description' => 'sanitize_text_field',
            'description' => 'wp_kses_post',
            'latitude' => 'floatval',
            'longitude' => 'floatval',
            'postal_code' => 'sanitize_text_field',
            'city' => 'sanitize_text_field',
        ];
    }
}
