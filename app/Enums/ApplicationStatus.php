<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case Submitted = 'submitted';
    case DdVerified = 'dd_verified';
    case UnderScrutiny = 'under_scrutiny';
    case Eligible = 'eligible';
    case Rejected = 'rejected';
    case Shortlisted = 'shortlisted';
    case Appeared = 'appeared';
    case Qualified = 'qualified';
    case DvPending = 'dv_pending';
    case Selected = 'selected';
    case FinalSelected = 'final_selected';
    case Waiting = 'waiting';
}
