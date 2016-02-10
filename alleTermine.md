---
title: alle Termine
layout: default
---
Terminübersicht
===============
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



Wohnzimmer Alsenstraße e.V.
---------------------------
Alsenstraße 27 // 44789 Bochum<br>
repaircafe@das-labor.org<br>


Labor e.V.
----------
Alleestraße 50 <br>
44793 Bochum <br>
repaircafe@das-labor.org<br>


Senioren-Begegnungsstätte "Haus der Caritas"
-------------------------------------------
Caritas-Zentrum Wattenscheid <br>
An der Papenburg 23<br>
44866 Bochum<br>
Telefon: 02327. 9461-26<br>
