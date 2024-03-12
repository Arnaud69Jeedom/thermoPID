<?php
/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';

class thermoPID extends eqLogic {
  /*     * *************************Attributs****************************** */

  /*
  * Permet de définir les possibilités de personnalisation du widget (en cas d'utilisation de la fonction 'toHtml' par exemple)
  * Tableau multidimensionnel - exemple: array('custom' => true, 'custom::layout' => false)
  public static $_widgetPossibility = array();
  */

  /*
  * Permet de crypter/décrypter automatiquement des champs de configuration du plugin
  * Exemple : "param1" & "param2" seront cryptés mais pas "param3"
  public static $_encryptConfigKey = array('param1', 'param2');
  */

  /*     * ***********************Methode static*************************** */

  /*
  * Fonction exécutée automatiquement toutes les minutes par Jeedom
  public static function cron() { }
  */

  /*
  * Fonction exécutée automatiquement toutes les 5 minutes par Jeedom
  public static function cron5() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 10 minutes par Jeedom
  */
  public static function cron10() {
    log::add(__CLASS__, 'debug', '*** cron10 ***');

    foreach (eqLogic::byType(__CLASS__) as $eqLogic) {
      if ($eqLogic->getIsEnable()) {
        $eqLogic->execute();
      }
    }
  }
  

  /*
  * Fonction exécutée automatiquement toutes les 15 minutes par Jeedom
  public static function cron15() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 30 minutes par Jeedom
  public static function cron30() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les heures par Jeedom
  public static function cronHourly() {}
  */

  /*
  * Fonction exécutée automatiquement tous les jours par Jeedom
  public static function cronDaily() {}
  */
  
  /*
  * Permet de déclencher une action avant modification d'une variable de configuration du plugin
  * Exemple avec la variable "param3"
  public static function preConfig_param3( $value ) {
    // do some checks or modify on $value
    return $value;
  }
  */

  /*
  * Permet de déclencher une action après modification d'une variable de configuration du plugin
  * Exemple avec la variable "param3"
  public static function postConfig_param3($value) {
    // no return value
  }
  */

  /*
   * Permet d'indiquer des éléments supplémentaires à remonter dans les informations de configuration
   * lors de la création semi-automatique d'un post sur le forum community
   public static function getConfigForCommunity() {
      return "les infos essentiel de mon plugin";
   }
   */

  /*     * *********************Méthodes d'instance************************* */

  // Fonction exécutée automatiquement avant la création de l'équipement
  public function preInsert() {
  }

  // Fonction exécutée automatiquement après la création de l'équipement
  public function postInsert() {
  }

  // Fonction exécutée automatiquement avant la mise à jour de l'équipement
  public function preUpdate() {
  }

  // Fonction exécutée automatiquement après la mise à jour de l'équipement
  public function postUpdate() {
  }

  // Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement
  public function preSave() {
  }

  // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement
  public function postSave() {
    log::add(__CLASS__, 'debug', '*** save ***');
    log::add(__CLASS__, 'info', 'save sur : ' . $this->getHumanName());

    $cmdConsigne = $this->getCmd(null, 'consigne');
    if (!is_object($cmdConsigne)) {
      $cmdConsigne = new thermoPIDCmd();
      $cmdConsigne->setLogicalId('consigne');
      $cmdConsigne->setName(__('consigne', __FILE__));
      $cmdConsigne->setType('info');
      $cmdConsigne->setSubType('numeric');

      $cmdConsigne->setConfiguration('minValue', 15);
      $cmdConsigne->setConfiguration('maxValue', 28);
      $cmdConsigne->setUnite('°C');
    }    
    $cmdConsigne->setEqLogic_id($this->getId());
    $cmdConsigne->save();
    $targetTmpCmdId = $cmdConsigne->getId();

    $cmd = $this->getCmd(null, 'consigne_cursor');
    if (!is_object($cmd)) {
      $cmd = new thermoPIDCmd();
      $cmd->setLogicalId('consigne_cursor');
      $cmd->setName(__('consigne_cursor', __FILE__));
      $cmd->setType('action');
      $cmd->setSubType('slider');
      
      $cmd->setValue($cmdConsigne->getId());
      $cmd->setConfiguration('minValue', 15);
      $cmd->setConfiguration('maxValue', 28);
      $cmd->setUnite('°C');

      $cmd->setConfiguration('message', '#slider#');
    }    
    $cmd->setEqLogic_id($this->getId());
    // Liaison des deux commandes
    $cmd->setValue($targetTmpCmdId);
    $cmd->save();

    $cmd = $this->getCmd(null, 'refresh');
    if (!is_object($cmd)) {
      $cmd = new thermoPIDCmd();
      $cmd->setLogicalId('refresh');
      $cmd->setName(__('Rafraichir', __FILE__));
      $cmd->setType('action');
      $cmd->setSubType('other');
    }    
    $cmd->setEqLogic_id($this->getId());
    $cmd->save();
  }

