---
title: alle Termine
layout: default
---
Terminüberischt
==============
<ul>
 {% for ent in site.data.termine %}
	{% capture d1 %}{{ ent.datum | date: '%F' }}{% endcapture %}
        {% capture d2 %}{{ site.time | date: '%F' }}{% endcapture %}
        {% capture d3 %}{{ site.time | date: '%s' | plus: 86400 | date: '%F' }}{% endcapture %}
        {% if d1 >= d3 %}
        	<li>Am {{ ent.datum | localize:"%d. %b %Y" }} {{ ent.ort }} {{ent.uhrzeit}}</li>
        {%endif%}
{% endfor %}
</ul>
