<?php
global $installFolder;
if (!$_SESSION['user']->isLogged()) {
    header('location: '.$installFolder.'connect');
}
?>

<div class="bs-docs-section">
    <div class="page-header">
        <h1>Classement - Promo <?php echo $_SESSION['user']->getPromo(); ?></h1>
    </div>

    <div class="row">
        <?php
        $users = $_SESSION['user']->getRankedUsers();
        if (!is_array($users)) {
            echo '<div class="panel panel-danger">
                    <div class="panel-heading">
                        <h3 class="panel-title">Une erreur est survenue !</h3>
                    </div>
                    <div class="panel-body">
                        '.$users.'
                    </div>
                </div>';
        } else {
            $paneltype = "primary";
            $old_score = -1;
            $col_size = 12;
            $col_size_sm = 12;
            foreach ($users as $user) {
                if ($old_score != -1 && $user['score'] != $old_score) {
                    $paneltype = "default";
                    $col_size = 4;
                    $col_size_sm = 6;
                }
                echo '<div class="col-sm-'.$col_size_sm.' col-md-'.$col_size.'">';
                echo '<div data-paneltype="'.$paneltype.'" class="panel panel-'.$paneltype.'">';
                echo '<div class="panel-heading clearfix">';
                echo '<h3 class="panel-title">';
                echo $user['login'];
                echo '<span class="badge pull-right">'.(intval(array_keys($users, $user)[0])+1).'</span>';
                if ($paneltype == "primary") {
                    echo '<span class="pull-right glyphicon glyphicon-star"></span> ';
                }
                echo '</h3>';
                echo '</div>';
                echo '<div class="panel-body">';
                setlocale(LC_TIME, 'fr_FR.utf8', 'fra');
                echo '<img class="col-xs-6 col-sm-6 col-md-3" alt="Profile Picture" src="https://cdn.local.epitech.eu/userprofil/profilview/'.$user['login'].'.jpg">';
                echo '<div class="col-xs-6 col-sm-6 col-md-9">';
                if ($paneltype == "primary")
                    echo '<h3>';
                echo 'Score: <span class="text-info">'.$user['score'].'</span><br />';
                echo 'Ville: <span class="text-info">'.$user['city'].'</span>';
                if ($paneltype == "primary")
                    echo '</h3>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                $old_score = $user['score'];
            }
        }
?>
    </div>
</div>
