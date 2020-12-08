<?php

namespace Ejetar\RestFormRequest;

use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;

class FormRequest extends BaseFormRequest {
	/**
	 * Create the default validator instance.
	 *
	 * @param \Illuminate\Contracts\Validation\Factory $factory
	 * @return \Illuminate\Contracts\Validation\Validator
	 * @throws Exceptions\InvalidRulesException
	 */
	protected function createDefaultValidator(ValidationFactory $factory) {
		return $factory->make(
			$this->validationData(),
			RestFormRequest::validation_rules($this->container->call([$this, 'rules'])),
			$this->messages(),
			$this->attributes()
		);
	}
}