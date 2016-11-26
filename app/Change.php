<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Change extends Model
{
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['document_id', 'key', 'old_value', 'new_value', 'created_at'];

     /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
    ];

    /**
     * Get the related document.
     */
    public function document()
    {
        return $this->belongsTo('App\Document');
    }
}
