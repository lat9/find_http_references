<?php
// -----
// Find "HTTP" References in database and language elements.  Created by lat9.
// Copyright (C) 2017-2018, Vinos de Frutas Tropicales https://vinosdefrutastropicales.com
//
include 'includes/application_top.php';

// -----
// We'll log the findings in /logs/http_references.log.
//
$logfile = DIR_FS_LOGS . '/http_references.log';
error_log('Finding http:// references, report run on ' . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL, 3, $logfile);

echo "Searching for http:// references, v1.0.0 &hellip;<br /><br />";

// -----
// First, look in the various language-related directories for http:// references, discounting
// those in the files' header comments pointing to zen-cart.com.
//
$language_directory = DIR_FS_CATALOG . DIR_WS_LANGUAGES;
$directory = new RecursiveDirectoryIterator($language_directory);
$iterator = new RecursiveIteratorIterator($directory);
$php_files = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
$references = 0;
$language_directory = substr($language_directory, 0, -1);
error_log('Inspecting ' . DIR_FS_CATALOG . DIR_WS_LANGUAGES . ', all .php files ...' . PHP_EOL, 3, $logfile);
foreach($php_files as $name => $object){
    $file_info = file_get_contents($name);
    if (stripos(str_replace(array('http://www.zen-cart', 'http://www.oscommerce'), '', $file_info), 'http://') !== false) {
        $references++;
        error_log("\t\t" . str_replace($language_directory, '', $name) . PHP_EOL, 3, $logfile);
    }
}
$message = 'Found ' . $references . ' in language files.';
echo "$message<br />";
error_log($message . PHP_EOL . PHP_EOL, 3, $logfile);
unset($file_info, $php_files, $iterator, $directory);

// -----
// Next, look in the various module-related directories for http:// references, discounting
// those in the files' header comments pointing to zen-cart.com.
//
$modules_directory = DIR_FS_CATALOG . DIR_WS_MODULES;
$directory = new RecursiveDirectoryIterator($modules_directory);
$iterator = new RecursiveIteratorIterator($directory);
$php_files = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
$references = 0;
$modules_directory = substr($modules_directory, 0, -1);
error_log('Inspecting ' . DIR_FS_CATALOG . DIR_WS_MODULES . ', all .php files ...' . PHP_EOL, 3, $logfile);
foreach($php_files as $name => $object){
    $file_info = file_get_contents($name);
    if (stripos(str_replace(array('http://www.zen-cart', 'http://www.oscommerce'), '', $file_info), 'http://') !== false) {
        $references++;
        error_log("\t\t" . str_replace($modules_directory, '', $name) . PHP_EOL, 3, $logfile);
    }
}
$message = 'Found ' . $references . ' in module files.';
echo "$message<br />";
error_log($message . PHP_EOL . PHP_EOL, 3, $logfile);
unset($file_info, $php_files, $iterator, $directory);

// -----
// Next, look in the various template-related directories for http:// references, discounting
// those in the files' header comments pointing to zen-cart.com.
//
$template_directory = DIR_FS_CATALOG . DIR_WS_TEMPLATES;
$directory = new RecursiveDirectoryIterator($template_directory);
$iterator = new RecursiveIteratorIterator($directory);
$php_files = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
$references = 0;
$template_directory = substr($template_directory, 0, -1);
error_log('Inspecting ' . DIR_FS_CATALOG . DIR_WS_TEMPLATES . ', all .php files ...' . PHP_EOL, 3, $logfile);
foreach($php_files as $name => $object){
    $file_info = file_get_contents($name);
    if (stripos(str_replace(array('http://www.zen-cart', 'http://www.oscommerce'), '', $file_info), 'http://') !== false) {
        $references++;
        error_log("\t\t" . str_replace($template_directory, '', $name) . PHP_EOL, 3, $logfile);
    }
}
$message = 'Found ' . $references . ' in template files.';
echo "$message<br />";
error_log($message . PHP_EOL . PHP_EOL, 3, $logfile);
unset($file_info, $php_files, $iterator, $directory);

// -----
// Now, check the products' descriptions for http:// references ...
//
$descriptions = $db->Execute(
    "SELECT products_id, language_id, products_name, products_description
       FROM " . TABLE_PRODUCTS_DESCRIPTION . "
   ORDER BY products_name ASC, products_id"
);
if (!$descriptions->EOF) {
    error_log('Inspecting ' . $descriptions->RecordCount() . ' product descriptions ...' . PHP_EOL, 3, $logfile);
    $references = 0;
    while (!$descriptions->EOF) {
        if (stripos($descriptions->fields['products_description'], 'http://') !== false) {
            $references++;
            error_log("\t\t" . $descriptions->fields['products_name'] . ' [' . $descriptions->fields['products_id'] . '], language_id = ' . $descriptions->fields['language_id'] . PHP_EOL, 3, $logfile);
        }
        $descriptions->MoveNext();
    }
    $message = "Found $references in the product descriptions.";
    echo "$message<br />";
    error_log($messages . PHP_EOL . PHP_EOL, 3, $logfile);
}
unset($descriptions);

// -----
// Next up, http:// references in any categories' descriptions ...
//
$descriptions = $db->Execute(
    "SELECT categories_id, language_id, categories_name, categories_description
       FROM " . TABLE_CATEGORIES_DESCRIPTION . "
   ORDER BY categories_name ASC, categories_id"
);
if (!$descriptions->EOF) {
    error_log('Inspecting ' . $descriptions->RecordCount() . ' category descriptions ...' . PHP_EOL, 3, $logfile);
    $references = 0;
    while (!$descriptions->EOF) {
        if (stripos($descriptions->fields['categories_description'], 'http://') !== false) {
            $references++;
            error_log("\t\t" . $descriptions->fields['categories_name'] . ' [' . $descriptions->fields['categories_id'] . '], language_id = ' . $descriptions->fields['language_id'] . PHP_EOL, 3, $logfile);
        }
        $descriptions->MoveNext();
    }
    $message = "Found $references in the category descriptions.";
    echo "$message<br />";
    error_log($message . PHP_EOL . PHP_EOL, 3, $logfile);
}
unset($descriptions);

// -----
// Finally, check for http:// references in EZ-Pages' titles and html-text ...
//
$ezpages = $db->Execute(
    "SELECT pages_id, languages_id, pages_title, pages_html_text
       FROM " . TABLE_EZPAGES . "
   ORDER BY pages_title ASC, pages_id"
);
if (!$ezpages->EOF) {
    error_log('Inspecting ' . $ezpages->RecordCount() . ' EZ-Pages ...' . PHP_EOL, 3, $logfile);
    $references = 0;
    while (!$ezpages->EOF) {
        if (stripos($ezpages->fields['pages_html_text'], 'http://') !== false) {
            $references++;
            error_log("\t\t" . $ezpages->fields['pages_title'] . ' [' . $ezpages->fields['pages_id'] . '], languages_id = ' . $ezpages->fields['languages_id'] . PHP_EOL, 3, $logfile);
        }
        $ezpages->MoveNext();
    }
    $message = "Found $references in EZ-Pages HTML text.";
    echo "$message<br />";
    error_log($message . PHP_EOL . PHP_EOL, 3, $logfile);
}
unset($ezpages);

echo "<br /><br />All finished, if additional messages were displayed above this message, check $logfile for details.";

include 'includes/application_bottom.php';