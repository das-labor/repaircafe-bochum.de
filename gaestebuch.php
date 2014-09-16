<?php
/**
* @link http://gist.github.com/385876
*/
function csv_to_array($filename='', $delimiter=',')
{
    if(!file_exists($filename) || !is_readable($filename))
        return FALSE;

    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE)
    {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
        {
            if(!$header)
                $header = $row;
            else
                $data[] = array_combine($header, $row);
        }
        fclose($handle);
    }
    return $data;
}

include 'php-liquid/Liquid.class.php';

define("GAESTEBUCH_DATEI","../_gaestebuch/gaestebuch.yml");
define("LAYOUT_DATEI","../_layouts/default.html");
define("CAPTCHA_DATEI","../_gaestebuch/captchas.yml");

$index = file_get_contents(LAYOUT_DATEI);
$book = yaml_parse_file(GAESTEBUCH_DATEI);
$captchas = yaml_parse_file(CAPTCHA_DATEI);
$reparaturen = csv_to_array("../_data/reparaturen.csv");
$termine = csv_to_array("../_data/termine.csv");

$body = "";

if($_SERVER['REQUEST_METHOD'] === "POST")
{
	if($_POST['name'] == "" || $_POST['comment'] == "" || $_POST['captcha'] == "" || $_POST['answer'] == "")
	{
		$body =
		 	'<div class="ink-alert block" role="alert">' .
			'<h4>Fehlerhafter Eintrag</h4>' .
			'<p>Bitte gib einen Name an, einen nicht leeren Kommentar und beantworte die Sicherheitsfrage.</p>' .
			'</div>';
	}
	else
	{
		$cap_answer = (int)($_POST['captcha']);
		$cap_index = (int)($_POST['answer']);

		if($cap_index < 0 || $cap_index > 100 || $captchas[$cap_index] !== $cap_answer)
		{
			$body =
				'<div class="ink-alert block" role="alert">' .
				'<h4>Fehlerhafter Eintrag</h4>' .
				'<p>Die Antwort der Sicherheitsfrage war falsch. Bitte versuche es erneut.</p>' .
				'</div>';
		}
		else
		{
			$new = array(
				'autor' => htmlentities($_POST['name']),
				'kommentar' => htmlentities($_POST['comment'])
			);

			$unique = true;
			foreach($book as $ent)
			{
				$unique = $unique && !($ent['autor'] == $new['autor'] && $ent['kommentar'] == $new['kommentar']);
			}

			if($unique)
			{
				array_push($book,$new);
				yaml_emit_file(GAESTEBUCH_DATEI,$book,YAML_UTF8_ENCODING);
			}
			else
			{
				$body =
					'<div class="ink-alert block" role="alert">' .
					'<h4>Fehlerhafter Eintrag</h4>' .
					'<p>Dieser Eintrag existiert bereits.</p>' .
					'</div>';
			}
		}

		unset($captchas[$cap_index]);
		yaml_emit_file(CAPTCHA_DATEI,$captchas,YAML_UTF8_ENCODING);
	}
}

foreach(array_reverse($book) as $ent)
{
	$body .= '<div class="guestbook-entry">' .
					 '<div class="guestbook-head">' . $ent['autor'] . ' sagte:</div>' .
					 '<p class="guestbook-body">' . $ent['kommentar'] . '</p>' .
					 '</div>';
}

$cap_a = rand(1,10);
$cap_b = rand(1,10);
$cap_index = rand(0,100);
$captchas[$cap_index] = $cap_a + $cap_b;
yaml_emit_file(CAPTCHA_DATEI,$captchas,YAML_UTF8_ENCODING);

$body .=
	'<div class="guestbook-form">' .
		'<h2>Eintrag hinterlassen</h2>' .
 		'<form action="" method="POST" class="ink-form">' .
			'<div class="control-group">' .
				'<label for="name">Name</label>' .
				'<div class="control">' .
					'<input id="name" name="name" type="text" placeholder="Dein Name">' .
				'</div>' .
			'</div>' .
			'<div class="control-group">' .
				'<label for="comment">Kommentar</label>' .
				'<div class="control">' .
					'<textarea id="comment" name="comment"></textarea>' .
				'</div>' .
			'</div>' .
			'<div class="control-group">' .
				'<label for="captcha">Bitte trage hier ein was <em>'.(string)($cap_a).' + '.(string)($cap_b).'</em> ist um zu zeigen, dass du ein Mensch bist</label>' .
				'<div class="control">' .
					'<input type="text" id="captcha" name="captcha" />' .
				'</div>' .
			'</div>' .
			'<input type="hidden" name="answer" value="' . $cap_index . '" />' .
			'<input type="submit" class="ink-button" value="Abschicken" />' .
		'</form>' .
	'</div>';

$liquid = new LiquidTemplate();
$ctx = array(
	'content' => $body,
	'page' => array(
		'nosidebar' => false,
		'data' => array(
			'reparaturen' => $reparaturen,
			'termine' => $termine
		)
	)
);

$index = preg_replace("/---/","",$liquid->parse($index)->render($ctx));
echo $index;
?>
