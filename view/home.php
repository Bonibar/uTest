<?php
global $installFolder;
if (!$_SESSION['user']->isLogged()) {
    header('location: '.$installFolder.'connect');
}
?>

<?php if ($_SESSION['user']->isLogged()) : ?>
<div class="bs-docs-content">
    <div class="page-header">
        <h1>Bonjour, <strong><?php echo ucwords($_SESSION['user']->getName()); ?></strong> !</h1>
    </div>
    <div class="row">
        <div class="well">
            <div class="media">
                <div class="media-left">
                    <img height="128" class="media-object" src="https://cdn.local.epitech.eu/userprofil/profilview/<?php echo $_SESSION['user']->getLogin(); ?>.jpg" alt="<?php echo $_SESSION['user']->getLogin(); ?>">
                </div>
                <div class="media-body">
                    <h4 class="media-heading"><?php echo ucwords($_SESSION['user']->getName()); ?></h4>
                    Login: <?php echo $_SESSION['user']->getLogin(); ?><br />
                    Rang: <?php global $rank; echo $rank[$_SESSION['user']->getLevel()]; ?><br />
                    Ville: <?php echo $_SESSION['user']->getCity(); ?><br />
                    Score: <span class="badge"><?php echo $_SESSION['user']->getScore(); ?></span><br />
                </div>
            </div>
        </div>
        <div class="well">
            <h4>Meilleurs tests</h4>
            
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
            <?php
                $first = 'class="active"';
                foreach ($_SESSION['user']->getProjects() as $project) {
                    echo '<li role="presentation" '.$first.'><a href="#'.$project->getId().'" aria-controls="'.$project->getId().'" role="tab" data-toggle="tab">'.$project->getTitle().'</a></li>';
                    $first = '';
                }
            ?>
            </ul>
            
            <!-- Tab panes -->
            <div class="tab-content">
            <?php
            $utests = array();
            function score($a, $b) {
                return $b->getScore() - $a->getScore();
            }
            $first = 'active';
            foreach ($_SESSION['user']->getProjects() as $project) {
                echo '<div role="tabpanel" class="tab-pane '.$first.'" id="'.$project->getId().'">';
                $tests = $project->getUtests();
                if ($tests != null) {
                    usort($tests, "score");
                    $paneltype = "primary";
                    $utest = $tests[0];
                    $old_score = -1;
                    foreach ($tests as $utest) {
                        if ($old_score != -1 && $utest->getScore() != $old_score) {
                            break;
                        }
                        array_push($utests, $utest);
                        $datatitle = "Score (+1)";
                        if ($utest->getUserId() == $_SESSION['user']->getId()) {
                            $datatitle = "Impossible de voter pour vous-même !";
                        }
                        echo '<div rel="tooltip" data-placement="bottom" data-original-title="Télécharger ce test" data-paneltype="'.$paneltype.'" class="panel panel-'.$paneltype.'" style="cursor: pointer;" onclick="addDownload(null, '.$utest->getId().'); window.open(\'./download.php\');">';
                        echo '<div class="panel-heading clearfix">';
                        echo '<h3 class="panel-title">';
                        echo $utest->getCommand();
                        echo '<span style="margin-left: 5px;" class="glyphicon glyphicon-search" rel="tooltip" data-placement="top" data-original-title="Apperçu" style="cursor: pointer;" onclick="$(\'#m'.$utest->getId().'\').modal(); event.cancelBubble=true;"></span>';
                        echo '<span class="badge pull-right" rel="tooltip" data-placement="bottom" data-original-title="'.$datatitle.'" style="cursor: pointer;" onclick="upvote(this, '.$project->getId().', '.$utest->getId().');event.cancelBubble=true;">'.$utest->getScore().'</span>';
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
                } else {
                        echo '<div style="cursor: pointer;" rel="tooltip" data-placement="bottom" data-original-title="Cliquez pour contribuer !" class="panel panel-info" onclick="document.location.href=\'./project/'.$project->getId().'/contribute\'">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Soyez le premier !</h3>
                                </div>
                                <div class="panel-body">
                                    Aucun test n\'a été proposé pour ce projet :/
                                </div>
                            </div>';
                }
                echo '</div>';
                $first = '';
            }
            $_SESSION['tmp'] = serialize($utests);
            $_SESSION['download_ids'] = '';
            ?>
  </div>

</div>
    </div>
</div>

<?php endif; ?>

<!-- Scripts -->
<script type="application/javascript" src="js/addDownload.js"></script>
<script type="application/javascript" src="js/upvote.js"></script>
<script type="text/javascript">
    $(function () {
        $("[rel='tooltip']").tooltip();
    });
</script>