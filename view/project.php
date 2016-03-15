<?php
global $installFolder;
if (!$_SESSION['user']->isLogged()) {
    header('location: '.$installFolder.'connect');
}
if (!isset($_SESSION['keys']) || count($_SESSION['keys']) < 1) {
    $_SESSION['user']->addNotice('Projet manquant', 'Une erreur est survenu lors du chargement du projet.', 'danger');
    header('location: ./home');
}

$project = $_SESSION['user']->getProject($_SESSION['keys'][1]);
if (!$project) {
    $_SESSION['user']->addNotice('Projet inconnu', 'Ce projet n\'existe pas.', 'danger');
    header('location: ../home');
}

?>

<!-- ---------- -->
<!-- Contribute -->
<!-- ---------- -->
<?php if (count($_SESSION['keys']) >= 2 && $_SESSION['keys'][2] == 'contribute') : ?>

<div class="bs-docs-section">
    <div class="page-header">
        <h1><?php echo $project->getTitle(); ?>
            <a class="btn btn-primary btn-sm" onclick="document.location.href='./project/<?php echo $project->getId(); ?>'">Retour aux tests</a>
        </h1>
    </div>

    <div class="row">
        <form class="well">
            <fieldset>
                <legend>Votre contribution</legend>
                
                <div class="form-group">
                    <label class="control-label" for="nut_cmd">Commande</label>
                    <div class="input-group">
                        <span class="input-group-addon">$</span>
                        <input type="text" class="form-control" id="nut_cmd" placeholder="Commande">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="nut_stdin" class="control-label">Entrée standard</label>
                    <div>
                        <textarea class="form-control" rows="3" id="nut_stdin"></textarea>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="nut_stdout" class="control-label">Sortie standard attendue</label>
                    <div>
                        <textarea class="form-control" rows="3" id="nut_stdout"></textarea>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="control-label" for="nut_retval">Valeur de retour attendue</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="nut_retval" placeholder="Valeur de retour">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="control-label" for="nut_opt_file">Fichier supplémentaire</label>
                    <div class="input-group">
                        <span class="input-group-addon" aria-hidden="true"><span class="glyphicon glyphicon-file"></span></span>
                        <input type="text" class="form-control" id="nut_opt_file" placeholder="Code pastebin">
                    </div>
                </div>
                
                <div class="form-group">
                    <div>
                        <button id="nut_reset" type="reset" class="btn btn-default">Annuler</button>
                        <button id="nut_submit" type="button" class="btn btn-primary" onclick="addUtest(<?php echo $project->getId(); ?>);">Contribuer !</button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>

<div class="bs-docs-content">  
    <div class="row">
        <div class="well">
            <h3>Informations utiles:</h3>
            Faîtes attention ! Vos tests ne seront pas modifiables !<br /><br />
            Les champs marqués d'une '<span style='color: red;'>*</span>' sont obligatoires.<br />
            Les autres champs sont optionnels, vous pouvez les laisser vides si vous ne souhaitez pas les utiliser.<br /><br />
            Si vous voulez faire télécharger un fichier à l'utilisateur, envoyez le sur <a href="http://www.pastebin.com/" target="_blank">pastebin</a> et entrez le code <a href="http://www.pastebin.com/" target="_blank">pastebin</a> comme ci-dessous:<br />
            <img src="./image/tuto_pastebin.png" /><br /><br />
            Votre fichier pastebin sera télécharger dans 'opt/file'.<br />
            <span class="text-muted">(exemple de commande: ./302poignee opt/file 5 6)</span><br /><br />
        </div>
    </div>
</div>

<!-- ------- -->
<!-- Project -->
<!-- ------- -->
<?php else : ?>

<div class="bs-docs-section">
    <div class="page-header">
        <h1><?php echo $project->getTitle(); ?>
            <a class="btn btn-primary btn-sm" onclick="document.location.href='./project/<?php echo $project->getId(); ?>/contribute'">Contribuer !</a>
            <a class="btn btn-sm btn-success" href="./download.php" target="_blank">Télécharger les tests !</a>
        </h1>
    </div>

    <div class="row">
        <?php
        $utests = $project->getUTests();
        $_SESSION['tmp'] = serialize($utests);
        $_SESSION['download_ids'] = '';
        if (!$utests) {
            echo '<div style="cursor: pointer;" rel="tooltip" data-placement="top" data-original-title="Cliquez pour contribuer !" class="panel panel-info" onclick="document.location.href=\'./project/'.$project->getId().'/contribute\'">
            <div class="panel-heading">
                <h3 class="panel-title">Soyez le premier !</h3>
            </div>
            <div class="panel-body">
                Aucun test n\'a été proposé pour ce projet :/
            </div>
        </div>';
        } elseif (!is_array($utests)) {
            echo '<div class="panel panel-danger">
                    <div class="panel-heading">
                        <h3 class="panel-title">Une erreur est survenue !</h3>
                    </div>
                    <div class="panel-body">
                        '.$utests.'
                    </div>
                </div>';
        } else {
            $paneltype = "primary";
            $old_score = -1;
            foreach ($utests as $utest) {
                if ($old_score != -1 && $utest->getScore() != $old_score) {
                    $paneltype = "default";
                }
                $datatitle = "Score (+1)";
                if ($utest->getUserId() == $_SESSION['user']->getId()) {
                    $datatitle = "Impossible de voter pour vous-même !";
                }
                echo '<div rel="tooltip" data-placement="top" data-original-title="Sélectionner ce test" data-paneltype="'.$paneltype.'" class="panel panel-'.$paneltype.'" style="cursor: pointer;" onclick="addDownload(this, '.$utest->getId().')">';
                echo '<div class="panel-heading clearfix">';
                echo '<h3 class="panel-title">';
                echo $utest->getCommand();
                echo '<span style="margin-left: 5px;" class="glyphicon glyphicon-search" rel="tooltip" data-placement="top" data-original-title="Apperçu" style="cursor: pointer;" onclick="$(\'#m'.$utest->getId().'\').modal(); event.cancelBubble=true;"></span>';
                echo '<span class="badge pull-right" rel="tooltip" data-placement="top" data-original-title="'.$datatitle.'" style="cursor: pointer;" onclick="upvote(this, '.$project->getId().', '.$utest->getId().');event.cancelBubble=true;">'.$utest->getScore().'</span>';
                if ($paneltype == "primary") {
                    echo '<span class="pull-right glyphicon glyphicon-star"></span> ';
                }
                echo '</h3>';
                echo '</div>';
                echo '<div class="panel-body">';
                setlocale(LC_TIME, 'fr_FR.utf8', 'fra');
                echo 'Proposé par <span class="text-info">'.$utest->getUsername().'</span> le <span class="text-primary">'.utf8_encode(strftime("%d %B %Y", strtotime($utest->getDate()->format("Y-m-d")))).'</span> à <span class="text-primary">'.$utest->getDate()->format("H\hi").'</span>';
                echo '</div>';
                echo '</div>';
                echo $utest->getModal();
                $old_score = $utest->getScore();
            }
        }
?>
    </div>
</div>

<?php endif; ?>

<!-- Scripts -->
<script type="application/javascript" src="js/addDownload.js"></script>
<script type="application/javascript" src="js/addUtest.js"></script>
<script type="application/javascript" src="js/upvote.js"></script>
<script type="text/javascript">
    $(function () {
        $("[rel='tooltip']").tooltip();
    });
</script>