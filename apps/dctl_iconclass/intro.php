<?php
 if (!defined('_INCLUDE')) die('"'.__FILE__.'" not executable... me, i abort.');

?>

<h2>Introduzione</h2>

<b>
Questa funzione permette di gestire una banca dati di riferimento per i codici Iconclass.<br />
La banca dati prevede l'associazione di un codice Iconclass ad un soggetto, ritornando per ciascun elemento un codice KEY.<br />
<br />
&Egrave; possibile interrogare la banca dati per recuperare:<br />
&#160;&#160;- un soggetto<br />
&#160;&#160;- un codice KEY<br />
&#160;&#160;- un codice Iconclass<br />
<br />
Il codice KEY &egrave; da inserire come attributo <strong>key</strong> dell'elemento <strong>&lt;dctl:topic&gt;</strong> nella codifica TEI:<br />
</b>
&#160;&#160;p.es. &lt;dctl:topic key=&quot;000042&quot;&gt;eremita&lt;dctl:topic&gt;<br />
<br />
