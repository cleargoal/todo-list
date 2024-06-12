<?php

namespace App\Http\Requests;

use App\Enums\PriorityEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'parent_id' => 'sometimes|nullable|integer|required_without_all:priority,description,title',
            'title' => 'sometimes|nullable|string|max:255|required_without_all:priority,description,parent_id',
            'description' => 'sometimes|nullable|string|max:10000|required_without_all:priority,title,parent_id',
            'priority' => [Rule::enum(PriorityEnum::class), 'sometimes', 'nullable', 'required_without_all:title,description,parent_id'],
        ];
    }
}
