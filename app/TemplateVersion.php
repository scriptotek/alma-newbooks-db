<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TemplateVersion extends Model
{
    public $timestamps = false;

     /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'body', 'created_by', 'created_at', 'template_id', 'parent_version_id',
    ];

    /**
     * The template.
     */
    public function template()
    {
        return $this->belongsTo('App\Template');
    }

    /**
     * The user that created this version.
     */
    public function createdBy()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }

    /**
     * The parent version.
     */
    public function parentVersion()
    {
        return $this->hasOne('App\TemplateVersion', 'id', 'parent_version_id');
    }
}
