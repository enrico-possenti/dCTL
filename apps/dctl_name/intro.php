<?php
 if (!defined('_INCLUDE')) die('"'.__FILE__.'" not executable... me, i abort.');

?>

<h2>Introduzione</h2>

<b>
Questa funzione permette di gestire una banca dati di riferimento per i nomi propri.<br />
La banca dati prevede l'associazione di molteplici forme varianti ad una sola Forma Normalizzata, ritornando per ciascuna forma un codice KEY relativo comunque alla Forma Normalizzata.<br />
<br />
&Egrave; possibile registrare i nomi in Forma Normalizzata o come variante, in pi&ugrave; lingue e permettere la memorizzazione centralizzata delle informazioni.<br />
<br />
&Egrave; possibile interrogare la banca dati per recuperare:<br />
&#160;&#160;- un nome<br />
&#160;&#160;- un codice KEY<br />
&#160;&#160;- l'elenco delle forme normali<br />
&#160;&#160;- l'elenco delle forme varianti<br />
<br />
Il codice KEY &egrave; da inserire come attributo dell'elemento NAME nella codifica TEI:<br />
</b>
&#160;&#160;p.es. &lt;name key=&quot;000042&quot;&gt;Aristotelem&lt;name&gt;<br />
<br />
<h3>Regole per la scrittura dei nomi</h3><br />
Fermo restando che la forma regolare è quella conforme all&apos;OPAC della Biblioteca Nazionale Centrale di Firenze, questa &egrave; la lista dei criteri generali da seguire:
<ul>
<li> non adoperare caratteri in stampatello;</li>
<li> nelle forme semplici dei nomi (es: Cecilia Gallerani) indicare sempre: Cognome, Nome ("Gallerani, Cecilia"). Inserire quindi tra le varianti l'eventuale forma Nome Cognome presente nel testo (Cecilia Gallerani);</li>
<li> per i nomi composti da un nome ed un cognome che indica la provenienza (Leonardo da Vinci), la forma regolare è Nome: Cognome ("Leonardo: da Vinci"). Anche in questo caso, se nel testo è riportata la forma Nome Cognome, questa andrà inserita come variante;</li>
<li> per i nomi mitologici e classici (Ettore, Antenore) si usa la forma latina ("Hector", "Antenor");</li>
<li> per i personaggi storici romani (Marco Aurelio), controllare ed usare i patronimici corretti ("Augustus, Marcus Aurelius Antoninus");</li>
<li> stesso vale per i personaggi storici italiani (Ludovico il Moro, nella forma regolare è "Sforza, Maria Ludovico detto Il Moro");</li>
<li> per i santi e le sante, la forma regolare è Nome latino (santo): "Petrus (santo)". Nel caso siano apostoli: "Marcus: Apostolus (santo)";</li>
<li> per i papi la forma regolare è Nome latino (papa; seguito eventualmente da punto e virgola e numero cardinale): Bonifacius (papa; 1);</li>
<li> per re, imperatori, e altre cariche politiche o religiose è necessario rifarsi alla OPAC, là dove un nome non è presente ognuno si comporterà secondo propria discrezione (magari riferendondo a tutto il gruppo la sua soluzione);</li>
<li> prima di attribuire una nuova key ad un nome, siete pregati/e di controllare SEMPRE che questo non sia già stato registrato...nell'elenco, infatti, c'è più di un caso di stessi nomi registrati con key diverse.</li>
</ul>
