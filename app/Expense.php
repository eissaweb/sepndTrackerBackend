<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
	protected $fillable = [
		'user_id',
		'category_id',
		'amount',
		'product_name',
		'notes',
		'spent_at'
	];
    public function category()
    {
    	return $this->belongsTo(Category::class, 'category_id');
    }
    public function user()
    {
    	return $this->belongsTo(User::class, 'user_id');
    }
    public $timestamps = [
    	'spent_at'
    ];
}
