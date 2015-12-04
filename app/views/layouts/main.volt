{{ elements.getMainMenu() }}
{% if elements.showSubMenu() == true %}
    <nav class="navbar navbar-inverse navbar-fixed-top white-color">
        <div class="col-xs-4">
            <div class="inner-nav">
                {{ elements.getLeftSubMenu() }}
            </div>
        </div>
        <div class="col-xs-4 ">
            {% if title is defined %}
                <p class="text-center visible-xs small-size">{{ title }}</p>
                <h2 class="text-center hidden-xs no-margin">{{ title }}</h2>
            {% endif %}
        </div>
        <div class="col-xs-4">
            <div class="inner-nav">
                {{ elements.getRightSubMenu() }}
            </div>
        </div>
     </nav>
{% endif %}
<div class="container">
    {{ flash.output() }}
    {{ content() }}
</div>