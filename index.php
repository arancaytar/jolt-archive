<?php

const POST_PATTERN = '%<div class="posttop"><div class="username">(?<user>.*?)</div><div class="date">(?<date>.*?)</div></div><div class="posttext">(?<post>.*)</div></div><hr />%s';
$request = trim(@$_SERVER['REQUEST_URI'], '/');
$args = explode('/', str_replace(['&', '?'], '/', $request));
array_shift($args);
$page = NULL;
if (preg_match('/page=([0-9]+)/', end($args), $match)) {
  $page = $match[1];
  array_pop($args);
}
$forum = $topic = NULL;

if ($page > 1) {
    $page = "-p-$page";
}
else {
    $page = '';
}
if (count($args) > 0) {
    $forum = (int) $args[0];
    if (count($args) > 1) {
        $topic = (int) $args[1];
        $file = "files/$forum/t-$topic$page.html";
    }
    else {
        $file = "files/$forum/f-$forum$page.html";
    }
} else $file = '';
$fora = [
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
];

foreach ($fora as $fid => $name) {
  $forum_link = $fid != $forum || $topic ? '<a href="/jolt/'.$fid.'">'.$name.'</a>' : $name;
  $nav_links[] = $fid == $forum ? "<strong><em>$forum_link</em></strong>" : $forum_link;
}

const HEAD = <<<DOC
<!DOCTYPE html>
<html>
<head>
DOC;
define('NAVBAR', '<div id="navbar">' . implode(' | ', $nav_links) . '</div><hr />');
const HEADER = '<h1><a href="/jolt">Jolt NS Archives</a></h1>';
const FOOTER = <<<DOC
<footer>
  <p>These are the archives of the Jolt Nationstates Forum, saved on Sunday, the seventh of February, 2010.</p>
  <p>They are hosted here by <a href="mailto:arancaytar@ermarian.net">Arancaytar</a> of the
    <a href="https://ermarian.net/">Ermarian Network</a>, of the
    <a href="https://www.nationstates.net/ermarian">Endless Empire of Ermarian</a>.
    All content is copyright 2002-2010 of the original authors; refer to the
    <a href="/jolt/disclaimer.html">copyright disclaimer</a>.
  </p>
</footer>
DOC;

if (file_exists($file)) {
    $data = file_get_contents($file);
    $data = iconv('windows-1252', 'utf-8', $data);

    $title = preg_match('%<title> (.*) \[Archive\] (( - Page [0-9]+)?) - Jolt Forums</title>%', $data, $match) ? $match[1] . $match[2] : NULL;
    $data = preg_replace('/<!.*?<head>/s', HEAD, $data);
    $data = str_replace('<hr />', '', $data);
    $data = preg_replace('/<meta.*?charset=.*? \/>/', '', $data);
    $data = preg_replace('%<div id="navbar">.*?</p>%s', HEADER . NAVBAR . "<h2>$title</h2>", $data);
    $data = preg_replace('/href="f-([0-9]+)(-p-([0-9]+))\.html"/', 'href="/jolt/$1/page=$3"', $data);
    $data = preg_replace('/href="f-([0-9]+)\.html"/', 'href="/jolt/$1"', $data);
    $data = preg_replace('/href="t-([0-9]+)(-p-([0-9]+))\.html"/', 'href="/jolt/'.$forum.'/$1/page=$3"', $data);
    $data = preg_replace('/href="t-([0-9]+)\.html"/', 'href="/jolt/'.$forum.'/$1"', $data);
    $data = str_replace('<div id="copyright">vBulletin&reg; v3.8.3, Copyright &copy;2000-2010, Jelsoft Enterprises Ltd.</div>', '<hr />'.FOOTER, $data);
    $data = str_replace('<link rel="stylesheet" type="text/css" href="http://forums.joltonline.com/archive/archive.css" />', '<link rel="stylesheet" type="text/css" href="/jolt/style.css" />', $data);
}
elseif ($file) {
    header('HTTP/1.1 404 Not Found');
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
    <h2>404</h2>
    <p>The requested page <em>$request</em> does not exist.</p>
    $FOOTER
  </body>
</html>
DOC;
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
  <body class="mainpage">
    <h1>Jolt NS Archives</h1>
    $NAVBAR
    $FOOTER
  </body>
</html>
DOC;
}
print($data);
