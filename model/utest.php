<?php

$rpath = dirname(__FILE__) . DIRECTORY_SEPARATOR;
include_once $rpath.'project.php';
include_once $rpath.'notice.php';
include_once $rpath.'../params.php';

class utest
{
    private $id;
    private $score;
    private $command;
    private $std_in;
    private $std_out;
    private $ret_value;
    private $udate;
    private $opt_file;
    
    private $fk_user;
    private $fk_user_login;
    
    public function __construct($id, $score, $command, $std_in, $std_out, $ret_value, $udate, $fk_user, $fk_user_login, $opt_file)
    {
        $this->id = $id;
        $this->score = $score;
        if (substr_count($command, '"') % 2 == 1)
            $command = str_replace('"', '', $command);
         if (substr_count($command, "'") % 2 == 1)
            $command = str_replace("'", "", $command);
        $this->command = $command;
        $this->std_in = $std_in;
        $this->std_out = $std_out;
        $this->ret_value = $ret_value;
        $this->udate = $udate;
        $this->fk_user = $fk_user;
        $this->fk_user_login = $fk_user_login;
        $this->opt_file = $opt_file;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getScore()
    {
        return $this->score;
    }
    
    public function getCommand()
    {
        return $this->command;
    }
    
    public function getStdin()
    {
        return $this->std_in;
    }
    
    public function getStdout()
    {
        return $this->std_out;
    }
    
    public function getReturnValue()
    {
        return $this->ret_value;
    }
    
    public function getDate()
    {
        return $this->udate;
    }
    
    public function getUserid()
    {
        return $this->fk_user;
    }
    
    public function getUsername()
    {
        return $this->fk_user_login;
    }
    
    public function getOptFile()
    {
        return $this->opt_file;
    }
    
    public function getModal()
    {
        $result = '<div class="modal fade" id="m'.$this->getId().'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">'.$this->getCommand().' - Détail du test</h4>
                            </div>
                            <div class="modal-body">';
        if ($this->getStdin() != null) {
            $result .= '<div class="panel panel-default">
                        <div class="panel-heading">Entrée standard attendue</div>
                        <div class="panel-body">
                            '.$this->getStdin().'
                        </div>
                    </div>';
        }
        if ($this->getStdout() != null) {
            $result .= '<div class="panel panel-default">
                        <div class="panel-heading">Sortie standard attendue</div>
                        <div class="panel-body">
                            '.$this->getStdout().'
                        </div>
                    </div>';
        }
        if ($this->getReturnValue() != null) {
            $result .= '<div class="panel panel-default">
                        <div class="panel-heading">Valeur de retour attendue</div>
                        <div class="panel-body">
                            '.$this->getReturnValue().'
                        </div>
                    </div>';
        }
        if ($this->getOptFile() != null) {
            $result .= '<div class="panel panel-default">
                        <div class="panel-heading">Fichier téléchargé</div>
                        <div class="panel-body">
                            <a target="_blank" href="http://pastebin.com/'.$this->getOptFile().'">http://pastebin.com/'.$this->getOptFile().'</a>
                        </div>
                    </div>';
        }
        $result .= '    </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>';
        return $result;
    }
    
    public function toString()
    {
        $res = "# Test propose par ".$this->getUsername()." le ".$this->getDate()->format("d-m-Y H:i:s").PHP_EOL;
        $res = $res."ERRORS=0".PHP_EOL;
        if ($this->getOptFile() != null) {
            $res = $res."curl -o opts/file http://pastebin.com/raw.php?i=".$this->getOptFile().PHP_EOL;
        }
        $res = $res.'echo "${BLU}#####'.str_replace('"', '\\"', $this->getCommand()).'#####${NC}"'.PHP_EOL;
        $res = $res.$this->getCommand()." 0>logs/in 1>logs/out 2>logs/err".PHP_EOL;
        $res = $res."RETVAL=$?".PHP_EOL;
        
        $res = $res.'echo "${BLU}#####'.str_replace('"', '\\"', $this->getCommand()).'#####${NC}" >> $TMPFILE'.PHP_EOL;
        
        $res = $res.'if [ $RETVAL -ge 128 ]'.PHP_EOL;
        $res = $res."then".PHP_EOL;
        $res = $res.'if [ $RETVAL -lt 194 ]'.PHP_EOL;
        $res = $res."then".PHP_EOL;
        $res = $res.'   echo "${RED}WARNING ! Program crashed !${NC}"'.PHP_EOL;
        $res = $res.'   echo "#### ${RED}CRASHED${NC} (return value: $RETVAL)" >> $TMPFILE'.PHP_EOL;
        $res = $res.'   CRASHES=$((CRASHES+1))'.PHP_EOL;
        $res = $res."fi".PHP_EOL;
        $res = $res."fi".PHP_EOL;
        
        if ($this->getReturnValue() != null) {
            $res = $res.'if [ $RETVAL -eq '.$this->getReturnValue()." ]".PHP_EOL;
            $res = $res."then".PHP_EOL;
            $res = $res.'   echo "Return value ${GRN}OK${NC}"'.PHP_EOL;
            $res = $res."else".PHP_EOL;
            $res = $res."   ERRORS=1".PHP_EOL;
            $res = $res.'   echo "Return value ${OGN}failed${NC} (expected '.$this->getReturnValue().', returned $RETVAL)"'.PHP_EOL;
            $res = $res."fi".PHP_EOL;
        }
        
        if ($this->getStdin() != null) {
            $res = $res."if [ \"`cat 'logs/in'`\" == \"`echo '".str_replace('\'', '\\\'', $this->getStdin())."'`\" ]".PHP_EOL;
            $res = $res."then".PHP_EOL;
            $res = $res.'   echo "Standard input ${GRN}OK${NC}"'.PHP_EOL;
            $res = $res."else".PHP_EOL;
            $res = $res."   ERRORS=1".PHP_EOL;
            $res = $res.'   echo "Standard input ${OGN}failed${NC}"'.PHP_EOL;
            $res = $res."fi".PHP_EOL;
        }
        
        if ($this->getStdout() != null) {
            $res = $res."if [ \"`cat 'logs/out'`\" == \"`echo '".str_replace('\'', '\\\'', $this->getStdout())."'`\" ]".PHP_EOL;
            $res = $res."then".PHP_EOL;
            $res = $res.'   echo "Standard output ${GRN}OK${NC}"'.PHP_EOL;
            $res = $res."else".PHP_EOL;
            $res = $res."   ERRORS=1".PHP_EOL;
            $res = $res.'   echo "Standard output ${OGN}failed${NC}"'.PHP_EOL;
            $res = $res."fi".PHP_EOL;
        }
        
        $res = $res.'TESTS=$((TESTS+1))'.PHP_EOL;
        $res = $res."if [ \$ERRORS -eq 0 ]".PHP_EOL;
        $res = $res."then".PHP_EOL;
        $res = $res.'   echo "${BLU}== ${GRN}Test SUCCESS${BLU} ==${NC}"'.PHP_EOL;
        $res = $res.'   echo "####${GRN}Test passed${NC}" >> $TMPFILE'.PHP_EOL;
        $res = $res."else".PHP_EOL;
        $res = $res.'   echo "${BLU}== ${RED}Test FAIL${BLU} ==${NC}"'.PHP_EOL;
        $res = $res.'   FAILS=$((FAILS+1))'.PHP_EOL;
        $res = $res.'   echo "####${RED}Test failed${NC}" >> $TMPFILE'.PHP_EOL;
        $res = $res."fi".PHP_EOL;
        
        if ($this->getReturnValue() != null) {
            $res = $res.'echo "--> PROGRAM RETURN VALUE <--" >> $TMPFILE'.PHP_EOL;
            $res = $res.'echo "$RETVAL" >> $TMPFILE'.PHP_EOL;
            $res = $res.'echo "--- expected" >> $TMPFILE'.PHP_EOL;
            $res = $res.'echo "'.$this->getReturnValue().'" >> $TMPFILE'.PHP_EOL;
        }
        if ($this->getStdin() != null) {
            $res = $res.'echo "--> PROGRAM STDIN <--" >> $TMPFILE'.PHP_EOL;
            $res = $res.'cat logs/in >> $TMPFILE'.PHP_EOL;
            $res = $res.'echo "--- expected" >> $TMPFILE'.PHP_EOL;
            $res = $res.'echo \''.str_replace('\'', '\\\'', $this->getStdin()).'\' >> $TMPFILE'.PHP_EOL;
        }
        if ($this->getStdout() != null) {
            $res = $res.'echo "--> PROGRAM STDOUT <--" >> $TMPFILE'.PHP_EOL;
            $res = $res.'cat logs/out >> $TMPFILE'.PHP_EOL;
            $res = $res.'echo "--- expected" >> $TMPFILE'.PHP_EOL;
            $res = $res.'echo \''.str_replace('\'', '\\\'', $this->getStdout()).'\' >> $TMPFILE'.PHP_EOL;
        }
        
        $res = $res.PHP_EOL;
        
        return $res;
    }
    
    public function upVote($uid)
    {
        if ($uid == $this->fk_user) {
            return 'Impossible de voter pour vous-même';
        }
        global $host, $sqluser, $sqlpass, $bdd, $bddtable;
        $sql = new mysqli($host, $sqluser, $sqlpass, $bdd);
        if (mysqli_connect_error()) {
            return 'Impossible de se connecter au serveur de base de données !';
        }
        $res = $sql->query("SELECT ".$bddtable['votes'].".* FROM ".$bddtable['votes']." WHERE ".$bddtable['votes'].".fk_user=".$uid." AND ".$bddtable['votes'].".fk_utest='".$this->getId()."';");
        if (!$res) {
            $sql->close();
            return 'Erreur lors de la récupération des tests';
        }
        if ($res->num_rows > 0) {
            $res->free();
            $sql->close();
            return 'Erreur, vous avez déjà voté pour ce test';
        }
        $res = $sql->query("UPDATE ".$bddtable['utest']." SET score = score + 1 WHERE ".$bddtable['utest'].".id = ".$this->getId().";");
        $res = $sql->query("INSERT INTO ".$bddtable['votes']." (fk_user, fk_utest) VALUES (".$uid.", ".$this->getId().");");
        $res = $sql->query("UPDATE ".$bddtable['user']." SET score = score + 1 WHERE ".$bddtable['user'].".id = ".$this->fk_user.";");
        $sql->close();
        return 'SUCCESS';
    }
}
