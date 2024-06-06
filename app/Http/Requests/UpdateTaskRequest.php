<?php

namespace App\Http\Requests;

use App\Enums\PriorityEnum;
use App\Enums\StatusEnum;
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => [Rule::enum(StatusEnum::class), 'sometimes', 'required_without_all:priority,title,description'],
            'priority' => [Rule::enum(PriorityEnum::class), 'sometimes', 'required_without_all:status,title,description'],
            'parent_id' => 'sometimes|integer|exclude',
            'title' => 'sometimes|string|max:255|required_without_all:priority,status,description',
            'description' => 'sometimes|string|max:10000|required_without_all:priority,title,status',
        ];
    }
}
