<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Market extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at',  'is_deleted', 'updated_by', 'created_by', 'month_id'];

    public function month()
    {
        return $this->belongsTo(Month::class);
    }
    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by')
            ////select only the fileds with associated permission..
            ->select('id', 'name', 'employee_type', 'permission_id')
            ->with('permission:id,permission_name');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by')
            ////select only the fileds with associated permission..
            ->select('id', 'name', 'employee_type', 'permission_id')
            ->with('permission:id,permission_name');
    }
}
