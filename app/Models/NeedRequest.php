<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['organization', 'contact_name', 'email', 'phone', 'need_type', 'description', 'location'])]
class NeedRequest extends Model
{
}