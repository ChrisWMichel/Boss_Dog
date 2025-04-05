<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'nullable',
            'deleted_images' => 'nullable|array',
            'deleted_images.*.id' => 'nullable|integer',
            'deleted_images.*.filename' => 'required|string',
            'image_positions' => 'nullable|array',
            'image_positions.*' => 'nullable|integer',
            'categories' => 'nullable|array',
            'categories.*' => 'nullable|integer|exists:categories,id',
            'published' => 'required|boolean',
            'imageUpdated' => 'nullable|boolean',
            'quantity' => 'nullable|integer',
            'no_new_images' => 'nullable|boolean',
        ];
    }

    /**
     * Get all of the input and files for the request.
     *
     * @return array
     */
    public function all($keys = null)
    {
        $data = parent::all($keys);

        // Log the raw request data for debugging
        // \Illuminate\Support\Facades\Log::info('Raw request data in ProductRequest:', [
        //     'files' => $this->allFiles(),
        //     'data' => $data,
        //     'has_images' => $this->hasFile('images'),
        //     'has_images_array' => $this->hasFile('images.0')
        // ]);

        return $data;
    }
}
