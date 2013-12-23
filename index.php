<?php
$args = explode('/', @$_SERVER['REQUEST_URI']);
array_shift($args);
array_shift($args);
$page = NULL;
if (preg_match('/page=([0-9]+)/', $_SERVER['REQUEST_URI'], $match)) {
  $page = $match[1];
}
$forum = $topic = NULL;

if ($page > 1) {
    $page = "-p-$page";
}
else {
    $page = '';
}
if (count($args) > 0) {
    $forum = ($args[0])*1;
    if (count($args) > 1) {
        $topic = ($args[1])*1;
        $file = "files/$forum/t-$topic$page.html";
    }
    else {
        $file = "files/$forum/f-$forum$page.html";
    }
}
$fora = array(
    1223 => 'NationStates',
    1224 => 'Technical',
    1225 => 'World Assembly',
    1226 => 'Jennifer Government (book, movie)',
    1227 => 'General',
    1230 => 'International Incidents',
    1231 => 'Moderation',
    1232 => 'Got Issues?',
    1233 => 'Archive',
    1234 => 'Gameplay',
);

foreach ($fora as $fid => $name) {
  $nav_links[] = $fid == $forum ? "<strong>$name</strong>" : '<a href="/jolt/'.$fid.'">'.$name.'</a>';
}

define('NAVBAR', '<div id="navbar">'.implode(' | ', $nav_links).'</div>');
define('HEADER', '<h1><a href="/jolt">Jolt NS Archives</a></h1>');
define('FOOTER', '<p>
    These are the archives of the Jolt Nationstates Forum, saved on Sunday, the seventh of February, 2010.
    </p>
    <p>
      They are hosted here by <a href="mailto:arancaytar@ermarian.net">Arancaytar</a> of the <a href="http://ermarian.net/">Ermarian Network</a>, of the <a href="http://www.nationstates.net/ermarian">Endless Empire of Ermarian</a>. All content is copyright 2002-2010 of the original authors; refer to the <a href="/disclaimer.html">copyright disclaimer</a>.
</p>');

if (file_exists($file)) {
    $data = file_get_contents($file);
    $data = preg_replace('%<div id="navbar">.*?</p>%s', HEADER . NAVBAR, $data);
    $data = preg_replace('/href="f-([0-9]+)(-p-([0-9]+))\.html"/', 'href="/jolt/$1&amp;page=$3"', $data);
    $data = preg_replace('/href="f-([0-9]+)\.html"/', 'href="/jolt/$1"', $data);
    $data = preg_replace('/href="t-([0-9]+)(-p-([0-9]+))\.html"/', 'href="/jolt/'.$forum.'/$1&amp;page=$3"', $data);
    $data = preg_replace('/href="t-([0-9]+)\.html"/', 'href="/jolt/'.$forum.'/$1"', $data);
    $data = str_replace('<div id="copyright">vBulletin&reg; v3.8.3, Copyright &copy;2000-2010, Jelsoft Enterprises Ltd.</div>', '<small><em>'.FOOTER.'</em></small>', $data);
    $data = str_replace('<link rel="stylesheet" type="text/css" href="http://forums.joltonline.com/archive/archive.css" />', '<link rel="stylesheet" type="text/css" href="/jolt/style.css" />', $data);
}
else {
    $NAVBAR = NAVBAR;
    $FOOTER = FOOTER;
    $data = <<<DOC
<!DOCTYPE html>
<html>
  <head>
    <title>Jolt NS Archives</title>
    <link rel="stylesheet" type="text/css" href="/jolt/style.css" />
  </head>
  <body>
    <h1>Jolt NS Archives</h1>
    $NAVBAR
    <hr />
    $FOOTER
  </body>
</html>
DOC;
}
print($data);
