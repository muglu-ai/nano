<?php

namespace App\Rules;

use App\Models\SponsorItem;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidSponsorItemCount implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //

    }
    protected $itemId;

    public function __construct($itemId)
    {
        $this->itemId = $itemId;
    }

    public function passes($attribute, $value)
    {
        $sponsorItem = SponsorItem::find($this->itemId);
        return $sponsorItem && $value <= $sponsorItem->count;
    }

    public function message()
    {
        return 'The count exceeds the available sponsorship items.';
    }
}
