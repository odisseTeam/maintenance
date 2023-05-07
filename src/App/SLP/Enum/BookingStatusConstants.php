<?php
namespace Odisse\Maintenance\App\SLP\Enum;

/**
 * Created by PhpStorm.
 * User: hedi
 * Date: 10/24/2019
 * Time: 3:58 PM
 */
class BookingStatusConstants
{
    public const Reserved = 1;
    public const Active = 2;
    public const Cancelled = 3;
    public const Finished = 4;
    public const TemporaryHold = 5;
    public const HOLD = 6;
    public const Cancel_Without_Charge = 7;

}
