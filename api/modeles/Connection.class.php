<?php
/**
 * Class Oeuvre
 * 
 * @author Jonathan Martel
 * @version 1.0
 * @update 2014-09-11
 * @license Creative Commons BY-NC 3.0 (Licence Creative Commons Attribution - Pas d’utilisation commerciale 3.0 non transposé)
 * @license http://creativecommons.org/licenses/by-nc/3.0/deed.fr
 * 
 * 
 * 
 */
class Connection extends Modele {		
	/**
	 * Retourne les information de l'utilisateur connecté
	 * @access public
	 * @return Array
	 */
	
    public function getConnectionUser($user, $mdp){
        $query = "SELECT u.nom, u.prenom,u.nom_connexion, r.type_acces
        from usager u
        join role r 
        on r.id_role = u.id_role
        where u.nom_connexion= '$user' and u.mot_passe = '$mdp'";
        if($mrResultat = $this->_db->query($query)){
            $utilisateur = $mrResultat->fetch_assoc();
            if($utilisateur == ""){
                echo "mauvaise combinaison username mdp";
            }
            else{
                return $utilisateur;
            }
           
        }else{
            echo "erreur sql";
        }

    }

    
    
}




?>