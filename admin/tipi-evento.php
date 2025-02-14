<?php

namespace Utilities;

require_once("../utilities/utilities.php");
require_once("../utilities/DBAccess.php");

use DB\DBAccess;

session_start();

$paginaHTML = file_get_contents("../template/admin/template-admin.html");
$content = file_get_contents("../template/admin/tipi-evento.html");

$title = 'Tipi Evento &minus; Admin &minus; Fungo';
$pageId = 'admin/' . basename(__FILE__, '.php');
$description = 'pagina di amministrazione per la gestione dei tipi evento';
$keywords = 'Fungo, amministrazione, tipi evento';
$menu = get_admin_menu($pageId);
$breadcrumbs = get_breadcrumbs($pageId);
$onload = '';
$classList = 'fullMenu';
$logo = get_content_between_markers($paginaHTML, 'logoLink');

if (!isset($_SESSION["login"])) {
    header("location: ../login.php");
    exit;
}

$connection = DBAccess::get_instance();
$connectionOk = $connection->open_DB_connection();

if ($connectionOk) {
    $messaggiForm = '';
    $messaggiFormHTML = get_content_between_markers($content, 'messaggiForm');
    $messaggioForm = get_content_between_markers($messaggiFormHTML, 'messaggioForm');
    $lista = '';

    if (isset($_GET['errore'])) {
        $messaggiForm .= multi_replace($messaggioForm, [
            '{tipoMessaggio}' => 'inputError',
            '{messaggio}' => "Errore imprevisto"
        ]);
    } elseif (isset($_GET['eliminato'])) {
        if ($_GET['eliminato'] == 'false') {
            $messaggiForm .= multi_replace($messaggioForm, [
                '{tipoMessaggio}' => 'inputError',
                '{messaggio}' => "Errore nell'eliminazione del Tipo Evento"
            ]);
        } elseif ($_GET['eliminato'] == 'true') {
            $messaggiForm .= multi_replace($messaggioForm, [
                '{tipoMessaggio}' => 'successMessage',
                '{messaggio}' => "Tipo Evento eliminato correttamente"
            ]);
        } else {
            $messaggiForm .= multi_replace($messaggioForm, [
                '{tipoMessaggio}' => 'inputError',
                '{messaggio}' => "Errore imprevisto"
            ]);
        }
    } elseif (isset($_GET['aggiunto'])) {
        if ($_GET['aggiunto'] == 'false') {
            $messaggiForm .= multi_replace($messaggioForm, [
                '{tipoMessaggio}' => 'inputError',
                '{messaggio}' => "Errore nell'aggiunta del Tipo Evento"
            ]);
        } elseif ($_GET['aggiunto'] == 'true') {
            $messaggiForm .= multi_replace($messaggioForm, [
                '{tipoMessaggio}' => 'successMessage',
                '{messaggio}' => "Tipo Evento aggiunto correttamente"
            ]);
        } else {
            $messaggiForm .= multi_replace($messaggioForm, [
                '{tipoMessaggio}' => 'inputError',
                '{messaggio}' => "Errore imprevisto"
            ]);
        }
    } elseif (isset($_GET['modificato'])) {
        if ($_GET['modificato'] == 'false') {
            $messaggiForm .= multi_replace($messaggioForm, [
                '{tipoMessaggio}' => 'inputError',
                '{messaggio}' => "Errore nella modifica del Tipo Evento"
            ]);
        } elseif ($_GET['modificato'] == 'true') {
            $messaggiForm .= multi_replace($messaggioForm, [
                '{tipoMessaggio}' => 'successMessage',
                '{messaggio}' => "Tipo Evento modificato correttamente"
            ]);
        } else {
            $messaggiForm .= multi_replace($messaggioForm, [
                '{tipoMessaggio}' => 'inputError',
                '{messaggio}' => "Errore imprevisto"
            ]);
        }
    }

    $tipiEvento = $connection->get_tipi_evento();
    $elementoLista = get_content_between_markers($content, 'elementoLista');
    $nessunElemento = get_content_between_markers($content, 'nessunElemento');

    foreach ($tipiEvento as $tipoEvento) {
        $lista .= multi_replace($elementoLista, [
            '{titolo}' => $tipoEvento['Titolo']
        ]);
    }

    $messaggiFormHTML = $messaggiForm == '' ? '' : replace_content_between_markers($messaggiFormHTML, ['messaggioForm' => $messaggiForm]);
    $lista = $lista == '' ? $nessunElemento : $lista;

    $content = replace_content_between_markers($content, [
        'elementoLista' => $lista,
        'nessunElemento' => '',
        'messaggiForm' => $messaggiFormHTML
    ]);

    $connection->close_DB_connection();
} else {
    header("location: ../errore500.php");
    exit;
}

echo multi_replace(replace_content_between_markers($paginaHTML, [
    'logo' => $logo,
    'breadcrumbs' => $breadcrumbs,
    'menu' => $menu
]), [
    '{title}' => $title,
    '{description}' => $description,
    '{keywords}' => $keywords,
    '{pageId}' => $pageId,
    '{content}' => $content,
    '{onload}' => $onload,
    '{classList}' => $classList
]);
