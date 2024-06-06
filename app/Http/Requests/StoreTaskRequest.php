<?php

namespace App\Http\Requests;

use App\Enums\PriorityEnum;
use App\Enums\StatusEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
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
            'status' => [Rule::enum(StatusEnum::class), 'sometimes'],
            'priority' => [Rule::enum(PriorityEnum::class), 'sometimes'],
            'parent_id' => 'sometimes|integer|min:0|exists:tasks,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:10000',
        ];
    }
}
