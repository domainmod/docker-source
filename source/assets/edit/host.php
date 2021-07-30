<?php
/**
 * /assets/edit/host.php
 *
 * This file is part of DomainMOD, an open source domain and internet asset manager.
 * Copyright (c) 2010-2021 Greg Chetcuti <greg@chetcuti.com>
 *
 * Project: http://domainmod.org   Author: http://chetcuti.com
 *
 * DomainMOD is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version.
 *
 * DomainMOD is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with DomainMOD. If not, see
 * http://www.gnu.org/licenses/.
 *
 */
?>
<?php
require_once __DIR__ . '/../../_includes/start-session.inc.php';
require_once __DIR__ . '/../../_includes/init.inc.php';
require_once DIR_INC . '/config.inc.php';
require_once DIR_INC . '/software.inc.php';
require_once DIR_ROOT . '/vendor/autoload.php';

$deeb = DomainMOD\Database::getInstance();
$system = new DomainMOD\System();
$layout = new DomainMOD\Layout();
$time = new DomainMOD\Time();
$form = new DomainMOD\Form();
$sanitize = new DomainMOD\Sanitize();
$unsanitize = new DomainMOD\Unsanitize();
$validate = new DomainMOD\Validate();

require_once DIR_INC . '/head.inc.php';
require_once DIR_INC . '/debug.inc.php';
require_once DIR_INC . '/settings/assets-edit-host.inc.php';

$system->authCheck();
$pdo = $deeb->cnxx;

$del = (int) $_GET['del'];

$whid = (int) $_GET['whid'];
$new_host = $sanitize->text($_REQUEST['new_host']);
$new_url = $sanitize->text($_POST['new_url']);
$new_notes = $sanitize->text($_REQUEST['new_notes']);
$new_whid = (int) $_REQUEST['new_whid'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $system->readOnlyCheck($_SERVER['HTTP_REFERER']);

    if ($validate->text($new_host)) {

        $stmt = $pdo->prepare("
            UPDATE hosting
            SET `name` = :new_host,
                url = :new_url,
                notes = :new_notes,
                update_time = :timestamp
            WHERE id = :new_whid");
        $stmt->bindValue('new_host', $new_host, PDO::PARAM_STR);
        $stmt->bindValue('new_url', $new_url, PDO::PARAM_STR);
        $stmt->bindValue('new_notes', $new_notes, PDO::PARAM_LOB);
        $timestamp = $time->stamp();
        $stmt->bindValue('timestamp', $timestamp, PDO::PARAM_STR);
        $stmt->bindValue('new_whid', $new_whid, PDO::PARAM_INT);
        $stmt->execute();

        $whid = $new_whid;

        $_SESSION['s_message_success'] .= sprintf(_('Web Host %s updated'), $new_host) . '<BR>';

        header("Location: ../hosting.php");
        exit;

    } else {

        if (!$validate->text($new_host)) $_SESSION['s_message_danger'] .= _("Enter the Web Host's name") . '<BR>';

    }

} else {

    $stmt = $pdo->prepare("
        SELECT `name`, url, notes
        FROM hosting
        WHERE id = :whid");
    $stmt->bindValue('whid', $whid, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();
    $stmt->closeCursor();

    if ($result) {

        $new_host = $result->name;
        $new_url = $result->url;
        $new_notes = $result->notes;

    }

}

if ($del === 1) {

    $stmt = $pdo->prepare("
        SELECT hosting_id
        FROM domains
        WHERE hosting_id = :whid
        LIMIT 1");
    $stmt->bindValue('whid', $whid, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchColumn();

    if ($result) {

        $_SESSION['s_message_danger'] .= _('This Web Host has domains associated with it and cannot be deleted') . '<BR>';

    } else {

        $stmt = $pdo->prepare("
            DELETE FROM hosting
            WHERE id = :whid");
        $stmt->bindValue('whid', $whid, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['s_message_success'] .= sprintf(_('Web Host %s deleted'), $new_host) . '<BR>';

        header("Location: ../hosting.php");
        exit;

    }

}
?>
<?php require_once DIR_INC . '/doctype.inc.php'; ?>
<html>
<head>
    <title><?php echo $layout->pageTitle($page_title); ?></title>
    <?php require_once DIR_INC . '/layout/head-tags.inc.php'; ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed text-sm select2-red<?php echo $layout->bodyDarkMode(); ?>">
<?php require_once DIR_INC . '/layout/header.inc.php'; ?>
<?php
echo $form->showFormTop('');
echo $form->showInputText('new_host', _('Web Host Name') . ' (100)', '', $unsanitize->text($new_host), '100', '', '1', '', '');
echo $form->showInputText('new_url', _("Web Host's URL") . ' (100)', '', $unsanitize->text($new_url), '100', '', '', '', '');
echo $form->showInputTextarea('new_notes', _('Notes'), '', $unsanitize->text($new_notes), '', '', '');
echo $form->showInputHidden('new_whid', $whid);
echo $form->showSubmitButton(_('Save'), '', '');
echo $form->showFormBottom('');

$layout->deleteButton(_('Web Host'), $new_host, 'host.php?whid=' . $whid . '&del=1');
?>
<?php require_once DIR_INC . '/layout/footer.inc.php'; ?>
</body>
</html>
