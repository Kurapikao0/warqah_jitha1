<?php

namespace App\Enums;

enum CustomDesignRequestStatus: string
{
    case New = 'new';
    case InReview = 'in_review';
    case Quoted = 'quoted';
    case Converted = 'converted';
    case Rejected = 'rejected';
}
