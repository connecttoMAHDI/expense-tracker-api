<?php

namespace App\Enum;

enum ExpenseCategory: string
{
    case OTHERS = 'Others';
    case HEALTH = 'Health';
    case CLOTHING = 'Clothing';
    case UTILITIES = 'Utilities';
    case ELECTRONICS = 'Electronics';
    case LEISURE = 'Leisure';
    case GROCERIES = 'Groceries';
}
