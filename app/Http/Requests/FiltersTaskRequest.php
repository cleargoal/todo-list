<?php

namespace App\Http\Requests;

use App\Enums\PriorityEnum;
use App\Enums\StatusEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FiltersTaskRequest extends FormRequest
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
            'title' => 'sometimes|nullable|string|max:255|required_without_all:priority,description,status',
            'description' => 'sometimes|nullable|string|max:10000|required_without_all:priority,title,status',
            'priority' => [Rule::enum(PriorityEnum::class), 'sometimes', 'nullable', 'int', 'required_without_all:title,description,status'],
            'status' => [Rule::enum(StatusEnum::class), 'sometimes', 'nullable', 'required_without_all:title,description,status'],
        ];
    }

    /**
     * Prepare for validation
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => $this->bodytitle,
            'description' => $this->bodydescription,
            'priority' => $this->bodypriority,
            'status' => $this->bodystatus,
        ]);
    }
}
