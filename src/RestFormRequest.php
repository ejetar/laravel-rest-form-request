<?php

namespace Ejetar\RestFormRequest;

use Ejetar\RestFormRequest\Exceptions\InvalidRulesException;

class RestFormRequest {
	/**
	 * This method is used to orchestrate the required fields within a FormRequest, based on the HTTP method.
	 *
	 * According to the REST standard, both PUT and PATCH are used to update an entity,
	 * however, with PUT you need to enter value for all fields, but with PATCH, you can
	 * inform only those you want to modify.
	 *
	 * From this method, informing "required" for a field, means that it will be mandatory for POST.
	 * Example
	 * Suppose I have the following code in a FormRequest:
	 * validation_rules([
	 * 		'id'        => 'integer',
	 *      'person_id' => 'integer|required',
	 *      'sub'       => 'string|max:255',
	 *      'password'  => 'string|max:255',
	 *      'status'    => 'in:ACTIVE,INACTIVE',
	 *
	 *      'roles[]'   => 'array'
	 * ]);
	 *
	 * The above code means that:
	 * - For the POST method, the only required field is 'person_id'. Because 'required' was informed;
	 * - For the PUT method, ALL the fields informed are mandatory (regardless of whether or not the 'required' is in the string)
	 * - For the PATCH method, NO fields are required;
	 *
	 * @param array $validation_rules - Validation rules. See https://laravel.com/docs/6.x/validation#available-validation-rules
	 *
	 * @return array - Final validation rules.
	 * @throws InvalidRulesException
	 */
	public static function validation_rules(array $validation_rules) {
		return array_map(function($item) {
			$rules = [];

			switch(gettype($item)) {
				case "string":
					$rules = explode("|", $item);
					break;

				case "array":
					$rules = $item;
					//do nothing
					break;

				default:
					throw new InvalidRulesException();
			}

			$indexOf_required = array_search('required', $rules);

			if (request()->method() === 'PATCH')
				if ($indexOf_required !== false)
					unset($rules[$indexOf_required]);
				elseif (request()->method() === 'PUT')
					if ($indexOf_required === false)
						array_unshift($rules, 'required');

			return $rules;
		}, $validation_rules);
	}
}