  // Fonction exécutée automatiquement avant la suppression de l'équipement
  public function preRemove() {
  }

  // Fonction exécutée automatiquement après la suppression de l'équipement
  public function postRemove() {
  }

  /*
  * Permet de crypter/décrypter automatiquement des champs de configuration des équipements
  * Exemple avec le champ "Mot de passe" (password)
  public function decrypt() {
    $this->setConfiguration('password', utils::decrypt($this->getConfiguration('password')));
  }
  public function encrypt() {
    $this->setConfiguration('password', utils::encrypt($this->getConfiguration('password')));
  }
  */

  /*
  * Permet de modifier l'affichage du widget (également utilisable par les commandes)
  public function toHtml($_version = 'dashboard') {}
  */

  /*     * **********************Getteur Setteur*************************** */
  private function getMyConfiguration(): StdClass {
    $configuration = new StdClass();

    // Obtenir la commande temperature
    $temperature = $this->getConfiguration('temperature');
    $temperature = str_replace('#', '', $temperature);
    if ($temperature != '') {
      $cmd = cmd::byId($temperature);
      if ($cmd == null) {
        log::add(__CLASS__, 'error', '  Mauvaise temperature :' . $temperature);
        throw new Exception('Mauvaise temperature');
      } else {
        $value = $cmd->execCmd();
        $configuration->temperature = floatval($value);
      }
    } else {
        log::add(__CLASS__, 'error', '  temperature non renseignée');
        throw new Exception('temperature non renseignée');
    }

    // Obtenir la commande Etat Equipement
    $stateAsset = $this->getConfiguration('stateAsset');
    $stateAsset = str_replace('#', '', $stateAsset);
    if ($stateAsset != '') {
      $cmd = cmd::byId($stateAsset);
      if ($cmd == null) {
        log::add(__CLASS__, 'error', '  Mauvaise stateAsset :' . $stateAsset);
        throw new Exception('Mauvaise stateAsset');
      } else {
        $value = $cmd->execCmd();
        $configuration->stateAsset = floatval($value);
      }
    } else {
        log::add(__CLASS__, 'error', '  stateAsset non renseignée');
        throw new Exception('stateAsset non renseignée');
    }

    // Obtenir la commande read consigne
    $readConsigne = $this->getConfiguration('readConsigne');
    $readConsigne = str_replace('#', '', $readConsigne);
    if ($readConsigne != '') {
      $cmd = cmd::byId($readConsigne);
      if ($cmd == null) {
        log::add(__CLASS__, 'error', '  Mauvaise readConsigne :' . $readConsigne);
        throw new Exception('Mauvaise readConsigne');
      } else {
        $value = $cmd->execCmd();
        $configuration->readConsigne = floatval($value);
      }
    } else {
        log::add(__CLASS__, 'error', '  readConsigne non renseignée');
        throw new Exception('readConsigne non renseignée');
    }

    // integr mini
    $minIntegr = $this->getConfiguration('minIntegr');  
    if ($minIntegr != '' && is_numeric($minIntegr)) {
        $configuration->minIntegr = floatval($minIntegr);
    } else {
        log::add(__CLASS__, 'error', '  minIntegr error :' . $minIntegr);
        throw new Exception('minIntegr renseignée');
    }

    // integr maxi
    $maxIntegr = $this->getConfiguration('maxIntegr');  
    if ($maxIntegr != '' && is_numeric($maxIntegr)) {
        $configuration->maxIntegr = floatval($maxIntegr);
    } else {
        log::add(__CLASS__, 'error', '  maxIntegr renseignée:' . $maxIntegr);
        throw new Exception('maxIntegr non renseignée');
    }

    // total min
    $minTotal = $this->getConfiguration('minTotal');  
    if ($minTotal != '' && is_numeric($minTotal)) {
        $configuration->minTotal = floatval($minTotal);
    } else {
        log::add(__CLASS__, 'error', '  minTotal renseignée:' . $minTotal);
        throw new Exception('minTotal non renseignée');
    }

    // total max
    $maxTotal = $this->getConfiguration('maxTotal');  
    if ($maxTotal != '' && is_numeric($maxTotal)) {
        $configuration->maxTotal = floatval($maxTotal);
    } else {
        log::add(__CLASS__, 'error', '  maxTotal renseignée:' . $maxTotal);
        throw new Exception('maxTotal non renseignée');
    }

    // Kp
    $Kp = $this->getConfiguration('coefKp');  
    if ($Kp != '' && is_numeric($Kp)) {
        $configuration->Kp = floatval($Kp);
    } else {
        log::add(__CLASS__, 'error', '  Kp renseignée:' . $Kp);
        throw new Exception('Kp non renseignée');
    }

    // Ki
    $Ki = $this->getConfiguration('coefKi');  
    if ($Ki != '' && is_numeric($Ki)) {
        $configuration->Ki = floatval($Ki);
    } else {
        log::add(__CLASS__, 'error', '  Ki renseignée:' . $Ki);
        throw new Exception('Ki non renseignée');
    }

    // Kd
    $Kd = $this->getConfiguration('coefKd');  
    if ($Kd != '' && is_numeric($Kd)) {
        $configuration->Kd = floatval($Kd);
    } else {
        log::add(__CLASS__, 'error', '  Kd renseignée:' . $Kd);
        throw new Exception('Kd non renseignée');
    }

    log::add(__CLASS__, 'debug', ' configuration :' . json_encode((array)$configuration));
    return $configuration;
  }

