<?php

namespace App\Enums;

use App\Traits\EnumValues;

enum Status: string
{
    use EnumValues;

    case ACTIVE = "ACTIVE";
    case INACTIVE = "INACTIVE";
    case PENDING = "PENDING";
    case APPROVED = "APPROVED";
    case REJECTED = "REJECTED";
    case COMPLETED = "COMPLETED";
    case DUE = "DUE";
    case OVERDUE = "OVERDUE";
    case CANCELLED = "CANCELLED";
    case SOLDOUT = "sold out";
    case INSTOCK = "in stock";


}
