<?php

declare(strict_types = 1);

namespace App\Http\Requests;

use App\Dto\TaskUpdateDto;
use App\Enums\PriorityEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
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

    public function toDto(): TaskUpdateDto
    {
        return new TaskUpdateDto(
            $this->input('parent_id'),
            $this->input('title'),
            $this->input('description'),
            /*$this->has('priority') ? */PriorityEnum::from($this->input('priority')),
        );
    }
}
