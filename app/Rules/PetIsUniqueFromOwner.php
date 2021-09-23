<?php

namespace App\Rules;

use App\Models\Pet;
use Illuminate\Contracts\Validation\Rule;

class PetIsUniqueFromOwner implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    public $owner;
    public $pet;

    public function __construct($owner_id, $pet_id = null)
    {
        $this->owner = $owner_id;
        $this->pet = $pet_id;
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
        $petFromOwner = Pet::where([['owner_id', $this->owner], ['name', $value]])->first();

        if ($petFromOwner) {
            if ($this->pet && $petFromOwner->id == $this->pet) {
                return true;
            }
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'El propietario tiene una mascota con el mismo nombre.';
    }
}
