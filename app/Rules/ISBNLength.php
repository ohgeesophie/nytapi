<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ISBNLength implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $len = strlen($value);
        return in_array($len, [0, 10, 13]);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute must be 10 or 13 chars in length';
    }
}