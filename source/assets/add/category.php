<?php
/**
 * /assets/add/category.php
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
require_once DIR_INC . '/settings/assets-add-category.inc.php';

$system->authCheck();
$system->readOnlyCheck($_SERVER['HTTP_REFERER']);

$new_category = $sanitize->text($_POST['new_category']);
$new_stakeholder = $sanitize->text($_POST['new_stakeholder']);
$new_notes = $sanitize->text($_POST['new_notes']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($validate->text($new_category)) {

        $pdo = $deeb->cnxx;

        $stmt = $pdo->prepare("
            INSERT INTO categories
            (`name`, stakeholder, notes, created_by, insert_time)
            VALUES
            (:new_category, :new_stakeholder, :new_notes, :created_by, :timestamp)");
        $stmt->bindValue('new_category', $new_category, PDO::PARAM_STR);
        $stmt->bindValue('new_stakeholder', $new_stakeholder, PDO::PARAM_STR);
        $stmt->bindValue('new_notes', $new_notes, PDO::PARAM_LOB);
        $stmt->bindValue('created_by', $_SESSION['s_user_id'], PDO::PARAM_INT);
        $timestamp = $time->stamp();
        $stmt->bindValue('timestamp', $timestamp, PDO::PARAM_STR);
        $stmt->execute();

        $_SESSION['s_message_success'] .= sprintf(_('Category %s added'), $new_category) . '<BR>';

        header("Location: ../categories.php");
        exit;

    } else {

        $_SESSION['s_message_danger'] .= _('Enter the Category name') . '<BR>';

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
echo $form->showInputText('new_category', _('Category Name') . ' (150)', '', $unsanitize->text($new_category), '150', '', '1', '', '');
echo $form->showInputText('new_stakeholder', _('Stakeholder') . ' (100)', '', $unsanitize->text($new_stakeholder), '100', '', '', '', '');
echo $form->showInputTextarea('new_notes', _('Notes'), '', $unsanitize->text($new_notes), '', '', '');
echo $form->showSubmitButton(_('Add Category'), '', '');
echo $form->showFormBottom('');
?>
<?php require_once DIR_INC . '/layout/footer.inc.php'; ?>
</body>
</html>
