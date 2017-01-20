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
        $all = [];
        foreach (Template::with('currentVersion')->get() as $tpl) {
            $name = 'template' . $tpl->id;
            $all[$name] = $tpl->currentVersion->body;
        }

        $currentTemplateName = 'template' . $this->id;

        if (!is_null($templateBody)) {
            $all[$currentTemplateName] = $templateBody;
        }

        $twig = new \Twig_Environment(new \Twig_Loader_Array($all));

        $humanDiffFilter = new \Twig_SimpleFilter('humandiff', function ($string, array $options = array()) {
            if (isset($options[0])) {
                setlocale(\LC_TIME, $options[0]);
                \Carbon\Carbon::setLocale(explode('_', $options[0])[0]);
            }
            return (new \Carbon\Carbon($string))->diffForHumans();
        }, array('is_variadic' => true));
        $twig->addFilter($humanDiffFilter);

        $dateformatFilter = new \Twig_SimpleFilter('dateformat', function ($string, array $options = array()) {
            if (isset($options[1])) {
                setlocale(\LC_TIME, $options[1]);
                \Carbon\Carbon::setLocale(explode('_', $options[1])[0]);
            }
            return (new \Carbon\Carbon($string))->formatLocalized($options[0]);
        }, array('is_variadic' => true));
        $twig->addFilter($dateformatFilter);

        return $twig->render($currentTemplateName, $doc->toArray());
    }

    public function addVersionAndSave($body, $created_by)
    {
        $parent_version_id = $this->current_version_id;

        $version = TemplateVersion::create([
            'template_id'       => $this->id,
            'parent_version_id' => $parent_version_id,
            'body'              => $body,
            'created_by'        => $created_by,
            'created_at'        => Carbon::now(),
        ]);

        $this->current_version_id = $version->id;
        $this->save();

        return $this;
    }

}
