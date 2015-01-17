<?php
function check_if_file_is_accessable($filename)
{
	if(!file_exists($filename))
	{
		error_log("Can't open " . $filename);
		return FALSE;
	}

	if(!is_readable($filename))
	{
		error_log("Can't read " . $filename);
		return FALSE;
	}
	return TRUE;
}


/**
* @link http://gist.github.com/385876
*/
function csv_to_array($filename='', $delimiter=',')
{
	if(!check_if_file_is_accessable($filename))
		return FALSE;

	$header = NULL;
	$data = array();
	if (($handle = fopen($filename, 'r')) !== FALSE)
	{
		while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
		{
			if(!$header)
			{
				$header = $row;
			}
			else
			{
				if(count($row) > count($header))
					error_log(var_dump($row));
				while(count($row) < count($header))
				{
					array_push($row,"");
				}
				$data[] = array_combine($header, $row);
			}
		}
		fclose($handle);
	}
	return $data;
}

include 'php-liquid/Liquid.class.php';

define("GUESTBOOK_FILE","../_guestbook/guestbook.yml");
define("LAYOUT_FILE","../_layouts/default.html");
define("CAPTCHA_FILE","../_guestbook/captchas.yml");

date_default_timezone_set("Europe/Berlin");

check_if_file_is_accessable(LAYOUT_FILE);
check_if_file_is_accessable(GUESTBOOK_FILE);
check_if_file_is_accessable(CAPTCHA_FILE);

$index = file_get_contents(LAYOUT_FILE);
$book = yaml_parse_file(GUESTBOOK_FILE);
$captchas = yaml_parse_file(CAPTCHA_FILE);
$reparaturen = csv_to_array("../_data/reparaturen.csv");
$termine = csv_to_array("../_data/termine.csv");


if($book === NULL)
{
	$book = array();
}

if($captchas === NULL)
{
	$captchas = array();
}

$body = "";

if($_SERVER['REQUEST_METHOD'] === "POST")
{
	if($_POST['name'] == "" || $_POST['comment'] == "")// || $_POST['captcha'] == "" || $_POST['answer'] == "")
	{
		$body =
		 	'<div class="ink-alert block" role="alert">' .
			'<h4>Fehlerhafter Eintrag</h4>' .
			'<p>Bitte gib einen Name an, einen nicht leeren Kommentar und beantworte die Sicherheitsfrage.</p>' .
			'</div>';
	}
	else
	{
		//$cap_answer = (int)($_POST['captcha']);
		//$cap_index = (int)($_POST['answer']);

		/*if($cap_index < 0 || $cap_index > 100 || $captchas[$cap_index] !== $cap_answer)
		{
			$body =
				'<div class="ink-alert block" role="alert">' .
				'<h4>Fehlerhafter Eintrag</h4>' .
				'<p>Die Antwort der Sicherheitsfrage war falsch. Bitte versuche es erneut.</p>' .
				'</div>';
		}
		else
		{*/
			$new = array(
				'author' => htmlentities($_POST['name']),
				'comment' => htmlentities($_POST['comment'])
			);

		$unique = true;
			foreach($book as $ent)
			{
				$unique = $unique && !($ent['author'] == $new['author'] && $ent['comment'] == $new['comment']);
			}

			if($unique)
			{
				array_push($book,$new);
				yaml_emit_file(GUESTBOOK_FILE,$book,YAML_UTF8_ENCODING);
			}
			else
			{
				$body =
					'<div class="ink-alert block" role="alert">' .
					'<h4>Fehlerhafter Eintrag</h4>' .
					'<p>Dieser Eintrag existiert bereits.</p>' .
					'</div>';
			}
		//}

		//unset($captchas[$cap_index]);
		yaml_emit_file(CAPTCHA_FILE,$captchas,YAML_UTF8_ENCODING);
	}
}

foreach(array_reverse($book) as $ent)
{
	$body .= '<div class="guestbook-entry">' .
					 '<div class="guestbook-head">' . $ent['author'] . ' sagte:</div>' .
					 '<p class="guestbook-body">' . $ent['comment'] . '</p>' .
					 '</div>';
}

$cap_a = rand(1,10);
$cap_b = rand(1,10);
$cap_index = rand(0,100);
$captchas[$cap_index] = $cap_a + $cap_b;
yaml_emit_file(CAPTCHA_FILE,$captchas,YAML_UTF8_ENCODING);

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
			/*'<div class="control-group">' .
				'<label for="captcha">Bitte trage hier ein was <em>'.(string)($cap_a).' + '.(string)($cap_b).'</em> ist um zu zeigen, dass du ein Mensch bist</label>' .
				'<div class="control">' .
					'<input type="text" id="captcha" name="captcha" />' .
				'</div>' .
				'</div>' .*/
			'<input type="hidden" name="answer" value="' . $cap_index . '" />' .
			'<input type="submit" class="ink-button" value="Abschicken" />' .
		'</form>' .
	'</div>';

$liquid = new LiquidTemplate();
$ctx = array(
	'content' => $body,
	'page' => array(
		'nosidebar' => false
	),
	'site' => array(
		'data' => array(
			'reparaturen' => $reparaturen,
			'termine' => $termine
		),
		'erfolgreich' => sprintf("%0.0f",floor((count($reparaturen)) * 0.7)),
		'time' => time()
	)
);

$index = preg_replace("/---/","",$liquid->parse($index)->render($ctx));
echo $index;
?>
