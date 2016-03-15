<?php

$rpath = dirname(__FILE__) . DIRECTORY_SEPARATOR;
include $rpath.'params.php';
include $rpath.'model/router.php';
include $rpath.'model/user.php';

session_start();

if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = new User;
}
if (!isset($_SESSION['keys'])) {
    $_SESSION['keys'] = array();
}
if ($_SESSION['user']->isLogged()) {
    $_SESSION['user']->getInternalUser();
    if ($_SESSION['user']->getLevel() < $min_level) {
        $_SESSION['user']->logout();
        header("Refresh:0");
    }
}

//$_SESSION['user']->addNotice('title', 'test-notice', 'info', true);

$router = new Router;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>uTest :: <?php echo $router->getSubtitle(); ?></title>
        <meta charset="utf-8">
        <base href="<?php echo $installFolder; ?>" />
        <link rel="icon" href="image/favicon.ico" />
        <link rel="stylesheet" type="text/css" href="css/bootstrap-theme.min.css">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <script type="application/javascript" src="js/jquery-1.11.3.min.js"></script>
    </head>
    <body>
        <header>
            <nav class="navbar navbar-default navbar-fixed-top">
                <div class="container">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                            <span class="sr-only">Menu</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="./home">uTest</a>
                    </div>
                    
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <?php if ($_SESSION['user']->isLogged()) : ?>
                        <ul class="nav navbar-nav">
                            <li><a href="./home"><?php echo $_SESSION['user']->getLogin(); ?></a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Projets <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <?php
                                        $projects = $_SESSION['user']->getProjects();
                                    foreach ($projects as $project) {
                                        echo '<li><a class="project" href="./project/'.$project->getId().'">';
                                        echo $project->getTitle();
                                        echo '</a></li>';
                                    }
                                    ?>
                                </ul>
                            </li>
                            <li><a href="./rank">Classement</a></li>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <li><a href="./disconnect">Se déconnecter</a></li>
                        </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </nav>
        </header>
        <article class="container">
            <div class="page-header">
            <?php
            $notices = $_SESSION['user']->getNotices();
            $_SESSION['user']->clearNotices();
            
            foreach ($notices as $notice) {
                echo $notice->toString();
            }
            ?>
            </div>
        <?php
            $_SESSION['keys'] = $router->getKeys();
            $router->route();
        ?>
        </article>
        <footer>
            <table>
                <colgroup>
                    <col style="width: 33%">
                    <col style="width: auto">
                    <col style="width: 33%">
                </colgroup>
                <tr>
                    <td><a class="flink" href="http://intra.epitech.eu/" target="_blank">Intra Epitech</a></td>
                    <td><a class="flink" href="mailto://utest@zwertv.fr">Rapporter un bug</a></td>
                    <td><a class="flink" href="">uTest © 2015</a></td>
                </tr>
            </table>
        </footer>    
    </body>
    <!-- Scripts -->
    <script type="application/javascript" src="js/bootstrap.min.js"></script>
</html>
<?php
