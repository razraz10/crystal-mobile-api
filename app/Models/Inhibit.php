<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inhibit extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at',  'is_deleted', 'updated_by', 'created_by'];



    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by')
            ////select only the fileds with associated permission..
            ->select('id', 'name', 'employee_type', 'permission_id')
            ->with('permission');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by')
            ////select only the fileds with associated permission..
            ->select('id', 'name', 'employee_type', 'permission_id')
            ->with('permission');
    }
}
