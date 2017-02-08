<?php

require_once '../models/class.dbinterface.php';
require_once '../models/class.profile.php';

$pID = $_GET['profile'];

echo "Contenu du profile ".$pID;

$p = new Profile($pID);
?>
<ul>
    <li>
        <strong>Name : </strong>
        <?php echo $p->getName(); ?>
    </li>
    <li>
        <strong>Desc : </strong>
        <?php echo $p->getDesc(); ?>
    </li>
    <li>
        <strong>Nbr views : </strong>
        <?php echo $p->getViews(); ?>
    </li>
    <li>
        <strong>Owner ID : </strong>
        <?php echo $p->getOwner(true); ?>
    </li>
    <li>
        <strong>private : </strong>
        <?php echo $p->isPrivate() ? "YES" : "NO"; ?>
    </li>
</ul>
