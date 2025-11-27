<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Stay()
 * @method static static Monthly()
 */
final class ReservationType extends Enum
{
    const Stay = 'séjour';
    const Monthly = 'mensuelle';
}
