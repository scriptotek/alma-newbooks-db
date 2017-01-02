<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'current_version_id',
    ];

    /**
     * Reports that use this template.
     */
    public function reports()
    {
        return $this->hasMany('App\Report');
    }

    /**
     * The template versions.
     */
    public function versions()
    {
        return $this->hasMany('App\TemplateVersion');
    }

    /**
     * The current version.
     */
    public function currentVersion()
    {
        return $this->hasOne('App\TemplateVersion', 'id', 'current_version_id');
    }

    public function render(Document $doc, $templateBody = null)
    {
        $twig = new \Twig_Environment(new \Twig_Loader_String());

        $humanDiffFilter = new \Twig_SimpleFilter('humandiff', function ($string, array $options = array()) {
            return (new \Carbon\Carbon($string))->diffForHumans();
        });
        $twig->addFilter($humanDiffFilter);

        $dateformatFilter = new \Twig_SimpleFilter('dateformat', function ($string, array $options = array()) {
            return (new \Carbon\Carbon($string))->formatLocalized($options[0]);
        }, array('is_variadic' => true));
        $twig->addFilter($dateformatFilter);

        return $twig->render($templateBody ?: $this->currentVersion->body, $doc->toArray());
    }

    public function addVersionAndSave($body)
    {
        $parent_version_id = $this->current_version_id;

        $version = TemplateVersion::create([
            'template_id'       => $this->id,
            'parent_version_id' => $parent_version_id,
            'body'              => $body,
            'created_by'        => \Auth::user()->id,
            'created_at'        => Carbon::now(),
        ]);

        $this->current_version_id = $version->id;
        $this->save();
    }

}
