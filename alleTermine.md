---
title: alle Termine
layout: default
---
Terminübersicht Bochum Zentrum
==============================
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


<a id="alsenstraße"></a>

Wohnzimmer Alsenstraße e.V.
---------------------------
Alsenstraße 27 <br>
44789 Bochum<br>
repaircafe@das-labor.org<br>
Termine finden alle zwei Monate abwecheslnd in der Alsenstraße und im Labor statt.

<a id="labor"></a>

Labor e.V.
----------
Alleestraße 50 <br>
44793 Bochum <br>
repaircafe@das-labor.org<br>
Termine finden alle zwei Monate abwecheslnd in der Alsenstraße und im Labor statt.

<a id="wattenscheid"></a>

Senioren-Begegnungsstätte "Haus der Caritas"
-------------------------------------------
Caritas-Zentrum Wattenscheid <br>
An der Papenburg 23<br>
44866 Bochum<br>
Telefon: 02327. 9461-26<br>
Termine: Jeden ersten Freitag im Monat. Bei Feiertagen verschiebt sich der Termin um eine Woche.

<a id="gerthe"></a>

Repair Café Bochum Gerthe
-------------------------
Im Quartierstreff 55 +<br>
Gerther Straße 20<br>
44805 Bochum<br>
Termine: Jeden letzten Freitag im Monat 14 bis 17 Uhr.


<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