  public function execute() {
    log::add(__CLASS__, 'debug', '> execute sur : '.$this->getHumanName());

    // configuration
    $configuration = $this->getMyConfiguration();

    // sortir si equipement éteint
    if ($configuration->stateAsset == 0) {
      log::add(__CLASS__, 'debug', '> etat éteint, on sort');
      return;
    } 

    log::add(__CLASS__, 'debug', '> etat allumé, on continue');

    // Cache : Correct_integr
    $cache = cache::byKey('Correct_integr');
		$Correct_integr = $cache->getValue();
    if ($Correct_integr == '') {
      $Correct_integr = 0;
    }
    // $Correct_integr = 0;
    log::add('thermoPID', 'debug', ' cache Correct_integr : ' . $Correct_integr);

    // Cache : Correct_erreur_previous
    $cache = cache::byKey('Correct_erreur_previous');
		$Correct_erreur_previous = $cache->getValue();
    if ($Correct_erreur_previous == '') {
      $Correct_erreur_previous = 0;
    }
    // $Correct_erreur_previous = 0;
    log::add('thermoPID', 'debug', ' cache Correct_erreur_previous : ' . $Correct_erreur_previous);

    // Consigne
    $consigne = $this->getCmd(null, 'consigne');
    $consigne = $consigne->execCmd();
    log::add('thermoPID', 'debug', ' consigne : ' . $consigne);

    // Calculs
    $Correct_erreur = $consigne - $configuration->temperature;
    log::add('thermoPID', 'debug', ' Correct_erreur : ' . $Correct_erreur);

    $Correct_prop = $configuration->Kp * $Correct_erreur;
    log::add('thermoPID', 'debug', ' Correct_prop : ' . $Correct_prop);


    $Correct_integr = $Correct_integr + $Correct_erreur;
    $Correct_integr = $configuration->Ki * $Correct_integr;
    log::add('thermoPID', 'debug', ' Correct_integr : ' . $Correct_integr);
    $Correct_integr = max(min($Correct_integr, $configuration->maxIntegr), $configuration->minIntegr); // a commenter
    log::add('thermoPID', 'debug', ' > Correct_integr : ' . $Correct_integr);

    $Correct_deriv = $configuration->Kd * ($Correct_erreur - $Correct_erreur_previous);
    $Correct_deriv = round($Correct_deriv, 2); // ADL 
    log::add('thermoPID', 'debug', ' Correct_deriv : ' . $Correct_deriv);

    $Correct_total = $Correct_prop + $Correct_integr + $Correct_deriv;
    log::add('thermoPID', 'debug', ' Correct_total : ' . $Correct_total);
    $Correct_total = max(min($Correct_total, $configuration->maxTotal), $configuration->minTotal);  // a commenter
    log::add('thermoPID', 'debug', ' > Correct_total : ' . $Correct_total);

    $Temp_consign_clim = round($consigne + $Correct_total);
    $Temp_consign_clim = max(min($Temp_consign_clim, 27), 15);
    log::add('thermoPID', 'debug', ' Temp_consign_clim : ' . $Temp_consign_clim);

    // Cache
    cache::set('Correct_integr', $Correct_integr);
    cache::set('Correct_erreur_previous', $Correct_erreur);

    // Cache : Correct_integr
    $cache = cache::byKey('Correct_integr');
		$Correct_integr = $cache->getValue();
    log::add('thermoPID', 'debug', ' cache Correct_integr : ' . $Correct_integr);

    // Cache : Correct_erreur_previous
    $cache = cache::byKey('Correct_erreur_previous');
		$Correct_erreur_previous = $cache->getValue();
    log::add('thermoPID', 'debug', ' cache Correct_erreur_previous : ' . $Correct_erreur_previous);

    // Comparaison avec consigne déjà setter
    if ($configuration->readConsigne == $Temp_consign_clim) {
      log::add('thermoPID', 'debug', ' valeur climatisation déjà ok : ' . $Temp_consign_clim. '=='.$configuration->readConsigne);
      return;
    }

    // set consigne
    log::add('thermoPID', 'debug', '  set consigne');

    $writeConsigne = $this->getConfiguration('writeConsigne');
    $writeConsigne = str_replace('#', '', $writeConsigne);
    if ($writeConsigne != '') {
      $cmd = cmd::byId($writeConsigne);
      if ($cmd == null) {
        log::add('thermoPID', 'error', '  Mauvaise writeConsigne :' . $writeConsigne);
        throw new Exception('Mauvaise writeConsigne');
      } else {
        log::add('thermoPID', 'debug', '  writeConsigne sur : ' . $cmd->getHumanName() .' avec : ' . $Temp_consign_clim);
        $option = array('slider'=>$Temp_consign_clim);
        $cmd->execCmd($option, $cache=0);
      }
    } else {
        log::add('thermoPID', 'error', '  writeConsigne non renseignée');
        throw new Exception('writeConsigne non renseignée');
    } 
  }
}

class thermoPIDCmd extends cmd {
  /*     * *************************Attributs****************************** */

  /*
  public static $_widgetPossibility = array();
  */

  /*     * ***********************Methode static*************************** */


  /*     * *********************Methode d'instance************************* */

  /*
  * Permet d'empêcher la suppression des commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
  public function dontRemoveCmd() {
    return true;
  }
  */

  // Exécution d'une commande
  public function execute($_options = array()) {
    log::add('thermoPID', 'info', '*** ' . __FUNCTION__ . ' ***');
    log::add('thermoPID', 'info', ' action : ' . $this->getLogicalId());

    if ($this->getLogicalId() == 'refresh') {
      $eqlogic = $this->getEqLogic(); //récupère l'éqlogic de la commande $this
      log::add('thermoPID', 'info', ' > refresh sur ' . $eqlogic->getHumanName());      
      $eqlogic->execute();
    }

    if ($this->getLogicalId() == 'consigne_cursor') {
      $eqlogic = $this->getEqLogic();
      $cmd = $eqlogic->getCmd('info', 'consigne');
      $cmd->event($_options['slider']);
      $eqlogic->execute();
    }
  }

  /*     * **********************Getteur Setteur*************************** */
}
