<?php

use App\Template;
use Illuminate\Database\Seeder;

class TemplatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $template1 = Template::create([
            'name' => 'Default RSS template without receiving date',
        ])->addVersionAndSave('{% if cover %}
    <img src="{{ cover }}" alt="Cover image"/>
{% endif %}
<div>
    {% if author %}
        {{ author }}
    {% endif %}
    {% if edition or publisher or publication_date %}
        ({%
            if edition %}{{ edition }}, {% endif %}{%
            if publisher %}{{ publisher }}, {% endif %}{%
            if publication_date %}{{ publication_date }}{%
            endif %})
    {% endif %}
</div>
{% if series %}
    <div>Series: <em>{{ series }}</em></div>
{% endif %}
{% block content %}{% endblock %}', 1);

        Template::create([
            'name' => 'Default RSS template with receiving date',
        ])->addVersionAndSave('{% extends "template' . $template1->id . '" %}
{% block content %}
<div>
    <span class="published-date">
        {% if receiving_date %}
            Received in the library {{ receiving_date | dateformat(\'%e %B\', \'en_US\') }}.
        {% elseif activation_date %}
            E-book activated {{ activation_date | dateformat(\'%e %B\', \'en_US\') }}.
        {% elseif sent_date %}
            Ordered {{ sent_date | dateformat(\'%e %B\', \'en_US\') }}.
        {% endif %}
    </span>
</div>
{% endblock %}', 1);
    }
}
