<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'subject',
    'memo_number',
    'from_department',
    'to_department',
    'memo_date',
    'category',
    'description',
    'file_path',
    'uploaded_by'
])]
class Memo extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'memo_date' => 'date',
        ];
    }

    /**
     * Get the user who uploaded the memo.
     */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Scope for searching memos.
     */
    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;
        
        return $query->where(function ($q) use ($search) {
            $q->where('subject', 'like', "%{$search}%")
              ->orWhere('memo_number', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('from_department', 'like', "%{$search}%")
              ->orWhere('to_department', 'like', "%{$search}%");
        });
    }

    /**
     * Scope for filtering by category.
     */
    public function scopeByCategory($query, $category)
    {
        if (!$category) return $query;
        return $query->where('category', $category);
    }

    /**
     * Scope for filtering by department.
     */
    public function scopeByDepartment($query, $dept)
    {
        if (!$dept) return $query;
        return $query->where(function ($q) use ($dept) {
            $q->where('from_department', $dept)
              ->orWhere('to_department', $dept);
        });
    }

    /**
     * Scope for filtering by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            return $query->whereBetween('memo_date', [$startDate, $endDate]);
        }
        if ($startDate) {
            return $query->where('memo_date', '>=', $startDate);
        }
        if ($endDate) {
            return $query->where('memo_date', '<=', $endDate);
        }
        return $query;
    }
}
